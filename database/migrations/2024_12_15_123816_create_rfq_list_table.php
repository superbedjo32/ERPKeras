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
        Schema::create('rfq_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('produk_id');
            $table->Integer('qty');
            $table->string('harga');
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('t_vendor')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('t_produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfq_list');
    }
};
