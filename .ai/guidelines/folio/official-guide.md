# Laravel Folio - Official Documentation Guide

**Official Docs**: [Laravel Folio Documentation](https://laravel.com/docs/12.x/folio)

## Overview
Laravel Folio is a powerful page based router designed to simplify routing in Laravel applications. With Laravel Folio, generating a route becomes as effortless as creating a Blade template within your application's `resources/views/pages` directory.

## Installation

```bash
composer require laravel/folio
php artisan folio:install
```

## Creating Routes

### Basic Routes
Create a Blade template in `resources/views/pages`:

```blade
{{-- resources/views/pages/greeting.blade.php --}}
<div>
    Hello World
</div>
```
Accessible at: `http://example.com/greeting`

### Nested Routes
```bash
php artisan folio:page user/profile
# pages/user/profile.blade.php → /user/profile
```

### Index Routes
```bash
php artisan folio:page index
# pages/index.blade.php → /

php artisan folio:page users/index
# pages/users/index.blade.php → /users
```

## Route Parameters

### Single Parameter
```bash
php artisan folio:page "users/[id]"
# pages/users/[id].blade.php → /users/1
```

Access in template:
```blade
<div>
    User {{ $id }}
</div>
```

### Multiple Parameters
```bash
php artisan folio:page "users/[...ids]"
# pages/users/[...ids].blade.php → /users/1/2/3
```

Access in template:
```blade
<ul>
    @foreach ($ids as $id)
        <li>User {{ $id }}</li>
    @endforeach
</ul>
```

## Route Model Binding

### Automatic Model Resolution
```bash
php artisan folio:page "users/[User]"
# pages/users/[User].blade.php → /users/1
```

Access in template:
```blade
<div>
    User {{ $user->id }}
</div>
```

### Customizing the Key
Use `[Post:slug].blade.php` to resolve via `slug` column instead of `id`.

### Fully Qualified Model Class
```bash
php artisan folio:page "users/[.App.Models.User]"
# pages/users/[.App.Models.User].blade.php → /users/1
```

### Soft Deleted Models
```php
<?php
use function Laravel\Folio\{withTrashed};

withTrashed();
?>
```

## Page Paths / URIs

### Multiple Page Directories
```php
use Laravel\Folio\Folio;

Folio::path(resource_path('views/pages/guest'))->uri('/');

Folio::path(resource_path('views/pages/admin'))
    ->uri('/admin')
    ->middleware([
        '*' => [
            'auth',
            'verified',
        ],
    ]);
```

### Subdomain Routing
```php
use Laravel\Folio\Folio;

Folio::domain('admin.example.com')
    ->path(resource_path('views/pages/admin'));

// With captured parameter
Folio::domain('{account}.example.com')
    ->path(resource_path('views/pages/admin'));
```

## Named Routes

```php
<?php
use function Laravel\Folio\name;

name('users.index');
?>
```

Generate URLs:
```blade
<a href="{{ route('users.index') }}">All Users</a>
```

With parameters:
```php
route('users.show', ['user' => $user]);
```

## Render Hooks

Customize the response or add additional data:

```php
<?php
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use function Laravel\Folio\render;

render(function (View $view, Post $post) {
    if (! Auth::user()->can('view', $post)) {
        return response('Unauthorized', 403);
    }

    return $view->with('photos', $post->author->photos);
});
?>

<div>
    {{ $post->content }}
</div>

<div>
    This author has also taken {{ count($photos) }} photos.
</div>
```

## Middleware

### Per-Page Middleware
```php
<?php
use function Laravel\Folio\{middleware};

middleware(['auth', 'verified']);
?>
```

### Group Middleware
```php
use Laravel\Folio\Folio;

Folio::path(resource_path('views/pages'))->middleware([
    'admin/*' => [
        'auth',
        'verified',
    ],
]);
```

### Inline Anonymous Middleware
```php
use Closure;
use Illuminate\Http\Request;
use Laravel\Folio\Folio;

Folio::path(resource_path('views/pages'))->middleware([
    'admin/*' => [
        'auth',
        'verified',
        function (Request $request, Closure $next) {
            // Custom middleware logic
            return $next($request);
        },
    ],
]);
```

## Listing Routes

```bash
php artisan folio:list
```

## Best Practices

1. **Use index routes** for directory roots (`index.blade.php`)
2. **Use brackets `[param]`** for dynamic segments (not `{param}`)
3. **Use named routes** for easier URL generation
4. **Group pages by folder** for better organization
5. **Use render hooks** for authorization checks
6. **Cache routes** in production: `php artisan route:cache`

## Common Patterns

### Paginated Index with Search (Folio + Volt)
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
    <input wire:model.live="search" placeholder="Search books..." />
    @foreach ($books as $book)
        <div>{{ $book->title }}</div>
    @endforeach
    {{ $books->links() }}
</div>
@endvolt
```

### Detail Page with Authorization
```php
<?php
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use function Laravel\Folio\{name, render};

name('posts.show');

render(function (View $view, Post $post) {
    abort_unless(Auth::user()->can('view', $post), 403);
    return $view;
});
?>

<x-post-detail :post="$post" />
```

## Resources

- [Official Documentation](https://laravel.com/docs/12.x/folio)
- [Introducing Folio Blog Post](https://laravel.com/blog/introducing-folio-page-based-routing)
- [GitHub Repository](https://github.com/laravel/folio)
