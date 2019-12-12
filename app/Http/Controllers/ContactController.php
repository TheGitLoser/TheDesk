<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private function getContact(){
        $myUserId = getMyId();
        $result = DB::select('SELECT u.unique_id, u.name, u.display_id 
                            FROM contact_list c JOIN user u ON c.contact_user_id = u.id
                            WHERE c.user_id = :myUserId 
                                and c.status = 1 and u.status = 1',
                        ['myUserId' => $myUserId]);
        return $result;
    }
    public function index(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $output = $this->getContact();

        return view('login.chatroom.contacts')->with('output', json_encode($output));
    }
    
    public function addContact($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $myId = getMyId();
        $contactUserId = uniqueIdToId($unique_id);
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
        return redirect()->route('login.chatroom.contacts');
    }

    public function hideContact($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        $myId = getMyId();
        $contactUserId = uniqueIdToId($unique_id);
        $contact = Contact::where('user_id', $myId) 
                            ->where('contact_user_id', $contactUserId)
                            ->first();
        $contact->status = 2;
        $contact->save();
        
        return back();
    }
    
}
