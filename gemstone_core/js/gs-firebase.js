// Import the functions you need from the SDKs you need
import {initializeApp} from "https://www.gstatic.com/firebasejs/9.6.6/firebase-app.js";
import {getAnalytics} from "https://www.gstatic.com/firebasejs/9.6.6/firebase-analytics.js";
import {getMessaging, getToken, onMessage} from "https://www.gstatic.com/firebasejs/9.6.6/firebase-messaging.js";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyAyjqMKXPrrvntwQ_0eRweL45gsKj5nV1I",
    authDomain: "gemstone-277007.firebaseapp.com",
    projectId: "gemstone-277007",
    storageBucket: "gemstone-277007.appspot.com",
    messagingSenderId: "806366596515",
    appId: "1:806366596515:web:f1cd72d8c61606c09d5e16",
    measurementId: "G-LN1HPPMKGC"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const messaging = getMessaging();
getToken(messaging, {vapidKey: 'BDrsf89K29PCS2GKuCAA-CLhsrOD3c4QNTsEQvEuCKs2Nyg9PGorelBq7u2hcZpjXsI0K9l-wwwzqMG8yZFjB14'}).then((currentToken) => {
    if (currentToken) {
        console.log("Token found:" + currentToken);
        Gemstone.firebaseToken = currentToken;
    } else {
        // Show permission request UI
        console.log('No registration token available. Request permission to generate one.');
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
});

onMessage(messaging, (payload) => {
    console.log('Message received. ', payload);
    // ...
});