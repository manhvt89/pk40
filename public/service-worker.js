self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('clinic-cache').then((cache) => {
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
                    return caches.open('clinic-cache').then((cache) => {
                        cache.put(event.request, fetchResponse.clone());
                        return fetchResponse;
                    });
                });
            })
        );
    }
});