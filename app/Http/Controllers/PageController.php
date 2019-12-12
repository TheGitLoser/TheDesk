<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// for logged in page
class PageController extends Controller
{
    public function index()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.home');
        }
    }
    public function profile()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.account.profile');
        }
    }
    public function editPassword()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.account.editPassword');
        }
    }
}
