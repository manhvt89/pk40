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

    public function list_waiting($parrams=0)
    {
        $rs = [
            ['id' => 1, 'name' => 'Mai Anh Bảo', 'dob' => '15/08/1999', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'B5 TTTK, Nguyễn Chí Thanh, Hà Nội', 'call_name'=>'Mời bệnh nhân Mai Anh Bảo'],
            ['id' => 2, 'name' => 'Nguyễn Văn An', 'dob' => '10/07/1998', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Ngõ 32, Đống Đa, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Văn An'],
            ['id' => 3, 'name' => 'Trần Thị Bích', 'dob' => '25/09/2000', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Số 5, Cầu Giấy, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Thị Bích'],
            ['id' => 4, 'name' => 'Phạm Minh Tuấn', 'dob' => '12/05/1987', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Số 8, Hoàn Kiếm, Hà Nội', 'call_name'=>'Mời bệnh nhân Phạm Minh Tuấn'],
            ['id' => 5, 'name' => 'Lê Thị Lan', 'dob' => '22/11/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Khu đô thị Mỹ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Thị Lan'],
            ['id' => 6, 'name' => 'Đỗ Thị Hồng', 'dob' => '30/06/1995', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Phố Vọng, Hà Nội', 'call_name'=>'Mời bệnh nhân Đỗ Thị Hồng'],
            ['id' => 7, 'name' => 'Ngô Văn Hải', 'dob' => '20/01/1988', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Số 12, Tây Hồ, Hà Nội', 'call_name'=>'Mời bệnh nhân Ngô Văn Hải'],
            ['id' => 8, 'name' => 'Hoàng Thị Mai', 'dob' => '16/09/1992', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Đại Mỗ, Hà Nội', 'call_name'=>'Mời bệnh nhân Hoàng Thị Mai'],
            ['id' => 9, 'name' => 'Vũ Minh Tâm', 'dob' => '04/12/1991', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Khu dân cư Nam Trung Yên, Hà Nội', 'call_name'=>'Mời bệnh nhân Vũ Minh Tâm'],
            ['id' => 10, 'name' => 'Trương Văn Hòa', 'dob' => '11/03/1985', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Quang Trung, Hà Nội', 'call_name'=>'Mời bệnh nhân Trương Văn Hòa'],
            ['id' => 11, 'name' => 'Phan Thị Bình', 'dob' => '27/07/1994', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Ngọc Hồi, Hà Nội', 'call_name'=>'Mời bệnh nhân Phan Thị Bình'],
            ['id' => 12, 'name' => 'Lưu Đình Dũng', 'dob' => '19/04/1993', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Vinhomes Riverside, Hà Nội', 'call_name'=>'Mời bệnh nhân Lưu Đình Dũng'],
            ['id' => 13, 'name' => 'Nguyễn Thị Lý', 'dob' => '21/08/1997', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Láng Hạ, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Lý'],
            ['id' => 14, 'name' => 'Trịnh Văn Hưng', 'dob' => '05/02/1989', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Phúc Diễn, Hà Nội', 'call_name'=>'Mời bệnh nhân Trịnh Văn Hưng'],
            ['id' => 15, 'name' => 'Đinh Thị Yến', 'dob' => '14/10/1992', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Bắc Từ Liêm, Hà Nội', 'call_name'=>'Mời bệnh nhân Đinh Thị Yến'],
            ['id' => 16, 'name' => 'Nguyễn Văn Bình', 'dob' => '28/11/1980', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Sài Đồng, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Văn Bình'],
            ['id' => 17, 'name' => 'Trần Thị Mai', 'dob' => '17/06/1996', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hà Đông, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Thị Mai'],
            ['id' => 18, 'name' => 'Lê Văn Trung', 'dob' => '03/05/1995', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Cổ Nhuế, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Văn Trung'],
            ['id' => 19, 'name' => 'Phạm Thị Ngọc', 'dob' => '24/12/1988', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Tân Triều, Hà Nội', 'call_name'=>'Mời bệnh nhân Phạm Thị Ngọc'],
            ['id' => 20, 'name' => 'Nguyễn Thị Thu', 'dob' => '02/09/1994', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hà Trì, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Thu'],
            ['id' => 21, 'name' => 'Lương Văn Đạt', 'dob' => '12/06/1989', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Ngọc Thụy, Hà Nội', 'call_name'=>'Mời bệnh nhân Lương Văn Đạt'],
            ['id' => 22, 'name' => 'Hoàng Thị Lan', 'dob' => '08/01/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Kim Mã, Hà Nội', 'call_name'=>'Mời bệnh nhân Hoàng Thị Lan'],
            ['id' => 23, 'name' => 'Vũ Thị Tuyết', 'dob' => '16/07/1993', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Thanh Xuân, Hà Nội', 'call_name'=>'Mời bệnh nhân Vũ Thị Tuyết'],
            ['id' => 24, 'name' => 'Trần Văn Công', 'dob' => '14/04/1997', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Đình Thôn, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Văn Công'],
            ['id' => 25, 'name' => 'Nguyễn Thị Hương', 'dob' => '30/05/1986', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Mỹ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Hương'],
            ['id' => 26, 'name' => 'Lê Thị Thu', 'dob' => '05/12/1991', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Nam Từ Liêm, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Thị Thu'],
            ['id' => 27, 'name' => 'Đỗ Văn Phúc', 'dob' => '11/01/1994', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Đại La, Hà Nội', 'call_name'=>'Mời bệnh nhân Đỗ Văn Phúc'],
            ['id' => 28, 'name' => 'Nguyễn Văn Hùng', 'dob' => '23/09/1989', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Láng, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Văn Hùng'],
            ['id' => 29, 'name' => 'Trần Thị Lệ', 'dob' => '04/08/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hà Đông, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Thị Lệ'],
            ['id' => 30, 'name' => 'Lê Văn Nam', 'dob' => '10/11/1985', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Khuất Duy Tiến, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Văn Nam'],
            ['id' => 31, 'name' => 'Nguyễn Thị Thúy', 'dob' => '19/02/1998', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hòa Bình, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Thúy'],
            ['id' => 32, 'name' => 'Trương Văn Đạt', 'dob' => '15/03/1987', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Cầu Giấy, Hà Nội', 'call_name'=>'Mời bệnh nhân Trương Văn Đạt'],
            ['id' => 33, 'name' => 'Hoàng Văn Sơn', 'dob' => '25/12/1991', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Số 4, Bồ Đề, Hà Nội', 'call_name'=>'Mời bệnh nhân Hoàng Văn Sơn'],
            ['id' => 34, 'name' => 'Vũ Thị Hòa', 'dob' => '22/11/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hạ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Vũ Thị Hòa'],
            ['id' => 35, 'name' => 'Nguyễn Văn Tuyển', 'dob' => '17/04/1986', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Láng Hạ, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Văn Tuyển'],
            ['id' => 36, 'name' => 'Lê Thị Thủy', 'dob' => '13/09/1993', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Nguyễn Trãi, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Thị Thủy'],
            ['id' => 37, 'name' => 'Trần Văn Bình', 'dob' => '20/10/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Thanh Xuân, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Văn Bình'],
            ['id' => 38, 'name' => 'Nguyễn Thị Thảo', 'dob' => '11/01/1987', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Bắc Từ Liêm, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Thảo'],
            ['id' => 39, 'name' => 'Lương Thị Hằng', 'dob' => '23/05/1995', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Tân Mai, Hà Nội', 'call_name'=>'Mời bệnh nhân Lương Thị Hằng'],
            ['id' => 40, 'name' => 'Trịnh Văn Thanh', 'dob' => '17/08/1994', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Mỹ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Trịnh Văn Thanh'],
            ['id' => 41, 'name' => 'Nguyễn Văn Tuấn', 'dob' => '05/04/1988', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Thái Hà, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Văn Tuấn'],
            ['id' => 42, 'name' => 'Lê Thị Minh', 'dob' => '22/02/1996', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Bắc Sơn, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Thị Minh'],
            ['id' => 43, 'name' => 'Trương Thị Bích', 'dob' => '10/03/1992', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Cổ Nhuế, Hà Nội', 'call_name'=>'Mời bệnh nhân Trương Thị Bích'],
            ['id' => 44, 'name' => 'Nguyễn Thị Ngọc', 'dob' => '12/12/1991', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Bến Cát, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Ngọc'],
            ['id' => 45, 'name' => 'Lê Văn Hùng', 'dob' => '27/11/1988', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Linh Đàm, Hà Nội', 'call_name'=>'Mời bệnh nhân Lê Văn Hùng'],
            ['id' => 46, 'name' => 'Trần Thị Hạnh', 'dob' => '21/04/1995', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Hạ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Thị Hạnh'],
            ['id' => 47, 'name' => 'Nguyễn Thị Dung', 'dob' => '08/06/1987', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Quang Trung, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Dung'],
            ['id' => 48, 'name' => 'Lương Văn Nam', 'dob' => '16/07/1989', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Mỹ Đình, Hà Nội', 'call_name'=>'Mời bệnh nhân Lương Văn Nam'],
            ['id' => 49, 'name' => 'Trần Văn Sơn', 'dob' => '30/08/1991', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Đại La, Hà Nội', 'call_name'=>'Mời bệnh nhân Trần Văn Sơn'],
            ['id' => 50, 'name' => 'Nguyễn Thị Hạnh', 'dob' => '11/09/1990', 'read' => '<button class="btn play_audio_btn btn-sm">Play</button>', 'add' => 'Đống Đa, Hà Nội', 'call_name'=>'Mời bệnh nhân Nguyễn Thị Hạnh'],
        ];
        
    
        // Định dạng JSON chính xác để Bootstrap Table đọc
        echo json_encode(['total' => count($rs), 'rows' => $rs]);
    }

    public function synthesize() {
        // Load Google API Client library
        

        $text = $this->input->post('text');
        //echo $this->config->item('cert');die();
        if (!file_exists($this->config->item('cert'))) {
            echo 'Lỗi e'; die();
        }

        echo $this->config->item('cert');
       
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $this->config->item('cert'));
        echo '|ABC|';

        echo getenv('GOOGLE_APPLICATION_CREDENTIALS');
        
        die();

    

        try {
            // Khởi tạo client với thông tin xác thực
            $client = new TextToSpeechClient();
    
            $input_text = (new SynthesisInput())->setText($text);
            $voice = (new VoiceSelectionParams())
                ->setLanguageCode('vi-VN')
                ->setName('vi-VN-Standard-A');
            $audioConfig = (new AudioConfig())->setAudioEncoding(AudioEncoding::MP3);
        
            $response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);
    
            $audioContent = $response->getAudioContent();
    
            header('Content-Type: audio/mpeg');
            echo $audioContent;
    
            $client->close();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}
