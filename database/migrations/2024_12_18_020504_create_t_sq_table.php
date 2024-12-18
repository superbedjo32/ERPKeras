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
        Schema::create('t_sq', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sq');
            $table->unsignedBigInteger('vendor_id');
            $table->date('tanggal_transaksi');
            $table->string('total_harga');
            $table->string('pembayaran');
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('t_vendor')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_sq');
    }
};
