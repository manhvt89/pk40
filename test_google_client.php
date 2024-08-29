<?php 
// test_google_client.php
require 'vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;


putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/application/cert/atvtts-fad5e0e1c8df.json');

$client = new TextToSpeechClient();
echo "Google Client loaded successfully!";
