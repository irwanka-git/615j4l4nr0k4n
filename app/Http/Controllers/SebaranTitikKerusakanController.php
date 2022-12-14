<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Jalan;
use App\Models\TingkatKerusakan;
use App\Models\TitikKerusakan;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Library\Format;
use DB;

class SebaranTitikKerusakanController extends Controller
{

    function public_index(){
        $pagetitle = "Peta Sebaran";
        $smalltitle = "Peta Sebaran Titik Kerusakan Ruas Jalan";
        $ref_tingkat_kerusakan = TingkatKerusakan::get();
        $ref_jalan = Jalan::get();
        $ref_kecamatan = Kecamatan::get();

        $list_ruas_jalan = Jalan::select('id as value', 
                DB::raw("concat(kode_jalan, '. ', nama_ruas_jalan) as text"))
                ->orderby('id')
                ->get();

        $list_tingkat_kerusakan = TingkatKerusakan::select('id as value', 
                DB::raw("concat(nama_kerusakan) as text"))
                ->orderby('id')
                ->get();


        $list_kecamatan = Kecamatan::select('id as value', 
                DB::raw("concat(kode_kecamatan, '. ', nama_kecamatan) as text"))
                ->orderby('id')
                ->get();
        $tahun = date('Y');
        $array_tahun = [];
        for($th = $tahun;$th>=$tahun - 5;$th--){
           $temp = array('value'=>$th,'text'=>$th);
           array_push($array_tahun,$temp);
        }
        $list_tahun_survey = json_decode(json_encode($array_tahun));

        return view('peta-sebaran-public', compact('pagetitle','smalltitle','ref_tingkat_kerusakan', 'list_ruas_jalan','list_kecamatan','list_tingkat_kerusakan','list_tahun_survey' ));
   }

   function index(){
        $pagetitle = "Peta Sebaran";
        $smalltitle = "Peta Seabaran Titik Kerusakan Ruas Jalan";
        $ref_tingkat_kerusakan = TingkatKerusakan::get();
        $ref_jalan = Jalan::get();
        $ref_kecamatan = Kecamatan::get();

        $list_ruas_jalan = Jalan::select('id as value', 
                DB::raw("concat(kode_jalan, '. ', nama_ruas_jalan) as text"))
                ->orderby('id')
                ->get();

        $list_tingkat_kerusakan = TingkatKerusakan::select('id as value', 
                DB::raw("concat(nama_kerusakan) as text"))
                ->orderby('id')
                ->get();


        $list_kecamatan = Kecamatan::select('id as value', 
                DB::raw("concat(kode_kecamatan, '. ', nama_kecamatan) as text"))
                ->orderby('id')
                ->get();
        $tahun = date('Y');
        $array_tahun = [];
        for($th = $tahun;$th>=$tahun - 5;$th--){
           $temp = array('value'=>$th,'text'=>$th);
           array_push($array_tahun,$temp);
        }
        $list_tahun_survey = json_decode(json_encode($array_tahun));

        return view('data.peta-sebaran', compact('pagetitle','smalltitle','ref_tingkat_kerusakan', 'list_ruas_jalan','list_kecamatan','list_tingkat_kerusakan','list_tahun_survey' ));
   }



   function get_data_map_default(){

        $min_latitude = TitikKerusakan::min('latitude');
        $max_latitude = TitikKerusakan::max('latitude');
        $min_longitude = TitikKerusakan::min('longitude');
        $max_longitude = TitikKerusakan::max('longitude');
        $centroid = Format::get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude);

