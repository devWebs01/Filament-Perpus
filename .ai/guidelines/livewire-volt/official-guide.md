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
