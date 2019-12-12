<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Chatroom;
use App\Models\ChatroomUser;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatroomController extends Controller
{
    private static function getChatroom(){
        $myChatroom = DB::select('SELECT c.unique_id, c.name, c.update_at, u2.name
                                FROM chatroom c JOIN chatroom_user cu 
                                    ON c.id = cu.chatroom_id AND cu.user_id = :myUserId
                                    JOIN chatroom_user cu2 ON c.id = cu2.chatroom_id AND cu2.user_id <> :myUserId2
                                    JOIN user u2 ON cu2.user_id = u2.id
                                WHERE c.status = 1 and cu.status = 1
                                GROUP BY c.unique_id, c.name, c.update_at, u2.name
                                ORDER BY c.update_at DESC',
                            ['myUserId' => \getMyId(), 'myUserId2' => \getMyId()]);
        foreach ($myChatroom as $item) {
            $item->initials = \initials($item->name);
        }
        return $myChatroom;
    }

    // call from view
    static function getChatroomList(){
        return json_encode(ChatroomController::getChatroom());
    }
    
    public function addToChat($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        // create new chatroom
        $chatroom = new Chatroom;
dd($chatroom);
        $chatroom->unique_id = \getUniqid();
        $chatroom->save();
        
        // add user into chatroom
        $addMe = new ChatroomUser;
        $addMe->chatroom_id = $chatroom->id;
        $addMe->user_id = \getMyId();
        $addMe->save();
        
        $contactUserId = uniqueIdToId($unique_id);
        $addContactUser = new ChatroomUser;
        $addContactUser->chatroom_id = $chatroom->id;
        $addContactUser->user_id = $contactUserId;
        $addContactUser->save();
        
        
        return back();
    }

    public function chat($chatroomUniqid){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }


        // check permission (TBC)

        $chatroom = Chatroom::where('unique_id', $chatroomUniqid)->first()->toArray();
        $chatroomId = $chatroom['id'];
        $chatroomUser = DB::select('SELECT cu.create_at as chatroomUserCreateAt, cu.update_at as chatroomUserUpdateAt, 
                                    u.unique_id, u.name, u.display_id, u.email,
                                    u.type, u.profile, u.profile_picture, u.status
                                    FROM chatroom_user cu JOIN user u ON cu.user_id = u.id
                                    WHERE cu.chatroom_id = :chatroomId AND cu.status = 1',
                                    ["chatroomId" => $chatroomId]);
        $message = DB::select('SELECT m.unique_id as messageUniqid, m.content as message, m.create_at as messageCreateAt,
                                    m.update_at as messageUpdateAt,u.unique_id as userUniqid, u.type as userType
                                    FROM message m JOIN user u ON m.user_id = u.id
                                    WHERE chatroom_id = :chatroomId',
                                    ["chatroomId" => $chatroomId]);
        
        $myUniqid = \getMyUniqid();
        $myUserType = session('user.auth');
        if(empty($chatroom['name'])){
            foreach ($chatroomUser as $user) {
                if($user->unique_id != $myUniqid){
                    $chatroom['name'] = $user->name;
                    break;
                }
            }
        }
        foreach ($message as $item) {
            if($item->userUniqid == $myUniqid){
                $item->messageType = 'myMessage';
            }elseif($item->userType == $myUserType){
                $item->messageType = 'sameType';
            }else{
                $item->messageType = 'oppositeType';
            }
        }
        unset($chatroom[0]['id']);
        return view('login.chatroom.chat')->with('chatroomUniqid', $chatroom['unique_id'])
                                            ->with('chatroom', json_encode($chatroom))
                                            ->with('chatroomUser', json_encode($chatroomUser))
                                            ->with('message', json_encode($message));

    }
}
