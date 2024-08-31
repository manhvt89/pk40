<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API tài liệu kiểm kê
 */

require APPPATH . 'core/MY_REST_Controller.php';
use app\models\api\Oinc_api;
class Oincs extends MY_REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        //$this->load->model('api/oinc_api','oinc_api');
        $this->oinc_api = new Oinc_api();
        //$this->oinc_api = new app\models\api\Oinc_api();
    }
    /**
     * Lấy danh sách tài liệu trả về json
     * @return void
     */
    public function list_oincs_post()
    {
        //var_dump($this->input->post('page'));
        $page = $this->input->post('page') ?? 1;  // Nếu không có 'page' trong POST, mặc định là 1
        $limit = $this->input->post('limit') ?? 5000;  // Nếu không có 'limit' trong POST, mặc định là 5000
        if($page==0)
        {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;
       // echo $page; echo $limit; die();
        $items = $this->oinc_api->get_docs($offset, $limit);

        if ($items) {
            $this->response($items, MY_REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No items found',
                'items'=>[]
            ], MY_REST_Controller::HTTP_OK);
        }
    }

    public function list_inc1s_post()
    {
        //var_dump($this->input->post('page'));
        $page = $this->input->post('page') ?? 1;  // Nếu không có 'page' trong POST, mặc định là 1
        $limit = $this->input->post('limit') ?? 5000;  // Nếu không có 'limit' trong POST, mặc định là 5000
        if($page==0)
        {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;
       // echo $page; echo $limit; die();
        $items = $this->oinc_api->get_items($offset, $limit);

        if ($items) {
            $this->response($items, MY_REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No items found',
                'items'=>[]
            ], MY_REST_Controller::HTTP_OK);
        }
    }

    /**
     * Lấy danh sách sản phẩm bắt đầu từ $id
     * Ngày 01.03.2023
     *
     */
    public function products_get($time=null)
    {
        if($time== null)
        {
            $time = 0;
        }
        if($time== 0)
        {
            $time = -1;
        }
        $items = $this->product->get_list_items_from_time($time);
        if(!empty($items)){
            //set the response and exit
            $this->response([
                'status' => TRUE,
                'data'=>$items,
                'message' => ''
            ], RestController::HTTP_OK);
        }else{
            //set the response and exit
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không có sản phẩm nào phù hợp'
            ], RestController::HTTP_NOT_FOUND);
        } 
    }
    /*
    ** Lấy danh sách sản phẩm theo mã danh mục: category_code (mỗi danh mục sẽ có một mã)
    ** Ngày 01.03.2023
    */
    public function productscategory_get($cate=null)
    {
        if($cate== null)
        {
            $cate = "";
        }
        //echo html_entity_decode($cate);
        $items = $this->product->get_list_items_by_category_code($cate);
        if(!empty($items)){
            //set the response and exit
            $this->response([ 
                'status'=>TRUE,
                'data'=>$items,
                'message'=>''
            ], RestController::HTTP_OK);
        }else{
            //set the response and exit
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không có sản phẩm nào trong danh mục này.'
            ], RestController::HTTP_NOT_FOUND);
        } 
    }
    /**
     * Lấy thông tin một sản phẩm với uid
     * Ngày 01.03.2023
     */
    public function the_product_get($uid=null)
    {
        if($uid== null)
        {
            $uid = 0;
        }
        $items = $this->product->get_the_product_by_uuid($uid);
        if(!empty($items)){
            //set the response and exit
            $this->response([ 
                'status'=>TRUE,
                'data'=>$items,
                'message'=>''
            ], RestController::HTTP_OK);
        }else{
            //set the response and exit
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không tìm thấy sản phẩm này.'
            ], RestController::HTTP_NOT_FOUND);
        } 
    }

    public function the_lens_categories_get()
    {
        $items = $this->config->item('iKindOfLens');

        if(!empty($items)){
            //set the response and exit
            $this->response([ 
                'status'=>TRUE,
                'data'=>$items,
                'message'=>''
            ], RestController::HTTP_OK);
        }else{
            //set the response and exit
            $this->response([
                'status' => FALSE,
                'data'=>'',
                'message' => 'Không có sản phẩm nào trong danh mục này.'
            ], RestController::HTTP_NOT_FOUND);
        } 
    }

    public function item_post()
    {
        echo 'Posst item: '. $this->input->post('uuid');
    }
}