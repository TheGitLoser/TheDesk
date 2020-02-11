<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\BusinessUser;
use Illuminate\Http\Request;

class BusinessAdminController extends Controller
{
    public function index(){
        if (!userTypeAccess(['business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $userNumber = DB::select(
                        'SELECT SUM(CASE WHEN u.type="business" THEN 1 END) as businessUser,
                                SUM(CASE WHEN u.type="business admin" THEN 1 END) as businessAdmin
                                    FROM user u JOIN business_user bu ON u.id = bu.user_id
                                    WHERE (u.type = "business" OR u.type = "business admin")
                                    AND bu.business_plan_id = :businessPlanId
                                    AND u.status = 1
                                            ',
                        ["businessPlanId" => \getMyBusinessPlanId()]);
        $output['businessUserNumber'] = $userNumber[0]->businessUser;
        $output['businessAdminNumber'] = $userNumber[0]->businessAdmin;

        return view('login.businessAdmin.home')->with('output', $output);
    }
    public function viewUser(){
        if (!userTypeAccess(['business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $output = app()->call('App\Http\Controllers\UserController@getUserList', ['colleague', '', '']); 

        return view('login.businessAdmin.viewUser')->with('output', json_encode($output));
    }
    public function addUser(){
        if (!userTypeAccess(['business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        return view('login.businessAdmin.addUser');
    }
    public function removeBusinessPlanUser($unique_id){
        if (!userTypeAccess(['business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $user = User::select('id')->where('unique_id', $unique_id)->first();
        $user->status = 4;
        $user->save();

        $businessUser = BusinessUser::where('user_id', $user['id'])->first();
        $businessUser->status = 4;
        $businessUser->save();

        return back();
    }
    public function ajaxCreateBusinessUser(Request $request){
        $input = $request->only('name', 'displayId', 'email', 'password', 'passwordConfirmation', 'phone', 'DOB');

        $checkUserEmail = User::where('email', $input['email'])->count();
        $checkUserDisplayId = User::where('display_id', $input['displayId'])->count();

        if($checkUserEmail){
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'Your email has been used';
        }elseif ($checkUserDisplayId) {
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'Your Display ID has been used';
        }else{
            $uniqid = \getUniqid();
            $user = new User;
            $user->unique_id = $uniqid;
            $user->name = $input['name'];
            $user->display_id = $input['displayId'];  
            $user->email = $input['email'];
            $user->password = password_hash($input['password'], PASSWORD_DEFAULT, ['cost'=> 11]);
            $user->type = 'business';
            
            $user->save();

            $businessUser = new BusinessUser;
            $businessUser->business_plan_id = session('user.info.businessPlanId');
            $businessUser->user_id = $user->id;

            $businessUser->save();

            $output['result'] = 'true';
            $output['redirect'] = route('login.businessAdmin.viewUser');
            
        }
        return response()->json(compact('output'));

    }
}
