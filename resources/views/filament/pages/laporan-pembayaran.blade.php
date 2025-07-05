<style>
@media print {
    body * {
        visibility: hidden;
    }
    .filament-page, .filament-page * {
        visibility: visible;
    }
    .filament-page {
        position: absolute;
        left: 0;
        top: 0;
    }
    button { display: none !important; }
}
</style>

<x-filament::page>
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">Laporan Pembayaran Lunas</h2>
        <button onclick="window.print()" class="bg-primary text-white px-4 py-2 rounded">🖨️ Cetak</button>
    </div>

    <table class="table-auto w-full border border-collapse border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Kode Pesanan</th>
                <th class="border px-2 py-1">Pemesan</th>
                <th class="border px-2 py-1">Produk</th>
                <th class="border px-2 py-1">Jumlah</th>
                <th class="border px-2 py-1">Total Harga</th>
                <th class="border px-2 py-1">Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLunas as $item)
                <tr>
                    <td class="border px-2 py-1">{{ $item->pesanan->kode_pesanan }}</td>
                    <td class="border px-2 py-1">{{ $item->pesanan->user->name }}</td>
                    <td class="border px-2 py-1">{{ $item->pesanan->produk->nama_produk }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->pesanan->jml_pesan }}</td>
                    <td class="border px-2 py-1 text-right">Rp{{ number_format($item->pesanan->harga_total) }}</td>
                    <td class="border px-2 py-1">{{ $item->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::page>
