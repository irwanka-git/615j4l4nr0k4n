<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    //
    protected $table = 'role_menu';
    public function menus()
    {
        return $this->belongsTo('App\Models\Menu', 'id_menu', 'id')->first();
    }
}
