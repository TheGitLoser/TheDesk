$(function() {  
    Socket = new WebSocket(socketUrl);
    
    Socket.onopen = function(event){
        // init connection message
        console.log(Socket.readyState);
        Socket.send(JSON.stringify(messageSend));
    }

    // when message comes form server
    Socket.onmessage = function(event){
        response = JSON.parse(event['data']);
console.log(response);
        if(response['socketType'] == "newChatroomMessage"){ // in current chatroom
            socketNewChatroomMessage(response); // show msg
            updateChatroomList(response, false);
            if(response['messageSide'] != 'myMessage'){
                // if not send by myself
                pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), true);
            }

        }else if(response['socketType'] == "notiNewChatroomMessage"){
            updateChatroomList(response, true);
            pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), false);
            unseenMessage.unshift({chatroomUniqid: response['chatroomUniqid'],
                                    chatroomName: chatroomName,
                                    chatroomType: response['chatroomType'],
                                    senderName: response['senderName'],
                                    unique_id: response['messageUniqid'],
                                    message: response['message'],
                                    update_at: response['messageUpdateAt']});
            outputNotification(unseenMessage);
        }else if(response['socketType'] == "notiNewInvitation"){
            indexToBeUpdate = chatroomList.findIndex( ({ unique_id }) => unique_id === response['chatroomUniqid'] );
            if(indexToBeUpdate == -1){
                // is new invitation
                getChatroomList();
            }

        }
    }

    Socket.onclose = function(event){
        console.log("Socket is closed now (onclose())");
        setTimeout(function() {
            Socket = new WebSocket(socketUrl);
          }, 10);
    }
});

function getChatroomURL(chatroomUniqid){
    return window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + chatroomUniqid;
}


function updateChatroomList(response, unseen){ 
    indexToBeUpdate = chatroomList.findIndex( ({ unique_id }) => unique_id === response['chatroomUniqid'] );
    chatroomName = chatroomList[indexToBeUpdate].name;
    chatroomList[indexToBeUpdate].update_at = response['messageUpdateAt'];
    if(unseen){
        // not current chatroom
        chatroomList[indexToBeUpdate].unseen ++;
    }

    // sort chatroom list by update_at desc
    chatroomList.sort(function (a, b) {
        return b.update_at.localeCompare(a.update_at);
    });
    outputChatroomList(chatroomList);
}

function newNoti(title, body, url, current){
    // show notification here
    var notify = new Notification(title, {
        body: body
    });
    if(current){
        // current page = this chatroom
        notify.onclick = function(event) {
            event.preventDefault(); // prevent the browser from focusing the Notification's tab
        };
    }else{
        notify.onclick = function(event) {
            event.preventDefault(); // prevent the browser from focusing the Notification's tab
            window.open(url, '_blank');
        };
    }
}

function pushNoti(title, body, url, current) {
    if (!window.Notification) {
        console.log('Browser does not support notifications.');
    } else {
        // check if permission is already granted
        if (Notification.permission === 'granted') {
            newNoti(title, body, url, current);
        } else {
            // request permission from user
            Notification.requestPermission().then(function (p) {
                if (p === 'granted') {
                    newNoti(title, body, url, current);
                } else {
                    console.log('User blocked notifications.');
                }
            }).catch(function (err) {
                console.error(err);
            });
        }
    }
}
