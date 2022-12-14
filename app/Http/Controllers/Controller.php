<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
        
    function base_route($path=""){
        $route = request()->segment(1);
        if($path!=""){
            return url($route."/".$path);
        }
        return url($route);
    }
}
