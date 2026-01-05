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
