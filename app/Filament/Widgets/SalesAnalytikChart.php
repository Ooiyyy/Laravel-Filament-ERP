<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Pembayaran;
use Filament\Widgets\ChartWidget;

class SalesAnalytikChart extends ChartWidget
{
    protected static ?string $heading = 'Analitik Penjualan per Sales';
    protected static ?string $maxHeight = '300px';
    protected static string $color = 'info';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        return [
            null => 'Semua Sales',
        ] + User::pluck('name', 'id')->toArray(); // asumsi nama field-nya `name`
    }

    protected function getData(): array
    {
        $query = Pembayaran::selectRaw('users.name as nama_sales, SUM(pesanan.jml_pesan) as total_terjual')
            ->join('pesanan', 'pembayaran.pesanan_id', '=', 'pesanan.id')
            ->join('users', 'pesanan.user_id', '=', 'users.id')
            ->where('pembayaran.keterangan', 'Lunas')
            ->groupBy('users.name');

        if ($this->filter) {
            $query->where('users.id', $this->filter);
        }

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Produk Terjual',
                    'data' => $data->pluck('total_terjual'),
                ],
            ],
            'labels' => $data->pluck('nama_sales'),
        ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
