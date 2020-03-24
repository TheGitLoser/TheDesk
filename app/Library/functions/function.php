<?php

    use Illuminate\Http\Request;
    
    use App\Models\User;

    // generate an unique id
    function getUniqid($table = NULL, $colName = NULL){
        return strtoupper(base_convert(uniqid(mt_rand()),16,36));
    }

    // check the user.Auth 
    function userTypeAccess($acceptTypes){
        if (session()->has('user.auth')) {
            $session = session('user.auth');   // indi, business, business admin, admin
            if (in_array($session, $acceptTypes)) {
                return true;
            }
        }
        return false;
    }

    function getMyId(){
        return session('user.info.id');
    }
    function getMyUniqid(){
        return session('user.info.uniqid');
    }
    function getMyBusinessPlanId(){
        return session('user.info.businessPlanId');
    }
    function userUniqidToId($uniqueId){
        $uniqueId = User::select('id')->where('unique_id', $uniqueId)->first();
        return $uniqueId['id'];
    }

    function initials($name){
        $words = explode(" ", $name);
        $initials = null;
        foreach ($words as $w) {
            if ($w[0] != "@")
                $initials .= $w[0];
        }
        return $initials;
    }
