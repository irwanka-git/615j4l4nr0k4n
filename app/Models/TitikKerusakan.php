<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikKerusakan extends Model
{
    //
    protected $table = 'titik_kerusakan';

    public function gambar()
    {
        return $this->belongsTo('App\Models\Gambar', 'id_gambar', 'id')->first();
    }

    public function kabupaten()
    {
        return $this->belongsTo('App\Models\Kabupaten', 'id_kabupaten', 'id')->first();
    }

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'id_kecamatan', 'id')->first();
    }

    public function desa()
    {
        return $this->belongsTo('App\Models\Desa', 'id_desa', 'id')->first();
    }

    public function tingkat_kerusakan()
    {
        return $this->belongsTo('App\Models\TingkatKerusakan', 'id_tingkat_kerusakan', 'id')->first();
    }

    

    public function jalan()
    {
        return $this->belongsTo('App\Models\Jalan', 'id_jalan', 'id')->first();
    }
}
