<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function discoverUser()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $user = User::select('unique_id', 'name', 'display_id')
                    -> where('type', 'indi')
                    -> where('status', '1')
                    -> where('id', '<>', \getMyId())
                    ->get();
        $output = $user;

        return view('login.chatroom.discover')->with('output', json_encode($output));
    }
}
