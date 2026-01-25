<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Yönetim';
    protected static ?string $modelLabel = 'İşletme';
    protected static ?string $pluralModelLabel = 'İşletmeler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->required()
                            ->label('İşletme Sahibi'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Beklemede',
                                'active' => 'Aktif',
                            ])
                            ->default('pending')
                            ->label('Durum'),

                        Forms\Components\Select::make('plan')
                            ->options([
                                'free' => 'Ücretsiz',
                                'pro' => 'Pro',
                            ])
                            ->default('free')
                            ->label('Plan'),

                        Forms\Components\TextInput::make('domain')
                            ->label('Özel Domain')
                            ->helperText('Örnek: isletmem.com (isteğe bağlı)'),
                    ])->columns(2),

                Forms\Components\Section::make('İşletme Profili')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('İşletme Adı')
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatik oluşturulur'),

                        Forms\Components\Select::make('category')
                            ->label('Ana Kategori')
                            ->options([
                                'yemek' => 'Yemek & İçecek',
                                'otel' => 'Otel & Konaklama',
                                'hizmet' => 'Hizmet',
                                'market' => 'Market & Alışveriş',
                                'ulasim' => 'Ulaşım',
                                'saglik' => 'Sağlık & Güzellik',
                                'egitim' => 'Eğitim',
                                'eglence' => 'Eğlence & Aktivite',
                                'spor' => 'Spor & Fitness',
                                'kultur' => 'Kültür & Sanat',
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record?->businessProfile?->category !== null)
                            ->helperText('Ana kategori bir kez seçildikten sonra değiştirilemez'),

                        Forms\Components\TagsInput::make('subcategories')
                            ->label('Alt Kategoriler')
                            ->placeholder('Taverna, Müzik, Canlı Müzik vb.')
                            ->helperText('Enter tuşu ile ekleyin'),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Kısa Açıklama')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Detaylı Açıklama')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Medya')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\Placeholder::make('media_info')
                            ->label('Resim Yükleme')
                            ->content('Resim yükleme özelliği bir sonraki güncellemede eklenecek. Şimdilik diğer bilgileri doldurun.')
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('İletişim Bilgileri')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel(),

                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel(),

                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email(),

                        Forms\Components\TextInput::make('website')
                            ->label('Web Sitesi')
                            ->url(),
                    ])->columns(2),

                Forms\Components\Section::make('Konum Bilgileri')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->rows(2)
                            ->columnSpanFull()
                            ->hidden(),

                        Forms\Components\TextInput::make('neighborhood')
                            ->label('Mahalle')
                            ->placeholder('Cumhuriyet Mahallesi'),

                        Forms\Components\TextInput::make('street')
                            ->label('Cadde / Sokak')
                            ->placeholder('Doktor Sadık Ahmet Caddesi'),

                        Forms\Components\TextInput::make('building_number')
                            ->label('Bina / Kapı No')
                            ->placeholder('15'),

                        Forms\Components\Hidden::make('postal_code'),

                        Forms\Components\Hidden::make('city')
                            ->default('Gökçeada'),

                        Forms\Components\Select::make('district')
                            ->label('Bölge')
                            ->options([
                                'Merkez' => 'Merkez',
                                'Kaleköy' => 'Kaleköy',
                                'Yeni Bademli' => 'Yeni Bademli',
                                'Eski Bademli' => 'Eski Bademli',
                                'Tepeköy' => 'Tepeköy',
                                'Zeytinli' => 'Zeytinli',
                                'Şirinköy' => 'Şirinköy',
                                'Uğurlu' => 'Uğurlu',
                                'Dereköy' => 'Dereköy',
                                'Aydıncık (Kefalos)' => 'Aydıncık (Kefalos)',
                                'Kuzuliman' => 'Kuzuliman',
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('latitude')
                            ->label('Enlem')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Boylam')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\View::make('filament.forms.components.map-picker')
                            ->label('Harita Üzerinde İşaretle')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Sosyal Medya')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\TextInput::make('social_links.instagram')
                            ->label('Instagram')
                            ->url()
                            ->prefix('@')
                            ->placeholder('kullaniciadi'),

                        Forms\Components\TextInput::make('social_links.facebook')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/...'),

                        Forms\Components\TextInput::make('social_links.tiktok')
                            ->label('TikTok')
                            ->url()
                            ->prefix('@')
                            ->placeholder('kullaniciadi'),

                        Forms\Components\TextInput::make('social_links.x')
                            ->label('X (Twitter)')
                            ->url()
                            ->prefix('@')
                            ->placeholder('kullaniciadi'),
                    ])->columns(2),

                Forms\Components\Section::make('Google Places Entegrasyonu')
                    ->relationship('businessProfile')
                    ->schema([
                        Forms\Components\TextInput::make('google_place_id')
                            ->label('Google Place ID')
                            ->helperText('Google İşletme Kimliği'),

                        Forms\Components\TextInput::make('avg_rating')
                            ->label('Ortalama Puan')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_synced_at')
                            ->label('Son Senkronizasyon')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('businessProfile.logo_path')
                    ->label('Logo')
                    ->circular(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Sahibi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('businessProfile.name')
                    ->label('İşletme Adı')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('businessProfile.category')
                    ->label('Kategori')
                    ->colors([
                        'success' => 'yemek',
                        'info' => 'otel',
                        'warning' => 'hizmet',
                        'primary' => 'market',
                        'secondary' => 'ulasim',
                    ]),

                Tables\Columns\TextColumn::make('businessProfile.city')
                    ->label('Şehir')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                    ]),

                Tables\Columns\BadgeColumn::make('plan')
                    ->label('Plan')
                    ->colors([
                        'secondary' => 'free',
                        'primary' => 'pro',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'active' => 'Aktif',
                    ]),

                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'free' => 'Ücretsiz',
                        'pro' => 'Pro',
                    ]),

                Tables\Filters\SelectFilter::make('businessProfile.category')
                    ->label('Kategori')
                    ->options([
                        'yemek' => 'Yemek & İçecek',
                        'otel' => 'Otel & Konaklama',
                        'hizmet' => 'Hizmet',
                        'market' => 'Market & Alışveriş',
                        'ulasim' => 'Ulaşım',
                        'saglik' => 'Sağlık & Güzellik',
                        'egitim' => 'Eğitim',
                        'eglence' => 'Eğlence & Aktivite',
                        'spor' => 'Spor & Fitness',
                        'kultur' => 'Kültür & Sanat',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Giriş Yap')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->url(fn ($record) => route('admin.tenants.impersonate', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}