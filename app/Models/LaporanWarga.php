<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanWarga extends Model
{
    //
    protected $table = 'laporan_warga';

    public function gambar()
    {
        return $this->belongsTo('App\Models\Gambar', 'id_gambar', 'id')->first();
    }
}
