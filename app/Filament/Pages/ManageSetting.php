<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ManageSetting extends Page implements HasSchemas
{
    use HasPageShield, InteractsWithSchemas;

    protected string $view = 'filament.pages.manage-setting';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Perpustakaan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan';

    public ?array $data = [];

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin') => 'Dasbor',
            static::getUrl() => $this->getTitle(),
        ];
    }

    public function mount(): void
    {
        $setting = Setting::first();
        $this->data = $setting ? $setting->toArray() : [];
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Aplikasi')
                            ->required(),

                        TextInput::make('phone')
                            ->numeric()
                            ->required()
                            ->label('Telepon'),

                        TextInput::make('limit_day')
                            ->numeric()
                            ->required()
                            ->label('Batas Peminjaman'),

                        FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->label('Logo Aplikasi')
                            ->directory('settings')
                            ->disk('public')
                            ->required(),

                        Textarea::make('address')
                            ->required()
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->rows(5),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->button()
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $payload = $state;

        // Normalize logo (FileUpload sometimes returns array or string)
        if (isset($payload['logo'])) {
            $logo = $payload['logo'];

            if (is_array($logo)) {
                $firstString = Arr::first($logo, function ($value) {
                    return is_string($value) && ! Str::contains($value, 'data:');
                });
                $payload['logo'] = $firstString ?? (string) Arr::first($logo);
            }

            if (empty($payload['logo'])) {
                unset($payload['logo']);
            }
        }

        $setting = Setting::first();

        if ($setting) {
            try {
                $setting->update($payload);
            } catch (\Throwable $e) {
                foreach ($payload as $key => $value) {
                    if (in_array($key, $setting->getFillable())) {
                        $setting->{$key} = $value;
                    }
                }
                $setting->save();
            }
        } else {
            Setting::create($payload);
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();

        $this->data = Setting::first()?->toArray() ?? [];
        $this->form->fill($this->data);
    }

    public function render(): View
    {
        return view('filament.pages.manage-setting');
    }
}
