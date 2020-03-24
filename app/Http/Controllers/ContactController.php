<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function getContact($searchType, $name, $displayId){
        $myUserId = getMyId();
        if ($searchType == 'indi') {
            $result = DB::select(
                "SELECT u.unique_id, u.name, u.display_id 
                                FROM contact_list c JOIN user u ON c.contact_user_id = u.id
                                WHERE c.user_id = :myUserId 
                                    and u.type = 'indi' and u.name LIKE :name and u.display_id LIKE :display_id
                                    and c.status = 1 and u.status = 1",
                ['myUserId' => $myUserId, 'name' => "%{$name}%", 'display_id' => "%{$displayId}%"]
            );
        }elseif($searchType == 'business'){
            $result = DB::select(
                "SELECT u.unique_id, u.name, u.display_id 
                                FROM contact_list c JOIN user u ON c.contact_user_id = u.id AND u.status = 1
                                JOIN business_user bu ON u.id = bu.user_id AND bu.status = 1
                                WHERE c.user_id = :myUserId 
                                    AND bu.business_plan_id != :businessPlanId
                                    and u.type LIKE 'business%' and u.name LIKE :name and u.display_id LIKE :display_id
                                    and c.status = 1",
                ["businessPlanId" => \getMyBusinessPlanId(), 
                'myUserId' => $myUserId, 'name' => "%{$name}%", 'display_id' => "%{$displayId}%"]
            );
        }else{  // colleague
            $result = DB::select(
                "SELECT u.unique_id, u.name, u.display_id 
                                FROM contact_list c JOIN user u ON c.contact_user_id = u.id
                                JOIN business_user bu ON u.id = bu.user_id
                                WHERE c.user_id = :myUserId 
                                    AND bu.business_plan_id = :businessPlanId
                                    and u.type LIKE 'business%' and u.name LIKE :name and u.display_id LIKE :display_id
                                    and c.status = 1 and u.status = 1",
                ["businessPlanId" => \getMyBusinessPlanId(), 
                'myUserId' => $myUserId, 'name' => "%{$name}%", 'display_id' => "%{$displayId}%"]
            );

        }
        return $result;
    }
    // shared form this@addContact & Chatroom@addToChat
    function checkContactExists($unique_id){
        $myId = getMyId();
        $contactUserId = \userUniqidToId($unique_id);
        $contact = Contact::where('user_id', $myId) 
                            ->where('contact_user_id', $contactUserId)
                            ->get();
        $count = $contact->count();

        if($count){
            // already in contacts
            $contact[0]->status = '1';
            $contact[0]->save();
        }else{
            // add to contact
            $newContact = new Contact;
            $newContact->user_id = $myId;
            $newContact->contact_user_id = $contactUserId;
            $newContact->save();

        }
    }
    public function index(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        if (session('user.auth') == 'indi') {
            $searchType = 'indi';
        }else{
            $searchType = 'colleague';
        }

        $output = $this->getContact($searchType, '', '');
        return view('login.chatroom.contacts')->with('output', json_encode($output))
                                                ->with('searchType', $searchType);
    }
    
    public function backendAddContact($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $this->checkContactExists($unique_id);
        
        return redirect()->route('login.chatroom.contacts');
    }

    public function backendHideContact($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        $myId = getMyId();
        $contactUserId = \userUniqidToId($unique_id);
        $contact = Contact::where('user_id', $myId) 
                            ->where('contact_user_id', $contactUserId)
                            ->first();
        $contact->status = 2;
        $contact->save();
        
        return back();
    }
    public function ajaxSearchContact(Request $request){
        $input = $request->only('name', 'id', 'searchType');
        
        $output = $this->getContact($input['searchType'], $input['name'], $input['id']);
        return response()->json(compact('output'));
    }
}
