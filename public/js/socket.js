$(function(){
    Socket = new WebSocket(socketUrl);
    console.log(Socket.readyState);

    Socket.onopen = function(event){
        // init connection message
        Socket.send(JSON.stringify(messageSend));
    }

    // when message comes form server
    Socket.onmessage = function(event){
        response = JSON.parse(event['data']);
console.log(response);
        if(response['socketType'] == "newChatroomMessage"){
            socketNewChatroomMessage(response);
            socketNewNotiInThisRoom(response);
        }else{
            socketNewNoti(response);
        }
    }

    Socket.onclose = function(event){
        console.log("Socket is closed now (onclose())");
    }
});

// new message in the same chatroom
function socketNewNotiInThisRoom(response){
    url = window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + response['chatroomUniqid'];
    pushNoti(response['chatroomName'], response['message'], url, true);
}

// new message or new chat room
function socketNewNoti(response){
    console.log('noti');
    url = window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + response['chatroomUniqid'];
    pushNoti(response['chatroomName'], response['message'], url, false);
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
