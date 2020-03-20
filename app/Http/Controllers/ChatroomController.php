<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Chatroom;
use App\Models\ChatroomUser;
use App\Models\Message;
use App\Models\MessageSeen;
use Illuminate\Http\Request;

class ChatroomController extends Controller
{
    // return user's chatroom list
    private static function getChatroom(){
        $myChatroom = DB::select('SELECT c.unique_id, c.name as chatroomName, c.update_at, u2.name as userName, u2.display_id as displayId, 
                                        count(msg.id) - count(msgSeen.id) as unseen
                                    FROM chatroom c
                                        JOIN chatroom_user cu ON c.id = cu.chatroom_id AND cu.user_id = :myUserId
                                        LEFT JOIN chatroom_user cu2 ON c.id = cu2.chatroom_id AND cu2.user_id <> cu.user_id AND cu2.status = 1
                                        LEFT JOIN user u2 ON cu2.user_id = u2.id 
                                        LEFT JOIN message msg ON msg.chatroom_id = c.id AND msg.status = 1
                                        LEFT JOIN message_seen msgSeen ON msgSeen.chatroom_id = c.id AND msgSeen.user_id = cu.user_id 
                                            AND msgSeen.seen_status = 1 AND msg.id = msgSeen.message_id
                                    WHERE c.status = 1 and cu.status = 1 
                                    GROUP BY c.unique_id, c.name, c.update_at
                                    ORDER BY c.update_at DESC',
                            ['myUserId' => \getMyId()]);
        foreach ($myChatroom as $item) {
            // if chatroom Name = null, use participate name
            if(is_null($item->chatroomName)){
                $item->name = $item->userName;
            }else{
                $item->name = $item->chatroomName;
            }
            $item->initials = \initials($item->name);
            
            unset($item->chatroomName);
        }
        return $myChatroom;
    }

    // call from view
    static function getChatroomList(){
        return json_encode(ChatroomController::getChatroom());
    }
    
    private function checkChatroomPermission($chatroomUniqid){
        // get chatroom id
        $chatroom = Chatroom::where('unique_id', $chatroomUniqid)->first();
        if(!$chatroom){
            return redirect()->route('login.chatroom.contacts');
        }

        // check permission, whether you are in this chatroom
        $checkPermission = ChatroomUser::where('chatroom_id', $chatroom->id)
                    -> where('user_id', \getMyId())
                    -> where('status', 1)
                    ->first();
        if(!$checkPermission){
            return redirect()->route('login.chatroom.contacts');
        }

        return $chatroom;
    }

    // in chatroom
    public function chat($chatroomUniqid){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }

        $chatroom = $this->checkChatroomPermission($chatroomUniqid);
        $chatroomId = $chatroom->id;

        $myUniqid = \getMyUniqid();
        $myId = \getMyId();

        $chatroomUser = DB::select('SELECT cu.create_at as chatroomUserCreateAt, cu.update_at as chatroomUserUpdateAt, cu.side,
                                    u.unique_id, u.name, u.display_id, u.email,
                                    u.type, u.profile, u.profile_picture, u.status
                                    FROM chatroom_user cu JOIN user u ON cu.user_id = u.id AND u.status = 1
                                    WHERE cu.chatroom_id = :chatroomId AND cu.status = 1',
                                    ["chatroomId" => $chatroomId]);
        $message = DB::select('SELECT m.id, m.unique_id as messageUniqid, m.content as message,  
                                    m.update_at as messageUpdateAt, u.unique_id as senderUniqid, 
                                    CASE WHEN seen.id is not null THEN 1 ELSE 0 END as seen
                                    FROM message m 
                                    JOIN user u ON m.user_id = u.id AND u.status = 1
                                    LEFT JOIN message_seen seen ON m.id = seen.message_id AND seen.user_id = :myId
                                    WHERE m.chatroom_id = :chatroomId AND m.status = 1
                                    ORDER BY m.update_at',
                                    ["chatroomId" => $chatroomId, "myId" => $myId]);

        $participationSide = [];
     //  dd($message); 
        foreach ($chatroomUser as $participant) {
            $participant->initials = \initials($participant->name);
            if($participant->unique_id == $myUniqid){
                // current user
                $participant->name = "You";
                $participant->currentUser = true;
                $mySide = $participant->side;
            }else{
                $participant->currentUser = false;
                $participationSide[$participant->unique_id] = $participant->side;
            }
        }

        if (empty($chatroom->name)) {
            // DM
            // get chat room name
            foreach ($chatroomUser as $user) {
                if ($user->unique_id != $myUniqid) {
                    $chatroom->name = $user->name;
                    break;
                }
            }
        }

