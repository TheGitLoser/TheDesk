$(function(){
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
        if(response['socketType'] == "newChatroomMessage"){
            socketNewChatroomMessage(response); // show msg
            updateChatroomList(response, false);
            socketNewNotiInThisRoom(response);  // noti new coming msg
        }else if(response['socketType'] == "notiNewChatroomMessage"){
            updateChatroomList(response, true);
            socketNewNoti(response);
        }else if(response['socketType'] == "notiNewInvitation"){

        }
    }

    Socket.onclose = function(event){
        console.log("Socket is closed now (onclose())");
    }
});

function updateChatroomList(response, unseen){
    indexToBeUpdate = chatroomList.findIndex( ({ unique_id }) => unique_id === response['chatroomUniqid'] );
    chatroomName = chatroomList[indexToBeUpdate].name;
    chatroomList[indexToBeUpdate].update_at = response['messageCreateAt'];
    if(unseen){
        chatroomList[indexToBeUpdate].unseen ++;
    }

    // sort chatroom list by update_at desc
    chatroomList.sort(function (a, b) {
        return b.update_at.localeCompare(a.update_at);
    });
    outputChatroomList(chatroomList);
}

// new message in the same chatroom
function socketNewNotiInThisRoom(response){
    if(response['messageSide'] != 'myMessage'){
        url = window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + response['chatroomUniqid'];
        pushNoti(chatroomName, response['message'], url, true);
    }
}

// new message or new chat room
function socketNewNoti(response){
    url = window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + response['chatroomUniqid'];
    pushNoti(chatroomName, response['message'], url, false);
    // update chatroom list

}

function newNoti(title, body, url, current){
    // show notification here
    var notify = new Notification(title, {
        body: body
    });
    console.log(current);
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
