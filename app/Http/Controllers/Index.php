<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Index extends Controller
{
    public function index(Request $request)
    {
        //die("hello");
        $domainInfo = $request->session()->get('domainInfo');
        //dd($domainInfo);
        # get the resolved domainInfo, if any
        if (empty($domainInfo) || $domainInfo->getService() === null) {
            if (Auth::check()) {
                return redirect(route('dashboard'));
            }
            return redirect(route('login'));
        }
        /*if (Auth::check()) {
            return redirect(route('dashboard'));
        }*/
    }
}
