<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->description('Basic user account information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::default())
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'Leave empty to keep current password' : 'Password must be at least 8 characters'),
                    ]),

                Forms\Components\Section::make('User Details')
                    ->description('Detailed information about the user')
                    ->schema([
                        Forms\Components\Repeater::make('userDetail')
                            ->label('User Details')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nik')
                                            ->label('NIK (National ID)')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('nis')
                                            ->label('NIS (Student ID)')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('nisn')
                                            ->label('NISN (National Student ID)')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('phone_number')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('class')
                                            ->label('Class')
                                            ->placeholder('e.g., 12A, 10B'),

                                        Forms\Components\TextInput::make('major')
                                            ->label('Major')
                                            ->placeholder('e.g., Science, Social'),

                                        Forms\Components\TextInput::make('semester')
                                            ->label('Semester')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(8),

                                        Forms\Components\Select::make('gender')
                                            ->label('Gender')
                                            ->options([
                                                'male' => 'Male',
                                                'female' => 'Female',
                                            ]),

                                        Forms\Components\Select::make('religion')
                                            ->label('Religion')
                                            ->options([
                                                'islam' => 'Islam',
                                                'christian' => 'Christian',
                                                'catholic' => 'Catholic',
                                                'hindu' => 'Hindu',
                                                'buddhist' => 'Buddhist',
                                                'confucian' => 'Confucian',
                                                'other' => 'Other',
                                            ]),

                                        Forms\Components\Select::make('membership_status')
                                            ->label('Membership Status')
                                            ->options([
                                                'active' => 'Active',
                                                'suspended' => 'Suspended',
                                                'expired' => 'Expired',
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('birth_date')
                                            ->label('Birth Date'),

                                        Forms\Components\TextInput::make('birth_place')
                                            ->label('Birth Place')
                                            ->maxLength(255),

                                        Forms\Components\DatePicker::make('join_date')
                                            ->label('Join Date')
                                            ->default(now()),

                                        Forms\Components\FileUpload::make('profile_photo')
                                            ->label('Profile Photo')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('profile-photos')
                                            ->visibility('public')
                                            ->avatar(),
                                    ]),

                                Forms\Components\Textarea::make('address')
                                    ->label('Address')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->collapsible()
                            ->collapsed(),
                    ]),

                Forms\Components\Section::make('Role Assignment')
                    ->description('Assign roles and permissions to the user')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Select one or more roles for this user'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied to clipboard')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('userDetail.getUserTypeDisplayName')
                    ->label('User Type')
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->userDetail?->isLibraryHead() => 'danger',
                        $record->userDetail?->isStudent() => 'success',
                        $record->userDetail?->isStaff() => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('userDetail.membership_status')
                    ->label('Membership')
                    ->badge()
                    ->color(fn ($record): string => match ($record->userDetail?->membership_status) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('userDetail.class')
                    ->label('Class')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('userDetail.major')
                    ->label('Major')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn ($record): string => $record->email_verified_at ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('User Type')
                    ->options([
                        'student' => 'Student',
                        'staff' => 'Library Staff',
                        'library_head' => 'Library Head',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('userDetail', function (Builder $query) use ($data) {
                            return match ($data['value']) {
                                'student' => $query->students(),
                                'staff' => $query->staff(),
                                'library_head' => $query->libraryHeads(),
                                default => $query,
                            };
                        });
                    }),

                Tables\Filters\SelectFilter::make('membership_status')
                    ->label('Membership Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('userDetail', function (Builder $query) use ($data) {
                            return $query->where('membership_status', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Verified Email'),

                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
                    ->label('Unverified Email'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete User')
                    ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete user'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Users')
                        ->modalDescription('Are you sure you want to delete these users? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete users'),
                ]),
            ])
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('No users have been registered yet.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create first user'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
