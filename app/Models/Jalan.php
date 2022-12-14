<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jalan extends Model
{
    //
    protected $table = 'jalan';

    public function titik_kerusakan()
    {
        return $this->hasMany('App\Models\TitikKerusakan', 'id_jalan', 'id')
                    ->orderby('id','asc')
                    ->get();
    }

    public function klasifikasi_jalan()
    {
        return $this->belongsTo('App\Models\KlasifikasiJalan', 'id_klasifikasi', 'id')->first();
    }
}
