<?php

namespace App\Console\Commands;

use App\Models\Status;
use App\Models\Transaction;
use App\Notifications\OverdueBookNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:check-overdue
                            {--days=1 : Minimum days overdue to notify}
                            {--dry-run : Run without sending notifications}
                            {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue books and send notifications to users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minDays = (int) $this->option('days');
        $isDryRun = $this->option('dry-run');
        $isVerbose = $this->option('detailed');

        $this->info("Checking for overdue books (minimum {$minDays} day(s) overdue)...");

        // Get borrowed and overdue statuses
        $borrowedStatus = Status::where('name', 'Dipinjam')->first();
        $overdueStatus = Status::where('name', 'Terlambat')->first();

        if (! $borrowedStatus || ! $overdueStatus) {
            $this->error('Required statuses not found in database.');

            return self::FAILURE;
        }

        // Get overdue transactions
        $overdueTransactions = Transaction::with(['book', 'user'])
            ->where('status_id', $borrowedStatus->id)
            ->where('due_date', '<', now()->subDays($minDays - 1))
            ->get();

        $count = $overdueTransactions->count();

        if ($count === 0) {
            $this->info('No overdue books found.');

            return self::SUCCESS;
        }

        $this->info("Found {$count} overdue book(s).");

        if ($isVerbose) {
            $this->table(
                ['ID', 'User', 'Book', 'Due Date', 'Days Overdue', 'Penalty'],
                $overdueTransactions->map(fn ($t) => [
                    $t->id,
                    $t->user->name,
                    $t->book->title,
                    $t->due_date->format('Y-m-d'),
                    $t->getDaysOverdue(),
                    'Rp '.number_format($t->getPenalty()),
                ])
            );
        }

        if ($isDryRun) {
            $this->warn('Dry run mode - no notifications sent.');

            return self::SUCCESS;
        }

        // Update status to overdue and send notifications
        $notificationsSent = 0;
        $bar = $this->output->createProgressBar($count);

        $bar->start();

        foreach ($overdueTransactions as $transaction) {
            // Update status to overdue
            $transaction->update(['status_id' => $overdueStatus->id]);

            // Send notification to user
            try {
                $transaction->user->notify(new OverdueBookNotification($transaction));
                $notificationsSent++;

                if ($isVerbose) {
                    Log::info('Overdue notification sent', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                        'book_id' => $transaction->book_id,
                    ]);
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to send notification for transaction {$transaction->id}: {$e->getMessage()}");
                Log::error('Failed to send overdue notification', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Notifications sent: {$notificationsSent}/{$count}");

        return self::SUCCESS;
    }
}