        // get message side
        foreach ($message as $item) {
            if($item->senderUniqid == $myUniqid){
                $item->messageSide = 'myMessage';
            }elseif($participationSide[$item->senderUniqid] == $mySide){
                $item->messageSide = 'sameSide';
            }else{
                $item->messageSide = 'oppositeSide';
            }

            // message seen
            if(!$item->seen){
                // not seen
                $messageSeen = new MessageSeen;
                $messageSeen->chatroom_id = $chatroomId;
                $messageSeen->message_id = $item->id;
                $messageSeen->user_id = $myId;
                $messageSeen->save();
            }
            unset($item->id);
        }

        unset($chatroom->id);


        return view('login.chatroom.chat')->with('chatroom', json_encode($chatroom))
                                            ->with('chatroomUser', json_encode($chatroomUser))
                                            ->with('message', json_encode($message));
    }

    public function setting($chatroomUniqid){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $chatroom = $this->checkChatroomPermission($chatroomUniqid);
        

        $chatroomUser = DB::select('SELECT cu.create_at as chatroomUserCreateAt, cu.update_at as chatroomUserUpdateAt, cu.side,
                                    u.unique_id, u.name, u.display_id, u.email,
                                    u.type, u.profile, u.profile_picture, u.status, u.phone, u.DOB
                                    FROM chatroom_user cu JOIN user u ON cu.user_id = u.id AND u.status = 1
                                    WHERE cu.chatroom_id = :chatroomId AND cu.status = 1',
                                    ["chatroomId" => $chatroom->id]);
        unset($chatroom->id);

        $myUniqid = \getMyUniqid();
        
        foreach ($chatroomUser as $participant) {
            if ($participant->unique_id == $myUniqid) {
                // current user
                $participant->name = $participant->name . " (You)";
                $participant->currentUser = true;
                $mySide = $participant->side;
            } else {
                $participant->currentUser = false;
            }
        }

        // check is DM or Channel
        if(is_null($chatroom['name'])){
            if(empty($chatroom->name)){
                foreach ($chatroomUser as $user) {
                    if($user->unique_id != $myUniqid){
                        $chatroom->name = $user->name;
                        break;
                    }
                }
            }
            // DM
            return view('login.chatroom.setting')->with('chatroom', json_encode($chatroom))
                                                    ->with('chatroomUser', json_encode($chatroomUser));
        }else{
             // Channel
            return view('login.chatroom.channelSetting')->with('chatroom', json_encode($chatroom))
                                                        ->with('chatroomUser', json_encode($chatroomUser))
                                                        ->with('mySide', $mySide);
        }
    }

    public function settingAddUser($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        
        $chatroom = $this->checkChatroomPermission($unique_id);
        
        if (session('user.auth') == 'indi') {
            $searchType = 'indi';
        }else{
            $searchType = 'colleague';
        }

        $output = app()->call('App\Http\Controllers\ContactController@getContact', [$searchType, '', '']); 
        return view('login.chatroom.channelAddUser')->with('chatroomUniqid', $unique_id)
                                                    ->with('output', json_encode($output))
                                                    ->with('searchType', $searchType);
    }

    public function createChannel(Request $request){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->input();
        unset($input["_token"]);
        $selectedUser = [];
        foreach ($input as $key => $value) {
            array_push($selectedUser, $key);
        }
        $user = User::select('unique_id', 'name', 'display_id')
                    -> whereIn('unique_id', $selectedUser)
                    -> where('status', '1')
                    -> get();
        return view('login.chatroom.createChannel')->with('selectedUser', json_encode($user));
    }

    // add to DM
    public function backendAddToChat($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $myId = \getMyId();
        $contactUserId = \userUniqidToId($unique_id);

        $checkChatroom = DB::select('SELECT cu.chatroom_id 
                                    FROM chatroom rm 
                                    LEFT JOIN chatroom_user cu ON rm.id = cu.chatroom_id
                                        JOIN chatroom_user cu2 ON cu.chatroom_id = cu2.chatroom_id 
                                    WHERE cu.user_id = :myId AND cu2.user_id = :contactUserId
                                        AND rm.name is null',
                                    ['myId' => $myId, 'contactUserId' => $contactUserId]);
        
        if($checkChatroom){
            // already exists
            $chatroom = Chatroom::select('unique_id')->where('id', $checkChatroom[0]->chatroom_id)->first();
            $chatroomUniqid = $chatroom->unique_id;

            return redirect()->route('login.chatroom.chat', ['uniqueId'=> $chatroomUniqid]);
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
            $addMe->side = '1';
            $addMe->save();
            
            $addContactUser = new ChatroomUser;
            $addContactUser->chatroom_id = $chatroom->id;
            $addContactUser->user_id = $contactUserId;
            $addContactUser->side = '0';
            $addContactUser->save();
            $chatroomUniqid = $chatroom->unique_id;

            return redirect()->route('login.chatroom.chat', ['uniqueId'=> $chatroomUniqid, 'type' => 'new']);
        }
    }

    // add to channel
    public function backendSettingAddUser(Request $request, $unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->input();
        unset($input["_token"]);
        if(empty($input)){
            return redirect()->route('login.chatroom.setting', ['unique_id' => $unique_id]);
        }

        $selectedUser = [];
        foreach ($input as $key => $value) {
            array_push($selectedUser, $key);
        }
        
        $chatroom = $this->checkChatroomPermission($unique_id);
        foreach ($input as $userUniqid => $value) {
            $userId = \userUniqidToId($key);
            // check in rm or not
            $chatroomUser = ChatroomUser::where('user_id', $userId)->where('chatroom_id', $chatroom->id)->first();
            if($chatroomUser){
                // already in chatroom

            }else{
                // add to room
                // get mySide
                $mySide = ChatroomUser::select('side')->where('user_id', \getMyId())->where('chatroom_id', $chatroom->id)->first();
                $chatroomUser = new ChatroomUser;
                $chatroomUser->chatroom_id = $chatroom->id;
                $chatroomUser->user_id = $userId;
                $chatroomUser->side = $mySide['side'];
                $chatroomUser->save();

                $chatroom->touch();
            }
        }
        return redirect()->route('login.chatroom.chat', ['unique_id' => $unique_id, 'type' => 'new']);
    }

    public function backendMessageSeen($unique_id){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $message = Message::where('unique_id', $unique_id)->first();

        $messageSeen = new MessageSeen;
        $messageSeen->chatroom_id = $message->chatroom_id;
        $messageSeen->message_id = $message->id;
        $messageSeen->user_id = \getMyId();
        $messageSeen->save();
    }
    
    public function ajaxNewMessage(Request $request){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('chatroomUniqid', 'message');

        $chatroom = $this->checkChatroomPermission($input['chatroomUniqid']);
        $chatroom->touch();

        $message = new Message;
        $message->unique_id = \getUniqid();
        $message->user_id = \getMyId();
        $message->chatroom_id = $chatroom->id;
        $message->content = $input['message'];
        $message->save();
        
        $output['messageUniqid'] = $message['unique_id'];
        $output['messageUpdateAt'] = $message['update_at']->format('Y-m-d H:i:s');
        return response()->json(compact('output'));
    }

    public function ajaxCreateChannel(Request $request){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('name', 'description', 'selectedUser');
        $selectedUserUniqid = array_keys($input['selectedUser']);

        $user = User::select('id', 'unique_id')->whereIn('unique_id', $selectedUserUniqid)->get()->toArray();

        $chatroom = new Chatroom;
        $chatroom->unique_id = \getUniqid();
        $chatroom->name = $input['name'];
        $chatroom->description = $input['description'];
        $chatroom->save();

        foreach ($user as $item) {
            $chatroomUser = new ChatroomUser;
            $chatroomUser->chatroom_id = $chatroom->id;
            $chatroomUser->user_id = $item['id'];
            $chatroomUser->side = $input['selectedUser'][$item['unique_id']];
            $chatroomUser->save();
        }
        
        $addMe = new ChatroomUser;
        $addMe->chatroom_id = $chatroom->id;
        $addMe->user_id = \getMyId();
        $addMe->side = '1';
        $addMe->save();

        $output['result'] = "true";
        $output['redirect'] = route('login.chatroom.chat', ["unique_id" => $chatroom->unique_id, 'type' => 'new']);
        
        return response()->json(compact('output'));
    }
    public function ajaxSetting(Request $request, $mode){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        if ($mode == 'direct') {
            $input = $request->only('uniqid', 'description');

            $chatroom = $this->checkChatroomPermission($input['uniqid']);

            $chatroom->description = $input['description'];
            $chatroom->save();
        }elseif ($mode == 'channel') {
            $input = $request->only('uniqid', 'name', 'description', 'userSide');
            
            $chatroom = $this->checkChatroomPermission($input['uniqid']);
            
            $chatroom->name = $input['name'];
            $chatroom->description = $input['description'];
            $chatroom->save();
            foreach ($input['userSide'] as $key => $value) {
                $id = \userUniqidToId($key);
                $chatroomUser = ChatroomUser::where('user_id', $id)->where('chatroom_id', $chatroom->id)->first();
                $chatroomUser->side = $value;
                $chatroomUser->save();
            }
        }
        
        $output['result'] = "true";
        $output['redirect'] = route('login.chatroom.chat', ["unique_id" => $chatroom->unique_id]);
        
        return response()->json(compact('output'));
    }

    public function ajaxGetChatroomList(){
        if (!userTypeAccess(['indi', 'business', 'business admin', 'admin'])) {
            return redirect()->route('logout.login');
        }
        return response()->json(ChatroomController::getChatroom());
    }
}
