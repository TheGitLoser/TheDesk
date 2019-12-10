<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request){
        $myId = getMyId($request);
        $result = DB::select('SELECT u.unique_id, u.name, u.display_id 
                            FROM contact_list c JOIN user u ON c.contact_user_id = u.id
                            WHERE c.user_id = :user_id 
                                and c.status = 1 and u.status = 1',
                        ['user_id' => $myId]);
        $output = $result;
        return view('login.chatroom.contacts')->with('output', json_encode($output));
    }
    
    public function addContact(Request $request, $unique_id){
        $myId = getMyId($request);
        $contactUserId = uniqueIdToId($unique_id);
        $contact = Contact::where('user_id', $myId) ->where('contact_user_id', $contactUserId)->get();
        
        $count = $contact->count();

        if($count){
            // already in contacts
            $contact[0]->touch();
        }else{
            // add to contact
            $newContact = new Contact;
            $newContact->user_id = $myId;
            $newContact->contact_user_id = $contactUserId;
            $newContact->save();

        }
        return redirect()->route('login.chatroom.contacts');
    }
    
}
