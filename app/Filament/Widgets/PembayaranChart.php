<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;

class PembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Pembayaran per Produk';
    protected static ?string $maxHeight = '300px';

    public ?string $filter = 'tahun';

    protected function getFilters(): ?array
    {
        return [
            'bulan' => 'Bulan Ini',
            'tahun' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $query = Pembayaran::select(
            'pesanan.product_id',
            'pesanan.jml_pesan',
            'pesanan.harga_total',
            'produk.nama_produk',
            'pembayaran.updated_at',
            'pembayaran.keterangan'
        )
            ->join('pesanan', 'pembayaran.pesanan_id', '=', 'pesanan.id')
            ->join('produk', 'pesanan.product_id', '=', 'produk.id')
            ->where('pembayaran.keterangan', 'Lunas');

        // Filter berdasarkan bulan atau tahun
        if ($this->filter === 'bulan') {
            $query->whereMonth('pembayaran.updated_at', now()->month)
                ->whereYear('pembayaran.updated_at', now()->year);
        } elseif ($this->filter === 'tahun') {
            $query->whereYear('pembayaran.updated_at', now()->year);
        }

        $pembayaran = $query->get();

        $labels = $pembayaran->map(fn($item) => $item->updated_at->format('d M'));
        $values = $pembayaran->map(fn($item) => $item->jml_pesan);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pembayaran Lunas',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\PembayaranChart::class,
        ];
    }
}
