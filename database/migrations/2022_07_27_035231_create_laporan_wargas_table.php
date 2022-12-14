<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaporanWargasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_warga', function (Blueprint $table) {
            $table->increments('id');
            $table->char('kode',6)->unique(); 
            $table->integer('id_jalan')->nullable();
            $table->double('latitude', 12, 8);
            $table->double('longitude', 12, 8);
            $table->integer('id_kabupaten')->nullable();
            $table->integer('id_kecamatan')->nullable();
            $table->integer('id_desa')->nullable();
            $table->integer('tahun');
            $table->integer('id_gambar');
            $table->string('geo_location')->nullable();
            $table->string('nama_pelapor')->nullable();
            $table->string('alamat_pelapor')->nullable();
            $table->string('no_hp_pelapor')->nullable();
            $table->string('isi_laporan')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->integer('verifikasi')->nullable();
            $table->string('uuid')->unique();
            $table->timestamps();
            $table->index(['kode']);
            $table->index(['id_kecamatan']);
            $table->index(['id_desa']);
            $table->index(['create_by']);
            $table->index(['update_by']);
            $table->index(['tahun']);
            $table->index(['id_jalan']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_warga');
    }
}
