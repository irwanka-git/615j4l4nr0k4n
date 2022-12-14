<?php

namespace App\Http\Controllers;


use App\Library\Access; 

use App\Models\Jalan;
use App\Models\LaporanWarga;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Library\Format; 
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LaporanTitikKerusakanController extends Controller
{
    //
    public  function public_index(){
        $pagetitle = "Laporan Kondisi Jalan";
        $smalltitle = "";
        return view('laporan-public', compact('pagetitle','smalltitle',));
    }

    function insert_laporan_warga(Request $r){
        
            $validator = Validator::make($r->all(), [
                'latitude' => ['required'],
                'longitude' => ['required'],
                'geo_location' => ['required'],
                'nama_pelapor' => ['required'],
                'alamat_pelapor' => ['required'],
                'no_hp_pelapor' => ['required'],
                'isi_laporan' => ['required'],
                'id_gambar' => ['required','integer', 'min:1'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = new LaporanWarga();
            $record->kode =  Format::generate_new_kode_laporan(date('ym'));
            $record->id_gambar =  (int)($r->id_gambar);
            $record->latitude =  trim($r->latitude);
            $record->longitude =  trim($r->longitude);
            $record->geo_location =  trim($r->geo_location);
            $record->nama_pelapor =  trim($r->nama_pelapor);
            $record->alamat_pelapor =  trim($r->alamat_pelapor);
            $record->no_hp_pelapor =  trim($r->no_hp_pelapor);
            $record->isi_laporan =  trim($r->isi_laporan);
            $record->uuid =  trim($uuid);
            $record->save();

            $respon = array('status'=>true,'message'=>'Terima Kasih Atas Laporan Anda!');
            return response()->json($respon);
    }

    function get_data_map(){
        $min_latitude = LaporanWarga::min('latitude');
        $max_latitude = LaporanWarga::max('latitude');
        $min_longitude = LaporanWarga::min('longitude');
        $max_longitude = LaporanWarga::max('longitude');
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
                    'min_latitude'=>$min_latitude, 
                    'max_latitude'=>$max_latitude, 
                    'min_longitude'=>$min_longitude, 
                    'max_longitude'=>$max_longitude, 
                  ];
            $centroid = Format::get_centroid_area($min_latitude, $max_latitude, $min_longitude, $max_longitude);
        }

        
        $data = array();
        $data['centroid']= $centroid;
        $points = LaporanWarga::select(
                         'laporan_warga.uuid',
                         'laporan_warga.valid',
                         'laporan_warga.kode',
                         'laporan_warga.latitude',
                         'laporan_warga.longitude',
                    )->where('valid','1')->get();

       $data['points']= $points;
       $data['bound'] = $bound;

        //$respon = array('status'=>true,'data'=>$data);
        return response()->json($data);
    }


    public  function index_admin(){
        $pagetitle = "Laporan Kondisi Jalan";
        $smalltitle = "";
        return view('data.laporan-warga', compact('pagetitle','smalltitle',));
    }


    function datatable(Request $r){
        $status = (int) $r->status;
        $filter = " valid = $status  ";

        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter .= " and ( lower(laporan_warga.geo_location) like '%$keyword%' 
                                or lower(laporan_warga.nama_pelapor) like '%$keyword%' ) ";
            }   
        }

        $query = LaporanWarga::select(
                         'laporan_warga.uuid',
                         'laporan_warga.valid',
                         'laporan_warga.kode',
                         'laporan_warga.latitude',
                         'laporan_warga.longitude',
                         'laporan_warga.nama_pelapor',
                         'laporan_warga.isi_laporan',
                         'laporan_warga.geo_location',
                         'laporan_warga.created_at'
                     )
                    ->whereRaw($filter)->orderby('laporan_warga.created_at','asc');

         return Datatables::of($query)
            ->editColumn('koordinat', function($q){
                return $q->latitude.", ".$q->longitude;
            })
            ->editColumn('kode', function($q){
                return '<a href="#" class="view-detil" data-uuid="'.$q->uuid.'">'.$q->kode."</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['kode'])
            ->make(true);
    }

    function get_data_detil($uuid){
        $data = LaporanWarga::where('uuid', $uuid)->first();
        if($data){
            $gambar = $data->gambar() ? $data->gambar()->image:"";
            $respon = array('status'=>true,
                'data'=>$data,'gambar'=>$gambar);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_verifikasi(Request $r){
        if(Access::UserCanUpdate()){
            $validator = Validator::make($r->all(), [
                'respon' => ['required'],
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $record = LaporanWarga::where('uuid', $r->uuid)->first();
            $record->valid =  1;
            $record->respon_laporan =  trim($r->respon);
            $record->update_by =  Auth::user()->id;
            $record->save();

            $respon = array('status'=>true,'message'=>'Laporan Warga Berhasil Diupdate!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_tolak(Request $r){
        if(Access::UserCanUpdate()){
            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

             LaporanWarga::where('uuid', $r->uuid)->delete();
            $respon = array('status'=>true,'message'=>'Laporan Warga Berhasil Ditolak!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function get_info_windows_public($uuid){
        $data = LaporanWarga::where('uuid', $uuid)->first();
        if($data){
          $respon = array('status'=>true,
            'informasi'=> "Kode: ".$data->kode
                    ."<br>Waktu Laporan: ".$data->created_at." WIB <br>"
                   ."<br>".$data->geo_location." (".$data->latitude.", ".$data->longitude.")<br>".
                   "<br>Nama Pelapor: <b>". Format::sensor_text($data->nama_pelapor)."</b>".
                   "<br>Alamat Pelapor: <b>". $data->alamat_pelapor."</b>".
                   "<br>No Hp Pelapor: <b>". Format::sensor_text($data->no_hp_pelapor)."</b><br>".
                   "<br>Isi Laporan:<br><b><i> ".$data->isi_laporan."</i></b><br>".
                   "<br>Tanggapan Laporan:<br><b><i> ".$data->respon_laporan."</i></b>".
                   "<br><img class='img-thumbnail img-fluid mt-3 image-view' width='100%' src='".$data->gambar()->image."'>"
          );
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function get_info_windows($uuid){
        $data = LaporanWarga::where('uuid', $uuid)->first();
        if($data){
          $respon = array('status'=>true,
            'informasi'=> "Kode: ".$data->kode
                    ."<br>Waktu Laporan: ".$data->created_at." WIB <br>"
                   ."<br>".$data->geo_location." (".$data->latitude.", ".$data->longitude.")<br>".
                   "<br>Nama Pelapor: <b>". $data->nama_pelapor."</b>".
                   "<br>Alamat Pelapor: <b>". $data->alamat_pelapor."</b>".
                   "<br>No Hp Pelapor: <b>". $data->no_hp_pelapor."</b><br>".
                   "<br>Isi Laporan:<br><b><i> ".$data->isi_laporan."</i></b><br>".
                   "<br>Tanggapan Laporan:<br><b><i> ".$data->respon_laporan."</i></b>".
                   "<br><img class='img-thumbnail img-fluid mt-3 image-view' width='100%' src='".$data->gambar()->image."'>"
          );
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
}
