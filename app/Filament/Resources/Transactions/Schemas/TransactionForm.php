<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Formulir Peminjaman Buku')
                    ->description('Lengkapi informasi peminjaman buku dengan benar.')
                    ->schema([
                        Select::make('book_id')
                            ->label('Buku')
                            ->relationship('book', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->author}")
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                                TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->password(),
                            ])
                            ->columnSpanFull(),

                        DatePicker::make('borrow_date')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('return_date')
                            ->label('Tanggal Kembali')
                            ->displayFormat('d/m/Y'),
                    ])->columnSpanFull(),
            ]);
    }
}
