import puppeteer from 'puppeteer';
import fs from 'fs';

const BASE_URL = 'http://localhost:8989/index.php';
const SCREENSHOT_DIR = './docs/screenshots';

// Đảm bảo thư mục screenshots tồn tại
if (!fs.existsSync(SCREENSHOT_DIR)){
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

const features = [
    { name: 'Dashboard', path: '/home' },
    { name: 'Khách hàng', path: '/customers' },
    { name: 'Hàng hóa', path: '/items' },
    { name: 'Gói hàng hóa', path: '/item_kits' },
    { name: 'Nhà cung cấp', path: '/suppliers' },
    { name: 'Bán hàng', path: '/sales' },
    { name: 'Nhập hàng', path: '/receivings' },
    { name: 'Đặt hàng', path: '/purchases' },
    { name: 'Nhân viên', path: '/employees' },
    { name: 'Phân quyền', path: '/roles' },
    { name: 'Chấm công', path: '/attendances' },
    { name: 'Kiểm kê', path: '/oincs' },
    { name: 'Cấu hình', path: '/config' },
    { name: 'Báo cáo tổng quan', path: '/reports' },
    { name: 'Báo cáo - Tóm tắt doanh thu', path: '/reports/date_input_sales' },
    { name: 'Báo cáo - Nhập hàng', path: '/reports/date_input_recv' },
    { name: 'Báo cáo - Tồn kho', path: '/reports/inventory_summary_input' },
    { name: 'Báo cáo - Hàng sắp hết', path: '/reports/inventory_low' },
    { name: 'Báo cáo - KH cụ thể', path: '/reports/specific_customer_input' },
    { name: 'Báo cáo - NV cụ thể', path: '/reports/specific_employee_input' },
    { name: 'Báo cáo - Chiết khấu', path: '/reports/specific_discount_input' },
    { name: 'Báo cáo - Cộng tác viên', path: '/reports/specific_ctvs_input' },
];

async function runTests() {
    console.log('Bắt đầu quy trình kiểm thử toàn diện...');
    const browser = await puppeteer.launch({
        headless: "new",
        channel: 'chrome',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });
    
    let results = [];

    try {
        // Đăng nhập
        console.log('Đang đăng nhập...');
        await page.goto(`${BASE_URL}/login`);
        await page.type('input[name="username"]', 'admin');
        await page.type('input[name="password"]', '123456789@');
        await Promise.all([
            page.waitForNavigation(),
            page.click('button[type="submit"]')
        ]);
        
        console.log('Đăng nhập thành công!');

        // Lặp qua từng tính năng để test
        for (const feature of features) {
            console.log(`Đang kiểm tra: ${feature.name} (${feature.path})`);
            try {
                const response = await page.goto(`${BASE_URL}${feature.path}`);
                const status = response.status();
                
                // Đợi load giao diện
                await new Promise(r => setTimeout(r, 1000));
                
                const title = await page.title();
                const screenshotPath = `${SCREENSHOT_DIR}/${feature.path.replace(/\//g, '_')}.png`;
                await page.screenshot({ path: screenshotPath });

                // Kiểm tra lỗi PHP trên màn hình
                const bodyText = await page.evaluate(() => document.body.innerText);
                let hasPhpError = bodyText.includes('A PHP Error was encountered') || bodyText.includes('Fatal error');
                
                if (status === 200 && !hasPhpError) {
                    results.push({ feature: feature.name, path: feature.path, status: 'PASS', title: title, screenshot: screenshotPath, error: '' });
                    console.log(`-> PASS: ${feature.name}`);
                } else {
                    let errStr = hasPhpError ? 'PHP Error Found' : `HTTP Status: ${status}`;
                    results.push({ feature: feature.name, path: feature.path, status: 'FAIL', title: title, screenshot: screenshotPath, error: errStr });
                    console.log(`-> FAIL: ${feature.name} - ${errStr}`);
                }
            } catch (err) {
                results.push({ feature: feature.name, path: feature.path, status: 'ERROR', title: 'N/A', screenshot: 'N/A', error: err.message });
                console.log(`-> ERROR: ${feature.name} - ${err.message}`);
            }
        }
        
        // Đăng xuất
        console.log('Đang đăng xuất...');
        await page.goto(`${BASE_URL}/home/logout`);
        
    } catch (e) {
        console.error('Lỗi nghiêm trọng trong quá trình test:', e);
    } finally {
        await browser.close();
    }
    
    // Lưu kết quả ra file JSON
    fs.writeFileSync('test_results.json', JSON.stringify(results, null, 2));
    
    // Tạo file báo cáo Markdown
    let mdContent = `# Báo cáo Kết quả Kiểm thử Chi tiết Tính năng (Functional UI Testing)
**Ngày thực hiện:** ${new Date().toLocaleString()}

| Nhóm Tính Năng | Đường dẫn | Trạng Thái | Ghi chú / Lỗi | Ảnh chụp màn hình |
|---|---|---|---|---|
`;

    for (const r of results) {
        const statusEmoji = r.status === 'PASS' ? '✅ PASS' : (r.status === 'FAIL' ? '❌ FAIL' : '⚠️ ERROR');
        const errInfo = r.error ? r.error : 'Hoạt động tốt';
        mdContent += `| **${r.feature}** | \`${r.path}\` | ${statusEmoji} | ${errInfo} | [Xem ảnh](file:///e:/projects/pk401/pk40/${r.screenshot}) |\n`;
    }

    mdContent += `\n## Đánh giá chung:\n`;
    const passed = results.filter(r => r.status === 'PASS').length;
    mdContent += `- Tổng số tính năng kiểm tra: **${results.length}**\n`;
    mdContent += `- Số tính năng PASS: **${passed}**\n`;
    mdContent += `- Số tính năng FAIL/ERROR: **${results.length - passed}**\n`;

    fs.writeFileSync('./docs/kq_kiem_thu_tinh_nang.md', mdContent);
    console.log('Đã tạo báo cáo tại ./docs/kq_kiem_thu_tinh_nang.md');
}

runTests();
