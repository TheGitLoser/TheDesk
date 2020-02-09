<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Chatroom;
use App\Models\ChatroomUser;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatroomController extends Controller
{
    // return user's chatroom list
    private static function getChatroom(){
        $myChatroom = DB::select('SELECT c.unique_id, c.name as chatroomName, c.update_at, u2.name as userName, u2.display_id as displayId
                                FROM chatroom c
                                    JOIN chatroom_user cu ON c.id = cu.chatroom_id AND cu.user_id = :myUserId
                                    JOIN chatroom_user cu2 ON c.id = cu2.chatroom_id AND cu2.user_id <> :myUserId2
                                    JOIN user u2 ON cu2.user_id = u2.id
                                WHERE c.status = 1 and cu.status = 1 and cu.status = 1 and u2.status = 1
                                GROUP BY c.unique_id, c.name, c.update_at
                                ORDER BY c.update_at DESC',
                            ['myUserId' => \getMyId(), 'myUserId2' => \getMyId()]);
        foreach ($myChatroom as $item) {
            // if chatroom Name = null, use participate name
            if(empty($item->chatroomName)){
                $item->name = $item->userName;
            }else{
                $item->name = $item->chatroomName;
            }
            $item->initials = \initials($item->userName);
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
        $myId = \getMyId();
        $contactUserId = \uniqueIdToId($unique_id);

        $checkChatroom = DB::select('SELECT cu.chatroom_id FROM chatroom_user cu 
                                    JOIN chatroom_user cu2 ON cu.chatroom_id = cu2.chatroom_id 
                                    WHERE cu.user_id = :myId AND cu2.user_id = :contactUserId',
                                    ['myId' => $myId, 'contactUserId' => $contactUserId]);
        
        if(count($checkChatroom)){
            // already exists
            $chatroomUniqid = Chatroom::select('unique_id')->where('id', $checkChatroom[0]->chatroom_id)->first();
            $chatroomUniqid = $chatroomUniqid->unique_id;
        }else{
            // check is it on Contact list
            app()->call('App\Http\Controllers\ContactController@checkContactExists', [$unique_id]); 

            // create new chatroom
            $chatroom = new Chatroom;
            $chatroom->unique_id = \getUniqid();
            $chatroom->save();
            
            // add user into chatroom
            $addMe = new ChatroomUser;
            $addMe->chatroom_id = $chatroom->id;
            $addMe->user_id = $myId;
            $addMe->save();
            
            $addContactUser = new ChatroomUser;
            $addContactUser->chatroom_id = $chatroom->id;
            $addContactUser->user_id = $contactUserId;
            $addContactUser->save();
            $chatroomUniqid = $chatroom->unique_id;
        }
                
        return redirect()->route('login.chatroom.chat', ['uniqueId'=> $chatroomUniqid]);
    }
    
    // in chatroom
    public function chat($chatroomUniqid){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        // get chatroom id
        $chatroom = Chatroom::where('unique_id', $chatroomUniqid)->first();
        if($chatroom){
            $chatroomId = $chatroom->id;
        }else{
            return redirect()->route('login.chatroom.contacts');
        }

        // check permission, whether you are in this chatroom
        $checkPermission = ChatroomUser::where('chatroom_id', $chatroomId)
                    -> where('user_id', \getMyId())
                    -> where('status', 1)
                    ->count();
        if($checkPermission == 0){
            return redirect()->route('login.chatroom.contacts');
        }

        $chatroomUser = DB::select('SELECT cu.create_at as chatroomUserCreateAt, cu.update_at as chatroomUserUpdateAt, 
                                    u.unique_id, u.name, u.display_id, u.email,
                                    u.type, u.profile, u.profile_picture, u.status
                                    FROM chatroom_user cu JOIN user u ON cu.user_id = u.id AND u.status = 1
                                    WHERE cu.chatroom_id = :chatroomId AND cu.status = 1',
                                    ["chatroomId" => $chatroomId]);
        $message = DB::select('SELECT m.unique_id as messageUniqid, m.content as message, m.create_at as messageCreateAt,
                                    m.update_at as messageUpdateAt,u.unique_id as userUniqid, u.type as userType
                                    FROM message m JOIN user u ON m.user_id = u.id AND u.status = 1
                                    WHERE chatroom_id = :chatroomId AND m.status = 1',
                                    ["chatroomId" => $chatroomId]);
        
        $myUniqid = \getMyUniqid();
        $myUserType = session('user.auth');
        if(empty($chatroom->name)){
            foreach ($chatroomUser as $user) {
                if($user->unique_id != $myUniqid){
                    $chatroom->name = $user->name;
                    break;
                }
            }
        }
        foreach ($chatroomUser as $item) {
            $item->initials = \initials($item->name);
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
        unset($chatroom->id);

        // ws connection details
        $wsConnection = env('WS_Protocol') ."://". env('WS_URL');
        
        return view('login.chatroom.chat')->with('chatroomUniqid', $chatroom['unique_id'])
                                            ->with('wsConnection', $wsConnection)
                                            ->with('chatroom', json_encode($chatroom))
                                            ->with('chatroomUser', json_encode($chatroomUser))
                                            ->with('message', json_encode($message));

    }
    public function ajaxNewMessage(Request $request){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('chatroomUniqid', 'message');

        $chatroom = Chatroom::select('id')->where('unique_id', $input['chatroomUniqid'])->first();
        $chatroom->touch();

        $message = new Message;
        $message->unique_id = \getUniqid();
        $message->user_id = \getMyId();
        $message->chatroom_id = $chatroom->id;
        $message->content = $input['message'];
        $message->save();
        
        $output['messageUniqid'] = $message['unique_id'];
        $output['messageCreateAt'] = $message['create_at']->format('Y-m-d H:i:s');
        return response()->json(compact('output'));
    }
}
