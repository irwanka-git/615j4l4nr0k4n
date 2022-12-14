<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitikKerusakansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titik_kerusakan', function (Blueprint $table) {
            $table->increments('id');
            $table->char('kode',6)->unique(); 
            $table->integer('id_jalan');
            $table->integer('id_tingkat_kerusakan');
            $table->double('latitude', 12, 8);
            $table->double('longitude', 12, 8);
            $table->integer('id_kabupaten');
            $table->integer('id_kecamatan');
            $table->integer('id_desa');
            $table->integer('tahun');
            $table->integer('id_gambar');
            $table->string('geo_location')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->string('uuid')->unique();
            $table->timestamps();
            $table->index(['kode']);
            $table->index(['id_kecamatan']);
            $table->index(['id_desa']);
            $table->index(['create_by']);
            $table->index(['update_by']);
            $table->index(['tahun']);
            $table->index(['id_tingkat_kerusakan']);
            $table->index(['id_jalan']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('titik_kerusakans');
    }
}
