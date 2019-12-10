<?php

    use Illuminate\Http\Request;
    
    use App\Models\User;

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

    function getMyId(Request $request){
        return $request->session()->get('user.info.id');
    }
    
    function uniqueIdToId($uniqueId){
        $uniqueId = User::select('id')->where('unique_id', $uniqueId)->get()->toArray();
        return $uniqueId[0]['id'];
    }
