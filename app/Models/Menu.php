<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    protected $table = 'menu';

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_menu', 'id_menu', 'id_role')
                    ->select('role.nama_role','role_menu.id_role')
                    ->orderby('id_role','asc')
                    ->get();
    }
}
