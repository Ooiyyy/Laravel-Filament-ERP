<?php

// app/Models/Pesanan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'product_id',
        'user_id',
        'jml_pesan',
        'harga_total',
        'tanggal_bayar',
    ];

    // protected static function booted()
    // {

    //     static::created(function ($pesanan) {
    //         \App\Models\Pesanan::create([
    //             'keterangan' => 'Belum Lunas',
    //         ]);
    //     });
    //     static::creating(function ($pesanan) {
    //         // Otomatis generate kode bayar, misalnya: BYR-20250624-001
    //         $latest = self::latest()->first();
    //         $urutan = $latest ? ((int) substr($latest->kode_pesanan, -3)) + 1 : 1;

    //         $pesanan->kode_pesanan = 'PSN-' . now()->format('Ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    //     });

    //     // 2. Buat pembayaran otomatis setelah pesanan berhasil dibuat
    //     static::created(function ($pesanan) {
    //         \App\Models\Pembayaran::create([
    //             'pesanan_id' => $pesanan->id,
    //             'keterangan' => 'Belum Lunas',
    //         ]);
    //     });
    // }
    protected static function booted()
    {
        // Generate kode pesanan otomatis
        static::creating(function ($pesanan) {

            $produk = \App\Models\Produk::find($pesanan->product_id);

            if (empty($pesanan->harga_total)) {
                $pesanan->harga_total = (int) ($pesanan->jml_pesan ?? 1) * (int) ($produk->harga ?? 1);
            }
            $latest = self::latest()->first();
            $urutan = $latest ? ((int) substr($latest->kode_pesanan, -3)) + 1 : 1;

            $pesanan->kode_pesanan = 'PSN-' . now()->format('Ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

            if ($produk && $produk->stok < $pesanan->jml_pesan) {
                throw new \Exception('Stok produk tidak mencukupi.');
            }
        });

        static::created(function ($pesanan) {

            // Kurangi stok produk
            $produk = \App\Models\Produk::find($pesanan->product_id);
            if ($produk && $pesanan->jml_pesan) {
                $produk->decrement('stok', $pesanan->jml_pesan);
            }

            // Buat pembayaran otomatis setelah pesanan dibuat
            \App\Models\Pembayaran::create([
                'pesanan_id' => $pesanan->id,
            ]);
        });
        // Kembalikan stok kalau pesanan dihapus
        static::deleting(function ($pesanan) {
            $produk = $pesanan->produk;
            if ($produk && $pesanan->jml_pesan) {
                $produk->increment('stok', $pesanan->jml_pesan);
            }
        });
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pembayarans()
    {
        return $this->hasOne(Pembayaran::class);
    }
        public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
