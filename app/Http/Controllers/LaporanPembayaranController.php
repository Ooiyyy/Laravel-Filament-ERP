<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;

// class LaporanPembayaranController extends Controller
// {
//     public function cetak(Request $request)
//     {
//         $from = $request->get('from');
//         $until = $request->get('until');
//         // $query = Pembayaran::with('pesanan.produk', 'pesanan.user')
//         //     ->where('keterangan', 'Lunas');

//         // if ($from) {
//         //     $query->whereDate('created_at', '>=', $from);
//         // }

//         // if ($until) {
//         //     $query->whereDate('created_at', '<=', $until);
//         // }

//         // $pembayaran = $query->get();
//         $pembayaran = Pembayaran::with('pesanan.produk', 'pesanan.user')
//             ->where('keterangan', 'Lunas')
//             ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
//             ->when($until, fn($q) => $q->whereDate('created_at', '<=', $until))
//             ->get();

//         // $pdf = Pdf::loadView('exports.laporan-pembayaran', [
//         //     'pembayaran' => $pembayaran,
//         //     'from' => $from,
//         //     'until' => $until,
//         // ])->setPaper('A4', 'portrait');
//         $pdf = Pdf::loadView('exports.laporan-pembayaran', compact('pembayaran', 'from', 'until'))
//             ->setPaper('A4', 'portrait');

//         return $pdf->stream('laporan-pembayaran.pdf');
//     }
// public function cetak(Request $request)
// {
//     $from = $request->input('from');
//     $until = $request->input('until');

//     $query = Pembayaran::with('pesanan.produk', 'pesanan.user')
//         ->where('keterangan', 'Lunas');

//     if ($from) {
//         $query->whereDate('created_at', '>=', $from);
//     }

//     if ($until) {
//         $query->whereDate('created_at', '<=', $until);
//     }

//     $pembayaran = $query->get();

//     $pdf = Pdf::loadView('exports.laporan-pembayaran', compact('pembayaran', 'from', 'until'))
//         ->setPaper('A4', 'portrait');

//     return $pdf->stream('laporan-pembayaran.pdf');
// }


// public function cetak(Request $request)
// {
//     $query = Pembayaran::with('pesanan.produk', 'pesanan.user')
//         ->where('keterangan', 'Lunas');

//     if ($request->has('from')) {
//         $query->whereDate('created_at', '>=', $request->input('from'));
//     }

//     if ($request->has('until')) {
//         $query->whereDate('created_at', '<=', $request->input('until'));
//     }

//     $pembayaran = $query->get();

//     $pdf = Pdf::loadView('exports.laporan-pembayaran', compact('pembayaran'))
//         ->setPaper('A4', 'portrait');

//     return $pdf->stream('laporan-pembayaran.pdf');
// }
// use Illuminate\Http\Request;

// namespace App\Http\Controllers;

// use Carbon\Carbon;
// use App\Models\Pembayaran;
// use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;
// use App\Http\Controllers\Controller;

class LaporanPembayaranController extends Controller
{
    // public function cetak(Request $request)
    // {
    //     $from = $request->get('from');
    //     $until = $request->get('until');

    //     $query = Pembayaran::with('pesanan.produk', 'pesanan.user')
    //         ->where('keterangan', 'Lunas');

    //     if ($from) {
    //         $query->whereDate('created_at', '>=', $from);
    //     }

    //     if ($until) {
    //         $query->whereDate('created_at', '<=', $until);
    //     }

    //     $pembayaran = $query->get();

    //     $pdf = Pdf::loadView('exports.laporan-pembayaran', [
    //         'pembayaran' => $pembayaran,
    //         'from' => $from,
    //         'until' => $until,
    //     ])->setPaper('A4', 'portrait');

    //     return $pdf->stream('laporan-pembayaran.pdf');
    // }
    public function cetak(Request $request)
    {
        $from = $request->query('from');
        $until = $request->query('until');

        $pembayaran = Pembayaran::with('pesanan.produk', 'pesanan.user')
            ->where('keterangan', 'Lunas')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($until, fn($q) => $q->whereDate('created_at', '<=', $until))
            ->get();

        $pdf = Pdf::loadView('exports.laporan-pembayaran', [
            'pembayaran' => $pembayaran,
            'from' => $from,
            'until' => $until,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-pembayaran.pdf');
    }
}
