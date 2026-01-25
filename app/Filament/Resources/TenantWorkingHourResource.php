<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantWorkingHourResource\Pages;
use App\Models\Tenant;
use App\Models\TenantWorkingHour;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class TenantWorkingHourResource extends Resource
{
    protected static ?string $model = TenantWorkingHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Çalışma Saatleri';

    protected static ?string $navigationGroup = 'Yönetim';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Süper admin tüm kayıtları görebilir
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // İşletme sahibi sadece kendi tenant'larının çalışma saatlerini görür
        $tenantIds = $user->tenants()->pluck('id');

        return $query->whereIn('tenant_id', $tenantIds);
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $tenant = $user->tenants()->first();

        return $tenant?->plan === 'pro';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('İşletme')
                    ->options(function () use ($user) {
                        $tenantQuery = Tenant::with('businessProfile');

                        if ($user && ! $user->hasRole('super_admin')) {
                            $tenantQuery->where('owner_id', $user->id);
                        }

                        return $tenantQuery
                            ->get()
                            ->mapWithKeys(fn ($tenant) => [
                                $tenant->id => $tenant->businessProfile?->name ?? "İşletme #{$tenant->id}",
                            ]);
                    })
                    ->required()
                    ->searchable()
                    ->default(function () use ($user) {
                        return $user?->tenants()->first()?->id ?? Tenant::first()?->id;
                    }),

                Forms\Components\Section::make('Sezonluk Çalışma Saatleri')
                    ->description('İşletmenizin belirli bir sezonda farklı saatlerde çalıştığı durumlarda sezon tarihlerini buradan ayarlayabilirsiniz.')
                    ->schema([
                        Forms\Components\DatePicker::make('season_start_date')
                            ->label('Sezon Başlangıç Tarihi')
                            ->native(false),
                        Forms\Components\DatePicker::make('season_end_date')
                            ->label('Sezon Bitiş Tarihi')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Hızlı Ayar')
                    ->description('Tüm günler için aynı çalışma saatlerini ayarlamak istiyorsanız burayı kullanın.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('all_days_open')
                                    ->label('Açılış Saati')
                                    ->seconds(false)
                                    ->live(),
                                Forms\Components\TimePicker::make('all_days_close')
                                    ->label('Kapanış Saati')
                                    ->seconds(false)
                                    ->live(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('apply_all_days')
                                        ->label('Tüm Günlere Uygula')
                                        ->icon('heroicon-o-arrow-down')
                                        ->color('primary')
                                        ->action(function ($livewire, $get, $set) {
                                            $openTime = $get('all_days_open');
                                            $closeTime = $get('all_days_close');
                                            
                                            if ($openTime && $closeTime) {
                                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                                foreach ($days as $day) {
                                                    $set($day . '_open', $openTime);
                                                    $set($day . '_close', $closeTime);
                                                    $set($day . '_closed', false);
                                                }
                                                
                                                \Filament\Notifications\Notification::make()
                                                    ->success()
                                                    ->title('Başarılı')
                                                    ->body('Çalışma saatleri tüm günlere uygulandı.')
                                                    ->send();
                                            }
                                        }),
                                ])
                                ->verticalAlignment('end'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Genel Çalışma Saatleri')
                    ->schema([
                        self::dayRow('Pazartesi', 'monday'),
                        self::dayRow('Salı', 'tuesday'),
                        self::dayRow('Çarşamba', 'wednesday'),
                        self::dayRow('Perşembe', 'thursday'),
                        self::dayRow('Cuma', 'friday'),
                        self::dayRow('Cumartesi', 'saturday'),
                        self::dayRow('Pazar', 'sunday'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Özel Günler ve Kapalı Günler')
                    ->description('Bayramlar, tatiller veya özel etkinlikler için çalışma saatlerinizi düzenleyin veya işletmenizi kapalı olarak işaretleyin.')
                    ->schema([
                        Forms\Components\Repeater::make('special_days')
                            ->label('Özel Günler')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Özel Gün Adı')
                                    ->placeholder('örn: Kurban Bayramı, Ramazan Bayramı')
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Başlangıç Tarihi')
                                            ->native(false),
                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('Bitiş Tarihi')
                                            ->native(false)
                                            ->after('start_date'),
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\ToggleButtons::make('status')
                                    ->label('Durum')
                                    ->options(fn ($get) => [
                                        'closed' => ($get('status') === 'closed' ? '🔴 KAPALI' : 'Kapalı'),
                                        'open' => ($get('status') === 'open' ? '🟢 AÇIK (Özel Saatler)' : 'Açık (Özel Saatler)'),
                                    ])
                                    ->colors([
                                        'closed' => 'danger',
                                        'open' => 'success',
                                    ])
                                    ->icons([
                                        'closed' => 'heroicon-o-x-mark',
                                        'open' => 'heroicon-o-clock',
                                    ])
                                    ->default('open')
                                    ->live()
                                    ->inline(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TimePicker::make('open_time')
                                            ->label('Açılış Saati')
                                            ->seconds(false)
                                            ->visible(fn ($get) => $get('status') === 'open'),
                                        Forms\Components\TimePicker::make('close_time')
                                            ->label('Kapanış Saati')
                                            ->seconds(false)
                                            ->visible(fn ($get) => $get('status') === 'open'),
                                    ])
                                    ->visible(fn ($get) => $get('status') === 'open'),
                            ])
                            ->itemLabel(fn (array $state): ?string => 
                                ($state['name'] ?? 'Özel Gün') . 
                                (isset($state['start_date'], $state['end_date']) 
                                    ? ' (' . $state['start_date'] . ' - ' . $state['end_date'] . ')' 
                                    : (isset($state['start_date']) ? ' (' . $state['start_date'] . ')' : '')
                                ) .
                                (($state['status'] ?? '') === 'closed' ? ' - Kapalı' : '')
                            )
                            ->addActionLabel('Yeni Özel Gün Ekle')
                            ->orderable()
                            ->reorderable()
                            ->collapsed()
                            ->cloneable(),
                    ]),
            ]);
    }

    protected static function dayRow(string $label, string $prefix): Forms\Components\Fieldset
    {
        return Forms\Components\Fieldset::make($label)
            ->schema([
                Forms\Components\TimePicker::make($prefix . '_open')
                    ->label('Açılış')
                    ->seconds(false),
                Forms\Components\TimePicker::make($prefix . '_close')
                    ->label('Kapanış')
                    ->seconds(false),
                Forms\Components\Toggle::make($prefix . '_closed')
                    ->label(fn ($get) => $get($prefix . '_closed') ? 'Kapalı' : 'Açık')
                    ->live()
                    ->inline(false)
                    ->default(false),
            ])
            ->columns(3);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('İşletme')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('season_start_date')
                    ->label('Sezon Başlangıç')
                    ->date(),
                Tables\Columns\TextColumn::make('season_end_date')
                    ->label('Sezon Bitiş')
                    ->date(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantWorkingHours::route('/'),
            'create' => Pages\CreateTenantWorkingHour::route('/create'),
            'edit' => Pages\EditTenantWorkingHour::route('/{record}/edit'),
        ];
    }
}
