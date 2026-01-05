<laravel-boost-guidelines>
=== .ai/custom-package-guidelines rules ===

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


=== .ai/official-guide rules ===

# Livewire Volt - Official Documentation Guide

## Overview
Volt is an elegantly crafted functional API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to coexist in the same file.

**Official Docs**: [Livewire Volt Documentation](https://livewire.laravel.com/docs/3.x/volt)

## Installation

```bash
composer require livewire/volt
php artisan volt:install
```

## Creating Components

```bash
# Basic component
php artisan make:volt counter

# With test file
php artisan make:volt counter --test --pest

# Class-based component
php artisan make:volt counter --class
```

## Core Concepts

### State Management

```php
<?php
use function Livewire\Volt\{state};

state(['count' => 0]);

// Or with closure for lazy evaluation
state(['count' => fn () => User::count()]);
?>
```

### Actions

```php
<?php
use function Livewire\Volt\{state};

state(['count' => 0]);

// Simple action
$increment = fn () => $this->count++;

// Action with dependencies
$delete = function (PostRepository $posts) {
    $posts->delete($this->postId);
};
?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

### Computed Properties

```php
<?php
use App\Models\User;
use function Livewire\Volt\{computed};

$count = computed(function () {
    return User::count();
});

// With cache persistence (default 3600s)
$count = computed(function () {
    return User::count();
})->persist();

// Custom cache duration
$count = computed(function () {
    return User::count();
})->persist(seconds: 10);
?>
```

### Lifecycle Hooks

```php
<?php
use function Livewire\Volt\{mount, boot, booted, hydrate, dehydrate, updating, updated};

mount(fn () => /* Component initialization */);
boot(fn () => /* Before mount */);
booted(fn () => /* After mount */);
hydrate(fn () => /* Before hydration */);
dehydrate(fn () => /* After dehydration */);
updating(['count' => fn () => /* Before updating count */]);
updated(['count' => fn () => /* After updating count */]);
?>
```

## Validation

```php
<?php
use function Livewire\Volt\{rules};

rules([
    'name' => 'required|min:6',
    'email' => 'required|email',
])
->messages([
    'email.required' => 'Email is required',
])
->attributes([
    'email' => 'email address',
]);

$submit = function () {
    $this->validate();
};
?>
```

## Forms

```php
<?php
use App\Livewire\Forms\PostForm;
use function Livewire\Volt\{form};

form(PostForm::class);

$save = function () {
    $this->form->store();
};
?>

<form wire:submit="save">
    <input type="text" wire:model="form.title">
    @error('form.title') <span>{{ $message }}</span> @enderror
    <button type="submit">Save</button>
</form>
```

## Pagination

```php
<?php
use function Livewire\Volt\{usesPagination, with};

usesPagination();

with(fn () => [
    'posts' => Post::paginate(10),
]);
?>

<div>
    @foreach ($posts as $post)
        <!-- ... -->
    @endforeach
    {{ $posts->links() }}
</div>
```

## Property Modifiers

```php
<?php
use function Livewire\Volt\{state};

// Locked - client cannot modify
state(['id'])->locked();

// Reactive - auto-update from parent
state(['todos'])->reactive();

// Modelable - share state via wire:model
state(['form'])->modelable();

// URL - sync with query params
state(['search'])->url();

// With options
state(['page' => 1])->url(as: 'p', history: true, keep: true);
?>
```

## Anonymous Components with @volt

```php
<?php
use function Livewire\Volt\{state};

state(['count' => 0]);
$increment = fn () => $this->count++;
?>

<x-app-layout>
    @volt('counter')
        <div>
            <h1>{{ $count }}</h1>
            <button wire:click="increment">+</button>
        </div>
    @endvolt
</x-app-layout>
```

## Class-Based Components

```php
<?php
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('layouts.guest')]
#[Title('Login')]
class extends Component
{
    use WithPagination;

    public string $name = '';

    public function with(): array
    {
        return [
            'posts' => Post::paginate(10),
        ];
    }

    public function rendering(\Illuminate\View\View $view): void
    {
        $view->title('Custom Title');
    }
}
?>
```

## File Uploads

```php
<?php
use function Livewire\Volt\{state, usesFileUploads};

usesFileUploads();

state(['photo']);

