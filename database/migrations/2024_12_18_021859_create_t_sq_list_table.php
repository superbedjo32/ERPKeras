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
        Schema::create('t_sq_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sq_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('qty')->nullable();;
            $table->string('satuan')->nullable();;
            $table->string('total')->nullable();
            $table->timestamps();

            $table->foreign('sq_id')->references('id')->on('t_sq')->onDelete('cascade');
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
        Schema::dropIfExists('t_sq_list');
    }
};
