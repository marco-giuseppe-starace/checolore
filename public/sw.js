// Minimal service worker — its only job is to exist (a registered SW is
// what makes the browser offer "Install app") and keep the app shell
// reachable if the network drops mid-session.
const CACHE = 'checolore-shell-v1';
const SHELL = ['/', '/manifest.json', '/pwa-icons/icon-192.png', '/pwa-icons/icon-512.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(SHELL)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Never cache API calls — data must always be fresh.
    if (event.request.url.includes('/api/')) {
        return;
    }

    // Navigations: try the network first, fall back to the cached shell
    // when offline.
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match('/'))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached ?? fetch(event.request))
    );
});
