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
const dbName = 'ospos_db';
const dbVersion = 1;

let db;

const request = indexedDB.open(dbName, dbVersion);

request.onerror = function(event) {
    console.error('Database error:', event.target.errorCode);
};

request.onsuccess = function(event) {
    db = event.target.result;
    console.log('Database initialized.');
};

request.onupgradeneeded = function(event) {
    const db = event.target.result;
    db.createObjectStore('items', { keyPath: 'item_id' });
    db.createObjectStore('inventory', { keyPath: 'trans_id' });
};

async function syncData(page = 1, limit = 5000) {
    // Tạo URL cơ bản mà không cần tham số đường dẫn
    const url = `http://localhost:8888/api/products`;
    const csrf_ospos_v3 = csrf_token(); // Đảm bảo csrf_token() trả về giá trị chính xác
    console.log(csrf_ospos_v3);

    // Tạo một đối tượng FormData
    const formData = new FormData();
    formData.append('page', page);
    formData.append('limit', limit);
    formData.append('csrf_ospos_v3', csrf_ospos_v3);
    console.log('Bắt đầu thực hiện: page '+page);
    try {
        // Sử dụng fetch với phương thức POST để gửi tham số qua body
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer A991997@1234$#@!WwhQerty@',
                'X-CSRF-Token': csrf_ospos_v3 // Thêm CSRF token vào headers nếu cần
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
            localStorage.setItem('syncError', page);
        }

        const items = await response.json();
        console.log(items);

        if (items.length > 0) {
            await saveItemsToDB(items); // Chờ hàm lưu dữ liệu hoàn tất

            // Tiếp tục đồng bộ dữ liệu với trang tiếp theo
            console.log('Đồng bộ hoàn tất. OD: ' + page);
            return syncData(page+1, limit); // Đệ quy nhưng cần chú ý giới hạn số lần gọi
        } else {
            console.log('Đồng bộ hoàn tất.');
            localStorage.setItem('syncError', 'C');
        }
    } catch (error) {
        console.error('Lỗi khi đồng bộ dữ liệu:', error);
        localStorage.setItem('syncError', page);
    }
}

function saveItemsToDB(items) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['items'], 'readwrite');
        const objectStore = transaction.objectStore('items');

        items.forEach(item => {
            objectStore.put(item);
        });

        transaction.oncomplete = () => resolve();
        transaction.onerror = (event) => reject(event.target.errorCode);
    });
}

function saveInventoryToDB(inventoryItems) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['inventory'], 'readwrite');
        const objectStore = transaction.objectStore('inventory');

        inventoryItems.forEach(item => {
            objectStore.put(item);
        });

        transaction.oncomplete = () => resolve();
        transaction.onerror = (event) => reject(event.target.errorCode);
    });
}

async function syncInventory(page=1, limit = 5000) {
    const urlInventory = `http://localhost:8888/api/inventories`;
    const csrf_ospos_v3 = csrf_token();

    console.log(csrf_ospos_v3);

    try {
        const formDataInventory = new FormData();
        formDataInventory.append('page', page);
        formDataInventory.append('limit', limit);
        formDataInventory.append('csrf_ospos_v3', csrf_ospos_v3);

        const responseInventory = await fetch(urlInventory, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer A991997@1234$#@!WwhQerty@',
            },
            body: formDataInventory,
        });

        if (!responseInventory.ok) {
            throw new Error('Network response for inventory was not ok');
            localStorage.setItem('syncErrorInventory', page);
        }

        const _aInventory = await responseInventory.json();
        console.log(_aInventory);

        if (_aInventory.length > 0) {
            await saveInventoryToDB(_aInventory);

            console.log('Đồng bộ inventory hoàn tất. page: ' + page);
            await syncInventory(page+1, limit); // Đệ quy tiếp tục đồng bộ inventory
        } else {
            console.log('Đồng bộ inventory hoàn tất.');
            localStorage.setItem('syncErrorInventory', 'C');
        }
    } catch (error) {
        console.error('Lỗi khi đồng bộ inventory:', error);
        localStorage.setItem('syncErrorInventory', page);
    }
}



let syncErrorInventory = localStorage.getItem('syncErrorInventory') || 1;
let syncError = localStorage.getItem('syncError') || 1;
// Gọi hàm đồng bộ khi tải trang
if(syncError == 'C')
{
  console.log('Đã hoàn thành, không load dữ liệu về');
} else {
  console.log('Bắt đầu thực hiện trang: '+ syncError);
  syncData(Number(syncError), 1000);
}
if(syncErrorInventory == 'C')
{
  console.log('Đã hoàn thành, không load dữ chi tiết kho  về');
} else {
    console.log('Bắt đầu thực hiện trang: '+ syncErrorInventory);
    syncInventory(Number(syncErrorInventory), 5000);
}

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(dbName, dbVersion);

        request.onupgradeneeded = function(event) {
            db = event.target.result;
            if (!db.objectStoreNames.contains('items')) {
                db.createObjectStore('items', { keyPath: 'id' });
            }
        };

        request.onsuccess = function(event) {
            db = event.target.result;
            resolve(db);
        };

        request.onerror = function(event) {
            reject(new Error('Database error: ' + event.target.errorCode));
        };
    });
}

async function fetchItemsFromIndexedDB(offset = 0, limit = 10) {
  await openDatabase();
  return new Promise((resolve, reject) => {
        if (!db) {
            return reject(new Error('Database not initialized'));
        }

        const transaction = db.transaction(['items'], 'readonly');
        const objectStore = transaction.objectStore('items');
        const items = [];
        let currentIndex = 0;

        const request = objectStore.openCursor();
        request.onsuccess = function(event) {
            const cursor = event.target.result;
            if (cursor) {
                if (currentIndex >= offset && items.length < limit) {
                    items.push(cursor.value);
                }
                currentIndex++;
                cursor.continue();
            } else {
                resolve(items);
            }
        };

        request.onerror = function(event) {
            reject(new Error('Error fetching items from IndexedDB: ' + event.target.errorCode));
        };
    });
}

async function prepareDataForTable(offset, pageSize) {
    console.log("offset" + offset)
    console.log("pageSize" + pageSize)
    try {
        const items = await fetchItemsFromIndexedDB(offset, pageSize);

        // Dữ liệu được chuẩn bị để phù hợp với header đã định nghĩa
        const tableData = items.map(item => ({
            item_id: item.item_id,
            item_number: item.item_number,
            name: item.name,
            category: item.category,
            company_name: item.company_name,
            cost_price: item.cost_price,
            unit_price: item.unit_price,
            quantity: item.quantity,
            tax_percents: item.tax_percents,
            standard_amount: item.standard_amount,
            inventory: '', // Chưa rõ định dạng của `inventory`, cần xử lý thêm nếu cần
            stock: '' // Tương tự như trên
        }));

        return tableData;

    } catch (error) {
        console.error('Lỗi khi chuẩn bị dữ liệu cho bảng:', error);
        return [];
    }
}

async function getTotalItemsCount() {
    await openDatabase();
    return new Promise((resolve, reject) => {
        if (!db) {
            return reject(new Error('Database not initialized'));
        }

        const transaction = db.transaction(['items'], 'readonly');
        const objectStore = transaction.objectStore('items');
        const request = objectStore.count();

        request.onsuccess = function(event) {
            resolve(event.target.result); // Trả về tổng số bản ghi
        };

        request.onerror = function(event) {
            reject(new Error('Error fetching total count from IndexedDB: ' + event.target.errorCode));
        };
    });
}
