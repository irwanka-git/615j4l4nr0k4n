<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    protected $table = 'role';

    public function menus()
    {
        return $this->belongsToMany('App\Models\Menu', 'role_menu', 'id_role', 'id_menu')
                    ->select('role_menu.ucc','role_menu.ucu','role_menu.ucd', 'menu.nama_menu','menu.url', 'menu.id')
                    ->orderby('urutan','asc')
                    ->get();
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_role', 'id_user', 'id_user')
                    ->select('users.id', 'users.name','users.email')
                    ->orderby('users.id','asc')
                    ->get();
    }

}
