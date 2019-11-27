<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
       //
       return view('logout.login');
   }
   /**
    * reset password after sending email
    *
    * @return \Illuminate\Http\Response
    */
    public function resetPassword()
    {
        //
        return view('logout.resetPassword');
    }
}
