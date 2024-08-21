// app.js

// Kiểm tra xem Service Worker có được hỗ trợ không
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.ready.then((registration) => {
            console.log('Service Worker is ready.');
        }).catch((error) => {
            console.log('Service Worker registration failed: ', error);
        });
    });
}

// Bạn có thể thêm các tính năng khác ở đây