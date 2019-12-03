<?php

    use Illuminate\Http\Request;
    // generate an unique id
    function getUniqid($table = NULL, $colName = NULL){
        return strtoupper(base_convert(uniqid(mt_rand()),16,36));
    }

    // check the user.Auth 
    function userTypeAccess(Request $request, $acceptTypes){
        if ($request->session()->has('user.auth')) {
            $session = $request->session()->get('user.auth');   // indi, business, business admin, admin
            if (in_array($session, $acceptTypes)) {
                return true;
            }
        }
        return false;
    }