$save = function () {
    $this->validate([
        'photo' => 'image|max:1024',
    ]);

    $this->photo->store('photos');
};
?>
```

## Event Listeners

```php
<?php
use function Livewire\Volt\{on};

on(['eventName' => function () {
    // Handle event
}]);

// Dynamic listeners
on(fn ($post) => [
    'event-'.$post->id => function () {
        // Handle dynamic event
    },
]);
?>
```

## Traits

```php
<?php
use function Livewire\Volt\{uses};

use App\Contracts\Sorting;
use App\Concerns\WithSorting;

uses([Sorting::class, WithSorting::class]);
?>
```

## Testing

```php
use Livewire\Volt\Volt;

it('increments the counter', function () {
    Volt::test('counter')
        ->assertSee('0')
        ->call('increment')
        ->assertSee('1');
});

// Nested component
Volt::test('users.stats');

// Anonymous component in page
$this->get('/users')
    ->assertSeeVolt('stats');
```

## Best Practices

1. **Use closures for expensive operations** - Prevents premature execution
2. **Use `wire:key` in loops** - Ensures proper reactivity
3. **Use `with()` for computed data** - Cleaner than inline logic
4. **Use `action()->renderless()` for non-rendering actions** - Performance optimization
5. **Use `protect()` for private helpers** - Security best practice

## Resources

- [Official Documentation](https://livewire.laravel.com/docs/3.x/volt)
- [GitHub Repository](https://github.com/livewire/volt)
- [Introducing Volt Blog Post](https://laravel.com/blog/introducing-volt-an-elegantly-crafted-functional-api-for-livewire)


=== .ai/pages rules ===

# Laravel Folio - Page Based Routing

## Overview
Folio menyediakan page-based routing untuk Laravel. Routes otomatis didaftarkan berdasarkan file structure di `resources/views/pages/`.

## Conventions

### File Structure = Routes
```
resources/views/pages/
├── catalog.blade.php           → /catalog
├── my-books/
│   └── index.blade.php         → /my-books
├── book-detail/
│   └── [book].blade.php        → /book-detail/{book}
└── api/
    └── [id].blade.php          → /api/{id}
```

### Named Routes
```php
<?php
use function Laravel\Folio\name;

name('catalog'); // Route bernama 'catalog'
```

### Route Parameters
```php
<?php
use function Laravel\Folio\{name, middleware};

// Parameter dari URL: [book].blade.php
$book = fn($book) => Book::findOrFail($book);

// Atau dari query string
$bookId = fn(?int $id = null) => $id ?? 1;
```

### Middleware
```php
<?php
use function Laravel\Folio\middleware;

middleware(['auth', 'verified']);

// Atau per-route
middleware(['auth'])->only([['book-detail', 'create']]);
```

## Best Practices

### 1. Gunakan Volt di Folio Pages
```php
<?php
use function Livewire\Volt\{state, with};

state(['search' => '']);

with([
    'books' => fn() => Book::when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))->paginate(12),
]);
?>

@volt
<div>{{ $books->links() }}</div>
@endvolt
```

### 2. Route Model Binding
```php
<?php
// Automatic model resolution
$book = fn(Book $book) => $book;

// Custom resolution with validation
$book = function ($book) {
    return Book::where('slug', $book)->firstOrFail();
};
?>
```

### 3. Validation di Page Level
```php
<?php
use Illuminate\Http\Request;

