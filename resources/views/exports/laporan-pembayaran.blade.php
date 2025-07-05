{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; margin-bottom: 20px;">
        Laporan Pembayaran
        @if ($from && $until)
            <br>
            Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
            s/d {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
        @endif
    </h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Pemesan</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembayaran as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->pesanan->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->pesanan->user->name ?? '-' }}</td>
                    <td>{{ $item->pesanan->jml_pesan }}</td>
                    <td>Rp {{ number_format($item->pesanan->harga_total, 0, ',', '.') }}</td>
                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html> --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h2>
        Laporan Pembayaran
    @if ($from && $until)
        <br>
        Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Pemesan</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembayaran as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->pesanan->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->pesanan->user->name ?? '-' }}</td>
                    <td>{{ $item->pesanan->jml_pesan }}</td>
                    <td>Rp {{ number_format($item->pesanan->harga_total, 0, ',', '.') }}</td>
                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total</strong></td>
                <td colspan="2">
                    <strong>
                        Rp {{ number_format($pembayaran->sum(fn($item) => $item->pesanan->harga_total), 0, ',', '.') }}
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>


