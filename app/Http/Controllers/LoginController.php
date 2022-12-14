<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Session;
use Hash;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\User;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\UserRole;

class LoginController extends Controller
{
    function page_login(){
    	return view('login');
    }	

    function ganti_password(){
        $pagetitle = "Ganti Password";
        $smalltitle = "Ubah Password User";
        return view('setting.ganti-password', compact('pagetitle','smalltitle'));
    }


    function submit_update_password(Request $r){
        $password1 = $r->password1;
        $password2 = $r->password2;
        $password3 = $r->password3;

        $user = Auth::user();
        if (Hash::check($password1, $user->password)){
            if ($password2 == $password3){
                if (strlen($password2)>=5){ 
                    $id = $user->id;
                    $record= array(
                        'password'=>Bcrypt($password2),
                        'updated_at'=>date("Y-m-d H:i:s")
                    );
                    DB::table('users')->where('id', $user->id)->update($record);
                    Session::flash('success', "Password Berhasil Diubah!");
                }else{
                    Session::flash('error', "Password Minimal 5 Karakter");
                }
            }else{
                Session::flash('error',"Konfirmasi Password Baru Tidak Sama!");
            }
        }else{
            Session::flash('error',"Password Lama Salah!");
        }

        return redirect('ganti-password');
    }

    function submit_login(Request $r){
    	$email = $r->email;
    	$password = $r->password;

    	if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $menu_user = Auth::user()->roles()->menus();
           	Session::put('menu_app',json_encode($menu_user));
            return redirect()->intended('dashboard');
        }else{
        	return redirect('login')->with('error', 'email dan Password Tidak Sesuai');
        }    
    }

    function logout(){
    	Auth::logout();
        Session::flush();
        return redirect('/');
    }

}
