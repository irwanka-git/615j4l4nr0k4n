<?php

namespace App\Http\Controllers;

use App\Library\Access;
use App\Library\Format;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Image;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;

use App\Models\Jalan;
use App\Models\TingkatKerusakan;
use App\Models\TitikKerusakan;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Gambar;

class DataTitikKerusakanController extends Controller
{
    //

     function index(){

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
        $ref_tingkat_kerusakan = TingkatKerusakan::get();

        $pagetitle = "Titik Kerusakan Ruas Jalan";
        $smalltitle = "Manajemen Data Titik Kerusakan Ruas Jalan";
        return view('data.entri-sebaran', compact('pagetitle','smalltitle',
            'list_ruas_jalan',
            'list_tingkat_kerusakan',
            'list_kecamatan',
            'list_tahun_survey',
            'ref_tingkat_kerusakan'
            ));
    }

    function datatable(Request $r){
        $filter = " titik_kerusakan.id > 0  ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter .= " and ( lower(jalan.nama_ruas_jalan) like '%$keyword%' 
                                or lower(titik_kerusakan.kode) like '%$keyword%' 
                                or lower(titik_kerusakan.tahun) like '%$keyword%' 
                                or lower(kecamatan.nama_kecamatan) like '%$keyword%' 
                                or lower(desa.nama_desa) like '%$keyword%' 
                                or lower(jalan.kode_jalan) like '%$keyword%' ) ";
            }   
        }
        $query = TitikKerusakan::join('jalan','jalan.id','=','titik_kerusakan.id_jalan')
                    ->join('kecamatan','kecamatan.id','=','titik_kerusakan.id_kecamatan')
                    ->join('desa','desa.id','=','titik_kerusakan.id_desa')
                    ->join('tingkat_kerusakan','tingkat_kerusakan.id','=','titik_kerusakan.id_tingkat_kerusakan')
                    ->select(
                        'titik_kerusakan.uuid',
                        'titik_kerusakan.kode',
                        'titik_kerusakan.tahun',
                        'titik_kerusakan.latitude',
                        'titik_kerusakan.longitude',
                        'jalan.nama_ruas_jalan',
                        'jalan.kode_jalan',
                        'kecamatan.nama_kecamatan',
                        'desa.nama_desa',
                        'tingkat_kerusakan.kode_rusak',
                        'tingkat_kerusakan.nama_kerusakan',
                     )
                    ->whereRaw($filter);

         return Datatables::of($query)
            ->addColumn('action', function ($query) {
                    $edit = ""; $delete = "";
                    if(Access::UserCanUpdate()){
                        $edit = '<button data-bs-toggle="modal" data-uuid="'.$query->uuid.'" data-bs-target="#modal-edit" class="btn btn-outline-secondary btn-outline btn-sm" type="button"><i class="las la-pen"></i></button>';
                    }
                    if(Access::UserCanDelete()){
                        $delete = '<button  data-uuid="'.$query->uuid.'" class="btn btn-outline-secondary btn-sm btn-konfirm-delete" type="button"><i class="las la-trash"></i></button>';
                    }
                   $action =  $edit." ".$delete;
                        if ($action==""){$action='<button class="btn btn-outline-secondary btn-sm">
                        <i class="la la-lock"></i></button>'; }
                    return $action;
            })
            ->editColumn('nama_ruas_jalan', function($q){
                return $q->kode_jalan." ".$q->nama_ruas_jalan;
            })
            ->editColumn('kode', function($q){
                return '<a href="#" class="view-detil" data-uuid="'.$q->uuid.'">'.$q->kode."</a>";
            })
            ->addIndexColumn()
            ->rawColumns(['action','nama_ruas_jalan','kode'])
            ->make(true);
    }


    function generate_list_desa($id){
        $kode_kecamatan = Kecamatan::find($id)->kode_kecamatan;
        $desa = Desa::where('kode_kecamatan', $kode_kecamatan)->select( 
                                'id',
                                DB::raw("concat(kode_desa,'. ', nama_desa) as name")
                                )->orderby('kode_desa')->get();
        $respon = array('status'=>true,'data'=>$desa);
        return response()->json($respon);
    }

    function get_data($uuid){
        $data = TitikKerusakan::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>str_pad($data->id, 4, "0", STR_PAD_LEFT)." : ".$data->jalan()->nama_ruas_jalan.", ". $data->desa()->nama_desa." Kec. ". $data->kecamatan()->nama_kecamatan." / ".$data->tingkat_kerusakan()->nama_kerusakan."/ Koordinat (".$data->latitude.", ".$data->longitude.")");
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function get_data_detil($uuid){
        $data = TitikKerusakan::where('uuid', $uuid)->first();
        if($data){
            $gambar = $data->gambar() ? $data->gambar()->image:"";
            $respon = array('status'=>true,
                'data'=>$data, 
                'kode'=>$data->kode,
                'jalan'=>$data->jalan(),
                'klasifikasi_jalan'=>$data->jalan()->klasifikasi_jalan(),
                'tingkat_kerusakan'=>$data->tingkat_kerusakan(),
                'kecamatan'=>$data->kecamatan(),
                'desa'=>$data->desa(),
                'gambar'=>$gambar,
                'informasi'=>$data->jalan()->nama_ruas_jalan." Desa/Kelurahan ". $data->desa()->nama_desa." Kec. ". $data->kecamatan()->nama_kecamatan." - ".$data->tingkat_kerusakan()->nama_kerusakan." (".$data->latitude.", ".$data->longitude.")");
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

   

    function upload_gambar(Request $request){

        $not_valid = $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $image = $request->file('image');
        $filename= rand(100,999).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/temp');
        $img = Image::make($image->getRealPath());
        $height  = $img->height();
        $width  = $img->width();
        if($height > 1500 && $height > $width){
            $img->resize(null, 1500, function ($constraint) {
                 $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$filename);
        }elseif($width > 1500 && $height < $width){
            $img->resize(1500, null, function ($constraint) {
                 $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$filename);
        }
        elseif($width > 1500 && $height == $width){
            $img->fit(1500)->save($destinationPath.'/'.$filename);
        }else{
            $img->save($destinationPath.'/'.$filename);
        } 

        $path = public_path().'/temp/'.$filename;
        $data = file_get_contents($path);
        $image_base64 = base64_encode($data);
        $type = pathinfo($path, PATHINFO_EXTENSION); 
        if ($image_base64){
           //hapus file asli
           if(file_exists(public_path('temp/'.$filename))){
                unlink(public_path('temp/'.$filename));
           }

                $image_resource = "data:image/".$type.";base64, ".$image_base64;
                $gambar = new Gambar();
                $gambar->image = "data:image/".$type.";base64, ".$image_base64;
                $gambar->width = $width; 
                $gambar->height = $height; 
                $gambar->extension = $type; 
                $gambar->save();

                $respon = array('status'=>true, 
                                'image'=>$gambar->image, 
                                'id_gambar'=>$gambar->id,
                                'height'=>$gambar->height, 
                                'width'=>$gambar->width);
                return response()->json($respon); 
        }
        else
        {
                $respon = array('status'=>false, "message"=>'Gagal Upload Gambar');
                return response()->json($respon); 
        }
    }

    function submit_insert(Request $r){
        if(Access::UserCanCreate()){
            $min_tahun_valid = date('Y') - 5;
            $max_tahun_valid = date('Y');
            $valid_ruas_jalan = Jalan::pluck('id')->toArray();
            $valid_tingkat_kerusakan = TingkatKerusakan::pluck('id')->toArray();
            $valid_kecamatan = Kecamatan::pluck('id')->toArray();
            $valid_desa = Desa::pluck('id')->toArray();
            $id_kabupaten = Kabupaten::where('kode_kabupaten', env('KODE_KABUPATEN'))->first()->id;

            $validator = Validator::make($r->all(), [
                'id_jalan' => ['required', Rule::in($valid_ruas_jalan),],
                'id_tingkat_kerusakan' => ['required', Rule::in($valid_tingkat_kerusakan),],
                'id_kecamatan' => ['required', Rule::in($valid_kecamatan),],
                'id_desa' => ['required', Rule::in($valid_desa),],
                'latitude' => ['required'],
                'longitude' => ['required'],
                'tahun' => ['required','integer','min:'.$min_tahun_valid, 'max:'.$max_tahun_valid],
                'id_gambar' => ['required','integer', 'min:1'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = new TitikKerusakan();
            $record->kode =  Format::generate_kode_titik_kerusakan($r->tahun);
            $record->id_jalan =  (int)($r->id_jalan);
            $record->id_tingkat_kerusakan =  (int)($r->id_tingkat_kerusakan);
            $record->id_kecamatan =  (int)($r->id_kecamatan);
            $record->id_desa =  (int)($r->id_desa);
            $record->id_kabupaten =  (int)($id_kabupaten);
            $record->tahun =  (int)($r->tahun);
            $record->id_gambar =  (int)($r->id_gambar);
            $record->latitude =  trim($r->latitude);
            $record->longitude =  trim($r->longitude);
            $record->geo_location =  trim($r->geo_location);
            $record->uuid =  trim($uuid);
            $record->create_by =  Auth::user()->id;
            $record->save();

            $respon = array('status'=>true,'message'=>'Data Titik Kerusakan Ruas Jalan Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update(Request $r){
        if(Access::UserCanUpdate()){
            $min_tahun_valid = date('Y') - 5;
            $max_tahun_valid = date('Y');
            $valid_ruas_jalan = Jalan::pluck('id')->toArray();
            $valid_tingkat_kerusakan = TingkatKerusakan::pluck('id')->toArray();
            $valid_kecamatan = Kecamatan::pluck('id')->toArray();
            $valid_desa = Desa::pluck('id')->toArray();
            $id_kabupaten = Kabupaten::where('kode_kabupaten', env('KODE_KABUPATEN'))->first()->id;

            $validator = Validator::make($r->all(), [
                'id_jalan' => ['required', Rule::in($valid_ruas_jalan),],
                'id_tingkat_kerusakan' => ['required', Rule::in($valid_tingkat_kerusakan),],
                'id_kecamatan' => ['required', Rule::in($valid_kecamatan),],
                'id_desa' => ['required', Rule::in($valid_desa),],
                'latitude' => ['required'],
                'longitude' => ['required'],
                'tahun' => ['required','integer','min:'.$min_tahun_valid, 'max:'.$max_tahun_valid],
                'id_gambar' => ['required','integer', 'min:1'],
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $record = TitikKerusakan::where('uuid', $r->uuid)->first();
            $record->id_jalan =  (int)($r->id_jalan);
            $record->id_tingkat_kerusakan =  (int)($r->id_tingkat_kerusakan);
            $record->id_kecamatan =  (int)($r->id_kecamatan);
            $record->id_desa =  (int)($r->id_desa);
            $record->id_kabupaten =  (int)($id_kabupaten);
            $record->tahun =  (int)($r->tahun);
            $record->id_gambar =  (int)($r->id_gambar);
            $record->latitude =  trim($r->latitude);
            $record->longitude =  trim($r->longitude);
            $record->geo_location =  trim($r->geo_location);
            $record->update_by =  Auth::user()->id;
            $record->save();

            $respon = array('status'=>true,'message'=>'Data Titik Kerusakan Ruas Jalan Berhasil Diperbarui!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete(Request $r){
        if(Access::UserCanDelete()){

            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }
            
            $record = TitikKerusakan::where('uuid', $r->uuid)->first();
            $record->delete();

            $respon = array('status'=>true,'message'=>'Data Titik Kerusakan Ruas Jalan Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }
}
