# Filament 4 - Official Documentation Guide

**Official Docs**: [Filament Documentation](https://filamentphp.com/docs/4.x)

## Overview
__Filament is a Server-Driven UI (SDUI) framework for Laravel.__ It allows you to define user interfaces entirely in PHP using structured configuration objects, rather than traditional templating. Built on top of Livewire, Alpine.js, and Tailwind CSS.

### What is Server-Driven UI?
SDUI moves control of the UI to the server, allowing for faster iteration, greater consistency, and centralized logic. Used by companies like Meta, Airbnb, and Shopify.

### Difference from Server-Rendered UI
- **Server-Rendered UI**: Static templates (Blade views) defined upfront
- **Server-Driven UI**: UI dynamically generated based on real-time configurations and business logic

## Installation

```bash
composer require filament/filament:"^4.0"
php artisan filament:install --panels
```

## Core Packages

Filament comprises several packages:

- `filament/filament` - Core package for building panels
- `filament/tables` - Data table builder
- `filament/schemas` - UI component configuration system
- `filament/forms` - Form input fields with validation
- `filament/infolists` - Read-only "description lists"
- `filament/actions` - Buttons and modal actions
- `filament/notifications` - Flash and database notifications
- `filament/widgets` - Dashboard widgets
- `filament/support` - Shared UI components and utilities

## New Features in Filament 4

### Performance
- **Faster Tables** - No code changes needed
- **Unified Architecture** - New "Schema" Core
- **Significant performance improvements**

### New Functionality
- **API Data Tables** - Tables with data from API sources
- **Nested Resources** - Deeply nested resource hierarchy
- **TipTap Rich Editing** - Enhanced rich text editor
- **Client-side JS Helpers**
- **MFA (Multi-Factor Authentication)** support

### v4.1 Additional Features
- **New Panel Layout** (No Topbar option)
- **Global Search** in sidebar
- **Compact Repeater Table**
- **Rich Editor Grid Tool**
- **Rich Editor Text Color Tool**
- **Compact Table** view

## Creating a Resource

```bash
php artisan make:filament-resource Book
```

Basic resource structure:

```php
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Book;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->rows(3),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

## Forms

### Common Form Components

```php
use Filament\Forms;
use Filament\Forms\Form;

// Text Input
Forms\Components\TextInput::make('email')
    ->email()
    ->required()
    ->unique(ignoreRecord: true);

// Textarea
Forms\Components\Textarea::make('description')
    ->rows(3)
    ->columnSpanFull();

// Select with Relationship
Forms\Components\Select::make('user_id')
    ->label('Author')
    ->relationship('author', 'name')
    ->required()
    ->searchable()
    ->preload();

// File Upload
Forms\Components\FileUpload::make('image')
    ->image()
    ->directory('uploads')
    ->maxSize(1024);

// Rich Editor (TipTap)
Forms\Components\RichEditor::make('content')
    ->columnSpanFull()
    ->toolbarButtons([
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
    ]);

// Toggle
Forms\Components\Toggle::make('is_active')
    ->default(true);

// Date Picker
Forms\Components\DatePicker::make('published_at')
    ->native(false);

// Repeater
Forms\Components\Repeater::make('items')
    ->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\TextInput::make('quantity')->numeric(),
    ])
    ->columns(2);
```

### Form Layout

```php
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

// Section
Section::make('Product Details')
    ->description('Basic product information')
    ->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\Textarea::make('description'),
    ])
    ->columns(2);

// Grid
Grid::make(3)
    ->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\TextInput::make('email')->email(),
        Forms\Components\TextInput::make('phone')->tel(),
    ]);

// Tabs
Tabs::make('Main Info')
    ->tabs([
        Tabs\Tab::make('Basic')->schema([
            Forms\Components\TextInput::make('name'),
        ]),
        Tabs\Tab::make('Advanced')->schema([
            Forms\Components\TextInput::make('slug'),
        ]),
    ]);
```

## Tables

### Common Table Columns

```php
use Filament\Tables;

