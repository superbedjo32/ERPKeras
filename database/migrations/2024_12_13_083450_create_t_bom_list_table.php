<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_bom_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kode_bom'); // Harus sesuai tipe data dengan id di t_bom
            $table->unsignedBigInteger('kode_produk'); // Harus sesuai tipe data dengan id di t_produk
            $table->integer('qty'); // Jumlah bahan
            $table->string('satuan'); // Satuan bahan (misal: kg, pcs)
            $table->string('harga_total'); // Satuan bahan (misal: kg, pcs)

            // Foreign key constraints
            $table->foreign('kode_bom')->references('id')->on('t_bom')->onDelete('cascade');
            $table->foreign('kode_produk')->references('id')->on('t_produk')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_bom_list');
    }
};
