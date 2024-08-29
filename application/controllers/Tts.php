<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class Tts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //echo $this->config->item('composer_autoload');
        require $this->config->item('composer_autoload');
    }

    public function index() {
        $this->load->view('tts_view');
    }

    public function synthesize() {
        // Load Google API Client library
        

        $text = $this->input->post('text');

        // Thiết lập cấu hình API key của bạn
        $client = new TextToSpeechClient([
            'credentials' => APPPATH.'cert/atvtts-fad5e0e1c8df.json'
        ]);

        // Xây dựng yêu cầu
        $input_text = (new SynthesisInput())
            ->setText($text);

        // Thiết lập thông tin giọng đọc
        $voice = (new VoiceSelectionParams())
            ->setLanguageCode('vi-VN')
            ->setName('vi-VN-Standard-A');

        // Thiết lập định dạng âm thanh đầu ra
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3);

        // Gọi API
        $response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);

        // Lấy kết quả âm thanh trả về từ API
        $audioContent = $response->getAudioContent();

        // Trả về dữ liệu âm thanh để phát
        header('Content-Type: audio/mpeg');
        echo $audioContent;

        // Đóng client
        $client->close();
    }
}
