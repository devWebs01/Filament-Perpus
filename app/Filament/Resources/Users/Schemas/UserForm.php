<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

/**
 * User Form Schema
 *
 * This form schema handles user creation and editing with integrated
 * role management and user details for the library system.
 */
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information Section
                Section::make('Basic Information')
                    ->description('User account information and credentials')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus(),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->helperText(fn (string $context): string => $context === 'edit' ? 'Leave empty to keep current password' : ''),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->helperText('When the email was verified. Leave empty for unverified.'),
                            ]),
                    ]),

                // User Details Section
                Section::make('Library Information')
                    ->description('Additional information for library system')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('userDetails.phone_number')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),

                                TextInput::make('userDetails.nik')
                                    ->label('NIK (National ID)')
                                    ->maxLength(16)
                                    ->helperText('16-digit national identification number'),

                                TextInput::make('userDetails.birth_date')
                                    ->label('Birth Date')
                                    ->type('date')
                                    ->maxDate('today'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('userDetails.birth_place')
                                    ->label('Birth Place')
                                    ->maxLength(100),

                                Select::make('userDetails.gender')
                                    ->label('Gender')
                                    ->options([
                                        'L' => 'Laki-laki (Male)',
                                        'P' => 'Perempuan (Female)',
                                    ])
                                    ->searchable(),
                            ]),

                        Textarea::make('userDetails.address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500),

                        // Student Information (conditional)
                        Grid::make(3)
                            ->schema([
                                TextInput::make('userDetails.nis')
                                    ->label('NIS (Student ID)')
                                    ->maxLength(20)
                                    ->helperText('Student identification number'),

                                TextInput::make('userDetails.nisn')
                                    ->label('NISN (National Student ID)')
                                    ->maxLength(10)
                                    ->helperText('10-digit national student ID'),

                                TextInput::make('userDetails.class')
                                    ->label('Class')
                                    ->maxLength(10)
                                    ->placeholder('e.g., 12A'),
                            ]),
                    ])
                    ->collapsed()
                    ->collapsible(),

                // Role Assignment Section
                Section::make('Role & Permissions')
                    ->description('Assign user roles and permissions for system access')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Assign Roles')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $availableRoles = [
                                    'super_admin' => 'Super Admin - Full system access',
                                    'library_head' => 'Library Head - Management permissions',
                                    'staff' => 'Library Staff - Operational permissions',
                                    'student' => 'Student - Basic access',
                                ];

                                // Only show roles that current user can assign
                                if (! auth()->user()->hasRole('super_admin')) {
                                    unset($availableRoles['super_admin']);
                                }

                                return $availableRoles;
                            })
                            ->bulkToggleable()
                            ->helperText(new HtmlString('
                                <strong>Role Permissions:</strong><br>
                                • <strong>Super Admin:</strong> Full system access<br>
                                • <strong>Library Head:</strong> Can manage books, users, transactions<br>
                                • <strong>Staff:</strong> Can handle books and transactions<br>
                                • <strong>Student:</strong> Can browse books and view own transactions
                            '))
                            ->required()
                            ->columns(2),

                        Select::make('userDetails.membership_status')
                            ->label('Library Membership Status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'expired' => 'Expired',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }
}
