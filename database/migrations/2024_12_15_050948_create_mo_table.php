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
        Schema::create('mo', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mo');
            $table->date('tgl');
            $table->unsignedBigInteger('kode_bom');
            $table->integer('qty');
            $table->integer('status')->default(1);

            $table->foreign('kode_bom')->references('id')->on('t_bom')->onDelete('cascade');
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
        Schema::dropIfExists('mo');
    }
};
