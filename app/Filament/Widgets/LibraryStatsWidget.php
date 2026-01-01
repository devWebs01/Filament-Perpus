<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LibraryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBooks = Book::count();
        $totalBooksAvailable = Book::sum('book_count');

        $borrowedStatus = Status::where('name', 'Dipinjam')->first()?->id;
        $overdueStatus = Status::where('name', 'Terlambat')->first()?->id;

        $activeBorrows = Transaction::where('status_id', $borrowedStatus)->count() ?? 0;
        $overdueBooks = Transaction::where('status_id', $overdueStatus)->count() ?? 0;

        $totalMembers = User::whereHas('roles', function ($query) {
            $query->where('name', 'member');
        })->count();

        $availableBooks = $totalBooksAvailable - $activeBorrows;

        return [
            Stat::make('Total Buku', number_format($totalBooks))
                ->description('Judul buku dalam katalog')
                ->chart([7, 12, 10, 14, 15, $totalBooks])
                ->color('primary'),

            Stat::make('Total Eksemplar', number_format($totalBooksAvailable))
                ->description('Jumlah semua eksemplar')
                ->color('success'),

            Stat::make('Buku Dipinjam', number_format($activeBorrows))
                ->description('Sedang dipinjam')
                ->chart([3, 5, 4, 6, 7, $activeBorrows])
                ->color('warning'),

            Stat::make('Buku Tersedia', number_format(max(0, $availableBooks)))
                ->description('Bisa dipinjam')
                ->color('success'),

            Stat::make('Buku Terlambat', number_format($overdueBooks))
                ->description('Melebihi tanggal kembali')
                ->color('danger'),

            Stat::make('Total Anggota', number_format($totalMembers))
                ->description('Anggota terdaftar')
                ->chart([10, 15, 13, 17, 20, $totalMembers])
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
