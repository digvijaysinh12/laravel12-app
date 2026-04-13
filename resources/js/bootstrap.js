import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

window.Pusher = Pusher;

const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';
const wsHost = import.meta.env.VITE_PUSHER_HOST || `ws-${cluster}.pusher.com`;
const wsPort = Number(import.meta.env.VITE_PUSHER_PORT || 443);
const forceTLS = (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster,
    wsHost,
    wsPort,
    wssPort: wsPort,
    forceTLS,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
    },
});          