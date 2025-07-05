<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
        $table->id();

        $table->string('kode_pesanan')->unique();
        $table->foreignId('product_id')->constrained('produk')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        $table->integer('jml_pesan')->default(1);
        $table->integer('harga_total')->default();
        $table->date('tanggal_bayar');

        $table->enum('keterangan', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
