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
        Schema::create('t_vendor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor');
            $table->integer('telpon');
            $table->text('alamat')->nullable();
            $table->integer('status')->nullable();
            $table->string('company')->nullable();
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
