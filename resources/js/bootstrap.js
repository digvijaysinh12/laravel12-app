import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// --- Real-time: Laravel Echo + Pusher ---
window.Pusher = Pusher;

const wsHost = import.meta.env.VITE_PUSHER_HOST || `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`;
const wsPort = import.meta.env.VITE_PUSHER_PORT || 443;
const useTLS = (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost,
    wsPort,
    wssPort: wsPort,
    forceTLS: useTLS,
    enabledTransports: ['ws', 'wss'],
});
