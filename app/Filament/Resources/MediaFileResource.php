<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFileResource\Pages;
use App\Models\MediaFile;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class MediaFileResource extends Resource
{
    protected static ?string $model = MediaFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Medya Galerisi';

    protected static ?string $modelLabel = 'Medya';

    protected static ?string $pluralModelLabel = 'Medya Dosyaları';

    protected static ?string $navigationGroup = 'Yönetim';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Süper admin her şeyi görür
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // İşletme sahibi sadece kendi tenant'larının medyasını görür
        $tenantIds = $user->tenants()->pluck('id');

        return $query->whereIn('tenant_id', $tenantIds);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        // Cache user's first tenant to avoid repeated queries
        $userTenant = auth()->user()?->tenants()->first();
        $isPro = $userTenant?->plan === 'pro';

        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('İşletme')
                    ->options(function () {
                        /** @var User|null $user */
                        $user = auth()->user();
                        $tenantQuery = Tenant::with('businessProfile');

                        if ($user && ! $user->hasRole('super_admin')) {
                            $tenantQuery->where('owner_id', $user->id);
                        }

                        return $tenantQuery
                            ->get()
                            ->mapWithKeys(fn ($tenant) => [
                                $tenant->id => $tenant->businessProfile?->name ?? "İşletme #{$tenant->id}"
                            ]);
                    })
                    ->default(fn () => $userTenant?->id ?? Tenant::first()?->id)
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),

                Forms\Components\Tabs::make('Medya Tipleri')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Logo')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_files')
                                    ->label('')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(1)
                                    ->directory(fn ($get) => 'tenants/' . $get('tenant_id') . '/logo')
                                    ->maxSize(10240)
                                    ->imageEditor()
                                    ->helperText('Maks 1 adet, 10MB'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Kapak')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_files')
                                    ->label('')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(1)
                                    ->directory(fn ($get) => 'tenants/' . $get('tenant_id') . '/cover')
                                    ->maxSize(10240)
                                    ->imageEditor()
                                    ->helperText('Maks 1 adet, 10MB'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Galeri')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Forms\Components\FileUpload::make('gallery_files')
                                    ->label('')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles($isPro ? 15 : 3)
                                    ->directory(fn ($get) => 'tenants/' . $get('tenant_id') . '/gallery')
                                    ->maxSize(10240)
                                    ->reorderable()
                                    ->helperText($isPro ? 'Maks 15 adet (Pro Plan)' : 'Maks 3 adet (Ücretsiz Plan)'),
                            ]),

                        Forms\Components\Tabs\Tab::make('360° Görsel')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\FileUpload::make('360_files')
                                    ->label('')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(10)
                                    ->directory(fn ($get) => 'tenants/' . $get('tenant_id') . '/360')
                                    ->maxSize(10240)
                                    ->reorderable()
                                    ->helperText('Maks 10 adet (Sadece Pro Plan)'),
                            ])
                            ->visible($isPro),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['tenant.businessProfile']))
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('path')
                        ->label('')
                        ->disk('public')
                        ->height(200)
                        ->extraImgAttributes(['class' => 'rounded-lg object-cover w-full'])
                        ->defaultImageUrl('https://placehold.co/400x300/374151/9ca3af?text=Resim'),
                    
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('tenant_name')
                            ->label('İşletme')
                            ->getStateUsing(fn ($record) => $record->tenant?->businessProfile?->name ?? 'İşletme #' . $record->tenant_id)
                            ->weight('bold')
                            ->size('sm'),
                        
                        Tables\Columns\TextColumn::make('type_label')
                            ->label('Tip')
                            ->getStateUsing(fn ($record) => match ($record->type) {
                                'logo' => 'Logo',
                                'cover' => 'Kapak Fotoğrafı',
                                '360' => '360° Fotoğraf',
                                default => 'Galeri',
                            })
                            ->badge()
                            ->color(fn ($record) => match ($record->type) {
                                'logo' => 'warning',
                                'cover' => 'primary',
                                '360' => 'success',
                                default => 'gray',
                            })
                            ->size('xs'),

                        Tables\Columns\TextColumn::make('size')
                            ->label('Boyut')
                            ->formatStateUsing(fn ($record) => $record->size ? round($record->size / 1024, 2) . ' MB' : '-')
                            ->color('gray')
                            ->size('xs'),
                    ])->space(1),
                ])->space(2),
            ])
            ->filters([
                Tables\Filters\Filter::make('Tümü')
                    ->label('Tümü')
                    ->query(fn ($query) => $query)
                    ->default(),
                
                Tables\Filters\Filter::make('Logo')
                    ->label('Logo')
                    ->query(fn ($query) => $query->where('type', 'logo')),
                
                Tables\Filters\Filter::make('Kapak Fotoğraf')
                    ->label('Kapak Fotoğraf')
                    ->query(fn ($query) => $query->where('type', 'cover')),
                
                Tables\Filters\Filter::make('Galeri')
                    ->label('Galeri')
                    ->query(fn ($query) => $query->where('type', 'gallery')),
                
                Tables\Filters\Filter::make('360 Galeri')
                    ->label('360 Galeri')
                    ->query(fn ($query) => $query->where('type', '360')),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\Action::make('view360')
                    ->label('360° Görüntüle')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn ($record) => route('filament.admin.resources.media-files.view-360', $record))
                    ->visible(fn ($record) => $record->type === '360')
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('viewGallery')
                    ->label('Medya Önizleme')
                    ->icon('heroicon-o-photo')
                    ->visible(fn ($record) => in_array($record->type, ['cover', 'logo', 'gallery', '360']))
                    ->url(fn ($record) => route('filament.admin.resources.media-files.view-gallery', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFiles::route('/'),
            'create' => Pages\CreateMediaFile::route('/create'),
            'edit' => Pages\EditMediaFile::route('/{record}/edit'),
            'view-360' => Pages\View360::route('/{record}/view-360'),
            'view-gallery' => Pages\ViewGallery::route('/{record}/view-gallery'),
        ];
    }
}
