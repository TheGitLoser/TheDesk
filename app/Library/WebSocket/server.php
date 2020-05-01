<?php
// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'PHPWebSocket.php';
require 'config.php';

function getChatroomParticipantInSocket($currentChatroomUser, $chatrooomUniqid){
    global $Server;
    $chatroomParticipantInSocket = [];  // (all) 
    $chatroomParticipantInSocketInCurrentChatroom = [];  // (in current chatroom) 

    // for each chatroom participant
    foreach ($currentChatroomUser as $newMessageChatroomUser) {
        $tempThisParticipantInCurrentChatroom =[];   // server->clientId to be send socket msg (in current chatroom)
      
        $checkThisParticipant = false;
// echo "\n\nnewMessageChatroomUser: ".$newMessageChatroomUser->unique_id;
        // for each connected participant
        foreach ($Server->wsClients as $id => $clientInfo) {
        // echo "D";
        // var_dump($Server->wsClients) ;
            if (!isset($clientInfo[50])) {
                // this socket is not a user
                continue;
            }
            // this socket is a user
            $userInfo = $clientInfo[50];
            if ($userInfo['userUniqid'] != $newMessageChatroomUser->unique_id) {
                // if socket is not a participant
                continue;
            }

            // if socket is a participant
            $checkThisParticipant = true;
// echo "\n   checkThisParticipant : ";
            if (isset($userInfo['currentChatroomUniqid']) && $userInfo['currentChatroomUniqid'] == $chatrooomUniqid) {
                // is viewing THIS chatroom
                $tempThisParticipantInCurrentChatroom[$userInfo['sessionId']] = $id;
            } else {
                $tempThisParticipantInCurrentChatroom[$userInfo['sessionId']] = $id;
            }
            array_push($chatroomParticipantInSocket, $id);
        }
        if($checkThisParticipant){
            // is participant
            foreach ($tempThisParticipantInCurrentChatroom as $tempId) {
                array_push($chatroomParticipantInSocketInCurrentChatroom, $tempId);
            }
        }
    }
    $output = ['chatroomParticipantInSocketInCurrentChatroom' => $chatroomParticipantInSocketInCurrentChatroom, 
                'chatroomParticipantInSocket' => $chatroomParticipantInSocket];
// var_dump($output);
    return $output;
}

