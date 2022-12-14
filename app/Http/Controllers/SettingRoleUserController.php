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
use Hash;

use App\Models\Role;
use App\Models\UserRole;
use App\User;
use App\Models\TitikKerusakan;

class SettingRoleUserController extends Controller
{
    //
    function index($uuid){
        $role = Role::where('uuid',$uuid)->first();
        if(!$role){
            return redirect('404');
        }
    	$pagetitle = "Pengaturan Akun ".$role->nama_role;
    	$smalltitle = "Fitur Ini Digunkan Untuk Mengelola Akun - ".$role->nama_role;
    	return view('setting.role-user', compact('pagetitle','smalltitle', 'role'));
    }

    function datatable($uuid){
        $role = Role::where('uuid',$uuid)->first();
        $filter = " user_role.id_role =  ".$role->id;
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter .= "  and ( lower(name) like '%$keyword%' or lower(email) like '%$keyword%' ) ";
            }   
        }
        $query  =   User::join('user_role', 'users.id','=', 'user_role.id_user')
                        ->select('users.uuid','users.name','users.email','users.phone')
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
                        if ($action==" "){$action='<button class="btn btn-outline-secondary btn-sm">
                        <i class="la la-lock"></i></button>'; }
                    return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_data($uuid){
        $data = User::where('uuid', $uuid)->first();
        if($data){
            $respon = array('status'=>true,'data'=>$data, 
                'informasi'=>'Nama Pengguna: '. $data->name);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
        if(Access::UserCanCreate()){
            $valid_role = Role::pluck('id')->toArray();
            $validator = Validator::make($r->all(), [
                'email' => ['required', 'email','unique:users,email'],
                'name' => ['required', 'min:3'],
                'password' => ['required', 'min:5'],
                'phone' => ['required', 'min:5'],
                'id_role' => ['required', Rule::in($valid_role),],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

	    	$uuid = Format::generate_uuid();
	    	$id_role = trim($r->id_role);
            $password = Hash::make(trim($r->password));

	    	$record = array(
	    		"name"=>trim($r->name), 
	    		"email"=>trim($r->email), 
	    		"phone"=>trim($r->phone), 
                "password"=>$password,
	    		"uuid"=>$uuid);

	    	$user = User::Create($record);
            if($user){
                $uuid = Format::generate_uuid();
                $user_role = new UserRole;
                $user_role->id_user = $user->id;
                $user_role->id_role = $id_role;
                $user_role->uuid = $uuid;
                $user_role->save();
            }
	    	$respon = array('status'=>true,'message'=>'Akun Pengguna Berhasil Ditambahkan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update(Request $r){
        if(Access::UserCanUpdate()){
            $user = User::where('uuid', $r->uuid)->first();

            $change_password = $r->change_password;
            $password_validasi = [];
            if($change_password){
                $password_validasi = ['required', 'min:5'];
            }

            $validator = Validator::make($r->all(), [
                'email' => ['required', 'email','unique:users,email,'.$user->id],
                'name' => ['required', 'min:3'],
                'password' => $password_validasi,
                'phone' => ['required', 'min:5'],
                'uuid' => ['required'],
            ]);

            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }            

            $record = array(
                "name"=>trim($r->name), 
                "email"=>trim($r->email), 
                "phone"=>trim($r->phone));

            $user->update($record);

            if ($change_password){
                $password = Hash::make(trim($r->password));
                $user->update(['password'=>$password]);
            }

            $respon = array('status'=>true,'message'=>'Akun Pengguna Berhasil Diperbarui!');
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

            $user = User::where('uuid', $r->uuid)->first();
            if($user){
                $dependen = TitikKerusakan::where('create_by', $user->id)->whereOr('update_by', $user->id)->count();
                if($dependen){
                    $respon = array('status'=>false,
                            'message'=>'Akun Tidak Bisa Dihapus, Ada Dependensi dengan Data Titik Kerusakan!');
                    return response()->json($respon);
                }else{
                    $user->delete();
                    $respon = array('status'=>true,'message'=>'Akun Pengguna Berhasil Dihapus!');
                    return response()->json($respon);
                }
            }else{
                $respon = array('status'=>false,'message'=>'Akun Pengguna Tidak Ditemukan!');
                return response()->json($respon);
            }    

        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }
}
