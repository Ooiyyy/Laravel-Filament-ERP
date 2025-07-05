<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pembayaran;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembayaranResource\Pages;
use App\Filament\Resources\PembayaranResource\RelationManagers;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    public static function canViewAny(): bool
    {
        return Auth::user()?->role === 'Admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'Admin';
    }

    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Pembayaran';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Kelola Pembayaran';
    protected static bool $canCreate = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('pesanan_id')
                //     ->label('Pesanan')
                //     ->options(Pesanan::all()->pluck('kode_pesanan', 'id'))
                //     // ->relationship('pesanan', 'id') // gunakan relasi
                //     ->searchable()
                //     ->required(),

                // // Tampilkan kode_bayar tapi disable, karena dibuat otomatis
                // Forms\Components\TextInput::make('kode_bayar')
                //     ->disabled()
                //     ->label('Kode Pembayaran'),
                Select::make('keterangan')
                    ->label('Status Pembayaran')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                    ])
                    ->required()
                    ->default('Belum Lunas')
                    ->native(false)
                    ->placeholder(null), // ✅ tidak pakai placeholder
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('pesanan.kode_pesanan')->label('Kode Pesanan'),
                // TextColumn::make('pesanan.nama_produk')
                //     ->label('Produk')
                //     ->sortable()
                //     ->searchable(),
                // TextColumn::make('pesanan.jml_pesan')->label('Jumlah'),
                // TextColumn::make('pesanan.harga_total')->label('Total')->money('IDR', true),
                // TextColumn::make('created_at')->label('Waktu Bayar')->dateTime(),
                // TextColumn::make('pesanan.keterangan')->label('Keterangan'),
                TextColumn::make('pesanan.kode_pesanan')->label('Kode Pesanan'),
                TextColumn::make('pesanan.produk.nama_produk')->label('Produk')->searchable(),
                TextColumn::make('pesanan.user.name')->label('Pemesan')->searchable(),
                TextColumn::make('pesanan.jml_pesan')->label('Jumlah')->sortable(),
                TextColumn::make('pesanan.harga_total')->label('Total')->money('IDR', true),
                TextColumn::make('keterangan')->label('Status')->sortable()->badge()->color(fn(string $state) => $state === 'Lunas' ? 'success' : 'warning'),
                TextColumn::make('created_at')->label('Tanggal Pesan')->dateTime('d M Y H:i'),
                TextColumn::make('pesanan.harga_total')
                    ->label('Total Harga')
                    ->money('IDR', true)
                    ->sortable()
                    ->summarize(Sum::make()->label('Subtotal')),
            ])
            ->filters([
                Filter::make('Periode')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah Status'),
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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
