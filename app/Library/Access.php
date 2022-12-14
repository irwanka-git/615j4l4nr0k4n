<?php

namespace App\Library;
use Request;
class Access
{
    static function UserCanUpdate(){
        $path = Request::segment(1);
        $session_akses = session('session_access'.'-'.$path);
        
        if($session_akses){
            $session_akses = json_decode($session_akses);
            if((int)$session_akses->update>=1) return true;
        }
        return false;	
    }

    static function isMobileDevice() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    //user_can_create
    static function UserCanCreate(){
        $path = Request::segment(1);
        $session_akses = session('session_access'.'-'.$path);
        if($session_akses){
            $session_akses = json_decode($session_akses);
            if((int)$session_akses->create>=1) return true;
        }
        return false;
    }
    
    //user_can_delete
    static function UserCanDelete(){
        $path = Request::segment(1);
        $session_akses = session('session_access'.'-'.$path);
        if($session_akses){
            $session_akses = json_decode($session_akses);
            if((int)$session_akses->delete>=1) return true;
        }
        return false;
    }
}
