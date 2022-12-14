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

use App\Models\Kabupaten;
use App\Models\Kecamatan;


class ReferensiKecamatanController extends Controller
{
    //
    function index(){
        $list_kabupaten = Kabupaten::select('kode_kabupaten as value', 
                DB::raw("concat(kode_kabupaten, '. ', nama_kabupaten) as text"))->get();

        $pagetitle = "Kecamatan";
        $smalltitle = "Data Referensi Kecamatan";
        return view('referensi.Kecamatan', compact('pagetitle','smalltitle','list_kabupaten'));
    }

    function datatable(Request $r){
        $env_kabupaten = env('KODE_KABUPATEN');
        $filter = " kecamatan.kode_kabupaten = '$env_kabupaten' ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " kecamatan.kode_kabupaten = '$env_kabupaten'  and ( lower(nama_kecamatan) like '%$keyword%' ) ";
            }   
        }
        $query = Kecamatan::join('kabupaten','kabupaten.kode_kabupaten','=','kecamatan.kode_kabupaten')
                    ->select('kecamatan.uuid','kode_kecamatan','nama_kecamatan','kabupaten.kode_kabupaten',
                                'kabupaten.nama_kabupaten')
                    ->whereRaw($filter)
                    ->orderby('kode_kecamatan');

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
            ->editColumn('nama_kabupaten', function($q){
                return $q->kode_kabupaten.". ".$q->nama_kabupaten;
            })
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

    function submit_insert(Request $r){
        if(Access::UserCanCreate()){

            $valid_kabupaten = Kabupaten::pluck('kode_kabupaten')->toArray();
            $validator = Validator::make($r->all(), [
                'kode_kecamatan' => ['required', 'unique:kecamatan,kode_kecamatan','digits:7'],
                'kode_kabupaten' => ['required', Rule::in($valid_kabupaten),],
                'nama_kecamatan' => ['required', 'min:4'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = new Kecamatan();
            $record->kode_kecamatan =  trim($r->kode_kecamatan);
            $record->kode_kabupaten =  trim($r->kode_kabupaten);
            $record->nama_kecamatan =  trim($r->nama_kecamatan);
            $record->uuid =  trim($uuid);
            $record->save();
            $respon = array('status'=>true,'message'=>'Data Referensi Kecamatan Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update(Request $r){
        if(Access::UserCanCreate()){

            $valid_kabupaten = Kabupaten::pluck('kode_kabupaten')->toArray();
            $validator = Validator::make($r->all(), [
                'kode_kecamatan' => ['required', 
                    'unique:kecamatan,kode_kecamatan,'.Kecamatan::where('uuid', $r->uuid)->first()->id,'digits:7'],
                'kode_kabupaten' => ['required', Rule::in($valid_kabupaten),],
                'nama_kecamatan' => ['required', 'min:4'],
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = Kecamatan::where('uuid', $r->uuid)->first();
            $record->kode_kecamatan =  trim($r->kode_kecamatan);
            $record->kode_kabupaten =  trim($r->kode_kabupaten);
            $record->nama_kecamatan =  trim($r->nama_kecamatan);
            $record->save();
            $respon = array('status'=>true,'message'=>'Data Referensi Kecamatan Berhasil Diperbarui!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_delete(Request $r){
        if(Access::UserCanCreate()){

            $valid_kabupaten = Kabupaten::pluck('kode_kabupaten')->toArray();
            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }
            $uuid = trim($r->uuid);
            $kecamatan = Kecamatan::where("uuid", $uuid)->first();
            if ($kecamatan->desa()->count() > 0){
                $respon = array('status'=>false,'message'=>'Referensi Kecamatan Tidak Bisa Dihapus Karena Ada Dependensi dengan Referensi Desa!');
                return response()->json($respon);
            }

            if ($kecamatan->titik_kerusakan()->count() > 0){
                $respon = array('status'=>false,'message'=>'Referensi Kecamatan Tidak Bisa Dihapus Karena Ada Dependensi dengan Data Titik Kerusakan!');
                return response()->json($respon);
            }
            Kecamatan::where("uuid", $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Referensi Kecamatan Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

     function get_data($uuid){
        $data = Kecamatan::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nama Kecamatan: '. $data->nama_kecamatan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
}
