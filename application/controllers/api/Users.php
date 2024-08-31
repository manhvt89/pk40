<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//use chriskacerguis\RestServer\RestController;


require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class Users extends RESTController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api/product','product');
        $this->load->model('api/user','user');
    }

    /**
     * Lấy danh sách sản phẩm bắt đầu từ $id
     * Ngày 01.03.2023
     *
     */
    public function token_get($username='')
    {
        if($username== '')
        {
            $id = 0;
        }
        $_oUser = $this->user->get_user_by_username($username);
        if(!empty($_oUser))
        {
            $token = $this->user->get_token_by($_oUser);
            if($token != '')
            {
                $this->response([
                    'status' => TRUE,
                    'data'=>$token,
                    'message' => ''
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'data'=>'',
                    'message' => 'Không có token'
                ], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không có username ton tai'
            ], RestController::HTTP_NOT_FOUND);
        }
        
        
    }

    public function token_post()
    {
        $username = $this->input->post('username');
        
        $_oUser = $this->user->get_user_by_username($username);
        if(!empty($_oUser))
        {
            $token = $this->user->get_token_by($_oUser);
            if($token != '')
            {
                $this->response([
                    'status' => TRUE,
                    'data'=>$token,
                    'message' => ''
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'data'=>'',
                    'message' => 'Không có token'
                ], RestController::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không có username ton tai'
            ], RestController::HTTP_NOT_FOUND);
        }
        
        
    }
    

    public function item_post()
    {
        echo 'Posst item: '. $this->input->post('uuid');
    }
}