import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Echo from "laravel-echo"
import Pusher from 'pusher-js';

//Pusher.logToConsole = true;
/*window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'app-key',
    encrypted: false,
    cluster: 'eu',
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    enableStats: true,
    forceTLS: window.location.hostname !== '127.0.0.1',
    enabledTransports: ['ws', 'wss'],
});*/
