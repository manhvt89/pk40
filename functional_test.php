<?php
$baseUrl = 'http://localhost:8989';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// 1. Get Login Page
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
$html = curl_exec($ch);
$csrf = '';
if (preg_match('/name="csrf_[a-zA-Z0-9_]+" value="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    preg_match('/name="(csrf_[a-zA-Z0-9_]+)"/', $html, $name_matches);
    $csrf_name = $name_matches[1];
}

// 2. Login
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login'); // OSPOS might use /login to submit
$post_data = [
    'username' => 'admin',
    'password' => 'pointofsale'
];
if ($csrf) {
    $post_data[$csrf_name] = $csrf;
}
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Login status: " . $info['http_code'] . "\n";
echo "Login redirect URL: " . $info['url'] . "\n";

// 3. Check Customers
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/customers');
curl_setopt($ch, CURLOPT_POST, false);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Customers status: " . $info['http_code'] . "\n";
echo "Customers page length: " . strlen($response) . " bytes\n";

// 4. Check Items
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/items');
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Items status: " . $info['http_code'] . "\n";
echo "Items page length: " . strlen($response) . " bytes\n";

// 5. Check Sales
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/sales');
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Sales status: " . $info['http_code'] . "\n";
echo "Sales page length: " . strlen($response) . " bytes\n";

// 6. Check Reports
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/reports');
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Reports status: " . $info['http_code'] . "\n";
echo "Reports page length: " . strlen($response) . " bytes\n";

// 7. Logout
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/home/logout');
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo "Logout status: " . $info['http_code'] . "\n";