function updateTypingStatus($message, $output, $primarySocketType){
    global $Server;

    $tempOutput = getChatroomParticipantInSocket($message->currentChatroomUser, $output['chatroomUniqid']);
    $chatroomParticipantInSocket = $tempOutput['chatroomParticipantInSocket'];   

    $output['socketType'] = $primarySocketType;
    foreach ($chatroomParticipantInSocket as $id) {
        $userInfo = $Server->wsClients[$id][50];
        if ($userInfo['socketType'] == "initChatroom" && $userInfo['userUniqid'] != $message->myUniqid && $userInfo['currentChatroomUniqid'] == $message->chatroomUniqid) { 
            print_r($output);
            $Server->wsSend($id, json_encode($output));     // send socket message
        }
    }
}

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary)
{
    global $Server;
    $ip = long2ip($Server->wsClients[$clientID][6]);

    // check if message length is 0
    if ($messageLength == 0) {
        $Server->wsClose($clientID);
        return;
	}
    $message = json_decode($message);	// to object
var_dump($message);
    switch ($message->socketType) {
        case 'sendMessage':
            // $id = client's id assigned by socket
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['chatroomType'] = $message->chatroomType;
            $output['senderUniqid'] = $message->myUniqid;
            $output['senderName'] = $message->myName;
            $output['senderSide'] = $message->mySide;
            // for output message
            $output['messageUniqid'] = $message->messageUniqid;
            $output['messageUpdateAt'] = $message->messageUpdateAt;
            $output['message'] = $message->message;
            
            $tempOutput = getChatroomParticipantInSocket($message->currentChatroomUser, $output['chatroomUniqid']);
            $chatroomParticipantInSocketInCurrentChatroom = $tempOutput['chatroomParticipantInSocketInCurrentChatroom']; // send noti
            $chatroomParticipantInSocket = $tempOutput['chatroomParticipantInSocket'];   // update ui only

            foreach ($chatroomParticipantInSocketInCurrentChatroom as $id) {
                $userInfo = $Server->wsClients[$id][50];
                // default socket msg
                $output['socketType'] = 'notiNewChatroomMessage';
                $output['messageSide'] = '';

                // if user is viewing chatroom && THIS chatroom
                if ($userInfo['socketType'] == "initChatroom" && $userInfo['currentChatroomUniqid'] == $output['chatroomUniqid']) {
                    $output['socketType'] = 'newChatroomMessage';
                    if ($userInfo['userUniqid'] == $output['senderUniqid']) { // = sender uniqid
                        $output['messageSide'] = 'myMessage';
                    } elseif ($userInfo['side'] == $output['senderSide']) {
                        $output['messageSide'] = 'sameSide';
                    } else {
                        $output['messageSide'] = 'oppositeSide';
                    }
                }

                print_r($output);
                $Server->wsSend($id, json_encode($output));     // send socket message
            }
            $chatroomParticipantUpdateUI = array_diff($chatroomParticipantInSocket, $chatroomParticipantInSocketInCurrentChatroom);    // to update ui
        // var_dump($chatroomParticipantInSocket);
        // var_dump($chatroomParticipantInSocketInCurrentChatroom);
        // var_dump($chatroomParticipantUpdateUI);
            foreach ($chatroomParticipantUpdateUI as $id) {
                $output['socketType'] = 'updateUINewMessage';
        print_r($output);
                $Server->wsSend($id, json_encode($output));     // send socket message
            }
            break;
        case 'initChatroom':
            $customUserInfo = &$Server->wsClients[$clientID][50];	// & = pointer

            $customUserInfo['socketType'] = $message->socketType;
            $customUserInfo['currentChatroomUniqid'] = $message->chatroomUniqid;
            $customUserInfo['userUniqid'] = $message->myUniqid;
            $customUserInfo['side'] = $message->mySide;
            $customUserInfo['sessionId'] = $message->id;

            // update user's other page UI
            foreach ($Server->wsClients as $id => $clientInfo) {
                if (!isset($clientInfo[50])) {
                    // this socket is not a user
                    continue;
                }
                // this socket is a user
                $userInfo = $clientInfo[50];
                if($userInfo['userUniqid'] == $message->myUniqid && $id != $clientID){
                    // same user, other than this socket
                    $output['socketType'] = 'updateUISeenMessage';
                    $output['chatroomUniqid'] = $message->chatroomUniqid;

                    $Server->wsSend($id, json_encode($output));
                }
            }

            break;
        case 'initConnection':
            $customUserInfo = &$Server->wsClients[$clientID][50];	// & = pointer

            $customUserInfo['socketType'] = $message->socketType;
            $customUserInfo['userUniqid'] = $message->myUniqid;
            $customUserInfo['sessionId'] = $message->id;
            break;
        case 'notiNewInvitation':
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['chatroomType'] = $message->chatroomType;
            $output['senderName'] = $message->myName;

            $tempOutput = getChatroomParticipantInSocket($message->currentChatroomUser, $output['chatroomUniqid']);
            $chatroomParticipantInSocketInCurrentChatroom = $tempOutput['chatroomParticipantInSocketInCurrentChatroom']; // send noti (primary)
            $chatroomParticipantInSocket = $tempOutput['chatroomParticipantInSocket'];   // update ui only
        
            $output['socketType'] = 'notiNewInvitation';
            foreach ($chatroomParticipantInSocketInCurrentChatroom as $id) {
                $userInfo = $Server->wsClients[$id][50];
                if ($userInfo['userUniqid'] != $message->myUniqid) { // != sender uniqid
            print_r($output);
                    $Server->wsSend($id, json_encode($output));     // send socket message
                }
            }
        
            $chatroomParticipantUpdateUI = array_diff($chatroomParticipantInSocket, $chatroomParticipantInSocketInCurrentChatroom);    // to update ui

            $output['socketType'] = 'updateUINewInvitation';
            foreach ($chatroomParticipantUpdateUI as $id) {
        print_r($output);
                $Server->wsSend($id, json_encode($output));     // send socket message
            }

            break;
        case 'startTypingMessage':
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['senderUniqid'] = $message->myUniqid;
            updateTypingStatus($message, $output, 'startTypingMessageInThisChatroom');
            break;
        case 'stopTypingMessage':
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['senderUniqid'] = $message->myUniqid;
            updateTypingStatus($message, $output, 'stopTypingMessageInThisChatroom');
            break;
        case 'checkSocketTypingStatus':
            $output['socketType'] = 'checkSocketTypingStatus';
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $chatroomTypingParticipate = $message->chatroomTypingParticipate;

            $tempOutput = getChatroomParticipantInSocket($message->currentChatroomUser, $message->chatroomUniqid);
            $chatroomParticipantInSocketInCurrentChatroom = $tempOutput['chatroomParticipantInSocketInCurrentChatroom']; // send noti
            $tempRemove = [];
            foreach ($chatroomParticipantInSocketInCurrentChatroom as $id) {
                $userInfo = $Server->wsClients[$id][50];
                // if user is viewing chatroom && THIS chatroom
                if ($userInfo['socketType'] == "initChatroom" && $userInfo['currentChatroomUniqid'] == $output['chatroomUniqid']) {
                    if ($userInfo['userUniqid'] != $message->myUniqid) {  // != sender uniqid
                        $output['updateSocketId'] = $clientID;
                        $Server->wsSend($id, json_encode($output));     // send socket message
                        array_push($tempRemove, $userInfo['userUniqid']);
                    }
                }
            }
            $chatroomTypingParticipate = array_diff($chatroomTypingParticipate, $tempRemove);

            // if other chatroom typing participate closed socket connection
            $output['socketType'] = "stopTypingMessageInThisChatroom";
            foreach ($chatroomTypingParticipate as $value) {
                $output['senderUniqid'] = $value;
                $Server->wsSend($clientID, json_encode($output));
            }
            break;
        case 'replySocketTypingStatus':
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['senderUniqid'] = $message->myUniqid;
            var_dump ($message->typing);
            if($message->typing == "true"){
                $output['socketType'] = "startTypingMessageInThisChatroom";
            }else{
                $output['socketType'] = "stopTypingMessageInThisChatroom";
            }
            $Server->wsSend($message->updateSocketId, json_encode($output));
            // var_dump($output);
            break;
        case 'backend':
            var_dump($Server->wsClients);
            $output = [];
            foreach ($Server->wsClients as $key => $value) {
                array_push($output, $value[50]);
                print_r($value[50]);
            }
            $Server->wsSend($clientID, json_encode($output));
            break;
        default:
            # code...
            break;
    }

}

// when a client connects
function wsOnOpen($clientID)
{
    global $Server;
    $ip = long2ip($Server->wsClients[$clientID][6]);

    $Server->log("$ip ($clientID) has connected.");
    // print_r($Server);
}

// when a client closes or lost connection
function wsOnClose($clientID, $status)
{
    global $Server;
    $ip = long2ip($Server->wsClients[$clientID][6]);
    $Server->log("$ip ($clientID) has disconnected.");
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))

$Server->wsStartServer($wsIP, $wsPort);
