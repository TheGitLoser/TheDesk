$(function () {
    serviceWorkerRegistration = navigator.serviceWorker.register('/js/service-worker.js');

    function setupWebSocket(){
        try {
            Socket = new WebSocket(socketUrl);
        } catch (error) {
            console.log(error);
        }

        Socket.onopen = function (event) {
            // init connection message
            console.log(Socket.readyState);
            Socket.send(JSON.stringify(messageSend));
        }

        // when message comes form server
        Socket.onmessage = function (event) {
            response = JSON.parse(event['data']);
        console.log(response);
            switch (response['socketType']) {
                case "newChatroomMessage":  // in current chatroom
                    socketNewChatroomMessage(response); // show msg
                    updateChatroomList(response, false);
                    if (response['messageSide'] != 'myMessage') {
                        // if not send by myself
                        if (location.protocol == 'https:') {
                            pushServiceWorkerNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), true, "noti-focus");
                        }else{
                            pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), true);
                        }
                    }
                    break;
                case "notiNewChatroomMessage":
                    updateChatroomList(response, 1);
                    if (location.protocol == 'https:') {
                        pushServiceWorkerNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), false, "noti-new");
                    }else{
                        pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), false);
                    }
                    unseenMessage.unshift({
                        chatroomUniqid: response['chatroomUniqid'],
                        chatroomName: chatroomName,
                        chatroomType: response['chatroomType'],
                        senderName: response['senderName'],
                        unique_id: response['messageUniqid'],
                        message: response['message'],
                        update_at: response['messageUpdateAt']
                    });
                    outputNotification(unseenMessage);
                    break;
                case "notiNewInvitation":
                    indexToBeUpdate = chatroomList.findIndex(({
                        unique_id
                    }) => unique_id === response['chatroomUniqid']);
                    if (indexToBeUpdate == -1) {
                        // is new invitation
                        getChatroomList();
                    }
                    break;
                case "updateUINewMessage":
                    updateChatroomList(response, 1);
                    unseenMessage.unshift({
                        chatroomUniqid: response['chatroomUniqid'],
                        chatroomName: chatroomName,
                        chatroomType: response['chatroomType'],
                        senderName: response['senderName'],
                        unique_id: response['messageUniqid'],
                        message: response['message'],
                        update_at: response['messageUpdateAt']
                    });
                    outputNotification(unseenMessage);
                    break;
                case "updateUISeenMessage":
                    updateChatroomList(response, "seen");

                    unseenMessage.forEach(element => {
                        if(element['chatroomUniqid'] == response['chatroomUniqid']){

                        }
                    });
                    var temp = [];
                    unseenMessage.forEach(function(item, index, object) {
                        if(item['chatroomUniqid'] == response['chatroomUniqid']){
                            temp.push(index);
                        }
                    });
                    temp.forEach(element => {
                        unseenMessage.splice(element,1);
                    });
                    outputNotification(unseenMessage);
                    break;
                case "startTypingMessageInThisChatroom":
                    chatroomTypingParticipate.push(response['senderUniqid']);
                    chatroomTypingParticipate = chatroomTypingParticipate.filter((v, i, a) => a.indexOf(v) === i); 
                    updateChatroomParticipate();
                    break;
                case "stopTypingMessageInThisChatroom":
                    chatroomTypingParticipate.pop(response['senderUniqid']);
                    updateChatroomParticipate();
                    break;
                case "checkSocketTypingStatus":
                    messageSend['socketType'] = "replySocketTypingStatus";
                    messageSend['updateSocketId'] = response['updateSocketId'];
                    messageSend['typing'] = typingMessage.toString();
                    Socket.send(JSON.stringify(messageSend));
                    console.log(messageSend);
                    break;
                default:
                    break;
            }
        }

        Socket.onclose = function (event) {
            console.log("Socket is closed now (onclose())");
            setTimeout(function () {
                setupWebSocket();
            }, 1000);  // 1sec
        }
    }
    setupWebSocket();

});
function updateChatroomParticipate(){
    chatroomParticipate = "";
    $.each(chatroomUser, function(i, item) {
        if(chatroomTypingParticipate.includes(item['unique_id'])){
            chatroomParticipate += item['name']+'(typing...), ';
        }else if(item['currentUser']){
        }else{
            chatroomParticipate += item['name']+', ';
        }
    });
    chatroomParticipate += 'You';
    $("#chatroomParticipate").text(chatroomParticipate);
    if(chatroomTypingParticipate.length){   // no current typing
        setTimeout(function () {
            checkSocketTypingStatus();
        }, 10000);  // 10sec
    }
}

function checkSocketTypingStatus(){
    messageSend['socketType'] = "checkSocketTypingStatus";
    Socket.send(JSON.stringify(messageSend));
    console.log('checkstatus');
}


function getChatroomURL(chatroomUniqid) {
    return window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + chatroomUniqid;
}


function updateChatroomList(response, unseenNumber) {
    indexToBeUpdate = chatroomList.findIndex(({
        unique_id
    }) => unique_id === response['chatroomUniqid']);
    chatroomName = chatroomList[indexToBeUpdate].name;  // global
    if (typeof response['messageUpdateAt'] !== 'undefined') {
        // if != user viewing this chatroom in other tab
        chatroomList[indexToBeUpdate].update_at = response['messageUpdateAt'];
    }
    
    switch (unseenNumber) {
        case false:
            // this chatroom
            break;
        case "seen":
            chatroomList[indexToBeUpdate].unseen = 0;
            break;
        default:
            // not current chatroom
            chatroomList[indexToBeUpdate].unseen += unseenNumber;
            break;
    }

    // sort chatroom list by update_at desc
    chatroomList.sort((a, b) => (a.update_at < b.update_at) ? 1 : -1);

    outputChatroomList(chatroomList);
}

function pushServiceWorkerNoti(title, body, url, current, type) {
    console.log('showNoti');
    if ('serviceWorker' in navigator) {
        serviceWorkerRegistration.then(function (registration) {
                console.log("Service Worker Registered");
                setTimeout(() => {
                    registration.showNotification(title, {
                        body: body,
                        data: {
                            action: {
                                url: url,
                                type: type
                            }
                        }
                    });
                    registration.update();
                }, 100);
            })
            .catch(function (err) {
                console.log("Service Worker Failed to Register", err);
            })

    }
}

function newNoti(title, body, url, current) {
    // show notification here
    var notify = new Notification(title, {
        body: body
    });
    if (current) {
        // current page = this chatroom
        notify.onclick = function (event) {
            event.preventDefault(); // prevent the browser from focusing the Notification's tab
        };
    } else {
        notify.onclick = function (event) {
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
