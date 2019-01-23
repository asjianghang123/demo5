<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Config;
use App\Http\Requests;

class TimeDimensionController extends Controller
{
   
   public function getTimeDimension(){
        $arr = config('option.'.env('CITY'))['time_dimension'];
        return json_encode($arr);
  
   }
    
}
