<?php

namespace App\Http\Controllers;
 
use App\Library\Access;
use App\Library\Format;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use DB;
use Session;
use Datatables;
use Crypt;

use App\Models\Desa;
use App\Models\Kecamatan;



class ReferensiDesaController extends Controller
{
    //

    function index(){
        $list_kecamatan = Kecamatan::select('kode_kecamatan as value', 
                DB::raw("concat(kode_kecamatan, '. ', nama_kecamatan) as text"))
                ->where('kode_kabupaten', env('KODE_KABUPATEN'))
                ->get();

        $pagetitle = "Desa/Kelurahan";
        $smalltitle = "Data Referensi Desa / Kelurahan";
        return view('referensi.desa', compact('pagetitle','smalltitle','list_kecamatan'));
    }

    function datatable(Request $r){
        $env_kabupaten = env('KODE_KABUPATEN');
        $filter = " kecamatan.kode_kabupaten = '$env_kabupaten' ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " kecamatan.kode_kabupaten = '$env_kabupaten'  and ( lower(nama_desa) like '%$keyword%' or lower(nama_kecamatan) like '%$keyword%' ) ";
            }   
        }
        $query = Desa::join('kecamatan','kecamatan.kode_kecamatan','=','desa.kode_kecamatan')
                    ->select('desa.uuid','kode_desa','nama_desa','nama_kecamatan','desa.kode_kecamatan')
                    ->whereRaw($filter)
                    ->orderby('kode_desa');

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
                        if ($action==" "){$action='<button class="btn btn-outline-secondary btn-sm">
                        <i class="la la-lock"></i></button>'; }
                    return $action;
            })
            ->editColumn('nama_kecamatan', function($q){
                return $q->kode_kecamatan.". ".$q->nama_kecamatan;
            })
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

    function submit_insert(Request $r){
        if(Access::UserCanCreate()){
            $kode_kabupaten = env('KODE_KABUPATEN');
            $valid_kecamatan = Kecamatan::where('kode_kabupaten',$kode_kabupaten)->pluck('kode_kecamatan')->toArray();
           
            $validator = Validator::make($r->all(), [
                'kode_desa' => ['required', 'unique:desa,kode_desa','digits:10'],
                'kode_kecamatan' => ['required', Rule::in($valid_kecamatan),],
                'nama_desa' => ['required', 'min:3'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = new Desa();
            $record->kode_kecamatan =  trim($r->kode_kecamatan);
            $record->kode_desa =  trim($r->kode_desa);
            $record->nama_desa =  trim($r->nama_desa);
            $record->uuid =  trim($uuid);
            $record->save();

            $respon = array('status'=>true,'message'=>'Data Referensi Desa Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update(Request $r){
        if(Access::UserCanCreate()){

            $kode_kabupaten = env('KODE_KABUPATEN');
            $valid_kecamatan = Kecamatan::where('kode_kabupaten',$kode_kabupaten)->pluck('kode_kecamatan')->toArray();
            $validator = Validator::make($r->all(), [
                'kode_desa' => ['required', 
                    'unique:desa,kode_desa,'.Desa::where('uuid', $r->uuid)->first()->id,'digits:10'],
                'kode_kecamatan' => ['required', Rule::in($valid_kecamatan),],
                'nama_desa' => ['required', 'min:3'],
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = Desa::where('uuid', $r->uuid)->first();
            $record->kode_kecamatan =  trim($r->kode_kecamatan);
            $record->kode_desa =  trim($r->kode_desa);
            $record->nama_desa =  trim($r->nama_desa);
            $record->save();
            $respon = array('status'=>true,'message'=>'Data Referensi Desa Berhasil Diperbarui!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete(Request $r){
        if(Access::UserCanCreate()){
            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }
            $uuid = trim($r->uuid);
            $desa = desa::where("uuid", $uuid)->first();
            if ($desa->titik_kerusakan()->count() > 0){
                $respon = array('status'=>false,'message'=>'Referensi Desa Tidak Bisa Dihapus Karena Ada Dependensi dengan Data Titik Kerusakan!');
                return response()->json($respon);
            }
            Desa::where("uuid", $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Referensi Desa Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

     function get_data($uuid){
        $data = Desa::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nama Desa: '. $data->nama_desa);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
}
