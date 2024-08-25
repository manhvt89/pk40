const CACHE_NAME = 'pk40-cache-v21'; // Cập nhật phiên bản cache
const DATA_CACHE_NAME = 'pk40-data-cache-v21';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('pk40-cache-v2').then((cache) => {
            return cache.addAll([
                '/',
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.url.includes('/images/') || event.request.url.includes('/dist/')) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request).then((fetchResponse) => {
                    return caches.open('pk40-cache-v2').then((cache) => {
                        cache.put(event.request, fetchResponse.clone());
                        return fetchResponse;
                    });
                });
            })
        );
    }
});

self.addEventListener('activate', (event) => {
    const cacheWhitelist = [CACHE_NAME, DATA_CACHE_NAME]; // Danh sách cache hiện tại

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        // Xóa cache không nằm trong whitelist
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});