$(function () {
    serviceWorkerRegistration = navigator.serviceWorker.register('/js/service-worker.js');

    Socket = new WebSocket(socketUrl);

    Socket.onopen = function (event) {
        // init connection message
        console.log(Socket.readyState);
        Socket.send(JSON.stringify(messageSend));
    }

    // when message comes form server
    Socket.onmessage = function (event) {
        response = JSON.parse(event['data']);
        console.log(response);
        if (response['socketType'] == "newChatroomMessage") { // in current chatroom
            socketNewChatroomMessage(response); // show msg
            updateChatroomList(response, false);
            if (response['messageSide'] != 'myMessage') {
                // if not send by myself
                // pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), true);
    pushServiceWorkerNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), true, "noti-focus");
            }

        } else if (response['socketType'] == "notiNewChatroomMessage") {
            updateChatroomList(response, true);
            // pushNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), false);
    pushServiceWorkerNoti(chatroomName, response['message'], getChatroomURL(response['chatroomUniqid']), false, "noti-new");
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
        } else if (response['socketType'] == "notiNewInvitation") {
            indexToBeUpdate = chatroomList.findIndex(({
                unique_id
            }) => unique_id === response['chatroomUniqid']);
            if (indexToBeUpdate == -1) {
                // is new invitation
                getChatroomList();
            }

        }
    }

    Socket.onclose = function (event) {
        console.log("Socket is closed now (onclose())");
        setTimeout(function () {
            Socket = new WebSocket(socketUrl);
        }, 10);
    }
});

function getChatroomURL(chatroomUniqid) {
    return window.location.protocol + "//" + window.location.hostname + "/chatroom/chat/" + chatroomUniqid;
}


function updateChatroomList(response, unseen) {
    indexToBeUpdate = chatroomList.findIndex(({
        unique_id
    }) => unique_id === response['chatroomUniqid']);
    chatroomName = chatroomList[indexToBeUpdate].name;
    chatroomList[indexToBeUpdate].update_at = response['messageUpdateAt'];
    if (unseen) {
        // not current chatroom
        chatroomList[indexToBeUpdate].unseen++;
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
