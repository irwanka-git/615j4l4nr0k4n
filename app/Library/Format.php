<?php

namespace App\Library;
use Uuid;
use DB;
use App\Models\TitikKerusakan;
use App\Models\KlasifikasiJalan;
use App\Models\LaporanWarga;
use App\Models\Jalan;

class Format
{
    static function generate_uuid()
    {
         list($usec, $sec) = explode(" ", microtime());
            $time = ((float)$usec + (float)$sec);
            $time = str_replace(".", "-", $time);
            $panjang = strlen($time);
            $sisa = substr($time, -1*($panjang-5));
            $uuid =  Uuid::generate(3,rand(10,99).rand(0,9).substr($time, 0,5).'-'.rand(0,9).rand(0,9)."-".$sisa,Uuid::NS_DNS);
            return $uuid;
    }

    static function get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude){

        $point1 = $min_latitude." ".$min_longitude;
        $point2 = $min_latitude." ".$max_longitude;
        $point3 = $max_latitude." ".$max_longitude;
        $point4 = $max_latitude." ".$min_longitude;
        $polygon_point = "($point1, $point2, $point3, $point4, $point1)";
        $st_geom_polygon = " ST_GeomFromText('POLYGON(".$polygon_point.")')";
        $query_centroid = "SELECT ST_X(ST_Centroid($st_geom_polygon)) as latitude, ST_Y(ST_Centroid($st_geom_polygon)) as longitude";
        $result = DB::select($query_centroid);
        if(count($result)==1){
            return $result[0];
        }else{
            return false;
        }
    }

    static function generate_kode_titik_kerusakan($tahun){
        $tahun = $tahun;
        $last = TitikKerusakan::where('tahun',$tahun)->orderby('kode','desc')->first();
        if($last){
            return (int)$last->kode + 1;
        }else{
            $prefix = $tahun - 2000;
            return $prefix."0001";
        }
    }

    static function generate_new_kode_laporan($prefix){
        $last = LaporanWarga::whereRaw("left(kode,4)=$prefix")->orderby('kode','desc')->first();
        if($last){
            return (int)$last->kode + 1;
        }else{
            return $prefix."0001";
        }
    }

    static function generate_kode_jalan($id_klasifikasi){
        $prefix = KlasifikasiJalan::find($id_klasifikasi)->kode_klasifikasi;
        $last = Jalan::where('id_klasifikasi',$id_klasifikasi)->orderby('kode_jalan','desc')->first();
        if($last){
            $last_kode = (int)str_replace($prefix,"",$last->kode_jalan) + 1;
            return $prefix.str_pad($last_kode,3,"0",STR_PAD_LEFT);
        }else{
            return $prefix."001";
        }
    }

    static function sensor_text($text){
        if (strlen($text)>4){
            $text1 = substr($text,0,4);
            $sisa = strlen($text) - 4;
            return $text1.str_repeat("*", $sisa);;
        }
        return $text;
    }

}
