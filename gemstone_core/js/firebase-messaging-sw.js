// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyAyjqMKXPrrvntwQ_0eRweL45gsKj5nV1I",
    authDomain: "gemstone-277007.firebaseapp.com",
    projectId: "gemstone-277007",
    storageBucket: "gemstone-277007.appspot.com",
    messagingSenderId: "806366596515",
    appId: "1:806366596515:web:f1cd72d8c61606c09d5e16",
    measurementId: "G-LN1HPPMKGC"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    //console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/template_gemstone/gemstone_core/image/gs-logo.png'
    };

    self.registration.showNotification(notificationTitle,
            notificationOptions);
});
