<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App;

class StoreController extends Controller
{
    public function initstore()
    {
       $user = null;

       if (Auth::check()) {
          $user = Auth::user();
       }

       return ['lang' => App::getLocale(), 'user' => $user, 'baseurl' => url ('/'), 'servertime' => time()];
    }
}
