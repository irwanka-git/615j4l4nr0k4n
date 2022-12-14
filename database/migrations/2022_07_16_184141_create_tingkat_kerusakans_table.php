<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTingkatKerusakansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tingkat_kerusakan', function (Blueprint $table) {
            $table->increments('id');
            $table->char('kode_rusak',1)->unique();
            $table->string('nama_kerusakan')->unique();
            $table->string('warna')->unique();
            $table->string('warna_stroke')->unique();
            $table->string('uuid')->unique();
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
        Schema::dropIfExists('tingkat_kerusakans');
    }
}
