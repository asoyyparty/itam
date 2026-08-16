const CACHE_NAME = 'itam-pwa-cache-v3';
const STATIC_ASSETS = [
  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.min.js'
];

// Install Event - Pre-cache only core static assets (NO HTML PAGES OR LOGIN FORMS)
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
});

// Activate Event - Instantly purge all old caches (v1, v2, etc.)
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('[ServiceWorker] Purging outdated cache:', cache);
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event - Bypasses HTML navigation/login completely so Chrome Mobile always gets live responses
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // 1. ALWAYS BYPASS CACHE for HTML Page Navigations, Login, Logout, Dashboard & Dynamic Routes
  if (
    event.request.mode === 'navigate' ||
    url.pathname.includes('/login') ||
    url.pathname.includes('/logout') ||
    url.pathname.includes('/dashboard') ||
    !url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|gif|ico|woff|woff2|ttf|eot)$/i)
  ) {
    event.respondWith(fetch(event.request));
    return;
  }

  // 2. Network-First Strategy ONLY for static media and CSS/JS assets
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});
