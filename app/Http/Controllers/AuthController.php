<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function home(Request $request)
    {
        if (userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('login.home');
        }else{
            return view('logout.home');
        }
    }
    public function index(Request $request)
    {
        if (userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('login.home');
        }else{
            return view('logout.login');
        }
    }

    public function register(Request $request)
    {
        if (userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('login.home');
        }else{
            return view('logout.register');
        }
    }
    public function forgotPassword(Request $request)
    {
        if (userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('login.home');
        }else{
            return view('logout.forgotPassword');
        }
    }
    public function resetPassword(Request $request)
    {
        if (userTypeAccess($request, ['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('login.home');
        }else{
            return view('logout.resetPassword');
        }
    }

    public function ajaxLogin(Request $request)
    {
        $input = $request->only('email', 'password', 'remember');
        
        $user = User::select('unique_id', 'type', 'password', 'status', 'name', 'display_name')
                    -> where('email', $input['email'])
                    -> where('status', '1')
                    ->get();
        $count = $user->count();
        $user = $user->toArray();

        if($count == 1 && password_verify($input['password'], $user[0]['password'])){
            $user = $user[0];
            $userInfo = array('unique_id' => $user['unique_id'], 'name' => $user['name'], 'display_name' => $user['display_name']);
            $request->session()->put('user.auth', 'indi');
            $request->session()->put('user.info', $userInfo);

            $output['result'] = "true";
            $output['redirect'] = route('login.home');
        }else{
            $output['result'] = "false";
            $output['message'] = "Your email or password is incorrect";
        } 
        return response()->json(compact('output'));
    }

    public function ajaxRegister(Request $request){
        $input = $request->only('name', 'email', 'password', 'passwordConfirmation', 'phone', 'DOB');

        $checkUser = User::where('email', $input['email'])->count();
        if($checkUser){
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'Your email has been used';
        }else{
            $uniqid = getUniqid();
            $user = new User;
            $user->unique_id = $uniqid;
            $user->name = $input['name'];
            $user->display_name = $input['name'];
            $user->email = $input['email'];
            $user->password = password_hash($input['password'], PASSWORD_DEFAULT, ['cost'=> 11]);
            $user->type = 'indi';
            $user->phone = $input['phone'];
            $user->DOB = $input['DOB'];
            
            $user->save();

            $userInfo = array('unique_id' => $user['unique_id'], 'name' => $user['name'], 'display_name' => $user['display_name']);
            $request->session()->put('user.auth', 'indi');
            $request->session()->put('user.info', $userInfo);

            $output['result'] = 'true';
            $output['redirect'] = route('login.home');
            
        }
        return response()->json(compact('output'));
    }

    public function show(Auth $auth)
    {
        //
    }

    public function edit(Auth $auth)
    {
        //
    }

    public function update(Request $request, Auth $auth)
    {
        //
    }

    public function destroy(Auth $auth)
    {
        //
    }
}
