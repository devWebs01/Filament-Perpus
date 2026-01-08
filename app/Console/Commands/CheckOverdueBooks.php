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
        $this->newLine();

        // Get borrowed status
        $borrowedStatus = Status::where('name', 'Dipinjam')->first();

        if (! $borrowedStatus) {
            $this->error('Required status "Dipinjam" not found in database.');

            return self::FAILURE;
        }

        // Get ALL transactions where:
        // 1. Status is "Dipinjam" (or we rely on return_date mostly, but status 'Dipinjam' is safer for now)
        // 2. Book is NOT returned yet (return_date is NULL)
        // 3. Due date has passed
        $overdueTransactions = Transaction::with(['book', 'user', 'status'])
            ->where('status_id', $borrowedStatus->id) // Ensure we only check borrowed books
            ->whereNull('return_date')
            ->where('due_date', '<', now()->startOfDay())
            ->where('due_date', '<=', now()->subDays($minDays - 1))
            ->get();

        $count = $overdueTransactions->count();

        if ($count === 0) {
            $this->info('No overdue books found.');

            return self::SUCCESS;
        }

        $this->info("Found {$count} overdue book(s).");
        $this->newLine();

        if ($isVerbose) {
            $this->table(
                ['ID', 'User', 'Email', 'Book', 'Due Date', 'Days Overdue', 'Penalty', 'Status'],
                $overdueTransactions->map(fn ($t) => [
                    $t->id,
                    $t->user->name,
                    $t->user->email,
                    $t->book->title,
                    $t->due_date->format('Y-m-d'),
                    $this->calculateDaysOverdue($t->due_date),
                    'Rp '.number_format($this->calculatePenalty($t->due_date)),
                    $t->status->name,
                ])
            );
            $this->newLine();
        }

        if ($isDryRun) {
            $this->warn('Dry run mode - no notifications sent.');

            return self::SUCCESS;
        }

        // Send notifications without updating status
        $notificationsSent = 0;
        $bar = $this->output->createProgressBar($count);

        $bar->start();

        foreach ($overdueTransactions as $transaction) {
            // Send notification to user
            try {
                $transaction->user->notify(new OverdueBookNotification($transaction));
                $notificationsSent++;

                if ($isVerbose) {
                    Log::info('Overdue notification sent', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                        'user_email' => $transaction->user->email,
                        'book_id' => $transaction->book_id,
                        'book_title' => $transaction->book->title,
                        'due_date' => $transaction->due_date->format('Y-m-d'),
                        'days_overdue' => $this->calculateDaysOverdue($transaction->due_date),
                    ]);
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to send notification for transaction {$transaction->id}: {$e->getMessage()}");
                Log::error('Failed to send overdue notification', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Notifications queued: {$notificationsSent}/{$count}");
        $this->newLine();

        if ($notificationsSent > 0) {
            $this->warn('Note: Emails are in queue. Run "php artisan queue:work" to process them.');
            $this->info('Or keep queue worker running: "php artisan queue:work --daemon"');
        }

        return self::SUCCESS;
    }

    /**
     * Calculate days overdue for a given due date.
     */
    private function calculateDaysOverdue($dueDate): int
    {
        return (int) $dueDate->diffInDays(now()->startOfDay());
    }

    /**
     * Calculate penalty for overdue books.
     * Rp 1.000 per day.
     */
    private function calculatePenalty($dueDate): float
    {
        return $this->calculateDaysOverdue($dueDate) * 1000;
    }
}
