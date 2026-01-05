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
