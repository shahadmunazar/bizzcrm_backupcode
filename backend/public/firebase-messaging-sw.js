importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging-compat.js');

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp({
  apiKey: "AIzaSyBwH_meBqlrMVrVqND9v9Nw7bcpZNDOn_w",
  authDomain: "shahad-a9b2f.firebaseapp.com",
  projectId: "shahad-a9b2f",
  storageBucket: "shahad-a9b2f.firebasestorage.app",
  messagingSenderId: "118598765196",
  appId: "1:118598765196:web:4b4e2621bfc4b6ff5649a9",
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log(
    '[firebase-messaging-sw.js] Received background message ',
    payload
  );
  // Customize notification here
  const notificationTitle = payload.notification?.title || 'Background Message Title';
  const notificationOptions = {
    body: payload.notification?.body || 'Background Message body.',
    icon: '/firebase-logo.png',
    data: {
      url: payload.data?.action_url || 'https://staging.bizzcrm.com/'
    }
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  // Get URL from payload or default to staging
  let click_action_url = 'https://staging.bizzcrm.com/';
  if (event.notification.data && event.notification.data.url) {
      click_action_url = event.notification.data.url;
  }
  
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then( windowClients => {
      // Check if there is already a window/tab open with the target URL
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        // If so, just focus it.
        if (client.url === click_action_url && 'focus' in client) {
          return client.focus();
        }
      }
      // If not, then open the target URL in a new window/tab.
      if (clients.openWindow) {
        return clients.openWindow(click_action_url);
      }
    })
  );
});