// Untuk form submissions
$validated = request()->validate([
    'title' => 'required|max:255',
    'author' => 'required',
]);
?>
```

## Folder Organization

### Grouped Routes
```
pages/
├── admin/              → Semua routes /admin/*
│   ├── users.blade.php
│   └── books.blade.php
├── user/               → Semua routes /user/*
│   ├── profile.blade.php
│   └── settings.blade.php
└── api/                → Semua routes /api/*
```

### Index & Show Pattern
```
pages/
├── books/
│   ├── index.blade.php      → /books (list)
│   └── [book].blade.php     → /books/{book} (detail)
```

## Accessing Named Routes

```blade
<!-- Generate URL -->
<a href="{{ route('catalog') }}">Catalog</a>

<!-- Redirect -->
{{ redirect()->route('my-books') }}

<!-- Livewire dispatch -->
<script>
    Livewire.dispatch('navigate', { url: '{{ route('catalog') }}' });
</script>
```

## Common Patterns

### Paginated Index dengan Search
```php
<?php
use function Laravel\Folio\name;
use function Livewire\Volt\{state, with};

name('books');

state(['search' => '']);

with([
    'books' => function () {
        return Book::when($this->search, fn($q) =>
            $q->where('title', 'like', '%' . $this->search . '%')
        )->paginate(12);
    },
]);
?>

@volt
<div>
    <input wire:model.live="search" placeholder="Cari buku..." />
    @foreach ($books as $book)
        <div>{{ $book->title }}</div>
    @endforeach
    {{ $books->links() }}
</div>
@endvolt
```

### Detail Page dengan Related Data
```php
<?php
use App\Models\Book;

$book = fn(Book $book) => $book->load(['category', 'author']);
?>

<x-book-detail :book="$book" />
```

## Tips

1. **List routes**: `php artisan folio:list`
2. **Cache routes**: `php artisan route:cache`
3. **Clear cache**: `php artisan route:clear`
4. **Gunakan folder untuk grouping** - lebih rapi daripada nama file panjang
5. **Parameter pakai bracket** `[slug].blade.php` bukan `{slug}.blade.php`


=== .ai/custom-components rules ===

# Livewire Volt - Functional Components

## Overview
Volt menyediakan functional API untuk membuat Livewire components. Menggunakan sintaks function-based daripada class-based.

## Conventions

### File Structure
- Volt components diletakkan di folder `resources/views/livewire/` atau sebagai Folio pages
- Gunakan `@volt` directive di Blade files
- Namespace: `App\Livewire` untuk class-based, `Livewire\Volt` untuk functional

### State Management
```php
// Define state dengan state()
state(['search' => '', 'filter' => 'all']);

// Access state dengan $this
$updatedSearch = function ($value) {
    $this->search = $value; // atau $this->search = ...
};
```

### Computed Properties (with)
```php
with([
    'transactions' => function () {
        return Transaction::paginate(10);
    },
    'stats' => function () {
        return ['active' => 1, 'returned' => 2];
    },
]);
```

### Actions
```php
$extendLoan = function ($transactionId) {
    $transaction = Transaction::find($transactionId);
    // logic here
};

// Call dari blade: onclick="Livewire.dispatch('extendLoan', { id: 1 })"
```

### Lifecycle Hooks
```php
$mount = function () {
    // Initialize component
};

$updatedSearch = function () {
    // Reactive side effect when search changes
};
```

## Best Practices

1. **Gunakan `wire:key` di loops**
   ```blade
   @foreach ($items as $item)
       <div wire:key="item-{{ $item->id }}">{{ $item->name }}</div>
   @endforeach
   ```

2. **Validasi di actions**
   ```php
   $submit = function () {
       $this->validate([
           'email' => 'required|email',
       ]);
   };
   ```

3. **Gunakan `wire:model.live` untuk real-time updates**
   ```blade
   <input wire:model.live="search" />
   ```

4. **Pagination reset**
   ```php
   $this->resetPage(); // Reset ke halaman 1
   ```

## Common Patterns

### Paginated List dengan Search & Filter
```php
state(['search' => '', 'filter' => 'all']);

with([
    'items' => function () {
        $query = Item::query();

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->paginate(10);
    },
]);

$refreshData = function () {
    $this->resetPage();
};
```

### Cache di Computed Properties
```php
with([
    'stats' => function () {
        $cacheKey = 'stats_' . auth()->id();
        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            // Expensive query here
        });
    },
]);
```

## Blade Integration
```blade
@volt
<div>
    {{ $transactions->links() }}
</div>
@endvolt

<!-- Atau dengan named volt component -->
<volt-my-component :userId="$user->id" />
```

## Important Notes

- Gunakan `<livewire:` tag untuk inline components dengan `:key`
- Hindari `@livewire()` directive dengan parameter `key:` (tidak supported)
- Dispatch events: `Livewire.dispatch('eventName', { param: value })`


=== .ai/integration-patterns rules ===

# Livewire 3 - Full Stack Framework

## Overview
Livewire 3 adalah major rewrite dengan performa lebih baik dan API yang lebih bersih. Namespace: `App\Livewire`

## Key Changes from Livewire 2

### 1. wire:model Deferred by Default
```blade
<!-- Livewire 3: deferred by default (update on blur/change) -->
<input wire:model="search" />

<!-- Untuk real-time updates, gunakan .live modifier -->
<input wire:model.live="search" />

<!-- Livewire 2: real-time by default (deprecated) -->
<input wire:model.defer="search" /> ❌
```

### 2. Dispatch Events
```php
// Livewire 3
$this->dispatch('postCreated');

// Livewire 2 (deprecated)
$this->emit('postCreated'); ❌
$this->emitTo('comments', 'postCreated'); ❌
$this->emitUp('postCreated'); ❌

// Dispatch to specific component
$this->dispatch('postCreated')->to(ComponentName::class);

// Dispatch to browser (JS)
$this->dispatch('notify')->self();
```

### 3. Component Namespace
```php
// Livewire 3
namespace App\Livewire;

class UserProfile extends Component
{
    // ...
}

// Livewire 2 (deprecated)
namespace App\Http\Livewire; ❌
```

### 4. Alpine.js Included
Alpine.js sudah included secara default. Tidak perlu include manual.

```blade
<!-- Alpine dan Livewire bekerja seamless -->
<div x-data="{ open: false }">
    <button @click="open = true">Open</button>
    <div x-show="open">
        <input wire:model="search" />
    </div>
</div>
```

## Conventions

### Component Structure
```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Reactive, Locked, Computed};

class UserProfile extends Component
{
    // Public properties = state
    public string $search = '';
    public int $userId;

    // Computed properties
    #[Computed]
    public function user(): User
    {
        return User::find($this->userId);
    }

    // Reactive props (auto-update saat parent berubah)
    #[Reactive]
    public $postId;

    // Locked props (tidak dikirim ke client)
    #[Locked]
    public string $apiKey;

    // Actions
    public function save(): void
    {
        $this->validate();
        // Save logic
    }

    // Lifecycle hooks
    public function mount(): void
    {
        // Initialize
    }

    public function updatingSearch(): void
    {
        // Before search updates
    }

    public function updatedSearch(): void
    {
        // After search updates - reset pagination
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
```

### Validation
```php
class CreatePost extends Component
{
    public string $title = '';
    public string $content = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'content' => 'required|min:10',
    ];

    // Custom error messages
    protected $messages = [
        'title.required' => 'Judul wajib diisi',
    ];

    public function save(): void
    {
        $validated = $this->validate();

        Post::create($validated);

        session()->flash('message', 'Post berhasil dibuat!');
    }

    // Real-time validation
    public function updated($property): void
    {
        $this->validateOnly($property);
    }
}
```

### Pagination
```php
class PostList extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind'; // atau 'bootstrap'

    public function render()
    {
        return view('livewire.post-list', [
            'posts' => Post::where('title', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ]);
    }

    // Reset page saat filter berubah
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
```

## Blade Directives

### New Directives di Livewire 3
```blade
<!-- Loading state -->
<div wire:loading>wire:loading class was added</div>
<div wire:loading.remove>Remove me when loading</div>
<div wire:loading.class="opacity-50">Add class when loading</div>
<div wire:loading.target="save">Only for save action</div>

<!-- Dirty state (unsaved changes) -->
<div wire:dirty>Changes unsaved</div>

<!-- Conditional rendering -->
<div wire:show="showModal">Show when $showModal is true</div>
<div wire:hide="showModal">Hide when $showModal is true</div>

<!-- Transitions -->
<div wire:transition.fade>Smooth fade</div>
<div wire:transition.slide>Slide effect</div>

<!-- Offline detection -->
<div wire:offline>You are offline</div>

<!-- Clock (auto-refresh every second) -->
<div wire:clock>2024-01-01 12:00:00</div>

<!-- Polling (auto-refresh) -->
<div wire:poll.5s>Refresh every 5 seconds</div>

<!-- Navigate without page reload -->
<a wire:navigate href="/other-page">SPA-like navigation</a>
```

## Best Practices

### 1. Eager Loading untuk Relationships
```php
public function render()
{
    // ❌ N+1 queries
    return view('livewire.posts', [
        'posts' => Post::paginate(10),
    ]);

    // ✅ Eager load
    return view('livewire.posts', [
        'posts' => Post::with(['author', 'category'])->paginate(10),
    ]);
}
```

### 2. Gunakan Form Request untuk Complex Validation
```php
use App\Http\Requests\StorePostRequest;

public function save(StorePostRequest $request): void
{
    Post::create($request->validated());
}
```

### 3. Database Transactions
```php
use Illuminate\Support\Facades\DB;

public function transferFunds(): void
{
    DB::transaction(function () {
        // Multiple related operations
    });
}
```

### 4. Cache Expensive Queries
```php
public function render()
{
    $stats = cache()->remember('user.stats', now()->addHour(), function () {
        return User::count();
    });

    return view('livewire.dashboard', compact('stats'));
}
```

## Testing
```php
use Livewire\Attributes\Validate;

test('user can create post', function () {
    Livewire::test(CreatePost::class)
        ->set('title', 'Test Post')
        ->set('content', 'Test content here')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas(Post::class, [
        'title' => 'Test Post',
    ]);
});
```

## Common Patterns

### Datatable dengan Search & Filter
```php
class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = 'all';
    public int $perPage = 10;

    public function render(): View
    {
        $query = User::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        return view('livewire.user-list', [
            'users' => $query->paginate($this->perPage),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }
}
```

### Modal Form
```php
class CreateUserModal extends Component
{
    public bool $showModal = false;
    public string $name = '';
    public string $email = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ];

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'email']);
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->closeModal();
        $this->dispatch('userCreated');
    }
}
```

## Tips

1. **Gunakan `wire:key` di loops** untuk reactivity yang benar
2. **Reset pagination** saat filter/search berubah
3. **Lazy load heavy components** dengan `#[Lazy]` attribute
4. **Gunakan `wire:navigate`** untuk SPA-like experience
5. **Profile dengan Laravel Telescope** untuk identifikasi bottleneck


=== .ai/livewire-3-guide rules ===

# Livewire 3 - Official Documentation Guide

**Official Docs**: [Livewire 3 Documentation](https://livewire.laravel.com/docs/3.x)

## Overview
Livewire is a full-stack framework for Laravel that allows you to build dynamic, reactive interfaces without leaving PHP. Livewire 3 is a major rewrite with improved performance and new features.

## Key Changes from Livewire 2

### 1. Namespace Change
```php
// Livewire 3 (Correct)
namespace App\Livewire;

class UserProfile extends Component
{
    // ...
}

// Livewire 2 (Deprecated)
namespace App\Http\Livewire; // ❌
```

### 2. wire:model Deferred by Default
```blade
<!-- Livewire 3: deferred by default -->
<input wire:model="search" />

<!-- For real-time updates, use .live -->
<input wire:model.live="search" />

<!-- Livewire 2 (Deprecated) -->
<input wire:model.defer="search" /> ❌
```

### 3. Dispatch Events
```php
// Livewire 3
$this->dispatch('postCreated');
$this->dispatch('postCreated')->to(ComponentName::class);
$this->dispatch('notify')->self();

// Livewire 2 (Deprecated)
$this->emit('postCreated'); ❌
$this->emitTo('comments', 'postCreated'); ❌
```

### 4. Alpine.js Included
Alpine.js is now included automatically. No need to manually include it.

## Component Structure

```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Reactive, Locked, Computed};

class UserProfile extends Component
{
    // Public properties = state
    public string $search = '';
    public int $userId;

    // Computed properties
    #[Computed]
    public function user(): User
    {
        return User::find($this->userId);
    }

    // Reactive props (auto-update when parent changes)
    #[Reactive]
    public $todos = [];

    // Locked props (client cannot modify)
    #[Locked]
    public string $apiKey;

    // Actions
    public function save(): void
    {
        $this->validate();
        // Save logic
    }

    // Lifecycle hooks
    public function mount(): void
    {
        // Initialize
    }

    public function updatingSearch(): void
    {
        // Before search updates
    }

    public function updatedSearch(): void
    {
        // After search updates - reset pagination
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
```

## New HTML Directives

```blade
<!-- Loading states -->
<div wire:loading>Loading...</div>
<div wire:loading.remove>Hide when loading</div>
<div wire:loading.class="opacity-50">Add class when loading</div>
<div wire:loading.target="save">Only for save action</div>

<!-- Dirty state (unsaved changes) -->
<div wire:dirty>You have unsaved changes</div>

<!-- Conditional rendering -->
<div wire:show="showModal">Show me</div>
<div wire:hide="showModal">Hide me</div>

<!-- Transitions -->
<div wire:transition.fade>Fade effect</div>
<div wire:transition.slide>Slide effect</div>

<!-- Offline detection -->
<div wire:offline>You are offline</div>

<!-- Clock (auto-refresh) -->
<div wire:clock>2024-01-01 12:00:00</div>

<!-- Polling -->
<div wire:poll.5s>Refresh every 5s</div>

<!-- SPA navigation -->
<a wire:navigate href="/other-page">No page reload</a>

<!-- Current page link -->
<a wire:current href="/about">Active class added</a>
```

## Validation

```php
use Livewire\Attributes\Validate;

class CreatePost extends Component
{
    public string $title = '';
    public string $content = '';

    // Using attribute
    #[Validate('required|min:3|max:255')]
    public string $title = '';

    // Or using property
    protected $rules = [
        'title' => 'required|min:3|max:255',
        'content' => 'required|min:10',
    ];

    protected $messages = [
        'title.required' => 'Judul wajib diisi',
    ];

    public function save(): void
    {
        $validated = $this->validate();

        Post::create($validated);

        session()->flash('message', 'Post berhasil dibuat!');
    }

    // Real-time validation
    public function updated($property): void
    {
        $this->validateOnly($property);
    }
}
```

## Pagination

```php
use Livewire\WithPagination;

class PostList extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.post-list', [
            'posts' => Post::where('title', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
```

## Lazy Loading

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class LazyComponent extends Component
{
    public function mount(): void
    {
        // Only runs when component is visible
    }

    public function placeholder(): string
    {
        return <<<'HTML'
            <div>Loading...</div>
        HTML;
    }

    public function render(): View
    {
        return view('livewire.lazy-component');
    }
}
```

## Morphing

```php
use Livewire\Attributes\Morph;

<Morph
    :attributes="$attributes"
    :alpine="$enableAlpine"
    :wire="$enableWire"
>
    {{ $slot }}
</Morph>
```

## Best Practices

### 1. Eager Loading
```php
// ❌ N+1 queries
public function render(): View
{
    return view('livewire.posts', [
        'posts' => Post::paginate(10),
    ]);
}

// ✅ Eager load
public function render(): View
{
    return view('livewire.posts', [
        'posts' => Post::with(['author', 'category'])->paginate(10),
    ]);
}
```

### 2. Database Transactions
```php
use Illuminate\Support\Facades\DB;

public function transferFunds(): void
{
    DB::transaction(function () {
        // Multiple related operations
    });
}
```

### 3. Cache Expensive Queries
```php
public function render(): View
{
    $stats = cache()->remember('user.stats', now()->addHour(), function () {
        return User::count();
    });

    return view('livewire.dashboard', compact('stats'));
}
```

## Testing

```php
use Livewire\Attributes\Validate;

test('user can create post', function () {
    Livewire::test(CreatePost::class)
        ->set('title', 'Test Post')
        ->set('content', 'Test content here')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas(Post::class, [
        'title' => 'Test Post',
    ]);
});

// Test events
Livewire::test(PostForm::class)
    ->call('save')
    ->assertDispatched('postCreated');
```

## Common Patterns

### Datatable with Search & Filter
```php
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = 'all';
    public int $perPage = 10;

    public function render(): View
    {
        $query = User::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        return view('livewire.user-list', [
            'users' => $query->paginate($this->perPage),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }
}
```

### Modal Form
```php
class CreateUserModal extends Component
{
    public bool $showModal = false;
    public string $name = '';
    public string $email = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ];

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'email']);
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->closeModal();
        $this->dispatch('userCreated');
    }

    public function render(): View
    {
        return view('livewire.create-user-modal');
    }
}
```

## Resources

- [Official Documentation](https://livewire.laravel.com/docs/3.x)
- [Livewire v3 Features - Scalybee](https://scalybee.com/whats-new-in-laravel-livewire-v3/)
- [Livewire 3 Has Rekindled My Interest - Laracasts](https://laracasts.com/series/lukes-larabits/episodes/5)


=== .ai/coding-style rules ===

# Project Coding Guidelines - Perpustakaan

## Overview
Aplikasi perpustakaan dengan sistem peminjaman buku menggunakan Laravel 12, Filament 4, dan Livewire 3.

## Conventions

### Database
- Selalu gunakan Eloquent relationships, hindari raw query
- Gunakan eager loading untuk mencegah N+1 queries
- Untuk date comparison yang database-agnostic, gunakan `now()->startOfDay()` bukan `CURDATE()` atau fungsi database spesifik lainnya

### Frontend (Livewire/Volt)
- Gunakan tag `<livewire:` untuk inline components dengan `:key` attribute
- Hindari `@livewire()` directive dengan parameter `key:` (tidak supported)
- Gunakan `wire:model.live` untuk real-time updates

### Models
- Status lookup gunakan relationship, bukan hardcode ID
- Contoh: `$transaction->status->name === 'Dipinjam'` bukan `$transaction->status_id === 2`

### Routes
- Gunakan named routes: `route('catalog')` bukan URL hardcoded
- Folio pages otomatis terdaftar, gunakan `name()` directive untuk named routes

## Common Patterns

### Pagination dengan filter dan search
```php
$query = Model::query();
if ($this->filter !== 'all') {
    $query->where('status', $this->filter);
}
if ($this->search) {
    $query->where('name', 'like', '%' . $this->search . '%');
}
return $query->paginate(10);
```

### Stats query yang database-agnostic
```php
$today = now()->startOfDay();
$result = Model::selectRaw('
    COUNT(CASE WHEN status = ? AND date >= ? THEN 1 END) as active
', [$statusId, $today])->first();
```

## Package Specific

### Filament v4
- Gunakan `Schemas\Components` untuk layout components (Grid, Section, dll)
- Icons gunakan `Filament\Support\Icons\Heroicon` enum

### Livewire 3
- Dispatch events dengan `$this->dispatch()` bukan `emit()`
- Components namespace: `App\Livewire`

### Spatie Permission
- Cek permission: `$user->can('permission name')` atau `@can('permission name')`
- Assign role: `$user->assignRole('role name')`


=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.6
- filament/filament (FILAMENT) - v4
- laravel/folio (FOLIO) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- larastan/larastan (LARASTAN) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- rector/rector (RECTOR) - v2
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== folio/core rules ===

## Laravel Folio

- Laravel Folio is a file based router. With Laravel Folio, a new route is created for every Blade file within the configured Folio directory. For example, pages are usually in in `resources/views/pages/` and the file structure determines routes:
    - `pages/index.blade.php` → `/`
    - `pages/profile/index.blade.php` → `/profile`
    - `pages/auth/login.blade.php` → `/auth/login`
- You may list available Folio routes using `php artisan folio:list`  or using Boost's `list-routes` tool.

### New Pages & Routes
- Always create new `folio` pages and routes using `php artisan folio:page [name]` following existing naming conventions.

<code-snippet name="Example folio:page Commands for Automatic Routing" lang="shell">
    // Creates: resources/views/pages/products.blade.php → /products
    php artisan folio:page "products"

    // Creates: resources/views/pages/products/[id].blade.php → /products/{id}
    php artisan folio:page "products/[id]"
</code-snippet>

- Add a 'name' to each new Folio page at the very top of the file so it has a named route available for other parts of the codebase to use.


<code-snippet name="Adding named route to Folio page" lang="php">
use function Laravel\Folio\name;

name('products.index');
</code-snippet>


### Support & Documentation
- Folio supports: middleware, serving pages from multiple paths, subdomain routing, named routes, nested routes, index routes, route parameters, and route model binding.
- If available, use Boost's `search-docs` tool to use Folio to its full potential and help the user effectively.


<code-snippet name="Folio Middleware Example" lang="php">
use function Laravel\Folio\{name, middleware};

name('admin.products');
middleware(['auth', 'verified', 'can:manage-products']);
?>
</code-snippet>


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@volt` directive.
- You must check existing Volt components to determine if they're functional or class based. If you can't detect that, ask the user which they prefer before writing a Volt component.

### Volt Functional Component Example

<code-snippet name="Volt Functional Component Example" lang="php">
@volt
<?php
use function Livewire\Volt\{state, computed};

state(['count' => 0]);

$increment = fn () => $this->count++;
$decrement = fn () => $this->count--;

$double = computed(fn () => $this->count * 2);
?>

<div>
    <h1>Count: {{ $count }}</h1>
    <h2>Double: {{ $this->double }}</h2>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
@endvolt
</code-snippet>


### Volt Class Based Component Example
To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:


<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>


### Testing Volt & Volt Components
- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
</code-snippet>


<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
</code-snippet>


### Common Patterns


<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>

<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>

<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== filament/filament rules ===

## Filament
- Filament is used by this application, check how and where to follow existing application conventions.
- Filament is a Server-Driven UI (SDUI) framework for Laravel. It allows developers to define user interfaces in PHP using structured configuration objects. It is built on top of Livewire, Alpine.js, and Tailwind CSS.
- You can use the `search-docs` tool to get information from the official Filament documentation when needed. This is very useful for Artisan command arguments, specific code examples, testing functionality, relationship management, and ensuring you're following idiomatic practices.
- Utilize static `make()` methods for consistent component initialization.

### Artisan
- You must use the Filament specific Artisan commands to create new files or components for Filament. You can find these with the `list-artisan-commands` tool, or with `php artisan` and the `--help` option.
- Inspect the required options, always pass `--no-interaction`, and valid arguments for other options when applicable.

### Filament's Core Features
- Actions: Handle doing something within the application, often with a button or link. Actions encapsulate the UI, the interactive modal window, and the logic that should be executed when the modal window is submitted. They can be used anywhere in the UI and are commonly used to perform one-time actions like deleting a record, sending an email, or updating data in the database based on modal form input.
- Forms: Dynamic forms rendered within other features, such as resources, action modals, table filters, and more.
- Infolists: Read-only lists of data.
- Notifications: Flash notifications displayed to users within the application.
- Panels: The top-level container in Filament that can include all other features like pages, resources, forms, tables, notifications, actions, infolists, and widgets.
- Resources: Static classes that are used to build CRUD interfaces for Eloquent models. Typically live in `app/Filament/Resources`.
- Schemas: Represent components that define the structure and behavior of the UI, such as forms, tables, or lists.
- Tables: Interactive tables with filtering, sorting, pagination, and more.
- Widgets: Small component included within dashboards, often used for displaying data in charts, tables, or as a stat.

### Relationships
- Determine if you can use the `relationship()` method on form components when you need `options` for a select, checkbox, repeater, or when building a `Fieldset`:

<code-snippet name="Relationship example for Form Select" lang="php">
Forms\Components\Select::make('user_id')
    ->label('Author')
    ->relationship('author')
    ->required(),
</code-snippet>


## Testing
- It's important to test Filament functionality for user satisfaction.
- Ensure that you are authenticated to access the application within the test.
- Filament uses Livewire, so start assertions with `livewire()` or `Livewire::test()`.

### Example Tests

<code-snippet name="Filament Table Test" lang="php">
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
</code-snippet>

<code-snippet name="Filament Create Resource Test" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Howdy',
            'email' => 'howdy@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Howdy',
        'email' => 'howdy@example.com',
    ]);
</code-snippet>

<code-snippet name="Testing Multiple Panels (setup())" lang="php">
    use Filament\Facades\Filament;

    Filament::setCurrentPanel('app');
</code-snippet>

<code-snippet name="Calling an Action in a Test" lang="php">
    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])->callAction('send');

    expect($invoice->refresh())->isSent()->toBeTrue();
</code-snippet>


### Important Version 4 Changes
- File visibility is now `private` by default.
- The `deferFilters` method from Filament v3 is now the default behavior in Filament v4, so users must click a button before the filters are applied to the table. To disable this behavior, you can use the `deferFilters(false)` method.
- The `Grid`, `Section`, and `Fieldset` layout components no longer span all columns by default.
- The `all` pagination page method is not available for tables by default.
- All action classes extend `Filament\Actions\Action`. No action classes exist in `Filament\Tables\Actions`.
- The `Form` & `Infolist` layout components have been moved to `Filament\Schemas\Components`, for example `Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.
- A new `Repeater` component for Forms has been added.
- Icons now use the `Filament\Support\Icons\Heroicon` Enum by default. Other options are available and documented.

### Organize Component Classes Structure
- Schema components: `Schemas/Components/`
- Table columns: `Tables/Columns/`
- Table filters: `Tables/Filters/`
- Actions: `Actions/`
</laravel-boost-guidelines>
