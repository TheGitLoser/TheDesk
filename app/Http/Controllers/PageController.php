<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// for logged in page
class PageController extends Controller
{
    public function index(Request $request)
    {
        if (userTypeAccess($request, ['indi'])) {
            return view('login.home');
        }else{
            return redirect()->route('logout.login');
        }
    }
}
