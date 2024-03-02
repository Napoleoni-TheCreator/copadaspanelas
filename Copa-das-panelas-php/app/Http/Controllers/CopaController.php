<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CopaController extends Controller
{
    
    public function shw_live()
    {
        return(view('live'));
    }
}
