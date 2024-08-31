<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;
class MY_REST_Controller extends RestController {

    public function __construct()
    {
        parent::__construct();
        if($this->config->item('rest_auth') == 'bearer')
        {
            $this->_check_access_token();
        }
    }

    private function _check_access_token()
    {
        $headers = $this->input->request_headers();
        $accessToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if (preg_match('/Bearer\s(\S+)/', $accessToken, $matches)) {
            $token = $matches[1];
            if (!$this->_validate_token($token)) {
                $this->response(['status' => false, 'message' => 'Invalid token'], RestController::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(['status' => false, 'message' => 'No token provided'], RestController::HTTP_UNAUTHORIZED);
        }
    }

    private function _validate_token($token)
    {
        // Kiểm tra token trong cơ sở dữ liệu hoặc một dịch vụ khác
        // Thay thế bằng logic xác thực thực tế của bạn
        $this->db->where('key', $token);
        $query = $this->db->get('keys');

        if ($query->num_rows() > 0) {
            return TRUE;
        }

        return FALSE;
    }

    private function _check_access_token1()
    {
        // Lấy headers từ request
        $headers = $this->input->request_headers();

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            
            // Tách lấy token từ header
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];

                // Kiểm tra token trong cơ sở dữ liệu
                if ($this->_is_valid_token($token)) {
                    return TRUE;
                }
            }
        }

        // Trả về lỗi nếu không có token hợp lệ
        $this->response([
            'status' => FALSE,
            'message' => 'Unauthorized'
        ], RestController::HTTP_UNAUTHORIZED);
        exit();
    }

    private function _is_valid_token($token)
    {
        // Kiểm tra token trong database
        $this->db->where('key', $token);
        $query = $this->db->get('keys');

        if ($query->num_rows() > 0) {
            return TRUE;
        }

        return FALSE;
    }
}
