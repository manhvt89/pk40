import puppeteer from 'puppeteer';
import fs from 'fs';

const BASE_URL = 'http://localhost:8989/index.php';
const SCREENSHOT_DIR = './docs/screenshots';

if (!fs.existsSync(SCREENSHOT_DIR)){
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
}

async function runCustomerTest() {
    console.log('Bắt đầu kiểm thử chi tiết Module Khách hàng (Customers)...');
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
        
        // 2. Truy cập trang Khách hàng
        console.log('Truy cập danh sách Khách hàng...');
        await page.goto(`${BASE_URL}/customers`);
        await delay(1000);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_list.png` });

        // 3. Thêm mới Khách hàng
        console.log('Đang mở form thêm mới Khách hàng...');
        await page.evaluate(() => {
            const btns = Array.from(document.querySelectorAll('button.modal-dlg'));
            const newBtn = btns.find(b => b.title && b.title.includes('Khách hàng mới'));
            if(newBtn) newBtn.click();
            else if(btns.length > 0) btns[btns.length - 1].click();
        });
        await page.waitForSelector('#customer_form', { visible: true });
        await delay(1000); // Chờ animation modal
        
        console.log('Đang điền thông tin khách hàng mới...');
        const uniquePhone = '098' + Math.floor(Math.random() * 10000000);
        await page.type('input[name="first_name"]', 'Test Tự Động');
        await page.type('input[name="age"]', '25'); // Có thể là năm hoặc ngày sinh tùy config
        await page.type('input[name="phone_number"]', uniquePhone);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_add_form.png` });

        // Submit form
        console.log('Đang lưu thông tin...');
        await page.click('#submit');
        await delay(2000); // Chờ lưu
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_add_success.png` });

        // 4. Tìm kiếm khách hàng vừa thêm
        console.log('Đang tìm kiếm khách hàng vừa thêm...');
        await page.type('.search input', uniquePhone);
        await delay(2000); // Chờ AJAX load
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_search_result.png` });

        // 5. Chọn và Sửa khách hàng
        console.log('Đang sửa thông tin khách hàng...');
        // Click vào nút sửa ở dòng đầu tiên
        await page.evaluate(() => {
            const editBtn = document.querySelector('table tbody tr:first-child a.modal-dlg');
            if(editBtn) editBtn.click();
        });
        await page.waitForSelector('#customer_form', { visible: true });
        await delay(1000);
        // Đổi tên
        await page.click('input[name="first_name"]', {clickCount: 3});
        await page.keyboard.press('Backspace');
        await page.type('input[name="first_name"]', 'Test Đã Sửa');
        await page.click('#submit');
        await delay(4000); // Chờ table load lại sau khi submit
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_edit_success.png` });

        // 6. Xóa khách hàng
        console.log('Đang xóa khách hàng...');
        await page.waitForSelector('table tbody tr:first-child input[type="checkbox"]', { visible: true });
        await page.evaluate(() => {
            const checkbox = document.querySelector('table tbody tr:first-child input[type="checkbox"]');
            if (checkbox) checkbox.click();
        });
        await delay(1000);
        // Handle confirm dialog
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        await page.evaluate(() => {
            const deleteBtn = document.querySelector('#delete');
            if (deleteBtn) deleteBtn.click();
        });
        await delay(3000);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/customers_delete_success.png` });

        console.log('✅ PASS: Hoàn thành kịch bản test Customers (Add, Edit, Search, Delete).');

    } catch (e) {
        console.error('❌ Lỗi trong quá trình test Customers:', e);
    } finally {
        await browser.close();
    }
}

runCustomerTest();
