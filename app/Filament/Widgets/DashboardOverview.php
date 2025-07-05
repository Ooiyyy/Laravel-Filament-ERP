<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AkunResource;
use Illuminate\Support\Facades\Auth;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Filament\Resources\ProdukResource;
use App\Filament\Resources\PesananResource;
use App\Filament\Resources\PembayaranResource;
use App\Filament\Resources\UserResource;

class DashboardOverview extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = null; // bisa diatur untuk realtime

    protected function getCards(): array
    {
        $isSales = Auth::user()?->role === 'Sales';
        return [
            Card::make('Total Produk', Produk::count())
                ->icon('heroicon-o-cube')
                ->color('success')
                ->url($isSales ? null : ProdukResource::getUrl()),

            Card::make('Total Pesanan', Pesanan::count())
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->url(PesananResource::getUrl()),

            Card::make('Pembayaran Lunas', Pembayaran::where('keterangan', 'Lunas')->count())
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->url($isSales ? null : PembayaranResource::getUrl()),

            Card::make('Total Pengguna', User::count())
                ->icon('heroicon-o-users')
                ->color('warning')
                ->url($isSales ? null : AkunResource::getUrl()),

            Card::make('Total Omzet', 'Rp ' . number_format(
                Pesanan::join('pembayaran', 'pesanan.id', '=', 'pembayaran.pesanan_id')
                    ->where('pembayaran.keterangan', 'Lunas')
                    ->sum('pesanan.harga_total'),
                0,
                ',',
                '.'
            ))
                ->icon('heroicon-o-chart-bar')
                ->color('success'),
            Card::make('Produk Terlaris', function () {
                return \App\Models\Pesanan::select('produk.nama_produk')
                    ->join('produk', 'pesanan.product_id', '=', 'produk.id')
                    ->join('pembayaran', 'pesanan.id', '=', 'pembayaran.pesanan_id')
                    ->where('pembayaran.keterangan', 'Lunas')
                    ->groupBy('produk.nama_produk')
                    ->orderByRaw('SUM(pesanan.jml_pesan) DESC')
                    ->limit(1)
                    ->value('produk.nama_produk') ?? 'Tidak Ada';
            })
                ->icon('heroicon-o-fire')
                ->color('rose'),
            Card::make('Sales Paling Produktif', function () {
                return \App\Models\Pesanan::select('users.name')
                    ->join('users', 'pesanan.user_id', '=', 'users.id') // join user dari pesanan
                    ->join('pembayaran', 'pesanan.id', '=', 'pembayaran.pesanan_id')
                    ->where('pembayaran.keterangan', 'Lunas')
                    ->groupBy('users.name')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(1)
                    ->value('users.name') ?? 'Tidak Ada';
            })
                ->icon('heroicon-o-user-circle')
                ->color('indigo'),
        ];
    }
}
