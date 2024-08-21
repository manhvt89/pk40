<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends CI_Controller {

    public function show_404() {
		
        // Nếu bạn muốn hiển thị trang 404 tùy chỉnh trong môi trường production
        if (ENVIRONMENT === 'production') {
            // Gửi mã trạng thái 404 tới trình duyệt
            $this->output->set_status_header('404');
            // Tải view tùy chỉnh cho trang 404
            $this->load->view('errors/custom_404');
        } else {
            // Trong môi trường không phải production, hiển thị trang 404 mặc định
            show_404();
        }
    }

    // Bạn có thể thêm các hàm khác cho các loại lỗi khác
}
