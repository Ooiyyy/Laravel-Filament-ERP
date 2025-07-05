<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan Bulanan';

    // protected function getData(): array
    // {
    //     $data = Trend::model(Pembayaran::class)
    //         ->between(
    //             start: now()->subMonths(6),
    //             end: now(),
    //         )
    //         ->perMonth()
    //         ->count();

    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'Pesanan',
    //                 'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
    //             ],
    //         ],
    //         'labels' => $data->map(fn (TrendValue $value) => $value->date),
    //     ];
    // }

    // protected function getType(): string
    // {
    //     return 'line';
    // }
    protected function getData(): array
    {
        $data = \Flowframe\Trend\Trend::query(
            \App\Models\Pesanan::query()
                ->whereHas('pembayaran', function ($query) {
                    $query->where('keterangan', 'Lunas');
                })
        )
            ->between(
                start: now()->subMonths(6),
                end: now()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Pesanan Lunas',
                    'data' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(\Flowframe\Trend\TrendValue $value) => $value->date),
        ];
    }
    protected function getType(): string
    {
        return 'line';
    }
}
