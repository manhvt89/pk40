<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Ciqrcode {
    private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}
    public function createQrCode($data, $size = 300) {
        // Tạo mã QR từ dữ liệu
        $qrCode = new QrCode($data);
        $qrCode->setSize($size);
        $qrCode->setMargin(10);

        // Tạo đối tượng PngWriter
        $writer = new PngWriter();

        // Ghi mã QR và lấy nội dung hình ảnh dưới dạng chuỗi
        $result = $writer->write($qrCode);
        
        // Trả về chuỗi base64 cho hình ảnh
        return base64_encode($result->getString());
    }

    public function createQrPayment($data) {
        $_sData = '';
        // Tạo mã QR từ dữ liệu
        // Ví dụ sử dụng
        /** Load config ***/
        $config = $this->loadConfig();

        //var_dump($config);die();
        $size = $config['qr_payment_size'];

        $_sData = $this->buildPayload($data, $config);
        //echo $_sData;die();
        $_sCRCValue = $this->calculateCRC16($_sData);
        $_sData = $_sData.$_sCRCValue;
        return $this->createQrCode($_sData, $size);
    }

    private function calculateCRC16($data) {
        $crc = 0xFFFF; // Giá trị khởi tạo CRC
        $polynomial = 0x1021; // Polynomial
    
        // Duyệt qua từng ký tự trong dữ liệu
        for ($i = 0; $i < strlen($data); $i++) {
            $byte = ord($data[$i]); // Lấy giá trị byte
    
            // Cập nhật CRC cho từng bit của byte
            $crc ^= ($byte << 8); // XOR với byte hiện tại
    
            for ($j = 0; $j < 8; $j++) {
                // Kiểm tra bit cuối cùng
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ $polynomial; // Dịch trái và XOR với polynomial
                } else {
                    $crc <<= 1; // Chỉ dịch trái
                }
            }
        }
    
        // Trả về giá trị CRC dưới dạng hex (chỉ lấy 4 ký tự cuối)
        return strtoupper(substr(dechex($crc & 0xFFFF), -4));
    }

    private function build_vietqr_string($string)
    {
        // Trả về chuỗi với chiều dài
        return sprintf('%02d', strlen($string)) . $string;
    }

    private function loadConfig() {
        // Lấy cấu hình và thiết lập giá trị mặc định
        return [
            'qr_payment_size' => $this->CI->config->item('qr_payment_size') ?: 250,
            'qr_payment_bankname' => $this->CI->config->item('qr_payment_bankname') ?: 'TechCombank',
            'qr_payment_bankaccount' => $this->CI->config->item('qr_payment_bankaccount') ?: '19033083989013',
            'qr_payment_accountname' => $this->CI->config->item('qr_payment_accountname') ?: 'Vu Thanh Manh',
            'qr_point_of_initiation_method' => $this->CI->config->item('qr_point_of_initiation_method') ?: '12',
            'qr_payload_format_indicator' => $this->CI->config->item('qr_payload_format_indicator') ?: '01',
            'qr_payment_transaction_currency' => $this->CI->config->item('qr_payment_transaction_currency') ?: '704',
            'qr_payment_country_code' => $this->CI->config->item('qr_payment_country_code') ?: 'VN',
            'qr_gui' => $this->CI->config->item('qr_gui') ?: 'A000000727',
            'qr_payment_bank_code' => $this->CI->config->item('qr_payment_bank_code') ?: '970407',
            'qr_payment_service_code' => $this->CI->config->item('qr_payment_service_code') ?: 'QRIBFTTA',
        ];
    }

    private function buildPayload($data, $config) {
        $_sData ='';

       
        //echo $_sData;die();
        // Customer Infomation to transfer
        $_sConsumerAccountInformationValue = '00'.$this->build_vietqr_string($config['qr_gui']); // GUI

        $_sSubConsumerAccountInformationValue =  '00'.$this->build_vietqr_string($config['qr_payment_bank_code']);
        $_sSubConsumerAccountInformationValue .= '01'.$this->build_vietqr_string($config['qr_payment_bankaccount']);

        $_sConsumerAccountInformationValue .= '01'.$this->build_vietqr_string($_sSubConsumerAccountInformationValue); // Add info to Customer Infomation
        
        $_sConsumerAccountInformationValue .= '02'.$this->build_vietqr_string($config['qr_payment_service_code']);

        $_sMessage = '08'.$this->build_vietqr_string($data['message']);

        $_sData .= '00'.$this->build_vietqr_string($config['qr_payload_format_indicator']); //add _format_indicator
        $_sData .= '01'.$this->build_vietqr_string($config['qr_point_of_initiation_method']); //Add method: 11 or 12
        $_sData .= '38'.$this->build_vietqr_string($_sConsumerAccountInformationValue); //Add Customer Infomation to transfer
        $_sData .= '53'.$this->build_vietqr_string($config['qr_payment_transaction_currency']); //Add Mã tiền tệ
        $_sData .= '54'.$this->build_vietqr_string($data['amount']); // Add số tiền thanh toán
        $_sData .= '58'.$this->build_vietqr_string($config['qr_payment_country_code']); // Thêm mã quốc gia
        $_sData .= '62'.$this->build_vietqr_string($_sMessage); // Thêm thông thông tin nội dung thanh toán
        $_sData .= '6304'; // Cố định

        return $_sData;
    }
    
    
    
}