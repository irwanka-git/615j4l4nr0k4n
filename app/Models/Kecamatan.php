<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    //
    protected $table = 'kecamatan';

    public function desa()
    {
        return $this->belongsTo('App\Models\Desa', 'kode_kecamatan', 'kode_kecamatan')
                    ->orderby('kode_desa','asc')
                    ->get();
    }

    public function titik_kerusakan()
    {
        return $this->hasMany('App\Models\TitikKerusakan', 'id_kecamatan', 'id')
                    ->orderby('id','asc')
                    ->get();
    }
     
}
