<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Filament\Forms\Components\Select;

class PesananChart extends ChartWidget
{
    protected static ?string $heading = 'Pesanan per Produk';
    protected static ?string $maxHeight = '300px';

    public ?string $filter = null; // default: semua produk

    protected function getFilters(): ?array
    {
        return [
            null => 'Semua Produk',
        ] + Produk::pluck('nama_produk', 'id')->toArray();
    }

    protected function getData(): array
    {
        // Query awal dengan join produk
        $query = Pesanan::selectRaw('produk.nama_produk, SUM(pesanan.jml_pesan) as total_pesan')
            ->join('produk', 'pesanan.product_id', '=', 'produk.id')
            ->groupBy('produk.nama_produk');

        // Jika ada filter produk
        if ($this->filter) {
            $query->where('produk.id', $this->filter);
        }

        $pesanan = $query->get();

        $labels = $pesanan->pluck('nama_produk');
        $values = $pesanan->pluck('total_pesan');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\PembayaranChart::class,
        ];
    }
}
