<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk'; // penting! karena nama tabel bukan 'produks'

    protected $fillable = [
        'nama_produk',
        'harga',
        'foto',
        'stok',
    ];
}
