<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use DB;
use Uuid;
use Session;

class AuthMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::check()){
            return redirect()->guest('/');
        }
        $path = $request->segment(1);

        if($path=='dashboard' || $path=='' || $path=='user-guide' || $path=='akun-saya'){
            return $next($request);
        }
        $menu_user =  Auth::user()->roles()->menus()->where('url', $path)->first();
        if (!$menu_user){
            return redirect()->guest('/');
        }else{
            $crud_akses = array('update'=>$menu_user->ucu,
                            'create'=>$menu_user->ucc,
                            'delete'=>$menu_user->ucd);
            Session::put('session_access-'.$path,json_encode($crud_akses));
        }

        return $next($request);
    }
}
