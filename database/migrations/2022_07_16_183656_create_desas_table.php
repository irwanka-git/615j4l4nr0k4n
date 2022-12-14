<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->increments('id'); 
            $table->char('kode_desa',10)->unique(); 
            $table->char('kode_kecamatan',7); 
            $table->string('nama_desa');
            $table->string('uuid')->unique();
            $table->timestamps();
            $table->index(['kode_kecamatan']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desas');
    }
}
