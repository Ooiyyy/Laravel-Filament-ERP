<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pembayaran;

class LaporanPembayaran extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.laporan-pembayaran';

    protected static ?string $navigationGroup = 'Kelola Pembayaran';

    public $dataLunas;

    public function mount(): void
    {
        // Ambil pembayaran yang lunas
        $this->dataLunas = Pembayaran::with(['pesanan.produk', 'pesanan.user'])
            ->whereHas('pesanan', fn ($q) => $q->where('keterangan', 'Lunas'))
            ->latest()
            ->get();
    }
}
