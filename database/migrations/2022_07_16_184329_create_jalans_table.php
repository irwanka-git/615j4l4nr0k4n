<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJalansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jalan', function (Blueprint $table) {
            $table->increments('id');
            $table->char('kode_jalan', 4)->unique();
            $table->integer('id_klasifikasi');
            $table->string('nama_ruas_jalan');
            $table->string('uuid')->unique();
            $table->timestamps();
            $table->index(['id_klasifikasi']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jalans');
    }
}
