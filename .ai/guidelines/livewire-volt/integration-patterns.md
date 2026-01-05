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
