# Filament 4 - Custom Packages Integration

## Overview
Project ini menggunakan beberapa package Filament tambahan. Berikut guidelines untuk penggunaannya.

## Package Terinstall

### 1. Filament Shield - `bezhansalleh/filament-shield`
Role & Permission management untuk Filament.

#### Commands
```bash
# Install shield
php artisan shield:install

# Generate permissions untuk resources
php artisan shield:generate --all

# Create super admin
php artisan shield:super-admin --user=1

# Publish translations
php artisan shield:translation --locale=id
```

#### Usage di Resources
```php
use BezhanSalleh\FilamentShield\Forms\Components\ShieldSelect;
use BezhanSalleh\FilamentShield\Support\Utils;

// Di Resource class
protected static ?string $permission = 'view_any_posts';

// Di form
ShieldSelect::make('roles')
    ->multiple()
    ->preload()
    ->searchable()
    ->relationship('roles', 'name');
```

#### Check Permissions
```php
// Di blade
@can('view_any_posts')
    // ...
@endcan

// Di PHP
$user->can('view_any_posts');
```

#### Config
```php
// config/shield.php
'permission_prefixes' => [
    'resource' => [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'delete_any',
        'force_delete',
        'force_delete_any',
        'restore',
        'restore_any',
    ],
],
```

---

### 2. Filament Edit Profile - `joaopaulolndev/filament-edit-profile`
Custom profile edit page.

#### Commands
```bash
php artisan filament-edit-profile:install
```

#### Custom Fields
```php
use Joaopaulolndev\FilamentEditProfile\Forms\Components\IconButton;

// Custom form di config atau publish views
// resources/views/vendor/filament-edit-profile/profile-form.blade.php
```

#### Common Usage
- Edit password
- Upload avatar
- Custom profile fields (phone, address, dll)

---

### 3. Filament Export - `alperenersoy/filament-export`
Export data ke berbagai format.

#### Usage di Resources
```php
use Alperenersoy\FilamentExport\Actions\FilamentExportBulkAction;
use Alperenersoy\FilamentExport\Actions\FilamentExportHeaderAction;

// Bulk action
public static function getTableBulkActions(): array
{
    return [
        FilamentExportBulkAction::make('export')
            ->fileName('posts-' . date('Y-m-d'))
            ->format([
                \Alperenersoy\FilamentExport\Enums\ExportFormat::Csv,
                \Alperenersoy\FilamentExport\Enums\ExportFormat::Xlsx,
            ]),
    ];
}

// Header action
public static function getTableActions(): array
{
    return [
        FilamentExportHeaderAction::make('export-all')
            ->timeFormat('d-m-Y_H-i'),
    ];
}
```

---

### 4. Filament QRCode Field - `jeffersongoncalves/filament-qrcode-field`
Generate dan display QR code di forms.

#### Usage
```php
use Jeffersongoncalves\FilamentQrcodeField\Forms\QrCodeColumn;
use Jeffersongoncalves\FilamentQrcodeField\Forms\QrCodeField;

// Di form
QrCodeField::make('qr_code')
    ->label('QR Code')
    ->value(fn ($record) => route('book-detail', $record))
    ->generateOnEntry() // Auto-generate saat create

// Di table
QrCodeColumn::make('qr_code')
    ->label('Scan')
```

---

### 5. Livewire Alert - `jantinnerezo/livewire-alert`
Flash notifications untuk Livewire.

#### Usage di Livewire Components
```php
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MyComponent extends Component
{
    use LivewireAlert;

    public function save(): void
    {
        // Success
        $this->alert('success', 'Data berhasil disimpan!');

        // Error
        $this->alert('error', 'Terjadi kesalahan!');

        // Warning
        $this->alert('warning', 'Perhatian!');

        // Info
        $this->alert('info', 'Informasi penting');

        // With configuration
        $this->alert('success', 'Saved!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
}
```

#### Available Positions
- 'top-right'
- 'top-left'
- 'top-center'
- 'bottom-right'
- 'bottom-left'
- 'bottom-center'
- 'center'

---

### 6. Mary - UI Components
Alternative UI components library.

#### Usage
```blade
<x-mary-button icon="o-user" label="Profile" />
<x-mary-input label="Email" wire:model="email" />
<x-mary-modal id="modal-id">
    <x-slot name="title">Modal Title</x-slot>
    Modal content here
</x-mary-modal>
```

---

### 7. Barcode - `milon/barcode`
Generate barcode untuk books/items.

#### Usage
```php
use Milon\Barcode\DNS2D;

// Di blade
{!! DNS2D::getBarcodeHTML('081231723897', 'QRCODE') !!}

// QR Code
{!! DNS2D::getBarcodeHTML($book->id, 'QRCODE') !!}

// Barcode 1D
{!! DNS1D::getBarcodeHTML('123456789012', 'C39') !!}
```

---

### 8. Google Translate - `stichoza/google-translate-php`
Translation service.

#### Usage
```php
use Stichoza\GoogleTranslate\GoogleTranslate;

$translate = new GoogleTranslate('id'); // Target language
$result = $translate->translate('Hello World'); // 'Halo Dunia'

// Detect language
$translate = new GoogleTranslate();
$lang = $translate->detect('Hallo Welt'); // 'de'

// Translate with source
$translate->setSource('en')->setTarget('id')->translate('Book');
```

---

### 9. Userstamps - `wildside/userstamps`
Automatic created_by, updated_by tracking.

#### Setup
```bash
php artisan userstamps:install
```

#### Usage di Models
```php
use Wildside\Userstamps\Userstamps;

class Book extends Model
{
    use Userstamps;

    // Auto-adds:
    // - created_by
    // - updated_by
    // - deleted_by
}
```

#### Migration
```php
$table->unsignedBigInteger('created_by')->nullable();
$table->unsignedBigInteger('updated_by')->nullable();
$table->unsignedBigInteger('deleted_by')->nullable();
```

---

## General Best Practices untuk Package Filament

### 1. Publish Configs
```bash
# Publish config untuk customization
php artisan vendor:publish --tag="filament-shield-config"
php artisan vendor:publish --tag="filament-edit-profile-config"
```

### 2. Override Views
```bash
# Publish views untuk customization
php artisan vendor:publish --tag="filament-edit-profile-views"
```

### 3. Check Package Updates
```bash
composer outdated
```

### 4. Cache Permissions (Shield)
```bash
php artisan shield:cache-reset
```

---

## Integration Pattern Examples

### Resource dengan Shield Permission
```php
class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $permission = 'books';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                QrCodeField::make('qr_code')
                    ->value(fn ($record) => $record->id)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                QrCodeColumn::make('id')
                    ->label('QR'),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export-all'),
            ]);
    }
}
```

### Livewire Component dengan Alert
```php
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BorrowBook extends Component
{
    use LivewireAlert;

    public function borrow($bookId): void
    {
        // Validate
        // Create transaction

        $this->alert('success', 'Buku berhasil dipinjam!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);
    }

    public function render()
    {
        return view('livewire.borrow-book');
    }
}
```
