<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function discoverUser(Request $request)
    {
        $user = User::select('unique_id', 'name', 'display_id')
                    -> where('type', 'indi')
                    -> where('status', '1')
                    ->get();
        $output = $user->toArray();

        return view('login.chatroom.discover')->with('output', json_encode($output));
    }
}