// Text Column
Tables\Columns\TextColumn::make('title')
    ->searchable()
    ->sortable()
    ->description(fn (Book $record): string => $record->description)
    ->wrap();

// Image Column
Tables\Columns\ImageColumn::make('image')
    ->circular()
    ->size(40);

// Badge Column
Tables\Columns\TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'gray',
        'published' => 'success',
        default => 'warning',
    });

// Icon Column
Tables\Columns\IconColumn::make('is_featured')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger');

// Relationship Count
Tables\Columns\TextColumn::make('comments_count')
    ->counts('comments')
    ->label('Comments');
```

### Filters

```php
use Filament\Tables\Filters;

// Select Filter
Filters\SelectFilter::make('category')
    ->relationship('category', 'name')
    ->multiple(),

// Ternary Filter
Filters\TernaryFilter::make('is_featured')
    ->placeholder('All books')
    ->trueLabel('Featured')
    ->falseLabel('Not featured'),

// Date Filter
Filters\Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('from'),
        Forms\Components\DatePicker::make('until'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    }),
```

### Actions

```php
use Filament\Tables;

// Row Actions
Tables\Actions\ViewAction::make(),
Tables\Actions\EditAction::make(),
Tables\Actions\DeleteAction::make(),

// Custom Action
Tables\Actions\Action::make('approve')
    ->icon('heroicon-o-check')
    ->color('success')
    ->requiresConfirmation()
    ->action(fn (Book $record) => $record->update(['status' => 'approved'])),

// Bulk Actions
Tables\Actions\BulkAction::make('publish')
    ->requiresConfirmation()
    ->action(fn ($records) => $records->each->update(['status' => 'published'])),
```

## Widgets

```bash
php artisan make:filament-widget StatsOverview
```

```php
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('New users this week')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Books', Book::count())
                ->description('Total books in library')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Active Loans', Transaction::where('status', 'active')->count())
                ->description('Books currently borrowed')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
```

## Panels

### Customizing Panels

```php
use Filament\Facades\Filament;
use Illuminate\Folio\Folio;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->brandName('Library App')
        ->sidebarCollapsibleOnDesktop()
        ->breadcrumbs()
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->middleware([
            'web',
            // ...
        ])
        ->authMiddleware([
            'auth',
        ]);
}
```

## Customizing Appearance

### Tailwind CSS Overrides

Filament uses Tailwind CSS compiled into semantic CSS classes. You can override these in your CSS:

```css
/* resources/css/filament.css */
.fi-btn {
    @apply rounded-sm; /* Override border radius */
}
```

For more customization, visit the [Customizing styling documentation](https://filamentphp.com/docs/4.x/support/customizing-the-appearance).

## Best Practices

1. **Use relationship() in selects** - For efficient queries
2. **Use eager loading** - Prevent N+1 problems
3. **Use form requests** - For complex validation
4. **Use Gates or Policies** - For authorization
5. **Use caching** - For expensive operations
6. **Use lazy loading** - For heavy components

## Testing

```php
use App\Filament\Resources\BookResource;
use App\Models\Book;
use Livewire\Livewire;

it('can render page', function () {
    Livewire::test(BookResource\Pages\ListBooks::class)
        ->assertStatus(200);
});

it('can create book', function () {
    Livewire::test(BookResource\Pages\CreateBook::class)
        ->fillForm([
            'title' => 'Test Book',
            'author' => 'Test Author',
        ])
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(Book::class, [
        'title' => 'Test Book',
    ]);
});
```

## Resources

- [Official Documentation](https://filamentphp.com/docs/4.x)
- [What's New in Filament v4](https://filamentphp.com/content/leandrocfe-whats-new-in-filament-v4)
- [Filament v4 Beta: 4 New Features - Laravel Daily](https://laraveldaily.com/post/filament-v4-beta-new-features)
- [Filament v4: What's New - Nabil Hassen](https://nabilhassen.com/filament-v4-whats-new-and-exciting)
