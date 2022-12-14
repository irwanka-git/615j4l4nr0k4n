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

use App\Models\Menu;


class SettingMenuController extends Controller
{
     
    //######################### SETTING MENU #####################################
    function index(){
    	$pagetitle = "Pengaturan Menu";
    	$smalltitle = "Pengaturan Menu Aplikasi";
    	return view('setting.menu', compact('pagetitle','smalltitle'));
    }

    function datatable(Request $r){
        
        $filter = "id > 0 ";
        if (request()->has('search')) {
            $search = request('search');
            $keyword = $search['value'];
            if(strlen($keyword)>=3){
                $keyword = strtolower($keyword);
                $filter = " id > 0 and ( lower(nama_menu) like '%$keyword%' ) ";
            }   
        }
        $query  =   Menu::query()->whereRaw($filter);

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
            ->rawColumns(['action','label'])
            ->make(true);
    }

    function get_data($uuid){
    	$menu = Menu::where('uuid', $uuid)->first();
        if($menu){
            $respon = array('status'=>true,'data'=>$menu, 
            	'informasi'=>'Nama Menu: '. $menu->nama_menu);
            return response()->json($respon);
        }
        $respon = array('status'=>false,'message'=>'Data Tidak Ditemukan');
        return response()->json($respon);
    }

    function submit_insert(Request $r){
    	if(Access::UserCanCreate()){
            $validator = Validator::make($r->all(), [
                'nama_menu' => ['required', 'unique:menu,nama_menu'],
                'urutan' => ['required', 'unique:menu,urutan'],
                'url' => ['required', 'unique:menu,url'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

	    	$uuid = Format::generate_uuid();
            $record = new Menu();
            $record->nama_menu =  trim($r->nama_menu);
            $record->url =  trim($r->url);
            $record->urutan =  trim($r->urutan);
            $record->uuid =  trim($uuid);
	    	$record->save();
	    	$respon = array('status'=>true,'message'=>'Menu Berhasil Ditambahkan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Aktivitas Sistem Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_update(Request $r){
    	if(Access::UserCanUpdate()){
	    	$uuid = $r->uuid;
            $validator = Validator::make($r->all(), [
                'nama_menu' => ['required','unique:menu,nama_menu,'.Menu::where('uuid', $r->uuid)->first()->id],
                'urutan' => ['required'],
                'uuid' => ['required'],
                'url' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => false,"message" => $validator->errors()->all()], 200);
            }

	    	$record = array( 
	    		"nama_menu"=>trim($r->nama_menu), 
	    		"url"=>trim($r->url),
	    		"urutan"=>$r->urutan
	    	);

	    	Menu::where('uuid', $uuid)->update($record);
	    	$respon = array('status'=>true,'message'=>'Data Menu Berhasil Disimpan!');
        	return response()->json($respon);
    	}else{
    		$respon = array('status'=>false,'message'=>'Akses Ditolak!');
        	return response()->json($respon);
    	}
    }

    function submit_delete(Request $r){
        if(Access::UserCanDelete()){
            $uuid = $r->uuid;
            $menu = Menu::where("uuid", $uuid)->first();
            if($menu->roles()->count()==0){
                Menu::where('uuid', $uuid)->delete();
                $respon = array('status'=>true,'message'=>'Data Menu Berhasil Dihapus!');
            }else{
                $respon = array('status'=>false,'message'=>'Data Menu Tidak Bisa Dihapus Ada Dependensi dengan Role Pengguna!');
                return response()->json($respon);
            }            
            return response()->json($respon);
        }else{
            $respon = array('status'=>false,'message'=>'Akses Ditolak!');
            return response()->json($respon);
        }
    }
}
