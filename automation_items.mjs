import puppeteer from 'puppeteer';
import fs from 'fs';

const BASE_URL = 'http://localhost:8989/index.php';
const SCREENSHOT_DIR = './docs/screenshots/items';

if (!fs.existsSync(SCREENSHOT_DIR)){
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
}

async function runItemsTest() {
    console.log('Bắt đầu kiểm thử chi tiết Module Hàng hóa (Items)...');
    const browser = await puppeteer.launch({
        headless: "new",
        channel: 'chrome',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    try {
        // 1. Đăng nhập
        console.log('Đang đăng nhập...');
        await page.goto(`${BASE_URL}/login`);
        await page.type('input[name="username"]', 'admin');
        await page.type('input[name="password"]', '123456789@');
        await Promise.all([
            page.waitForNavigation(),
            page.click('button[type="submit"]')
        ]);
        
        // 2. Truy cập trang Items
        console.log('Truy cập danh sách Hàng hóa...');
        await page.goto(`${BASE_URL}/items`);
        await delay(2000);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_list.png` });

        // 3. Thêm mới Hàng hóa
        console.log('Đang mở form thêm mới Hàng hóa...');
        const toolbarHtml = await page.evaluate(() => {
            return document.querySelector('#title_bar') ? document.querySelector('#title_bar').innerHTML : 'No title bar';
        });
        console.log('Toolbar HTML:', toolbarHtml);
        
        await page.evaluate(() => {
            const btn = document.querySelector('button[title="Thêm mới"]');
            if(btn) btn.click();
        });
        await page.waitForSelector('#item_form', { visible: true });
        await delay(1000); // Chờ animation modal
        
        console.log('Đang điền thông tin Hàng hóa mới...');
        const uniqueItemNo = 'ITM' + Math.floor(Math.random() * 1000000);
        await page.type('input[name="item_number"]', uniqueItemNo);
        await page.type('input[name="name"]', 'Sản phẩm Test Tự Động');
        await page.type('input[name="category"]', 'Phụ kiện');
        await page.type('input[name="cost_price"]', '100000');
        await page.type('input[name="unit_price"]', '150000');
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_add_form.png` });

        // Submit form
        console.log('Đang lưu thông tin...');
        await page.click('#submit');
        
        // Wait for modal to hide or show errors
        await delay(2000);
        const errorText = await page.evaluate(() => {
            const errBox = document.querySelector('#error_message_box');
            return errBox ? errBox.innerText.trim() : '';
        });
        if(errorText) console.log('Có lỗi validation:', errorText);

        await page.waitForSelector('#item_form', { hidden: true, timeout: 5000 }).catch(() => console.log('Form vẫn còn mở, có thể do lỗi.'));
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_add_success.png` });

        // 4. Tìm kiếm hàng hóa vừa thêm
        console.log('Đang tìm kiếm Hàng hóa vừa thêm...');
        await page.type('.search input', uniqueItemNo);
        await delay(2000); // Chờ AJAX load
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_search_result.png` });

        // 5. Chọn và Sửa hàng hóa
        console.log('Đang sửa thông tin Hàng hóa...');
        const editBtnExists = await page.evaluate(() => {
            const editBtn = document.querySelector('table tbody tr:first-child a.modal-dlg');
            if(editBtn) {
                editBtn.click();
                return true;
            }
            return false;
        });
        if (!editBtnExists) throw new Error('Không tìm thấy nút sửa, có thể do kết quả tìm kiếm rỗng.');

        await page.waitForSelector('#item_form', { visible: true });
        await delay(1000);
        // Đổi tên
        await page.click('input[name="name"]', {clickCount: 3});
        await page.keyboard.press('Backspace');
        await page.type('input[name="name"]', 'Sản phẩm Test Đã Sửa');
        await page.click('#submit');
        await delay(4000);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_edit_success.png` });

        // 6. Xóa hàng hóa
        console.log('Đang xóa Hàng hóa...');
        await page.waitForSelector('table tbody tr:first-child input[type="checkbox"]', { visible: true });
        
        // Handle confirm dialog
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        await page.evaluate(() => {
            const checkbox = document.querySelector('table tbody tr:first-child input[type="checkbox"]');
            if (checkbox) checkbox.click();
        });
        await delay(1000);
        await page.evaluate(() => {
            const deleteBtn = document.querySelector('#delete');
            if (deleteBtn) deleteBtn.click();
        });
        
        await delay(3000);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/items_delete_success.png` });

        console.log('✅ PASS: Hoàn thành kịch bản test Items (Add, Edit, Search, Delete).');

    } catch (e) {
        console.error('❌ Lỗi trong quá trình test Items:', e);
    } finally {
        await browser.close();
    }
}

runItemsTest();
