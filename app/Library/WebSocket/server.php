<?php
// prevent the server from timing out
set_time_limit(0);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'PHPWebSocket.php';

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
var_dump($message);
$message = json_decode($message);	// to object
var_dump($message);
    //print_r($message);
    // echo 'type= ' .$message->type .'\n';
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
            
// var_dump($Server->wsClients);
// var_dump($message->currentChatroomUser);
            foreach ($Server->wsClients as $id => $client) {
                // echo 'open   ';
                // var_dump($newMessageChatroomUser);
                $userInfo = $client[50];
                foreach ($message->currentChatroomUser as $newMessageChatroomUser) {
                    // current message's chatroom's participants
                    // echo 'id='.$id.'   ';
                    // var_dump($userInfo);
                    // echo $userInfo['senderUniqid'] .'                '. $newMessageChatroomUser->unique_id.'        ';
                    // echo $userInfo['currentChatroomUniqid'] .'                '. $output['chatroomUniqid'];
                    if ($userInfo['userUniqid'] == $newMessageChatroomUser->unique_id) {
                        // if user in this chatroom
                        if ($userInfo['currentChatroomUniqid'] == $output['chatroomUniqid']) {
                            // if user is viewing this chatroom
                            $output['socketType'] = 'newChatroomMessage';
                            if ($userInfo['userUniqid'] == $output['senderUniqid']) { // = sender uniqid
                                $output['messageType'] = 'myMessage';
                            } elseif ($userInfo['side'] == $output['senderSide']) { 
                                $output['messageType'] = 'sameType';
                            } else {
                                $output['messageType'] = 'oppositeType';
                            }
                            echo "uniqid = ".$userInfo['userUniqid'] . " side = ".$userInfo['side']." sender side = ".$output['senderSide'];
                        } else {
                            // if user is not viewing this chatroom
                            $output['socketType'] = 'notiNewChatroomMessage';
                        }
                        print_r($output);
                        $Server->wsSend($id, json_encode($output));
                    }
                }
            }
            break;
        case 'initChatroom':
            $customUserInfo = &$Server->wsClients[$clientID][50];	// & = pointer
            $customUserInfo['currentChatroomUniqid'] = $message->chatroomUniqid;
            $customUserInfo['userUniqid'] = $message->myUniqid;
            $customUserInfo['side'] = $message->mySide;
            break;
        case 'initNoti':
            # code...
            break;
        default:
            # code...
            break;
    }


    //The speaker is the only person in the room. Don't let them feel lonely.
    // if (sizeof($Server->wsClients) == 1) {
    //     $Server->wsSend($clientID, "There isn't anyone else in the room, but I'll still listen to you. --Your Trusty Server");
    // } else {
    //     //Send the message to everyone but the person who said it
    //     foreach ($Server->wsClients as $id => $client) {
    //         if ($id != $clientID) {
    //             $Server->wsSend($id, "Visitor $clientID ($ip) said \"$message\"");
    //         }
    //     }
    // }
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

$wsIP = $_ENV["WS_Server_IP"];
$wsPort = $_ENV["WS_Server_Port"];

$Server->wsStartServer($wsIP, $wsPort);
