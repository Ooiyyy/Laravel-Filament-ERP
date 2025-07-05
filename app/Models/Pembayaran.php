<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

// class Pembayaran extends Model
// {
//     use HasFactory;

//     protected $table = 'pembayaran';

//     protected $fillable = ['pesanan_id',];

//     public function pesanan()
//     {
//         return $this->belongsTo(Pesanan::class);
//     }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran'; // Nama tabel custom

    protected $fillable = [
        'pesanan_id',
        'keterangan',
    ];

    /**
     * Relasi ke model Pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Ambil data nama produk dari relasi pesanan
     */
    public function getNamaProdukAttribute()
    {
        return $this->pesanan->produk->nama_produk ?? '-';
    }

    /**
     * Ambil data pemesan dari relasi pesanan
     */
    public function getNamaPemesanAttribute()
    {
        return $this->pesanan->user->name ?? '-';
    }

    /**
     * Ambil data jumlah pesan dari relasi pesanan
     */
    public function getJumlahPesanAttribute()
    {
        return $this->pesanan->jml_pesan ?? 0;
    }

    /**
     * Ambil data total harga dari relasi pesanan
     */
    public function getTotalHargaAttribute()
    {
        return $this->pesanan->harga_total ?? 0;
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
