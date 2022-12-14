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

use App\Models\Jalan;
use App\Models\KlasifikasiJalan;



class MasterJalanController extends Controller
{
    //
    function index(){

        $list_klasifikasi_jalan = KlasifikasiJalan::select('id as value', 
                DB::raw("concat(kode_klasifikasi, '. ', nama_klasifikasi) as text"))
                ->orderby('id')
                ->get();

        $pagetitle = "Data Ruas Jalan";
        $smalltitle = "Manajemen Data Master Ruas Jalan";
        return view('referensi.jalan', compact('pagetitle','smalltitle','list_klasifikasi_jalan'));
    }

    function datatable(Request $r){
        $filter = " jalan.id > 0  ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " jalan.id > 0 and ( lower(nama_ruas_jalan) like '%$keyword%' or lower(nama_klasifikasi) like '%$keyword%' ) ";
            }   
        }
        $query = Jalan::join('klasifikasi_jalan','klasifikasi_jalan.id','=','jalan.id_klasifikasi')
                    ->select('jalan.uuid',
                        'jalan.nama_ruas_jalan',
                        'jalan.kode_jalan',
                        'klasifikasi_jalan.nama_klasifikasi',
                        'klasifikasi_jalan.kode_klasifikasi',)
                    ->whereRaw($filter)
                    ->orderby('jalan.id_klasifikasi')
                    ->orderby('jalan.kode_jalan');

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
            ->editColumn('nama_klasifikasi', function($q){
                return $q->kode_klasifikasi.". ".$q->nama_klasifikasi;
            })
            ->addIndexColumn()
            ->rawColumns(['action','label'])
            ->make(true);
    }

     function generate_kode_jalan($id_klasifikasi){
        $kode_jalan =  Format::generate_kode_jalan($id_klasifikasi);
        $respon = array('status'=>true,'kode_jalan'=>$kode_jalan);
        return response()->json($respon);
    }

    function submit_insert(Request $r){
        if(Access::UserCanCreate()){

            $valid_klasifikasi = KlasifikasiJalan::pluck('id')->toArray();
            $validator = Validator::make($r->all(), [
                'kode_jalan' => ['required', 'unique:jalan,kode_jalan','min:4', 'max:4'],
                'id_klasifikasi' => ['required', Rule::in($valid_klasifikasi),],
                'nama_ruas_jalan' => ['required', 'min:3'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = new Jalan();
            $record->kode_jalan =  strtoupper($r->kode_jalan);
            $record->id_klasifikasi =  trim($r->id_klasifikasi);
            $record->nama_ruas_jalan =  trim($r->nama_ruas_jalan);
            $record->uuid =  trim($uuid);
            $record->save();

            $respon = array('status'=>true,'message'=>'Data Referensi Ruas Jalan Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

    function submit_update(Request $r){
        if(Access::UserCanCreate()){

            $valid_klasifikasi = KlasifikasiJalan::pluck('id')->toArray();
            $validator = Validator::make($r->all(), [
                'kode_jalan' => ['required', 
                    'unique:jalan,kode_jalan,'.Jalan::where('uuid', $r->uuid)->first()->id,'min:4', 'max:4'],
                'id_klasifikasi' => ['required', Rule::in($valid_klasifikasi),],
                'nama_ruas_jalan' => ['required', 'min:3'],
                'uuid' => ['required'],
            ]);
            
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $record = Jalan::where('uuid', $r->uuid)->first();
            $record->kode_jalan =  strtoupper($r->kode_jalan);
            $record->id_klasifikasi =  trim($r->id_klasifikasi);
            $record->nama_ruas_jalan =  trim($r->nama_ruas_jalan);
            $record->save();
            $respon = array('status'=>true,'message'=>'Data Referensi Ruas Jalan Berhasil Diperbarui!');
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
            $jalan = Jalan::where("uuid", $uuid)->first();
            if ($jalan->titik_kerusakan()->count() > 0){
                $respon = array('status'=>false,'message'=>'Referensi Ruas Jalan Tidak Bisa Dihapus Karena Ada Dependensi dengan Data Titik Kerusakan!');
                return response()->json($respon);
            }
            Jalan::where("uuid", $uuid)->delete();
            $respon = array('status'=>true,'message'=>'Data Referensi Jalan Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

     function get_data($uuid){
        $data = Jalan::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nama Ruas Jalan: '. $data->kode_jalan.". ".$data->nama_ruas_jalan);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }
}
