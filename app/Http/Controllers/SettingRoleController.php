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

use App\Models\Role;
use App\Models\Menu;
use App\Models\RoleMenu;

class SettingRoleController extends Controller
{
     
    //######################### SETTING Role #####################################
    function index(){
    	$pagetitle = "Pengaturan Role";
    	$smalltitle = "Pengaturan Role dan Akses Menu";
    	return view('setting.role', compact('pagetitle','smalltitle'));
    }

    function datatable_role(Request $r){
        
        $filter = "id > 0 ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " id > 0 and ( lower(nama_role) like '%$keyword%' ) ";
            }   
        }
        $query  =   Role::query()->whereRaw($filter);

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
            ->addColumn('menu', function ($query) {
                   return '<a href="'.url('setting-role/menu/'.$query->uuid).'" class="btn btn-outline-secondary btn-sm"><i class="la la-cog"></i> '.$query->menus()->count().' Menu</a>';
            })
            ->addColumn('user', function ($query) {
                return '<a href="'.url('setting-role/user/'.$query->uuid).'" class="btn btn-outline-secondary btn-sm"><i class="la la-user"></i> '.$query->users()->count().' Akun</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['action','menu','user'])
            ->make(true);
    }

    function get_data_role($uuid){
    	$data = Role::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
            	'informasi'=>'Nama Role: '. $data->nama_role);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert_role(Request $r){
    	if(Access::UserCanCreate()){

            $validator = Validator::make($r->all(), [
                'nama_role' => ['required', 'unique:role,nama_role'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }
	    	$uuid = Format::generate_uuid();
            $record = new Role();
            $record->nama_role = trim($r->nama_role);
            $record->uuid = trim($uuid);
            $record->save();
	    	$respon = array('status'=>true,'message'=>'Jenis Pengguna Berhasil Ditambahkan!', '_token'=>csrf_token());
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update_role(Request $r){
        if(Access::UserCanUpdate()){
            $validator = Validator::make($r->all(), [
                'nama_role' => ['required','unique:role,nama_role,'.Role::where('uuid', $r->uuid)->first()->id],
                'uuid' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

	    	//$uuid = Format::generate_uuid();
	    	$record = array(
	    		"nama_role"=>trim($r->nama_role));
	    	Role::where('uuid', $r->uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Jenis Pengguna Berhasil Disimpan!', '_token'=>csrf_token());
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
        	return response()->json($respon);
        }
    }

    function submit_delete_role(Request $r){
        if(Access::UserCanDelete()){
            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = $r->uuid;
            $role = Role::where("uuid", $uuid)->first();
            if ($role->users()->count() > 0){
                $respon = array('status'=>false,'message'=>'Data Role Tidak Bisa Dihapus Ada Dependensi dengan Akun Pengguna!');
                return response()->json($respon);
            }
            //hapus menu pada role
            RoleMenu::where('id_role', $role->id)->delete();
            //hapus role
            Role::where('id', $role->id)->delete();          
            $respon = array('status'=>true,'message'=>'Data Role Berhasil Dihapus!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
}
