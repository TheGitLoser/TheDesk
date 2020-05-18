function openNoti(event) {
    /**** START notificationOpenWindow ****/
    console.log('openNoti');

    promiseChain = clients.openWindow(event.notification.data.action.url);
    event.waitUntil(promiseChain);
    /**** END notificationOpenWindow ****/
}

function focusWindow(event) {
    console.log('focus');
    /**** START notificationFocusWindow ****/
    /**** START urlToOpen ****/
    urlToOpen = new URL(event.notification.data.action.url, self.location.origin).href;
    /**** END urlToOpen ****/

    /**** START clientsMatchAll ****/
    promiseChain = clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        /**** END clientsMatchAll ****/
        /**** START searchClients ****/
        .then((windowClients) => {
            let matchingClient = null;

            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                if (windowClient.url === urlToOpen) {
                    matchingClient = windowClient;
                    break;
                }
            }

            if (matchingClient) {
                return matchingClient.focus();
            } else {
                return clients.openWindow(urlToOpen);
            }
        });
    /**** END searchClients ****/

    event.waitUntil(promiseChain);
    /**** END notificationFocusWindow ****/
}



self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    console.log(event.notification.data.action.type);
    switch (event.notification.data.action.type) {
        case 'noti-new':
            console.log('new');
            openNoti(event);
            break;
        case 'noti-focus':
            console.log('new');
            focusWindow(event);
            break;
        default:
            console.log(event.notification.data.action.type);
            // NOOP
            break;
    }
});

const notificationCloseAnalytics = () => {
    return Promise.resolve();
};

/**** START notificationCloseEvent ****/
self.addEventListener('notificationclose', function (event) {
    const dismissedNotification = event.notification;

    const promiseChain = notificationCloseAnalytics();
    event.waitUntil(promiseChain);
});
/**** END notificationCloseEvent ****/
