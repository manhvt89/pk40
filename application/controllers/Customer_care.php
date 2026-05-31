<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Customer_care extends Secure_Controller
{
    public function __construct()
    {
        parent::__construct('customer_care');
        $this->load->model('Customer_care_model');
        // Đảm bảo ngôn ngữ được tải nếu cần thiết
    }

    public function index()
    {
        $this->load->view('customer_care/manage');
    }

    public function search()
    {
        $status = $this->input->get('status', TRUE) ?: 'new'; // 'new' hoặc 'contacted'
        $search = $this->input->get('search', TRUE) ?: '';
        $limit  = $this->input->get('limit', TRUE) ?: 10;
        $offset = $this->input->get('offset', TRUE) ?: 0;
        $sort   = $this->input->get('sort', TRUE) ?: 'total_amount';
        $order  = $this->input->get('order', TRUE) ?: 'desc';

        try {
            $customers = $this->Customer_care_model->search_customers($status, $limit, $offset, $sort, $order, $search);
            $total_rows = $this->Customer_care_model->count_customers($status, $search);

            $data_rows = [];
            foreach ($customers->result() as $person) {
                // Các nút hành động
                $action_buttons = '<a href="'.site_url('customers/view/'.$person->person_id).'" class="modal-dlg btn btn-info btn-sm" title="Chi tiết khách hàng"><span class="glyphicon glyphicon-eye-open"></span> Chi tiết</a> ';
                
                if ($status == 'new') {
                    $action_buttons .= '<button onclick="markContacted(\''.$person->person_id.'\')" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-ok"></span> Đã liên hệ</button>';
                }

                $data_rows[] = [
                    'person_id' => $person->person_id,
                    'name' => get_fullname($person->first_name, $person->last_name),
                    'phone_number' => $person->phone_number,
                    'address' => $person->address_1,
                    'total_amount' => number_format($person->total_amount, 0, ',', '.') . ' ₫',
                    'last_contact_time' => $person->last_contact_time ? date('d/m/Y H:i', $person->last_contact_time) : '<span class="label label-warning">Chưa liên hệ</span>',
                    'action' => $action_buttons
                ];
            }

            echo json_encode([
                'total' => $total_rows,
                'rows' => $data_rows
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'total' => 0,
                'rows' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function mark_contacted()
    {
        $customer_id = $this->input->post('customer_id');
        $employee_id = $this->session->userdata('person_id');
        $notes = $this->input->post('notes') ?: '';

        if ($this->Customer_care_model->log_contact($customer_id, $employee_id, $notes)) {
            echo json_encode(['success' => TRUE, 'message' => 'Đã cập nhật trạng thái liên hệ thành công.']);
        } else {
            echo json_encode(['success' => FALSE, 'message' => 'Lỗi khi lưu dữ liệu.']);
        }
    }
}
