// Detect broadcasting driver from environment
const broadcaster = import.meta.env.VITE_BROADCAST_CONNECTION || 'pusher';

// Echo must be created exactly once on window before Alpine x-init runs.
// This file is imported from resources/js/app.js, so it should load early in Vite.
try {
    if (typeof window.Echo !== 'undefined' && typeof window.Echo.private === 'function') {
        // Already initialized by a layout (fallback). Do nothing.
        console.log('📡 Echo already initialized');
    } else if (broadcaster === 'reverb') {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
            wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });
        console.log('📡 Using Reverb for real-time updates');
    } else {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
            forceTLS: true,
        });
        console.log('📡 Using Pusher for real-time updates');
    }
} catch (e) {
    console.error('❌ Echo initialization failed:', e);
    // Ensure Alpine guards won't crash.
    window.Echo = undefined;
}

