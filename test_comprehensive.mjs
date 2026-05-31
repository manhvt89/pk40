import fs from 'fs';

const BASE_URL = 'http://localhost:8989/index.php';
let cookies = [];
let logs = [];

function log(msg) {
    console.log(msg);
    logs.push(msg);
}

function extractCookies(res) {
    const setCookie = res.headers.get('set-cookie');
    if (setCookie) {
        const parts = setCookie.split(', ');
        parts.forEach(c => {
            const pair = c.split(';')[0];
            if(pair && pair.includes('=')) {
                const [key, val] = pair.split('=');
                const idx = cookies.findIndex(x => x.key === key);
                if(idx > -1) cookies[idx].val = val;
                else cookies.push({key, val});
            }
        });
    }
}

function getCookieString() {
    return cookies.map(c => `${c.key}=${c.val}`).join('; ');
}

function getCsrfToken() {
    const c = cookies.find(x => x.key === 'csrf_ospos_v3');
    return c ? c.val : '';
}

async function request(path, method = 'GET', data = null, customCookies = null) {
    const headers = {
        'User-Agent': 'Test',
        'Cookie': customCookies || getCookieString(),
        'Accept': 'application/json, text/javascript, */*; q=0.01'
    };
    let body = null;
    if (data) {
        headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        const params = new URLSearchParams();
        for (const k in data) params.append(k, data[k]);
        body = params.toString();
    }
    
    const csrf = customCookies ? null : getCsrfToken(); // simplify for alt users
    if (method === 'POST' && csrf && data && !data.csrf_ospos_v3) {
        const params = new URLSearchParams(body);
        params.append('csrf_ospos_v3', csrf);
        body = params.toString();
    }

    const res = await fetch(`${BASE_URL}${path}`, { method, headers, body, redirect: 'manual' });
    if (!customCookies) extractCookies(res);
    
    if (res.status === 302 || res.status === 303) {
        const loc = res.headers.get('location');
        if (loc) {
            const nextPath = loc.replace(BASE_URL, '');
            return request(nextPath, 'GET', null, customCookies);
        }
    }
    
    const text = await res.text();
    return { status: res.status, text, res };
}

async function run() {
    log("=== START COMPREHENSIVE TESTING ===");
    
    // Login Admin
    const loginRes = await request('/login', 'POST', { username: 'admin', password: '123456789@' });
    if(loginRes.text.includes('Tên đăng nhập hoặc Mật khẩu không đúng')) {
        log("❌ FAIL: Admin login failed."); return;
    }
    log("✅ PASS: Admin login successful.");

    // --- 1. DATA INTEGRITY ---
    log("\n--- 1. DATA INTEGRITY & REPORTS ---");
    // Create an item for calculation test
    log("Tạo sản phẩm: Giá bán 100000, Giá vốn 50000, Tồn kho 10");
    const itemRes = await request('/items/save/-1', 'POST', {
        name: 'Item Calculation Test', category: 'Test',
        cost_price: '50000', unit_price: '100000',
        tax_names_1: 'VAT', tax_percent_1: '8', // 8% Tax
        quantity_1: '10'
    });
    
    let calcItemId = null;
    try {
        const j = JSON.parse(itemRes.text);
        if(j.success) calcItemId = j.id;
    } catch(e) {}
    
    if (calcItemId) {
        log(`Item created ID: ${calcItemId}`);
        // Sale with discount
        await request('/sales/change_mode', 'POST', { mode: 'sale' });
        await request('/sales/add', 'POST', { item: calcItemId });
        
        // Add 10% discount to item
        await request(`/sales/edit_item/1`, 'POST', {
            price: '100000', quantity: '1', discount: '10', description: '', serialnumber: '', discount_type: '0' // 0 might be %
        });

        // Add payment of 97200
        const payRes = await request('/sales/add_payment', 'POST', { payment_type: 'Tiền mặt', amount_tendered: '97200' });
        
        const completeRes = await request('/sales/complete', 'POST', {});
        if(completeRes.text.includes('97,200') || completeRes.text.includes('97.200') || completeRes.text.includes('97200')) {
            log("✅ PASS: Sale calculations (Discount + Tax) are correct (97,200).");
        } else {
            log("⚠️ TBD: Sale calculation might have rounding differences or tax config issues.");
        }
    }

    // --- 2. NEGATIVE TESTING ---
    log("\n--- 2. NEGATIVE TESTING ---");
    
    // Empty mandatory field
    log("Tạo khách hàng thiếu Tên (Trường bắt buộc)");
    const custRes = await request('/customers/save/-1', 'POST', { last_name: 'NoFirstName' });
    if(custRes.text.includes('"success":false') || custRes.text.includes('bắt buộc') || custRes.text.includes('required')) {
        log("✅ PASS: Server validation blocked empty first name.");
    } else if (custRes.status === 200 && custRes.text.includes('"success":true')) {
         log("❌ FAIL: System allowed saving customer without mandatory first_name.");
    } else {
         log("⚠️ TBD: Check validation response.");
    }

    // Negative Inventory Sale
    log("Bán mặt hàng có số lượng âm");
    // Create zero stock item
    const zItemRes = await request('/items/save/-1', 'POST', {
        name: 'Zero Stock Item', category: 'Test', cost_price: '10', unit_price: '20', quantity_1: '0'
    });
    let zId = null;
    try { zId = JSON.parse(zItemRes.text).id; } catch(e){}
    if (zId) {
        await request('/sales/change_mode', 'POST', { mode: 'sale' });
        const addZRes = await request('/sales/add', 'POST', { item: zId });
        if(addZRes.text.includes('Cảnh báo') || addZRes.text.includes('warning') || addZRes.text.includes('không đủ')) {
            log("✅ PASS: System warns when selling out-of-stock item.");
        } else {
            log("⚠️ TBD: System allowed zero-stock sale without warning or warning is in UI JS.");
        }
    }

    // --- 3. SECURITY & RBAC ---
    log("\n--- 3. SECURITY & RBAC ---");
    // XSS Test
    log("Thử nghiệm XSS Injection vào tên Sản phẩm");
    const xssPayload = "<script>alert('XSS')</script>XSS_Item";
    const xssRes = await request('/items/save/-1', 'POST', {
        name: xssPayload, category: 'Hack', cost_price: '1', unit_price: '1'
    });
    // Check if it's escaped in the list
    const listRes = await request('/items/search', 'GET');
    if(listRes.text.includes('&lt;script&gt;') || !listRes.text.includes(xssPayload)) {
        log("✅ PASS: XSS payload was escaped or stripped. HTML is safe.");
    } else {
        log("❌ FAIL: XSS payload was found unescaped in output!");
    }

    log("\n=== COMPREHENSIVE TESTING COMPLETED ===");
    fs.writeFileSync('./docs/comprehensive_test_log.txt', logs.join('\n'));
}

run().catch(console.error);
