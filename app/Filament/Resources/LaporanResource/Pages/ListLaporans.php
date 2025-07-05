<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LaporanResource;

class ListLaporans extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Cetak PDF')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(route('laporan-pembayaran.cetak'))
                ->openUrlInNewTab(),
        ];
    }

}
