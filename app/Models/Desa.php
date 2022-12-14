<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    //
    protected $table = 'desa';
    public function titik_kerusakan()
    {
        return $this->hasMany('App\Models\TitikKerusakan', 'id_desa', 'id')
                    ->orderby('id','asc')
                    ->get();
    }

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'kode_kecamatan', 'kode_kecamatan')->first();
    }
}
