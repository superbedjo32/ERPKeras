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
        Schema::create('t_po', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po');
            $table->date('tgl_pembayaran');
            $table->string('pembayaran')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedBigInteger('rfq_id');
            $table->timestamps();

            $table->foreign('rfq_id')->references('id')->on('t_rfq')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_po');
    }
};
