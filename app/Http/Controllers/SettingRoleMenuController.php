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
use App\Models\RoleMenu;
use App\Models\Menu;



class SettingRoleMenuController extends Controller
{
    //
     function index($uuid){
        $role = Role::where('uuid',$uuid)->first();
        if(!$role){
            return redirect('404');
        }
        $pagetitle = "Pengaturan Akses Menu ".$role->nama_role;
        $smalltitle = "Fitur Ini Digunkan Untuk Mengelola Akses Menu ".$role->nama_role;
        $list_menu = Menu::select('id as value', 'nama_menu as text')->orderby('urutan')->get();
        return view('setting.role-menu', compact('pagetitle','smalltitle', 'role','list_menu'));
    }

    function datatable($uuid){
        $role = Role::where('uuid',$uuid)->first();
        $filter = " role_menu.id_role =  ".$role->id;
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter .= "  and ( lower(nama_menu) like '%$keyword%') ";
            }   
        }
        $query  =   Menu::join('role_menu', 'menu.id','=', 'role_menu.id_menu')
                        ->select('role_menu.uuid','nama_menu','urutan','role_menu.ucc', 'role_menu.ucu', 'role_menu.ucd')
                        ->whereRaw($filter)
                        ->orderby('urutan');

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
            ->editColumn('ucc', function($q){
                if ($q->ucc==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucc==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->editColumn('ucu', function($q){
                if ($q->ucu==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucu==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->editColumn('ucd', function($q){
                if ($q->ucd==0) { return '<span class="badge bg-danger">No</span>';}
                if ($q->ucd==1) { return '<span class="badge bg-success">Yes</span>';}
            })
            ->addIndexColumn()
            ->rawColumns(['action','ucc','ucu','ucd'])
            ->make(true);
    }

    function get_data($uuid){
        $data = RoleMenu::where('uuid', $uuid)->first();
        if($data){
            $menu = $data->menus();
            $respon = array('status'=>true,'data'=>$data, 'menu'=>$menu,
                'informasi'=>'Nama Menu: ' . $menu->nama_menu);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }


    function submit_insert(Request $r){
        if(Access::UserCanCreate()){
            $valid_role = Role::pluck('id')->toArray();
            $validator = Validator::make($r->all(), [
                'id_menu' => ['required', 
                    'unique:role_menu,id_menu,null,null,id_role,'.$r->id_role
                ],
                'id_role' => ['required', Rule::in($valid_role),],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $uuid = Format::generate_uuid();
            $id_role = (int)($r->id_role);

            $record = new RoleMenu();
            $record->id_role = $id_role;
            $record->id_menu = (int)($r->id_menu);
            $record->ucc = (int)($r->ucc);
            $record->ucu = (int)($r->ucu);
            $record->ucd = (int)($r->ucd);
            $record->uuid = $uuid;
            $record->save();

            $respon = array('status'=>true,'message'=>'Akses Menu Berhasil Ditambahkan!');
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }


    function submit_update(Request $r){
        if(Access::UserCanCreate()){

            $validator = Validator::make($r->all(), [
                'uuid' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

            $role_menu = RoleMenu::where('uuid', $r->uuid)->update(
                                                            [   
                                                                'ucc' => (int)$r->ucc,
                                                                'ucu' => (int)$r->ucu,
                                                                'ucd' => (int)$r->ucd
                                                            ]
                                                            );
            $respon = array('status'=>true,'message'=>'Akses Menu Berhasil Diperbarui!');
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

             RoleMenu::where('uuid', $r->uuid)->delete();
             $respon = array('status'=>true,'message'=>'Hak Akses Menu Berhasil Dihapus!');
                    return response()->json($respon);  

        }else{
            $respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
            return response()->json($respon);
        }
    }

}
