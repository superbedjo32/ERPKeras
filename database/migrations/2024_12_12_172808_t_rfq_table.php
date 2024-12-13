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
        Schema::create('t_rfq', function (Blueprint $table) {
            $table->id();
            $table->string('id_reference');
            $table->string('nama_produk');
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->integer('qty')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('status')->default(1); // 1: aktif, 2: nonaktif
            $table->timestamps(); // created_at, updated_at
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};