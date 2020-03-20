<?php
// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'PHPWebSocket.php';
require 'config.php';

function getChatroomParticipantInSocket($currentChatroomUser, $chatrooomUniqid){
    global $Server;
    $chatroomParticipantInSocket = [];

    // for each chatroom participant
    foreach ($currentChatroomUser as $newMessageChatroomUser) {
        $tempThisParticipant;   // server->clientId to be send socket msg
        $checkThisParticipant = false;

        // for each connected participant
        foreach ($Server->wsClients as $id => $clientInfo) {
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

            if (isset($userInfo['currentChatroomUniqid']) && $userInfo['currentChatroomUniqid'] == $chatrooomUniqid) {
                // is viewing THIS chatroom
                $tempThisParticipant = $id;
                break;
            } else {
                $tempThisParticipant = $id;
            }
        }
        if($checkThisParticipant){
            // is participant
            array_push($chatroomParticipantInSocket, $tempThisParticipant);
        }
    }
    return $chatroomParticipantInSocket;
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
            $output['socketType'] = 'newChatroomMessage';
            $output['chatroomUniqid'] = $message->chatroomUniqid;
            $output['senderUniqid'] = $message->myUniqid;
            $output['senderSide'] = $message->mySide;
            // for output message
            $output['messageUniqid'] = $message->messageUniqid;
            $output['messageCreateAt'] = $message->messageCreateAt;
            $output['message'] = $message->message;
            
            $chatroomParticipantInSocket = getChatroomParticipantInSocket($message->currentChatroomUser, $output['chatroomUniqid']);
            
            foreach ($chatroomParticipantInSocket as $id) {
                $userInfo = $Server->wsClients[$id][50];
                // default socket msg
                $output['socketType'] = 'notiNewChatroomMessage';
                $output['messageSide'] = '';

                // if is socket is viewing chatroom
                if ($userInfo['connectionType'] == "initChatroom") {
                    // if user is viewing THIS chatroom
                    if ($userInfo['currentChatroomUniqid'] == $output['chatroomUniqid']) {
                        $output['socketType'] = 'newChatroomMessage';
                        if ($userInfo['userUniqid'] == $output['senderUniqid']) { // = sender uniqid
                            $output['messageSide'] = 'myMessage';
                        } elseif ($userInfo['side'] == $output['senderSide']) {
                            $output['messageSide'] = 'sameSide';
                        } else {
                            $output['messageSide'] = 'oppositeSide';
                        }
                    }
                }

                print_r($output);
                $Server->wsSend($id, json_encode($output));     // send socket message
            }
            break;
        case 'initChatroom':
            $customUserInfo = &$Server->wsClients[$clientID][50];	// & = pointer

            $customUserInfo['connectionType'] = $message->socketType;
            $customUserInfo['currentChatroomUniqid'] = $message->chatroomUniqid;
            $customUserInfo['userUniqid'] = $message->myUniqid;
            $customUserInfo['side'] = $message->mySide;
            break;
        case 'initConnection':
            $customUserInfo = &$Server->wsClients[$clientID][50];	// & = pointer

            $customUserInfo['connectionType'] = $message->socketType;
            $customUserInfo['userUniqid'] = $message->myUniqid;
            break;
        case 'notiNewInvitation':
            $output['socketType'] = $message->socketType;
            $output['chatroomUniqid'] = $message->chatroomUniqid;

            $chatroomParticipantInSocket = getChatroomParticipantInSocket($message->currentChatroomUser, $output['chatroomUniqid']);
            foreach ($chatroomParticipantInSocket as $id) {
                $userInfo = $Server->wsClients[$id][50];
                if ($userInfo['userUniqid'] != $message->myUniqid) { // = sender uniqid
                    print_r($output);
                    $Server->wsSend($id, json_encode($output));     // send socket message
                }
            }

            break;
        case 'backend':
            var_dump($Server->wsClients);
            $Server->wsSend($clientID, json_encode($Server->wsClients));
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
