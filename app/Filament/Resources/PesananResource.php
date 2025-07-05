<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Produk;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pembayaran;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\PesananResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PesananResource\RelationManagers;



class PesananResource extends Resource
{

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role, ['Admin', 'Sales']);
    }

    public static function canDelete($record): bool
    {
        return in_array(Auth::user()?->role, ['Admin', 'Sales']);
    }

    protected static ?string $model = Pesanan::class;

    protected static ?string $label = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Kelola Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_pesanan')
                    ->disabled()
                    ->default(function () {
                        $latest = \App\Models\Pesanan::latest()->first();
                        $urutan = $latest ? ((int) substr($latest->kode_pesanan, -3)) + 1 : 1;
                        return 'PSN-' . now()->format('Ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
                    })
                    ->label('Kode Pesanan'),
                Select::make('product_id')
                    ->label('Produk')
                    ->relationship('produk', 'nama_produk')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $produk = \App\Models\Produk::find($state);
                        $harga = $produk?->harga ?? 0;

                        $set('harga_satuan', $harga);
                        $set('harga_total', $harga); // asumsi jml default = 1
                    }),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                // TextInput::make('user_id.name')
                //     ->label('Pemesan')
                //     ->default(Auth::id())
                //     ->dehydrated(false),
                TextInput::make('user_name')
                    ->label('Pemesan')
                    ->default(Auth::user()?->name)
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('jml_pesan')
                    ->label('Jumlah Pesan')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(
                        fn($state, callable $get, callable $set) =>
                        $set('harga_total', (int)$state * (int)(\App\Models\Produk::find($get('product_id'))?->harga ?? 0))
                    ),

                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->disabled()
                    ->reactive()
                    ->dehydrated(false), // tetap disimpan ke database

                TextInput::make('harga_total')
                    ->label('Total Harga')
                    ->disabled()
                    ->dehydrated(true) // agar tetap disimpan
                    ->numeric(),

                DatePicker::make('tanggal_bayar')
                    ->label('Tanggal Pesan')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pesanan')->label('Kode Pesanan'),
                TextColumn::make('produk.nama_produk')->label('Produk'),
                TextColumn::make('user.name')->label('Pemesan'),
                TextColumn::make('jml_pesan'),
                TextColumn::make('harga_total')->money('IDR', true),
                TextColumn::make('tanggal_bayar')->date()->label('Tanggal Pemesanan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Pesanan $record) => $record->delete())
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
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
