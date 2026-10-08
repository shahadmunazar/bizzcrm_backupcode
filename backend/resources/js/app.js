import './bootstrap';
import { messaging, getToken, onMessage } from './firebase';

window.initializeNotifications = function(userId) {
    // 1. Reverb / Echo Setup
    if (window.Echo && userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('New notification via Reverb:', notification);
                
                // Immediately update UI counts since we got a new notification
                if (typeof window.fetchNotificationCounts === 'function') {
                    window.fetchNotificationCounts();
                }

                const actionUrl = notification.action_url || 'https://staging.bizzcrm.com/';
                
                if (typeof toastr !== 'undefined') {
                    toastr.info(notification.message, notification.title, {
                        onclick: function() { window.open(actionUrl, '_blank'); }
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: notification.title,
                        text: notification.message,
                        icon: 'info',
                        confirmButtonText: 'View',
                        showCancelButton: true,
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed) window.open(actionUrl, '_blank');
                    });
                } else {
                    if (confirm(notification.title + '\n' + notification.message + '\n\nClick OK to view.')) {
                        window.open(actionUrl, '_blank');
                    }
                }
            });
    }

    // 2. Firebase Push Notification Setup
    if (messaging) {
        // Handle foreground messages
        onMessage(messaging, (payload) => {
            console.log('Message received in foreground. ', payload);
            const title = payload.notification?.title || 'New Notification';
            const body = payload.notification?.body || '';
            const actionUrl = payload.data?.action_url || 'https://staging.bizzcrm.com/';
            
            // Immediately update UI counts since we got a new notification
            if (typeof window.fetchNotificationCounts === 'function') {
                window.fetchNotificationCounts();
            }
            
            if (typeof toastr !== 'undefined') {
                toastr.info(body, title, {
                    onclick: function() { window.open(actionUrl, '_blank'); }
                });
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: body,
                    icon: 'info',
                    confirmButtonText: 'View',
                    showCancelButton: true,
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) window.open(actionUrl, '_blank');
                });
            } else {
                if (confirm(title + '\n' + body + '\n\nClick OK to view.')) {
                    window.open(actionUrl, '_blank');
                }
            }
        });
    }
};

window.requestFirebaseToken = function() {
    // Request permission and get token
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            getToken(messaging, { vapidKey: 'YOUR_VAPID_KEY_HERE' }) // NOTE: vapidKey is optional but recommended
                .then((currentToken) => {
                    if (currentToken) {
                        console.log('FCM Token:', currentToken);
                        // Send the token to your server and update the UI if necessary
                        // e.g. axios.post('/api/device/register-fcm-token', { fcm_token: currentToken, device_id: 'web' })
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
        } else {
            console.log('Unable to get permission to notify.');
        }
    });
};
