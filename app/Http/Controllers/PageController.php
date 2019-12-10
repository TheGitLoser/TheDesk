<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// for logged in page
class PageController extends Controller
{
    public function index(Request $request)
    {
        if (!userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.home');
        }
    }
    public function profile(Request $request)
    {
        if (!userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.account.profile');
        }
    }
    public function editPassword(Request $request)
    {
        if (!userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.account.editPassword');
        }
    }
}