       $bound = [
                    'min_latitude'=>$min_latitude - 0.008, 
                    'max_latitude'=>$max_latitude  + 0.008, 
                    'min_longitude'=>$min_longitude - 0.008, 
                    'max_longitude'=>$max_longitude + 0.008, 
                  ];
        $data = array();
        if (!$centroid->latitude){
            $min_latitude = env('DEFAULT_MIN_LATITUDE');
            $max_latitude = env('DEFAULT_MAX_LATITUDE');
            $min_longitude = env('DEFAULT_MIN_LONGITUDE');
            $max_longitude = env('DEFAULT_MAX_LONGITUDE');

            $bound = [
                    'min_latitude'=>$min_latitude - 0.008, 
                    'max_latitude'=>$max_latitude  + 0.008, 
                    'min_longitude'=>$min_longitude - 0.008, 
                    'max_longitude'=>$max_longitude + 0.008, 
                  ];
                  
            $centroid = Format::get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude);
        }
        $data['centroid']= $centroid;
        $points = TitikKerusakan::join('tingkat_kerusakan','tingkat_kerusakan.id', 'titik_kerusakan.id_tingkat_kerusakan')
                    ->select(
                         'titik_kerusakan.uuid',
                         'titik_kerusakan.kode',
                         'titik_kerusakan.latitude',
                         'titik_kerusakan.longitude',
                         'tingkat_kerusakan.kode_rusak',
                         'tingkat_kerusakan.warna',
                         'tingkat_kerusakan.warna_stroke'
                    )->get();

       $data['points']= $points;
       $data['bound'] = $bound;

        //$respon = array('status'=>true,'data'=>$data);
        return response()->json($data);
   }

   function get_data_map_search($id_kecamatan, $tahun, $id_tingkat_kerusakan){
     $filter = " titik_kerusakan.id > 0 ";
     if($id_kecamatan!='_all'){
          $filter .=" and titik_kerusakan.id_kecamatan = $id_kecamatan";
     }

     if($tahun!='_all'){
          $filter .=" and titik_kerusakan.tahun = $tahun";
     }

     if($id_tingkat_kerusakan!='_all'){
          $filter .=" and titik_kerusakan.id_tingkat_kerusakan = $id_tingkat_kerusakan";
     }

     $query = TitikKerusakan::join('tingkat_kerusakan','tingkat_kerusakan.id', 'titik_kerusakan.id_tingkat_kerusakan')
                         ->select(
                              'titik_kerusakan.uuid',
                              'titik_kerusakan.kode',
                              'titik_kerusakan.latitude',
                              'titik_kerusakan.longitude',
                              'tingkat_kerusakan.kode_rusak',
                              'tingkat_kerusakan.warna',
                              'tingkat_kerusakan.warna_stroke',)->whereRaw($filter);
                    ;
     $min_latitude = $query->min('latitude');
     $max_latitude = $query->max('latitude');
     $min_longitude = $query->min('longitude');
     $max_longitude = $query->max('longitude');
     $centroid = Format::get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude);

     $bound = [
               'min_latitude'=>$min_latitude, 
               'max_latitude'=>$max_latitude, 
               'min_longitude'=>$min_longitude, 
               'max_longitude'=>$max_longitude, 
             ];
     $data = array();
     $data['centroid']= $centroid;

     $points = $query->get();

     $data['points']= $points;
     $data['bound'] = $bound;
     if(count($points) == 0){
        $min_latitude = TitikKerusakan::min('latitude');
        $max_latitude = TitikKerusakan::max('latitude');
        $min_longitude = TitikKerusakan::min('longitude');
        $max_longitude = TitikKerusakan::max('longitude');
        $centroid = Format::get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude);

        $bound = [
                    'min_latitude'=>$min_latitude, 
                    'max_latitude'=>$max_latitude, 
                    'min_longitude'=>$min_longitude, 
                    'max_longitude'=>$max_longitude, 
                  ];
        $data = array();
        $data['centroid']= $centroid;
        $data['points']= [];
        $data['bound'] = $bound;
        $respon = array('status'=>true, 'data'=>$data);
     }else{
           $respon = array('status'=>true, 'data'=>$data);
     }
     return response()->json($respon);
        //$respon = array('status'=>true,'data'=>$data);
    

   }

   function get_info_windows($uuid){
        $data = TitikKerusakan::where('uuid', $uuid)->first();
        if($data){
          $jalan = $data->jalan();
          $kecamatan = $data->kecamatan();
          $desa = $data->desa();
          $tingkat_kerusakan = $data->tingkat_kerusakan();
          $respon = array('status'=>true,
            'informasi'=> "Kode: ".$data->kode
                   ."<br>".$jalan->nama_ruas_jalan." (".$data->latitude.", ".$data->longitude.")".
                   "<br>Desa/Kelurahan: ". $desa->nama_desa.
                   "<br>Kecamatan: ". $kecamatan->nama_kecamatan.
                   "<br>Kondisi:  ".$tingkat_kerusakan->nama_kerusakan." (Thn. ". $data->tahun.")".
                   "<br><img class='img-thumbnail img-fluid mt-3 image-view' width='100%' src='".$data->gambar()->image."'>"
          );
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
   }
}
