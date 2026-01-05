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
