import puppeteer from 'puppeteer';
import fs from 'fs';

(async () => {
    console.log('Starting Customer Care Filter Test...');
    const browser = await puppeteer.launch({
        headless: "new",
        channel: 'chrome',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();

    try {
        console.log('Logging in...');
        await page.goto('http://localhost:8989/login', { waitUntil: 'networkidle2' });
        await page.type('input[name="username"]', 'admin');
        await page.type('input[name="password"]', '123456789@');
        await Promise.all([
            page.click('input[type="submit"], button[type="submit"], input[name="loginButton"]'),
            page.waitForNavigation({ waitUntil: 'networkidle2' }),
        ]);

        console.log('Fetching customer care with amount filter...');
        // Test amount_from=500000 & amount_to=1500000
        const url = 'http://localhost:8989/customer_care/search?status=new&amount_from=500000&amount_to=1500000&limit=200';
        await page.goto(url, { waitUntil: 'networkidle2' });
        
        // The response should be a JSON string since it's an API endpoint.
        const bodyText = await page.evaluate(() => document.body.innerText);
        
        console.log('Response body snippet (first 200 chars):', bodyText.substring(0, 200));
        
        const json = JSON.parse(bodyText);
        console.log(`Total rows reported by API: ${json.total}`);
        console.log(`Rows returned in this page: ${json.rows.length}`);
        
        let valid = true;
        for (let row of json.rows) {
            // total_amount is returned as string like "1,000,000 ₫", we need to parse it
            let amountStr = row.total_amount.replace(/[^0-9]/g, '');
            let amount = parseInt(amountStr);
            console.log(`Customer: ${row.name}, Amount: ${amount}`);
            if (amount < 500000 || amount > 1500000) {
                console.error(`ERROR: Found amount ${amount} which is out of range 500000-1500000!`);
                valid = false;
            }
        }
        
        if (valid) {
            console.log('SUCCESS: All rows match the filter correctly!');
        } else {
            console.log('FAILED: Some rows do not match the filter.');
        }

    } catch (e) {
        console.error('Test Failed:', e);
    } finally {
        await browser.close();
        console.log('Browser closed.');
    }
})();
