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
