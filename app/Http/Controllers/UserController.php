<?php

namespace App\Http\Controllers;

use DB;
use App\Models\BusinessPlan;
use App\Models\User;
use App\Models\NewRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function getUserList($searchType, $name, $displayId){
        if ($searchType == 'indi') {
            $user = User::select('unique_id', 'name', 'display_id')
            -> where('type', $searchType)
            -> where('name', 'LIKE', "%{$name}%")
            -> where('display_id', 'LIKE', "%{$displayId}%")
            -> where('status', '1')
            -> where('id', '<>', \getMyId())
            ->get();
        }elseif($searchType == 'business'){
            $user = BusinessPlan::select('unique_id', 'name')
                    -> where('name', 'LIKE', "%{$name}%")
                    -> where('status', '1')
                    ->get();
        }elseif($searchType == 'colleague'){
            // myCompany
            $user = DB::select(
                'SELECT u.unique_id, u.name, u.display_id, bu.business_plan_id
                                        FROM user u JOIN business_user bu ON u.id = bu.user_id
                                        WHERE bu.business_plan_id = :businessPlanId
                                        AND u.name LIKE :name
                                        AND u.display_id LIKE :displayId
                                        AND u.id != :myId
                                        AND u.status = 1
                                                ',
                ["businessPlanId" => \getMyBusinessPlanId(), "name" => "%".$name."%", "displayId" => "%".$displayId."%", "myId" => \getMyId()]
            );
        }else{  // exact
            $user = User::select('unique_id', 'name', 'display_id')
                ->where(function($q)use($name, $displayId) {
                    $q->where('name', $name)
                    ->orWhere('display_id', $displayId);
                })
                -> where('status', '1')
                -> where('id', '<>', \getMyId())
                ->get();
        }
        return $user;
    }

    public function index()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }elseif(userTypeAccess(['indi'])){
            $newRequest = 0;
        }else{
            $newRequest = NewRequest::where('business_plan_id', \getMyBusinessPlanId())->where('status', 2)->count();    // status 2 = Waiting to response
        }
        
        return view('login.home')->with('newRequest', $newRequest);
    }

    public function discoverUser()
    {
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        if(session('user.auth') == 'indi'){
            $searchType = 'indi';
        }else{
            $searchType = 'colleague';
        }

        $output = $this->getUserList($searchType, '', '');
        
        return view('login.chatroom.discover')->with('output', json_encode($output))
                                              ->with('searchType', $searchType);
    }

    public function profile(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $user = User::where('id', \getMyId())->first();
        $user->DOB = date_format(date_create($user->DOB),"Y-m-d");
        return view('login.account.profile')->with('profile', $user);
    }

    public function editPassword(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }else{
            return view('login.account.editPassword');
        }
    }

    public function ajaxDiscover(Request $request){
        $input = $request->only('name', 'id', 'searchType');
        
        $output = $this->getUserList($input['searchType'], $input['name'], $input['id']);
        return response()->json(compact('output'));
    }
    
    public function ajaxUpdateProfile(Request $request){
        $input = $request->only('name', 'displayId', 'phone', 'DOB', 'profile');

        $checkUserDisplayId = User::where('display_id', $input['displayId'])->where('id', '!=', \getMyId())->count();
        if($checkUserDisplayId) {
            $output['result'] = 'false';
            $output['message'] = 'Your Display ID has been used';
            return response()->json(compact('output'));
        }

        $user = User::where('id', \getMyId())->first();
        $user->name = $input['name'];
        $user->display_id = $input['displayId'];
        $user->phone = $input['phone'];
        $user->DOB = $input['DOB'];
        $user->profile = $input['profile'];
        $user->save();
        $output['result'] = "true";
        $output['redirect'] = route('login.home');
        return response()->json(compact('output'));
    }

    public function ajaxEditPassword(Request $request){
        $input = $request->only('currentPassword', 'newPassword', 'passwordConfirmation');
        if($input['newPassword'] != $input['passwordConfirmation']){
            $output['result'] = "false";
            $output['message'] = "New password and Confirm new password must be the same";
        }else{
            $user = User::where('id', \getMyId())->first();
            if(password_verify($input['currentPassword'], $user['password'])){
                $user->password = password_hash($input['newPassword'], PASSWORD_DEFAULT, ['cost'=> 11]);
                $user->save();
                $output['result'] = "true";
                $output['redirect'] = route('login.home');
            }else{
                $output['result'] = "false";
                $output['message'] = "Your password is incorrect";
            } 
        }
        return response()->json(compact('output'));
    }
}
