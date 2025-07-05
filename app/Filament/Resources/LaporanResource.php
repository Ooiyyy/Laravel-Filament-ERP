<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Laporan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Livewire\Attributes\Layout;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\LaporanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LaporanResource\RelationManagers;

class LaporanResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
    protected static string $view = 'filament.pages.laporan-pembayaran';

    protected static ?string $label = 'Laporan Pembayaran';
    protected static ?string $pluralLabel = 'Laporan Pembayaran';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Kelola Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Pembayaran::query()
                    ->where('keterangan', 'Lunas') // hanya yang Lunas
                    ->with('pesanan.produk', 'pesanan.user')
            )
            ->columns([
                TextColumn::make('pesanan.kode_pesanan')->label('Kode'),
                TextColumn::make('pesanan.produk.nama_produk')->label('Produk'),
                TextColumn::make('pesanan.user.name')->label('Pemesan'),
                TextColumn::make('pesanan.jml_pesan')->label('Jumlah'),
                TextColumn::make('pesanan.harga_total')->label('Total')->money('IDR', true),
                TextColumn::make('created_at')->label('Tanggal Bayar')->dateTime('d M Y '),
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
                Tables\Actions\Action::make('cetakPdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    // ->url(fn($livewire) => route('laporan-pembayaran.cetak', [
                    //     'from' => $livewire->getTableFilterState('Periode')['from'] ?? null,
                    //     'until' => $livewire->getTableFilterState('Periode')['until'] ?? null,
                    // ]))
                    // ->url(fn() => url('/laporan-pembayaran/cetak?' . http_build_query([
                    //     'from' => request('tableFilters.Period.from'),
                    //     'until' => request('tableFilters.Period.until'),
                    // ])))
                    ->url(function ($livewire) {
                        $filters = 'Periode';

                        return route('laporan-pembayaran.cetak', [
                            'from' => $filters['from'] ?? null,
                            'until' => $filters['until'] ?? null,
                        ]);
                    })
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }
}
