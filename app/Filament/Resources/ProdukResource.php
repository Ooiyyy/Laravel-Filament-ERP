<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Navigation\NavigationGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProdukResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProdukResource\RelationManagers;


class ProdukResource extends Resource
{

    protected static ?string $model = Produk::class;
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'Admin';
    }

    protected static ?string $label = 'Produk';
    protected static ?string $pluralLabel = 'Produk';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Kelola Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                            TextInput::make('nama_produk')
                ->label('Nama Produk')
                ->required()
                ->maxLength(255),

            TextInput::make('harga')
                ->numeric()
                ->required(),

            TextInput::make('stok')
                ->numeric()
                ->required(),

            FileUpload::make('foto')
                ->image()
                ->directory('produk')
                ->disk('public')
                ->preserveFilenames()
                ->afterStateUpdated(function ($state) {
                    Log::info('File upload state:', ['state' => $state]);
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_produk')->searchable()->sortable(),
                TextColumn::make('harga')->money('IDR', true),
                TextColumn::make('stok'),
                ImageColumn::make('foto')->label('Foto'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
