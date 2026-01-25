<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Yönetim';
    protected static ?string $modelLabel = 'Kullanıcı';
    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kullanıcı Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Ad Soyad'),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Email'),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->label('Telefon Numarası'),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->label('Şifre'),
                    ])->columns(2),

                Forms\Components\Section::make('Durum ve Yetkiler')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->visibility('public')
                            ->label('Profil Fotoğrafı'),

                        Forms\Components\Toggle::make('is_banned')
                            ->label('Engelli Kullanıcı (Erişim Kısıtla)')
                            ->onColor('danger')
                            ->offColor('success')
                            ->default(false),
                            
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roller'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->getStateUsing(fn ($record) => $record->avatar ? asset('storage/avatars/' . basename($record->avatar)) : null)
                    ->circular()
                    ->label('Fotoğraf'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Ad Soyad'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Telefon'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('info')
                    ->label('Rolü')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin' => 'Süper Yönetici',
                        'business_owner' => 'İşletme Sahibi',
                        'tourist' => 'Turist',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_banned')
                    ->boolean()
                    ->label('Durum'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Oluşturma Tarihi'),
            ])
            ->filters([
                //
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
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}