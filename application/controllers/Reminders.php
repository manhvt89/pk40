<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Reminders extends Secure_Controller
{
    public $person_id;
    public function __construct()
	{
		parent::__construct('reminders');
        $this->person_id = $this->session->userdata('person_id');
        $this->load->library('sms_lib');
	}
	
	public function index()
	{


	    //if(!$this->Employee->has_grant('customers_manager', $this->person_id))
        //{
        //    redirect('no_access/customers/manage_customers');
       //}else {
            $data['table_headers'] = $this->xss_clean(get_reminder_table_headers());

            $this->load->view('reminders/manage', $data);
       // }
	}

    public function send_form($id = -1)
    {
		/*
		 $config['reminder_status'] = [
			''=>'',
			'Sai số điện thoại'=>'Sai số điện thoại', //1
			'Chưa liên lạc được'=>'Chưa liên lạc được', //2
			'Chưa sắp xếp được thời gian' => 'Chưa sắp xếp được thời gian',//3
			'Đã đặt lịch'=>'Đã đặt lịch'//4
			//'Đã khám'//5
		];
		 
		*/
        $phone   = $this->input->post('phone');
        $message = $this->input->post('message');
		$status =  $this->input->post('status');
		$employeer_id = 0;
		$employee_name = '';
        $info = $this->Reminder->get_info($id);
		//var_dump($_status);die();
		

		$employeer_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$employee_info = $this->Employee->get_info($employeer_id);
		$employee_name = get_fullname($employee_info->first_name,$employee_info->last_name);

		$history_reminder = [
			'employeer_id'=> $employeer_id,
  			'customer_id'=>$info->customer_id,
  			'reminder_id'=>$info->id,
  			'customer_name' =>$info->name,
  			'employee_name' =>$employee_name,
  			'content' => $message == null? '':$message,
  			'status' => $status,
			'created_time'=>time()
		];

		$rs = $this->Reminder->save_history($history_reminder);

        if($rs > 0)
        {
            //save to messages table
            $data['status'] = $status;
            $this->Reminder->update($id,$data);
            echo json_encode(array('success' => TRUE, 'message' => 'Đã liên hệ đến số' . ' ' . $phone, 'id' => $this->xss_clean($id)));
        }
        else
        {
            echo json_encode(array('success' => FALSE, 'message' => 'Lỗi cập nhận liên hệ đến số' . ' ' . $phone, 'id' => -1));
        }
    }

	public function smsview($uuid = -1)
    {
        //$info = $this->Reminder->get_info($id);
		$info = $this->Reminder->get_info_by_uuid($uuid);
		$data['status'] = $this->config->item('reminder_status');
		//$data['$selected_status'] = 
		$histories = null;
		if($info == null)
		{
			$info->id = 0;
			$info->name = '';
			$info->phone = '';
			$info->address = '';
			$info->status = '';
		} else {
			$histories = $this->Reminder->get_histories_by_reminder_id($info->id);
		}
		$data['histories'] = $histories;
        $data['reminder_info'] = $info;

        $this->load->view('reminders/form_sms', $data);
    }
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
        $filters = array('type' => 'all',
            'location_id' => 'all'
            );

		$reminders = $this->Reminder->search($search, $filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Reminder->get_found_rows($search,$filters);

		$data_rows = array();
        $i = 1;
		foreach($reminders->result() as $reminder)
		{
			$reminder->no = $i;
            $i++;
		    $data_rows[] = get_reminder_data_row($reminder, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Reminder->get_search_suggestions($this->input->get('term'), TRUE));
		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Reminder->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	    Loads the customer edit form
	*/
	public function view($customer_id = -1)
	{
		$info = $this->Customer->get_info($customer_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $info;
		$data['total'] = $this->xss_clean($this->Customer->get_totals($customer_id)->total);
		$this->load->view("customers/form", $data);
	}
	
	/*
	Inserts/updates a customer
	*/
	public function save($customer_id = -1)
	{
		$person_data = array(
			'first_name' => mb_convert_case($this->input->post('first_name'), MB_CASE_TITLE, "UTF-8"),
			'last_name' => mb_convert_case($this->input->post('last_name'), MB_CASE_TITLE, "UTF-8"),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'phone_number' => $this->input->post('phone_number'),
			'address_1' => $this->input->post('address_1'),
			'address_2' => $this->input->post('address_2'),
			'city' => $this->input->post('city'),
			'state' => $this->input->post('state'),
			'zip' => $this->input->post('zip'),
			'country' => $this->input->post('country'),
			'comments' => $this->input->post('comments'),
            'age'=>$this->input->post('age')
		);
        if($customer_id > 0)
        {

            $customer_data = array(

                'company_name' => $this->input->post('company_name') == '' ? NULL : $this->input->post('company_name'),
                'discount_percent' => $this->input->post('discount_percent') == '' ? 0.00 : $this->input->post('discount_percent'),
                'taxable' => $this->input->post('taxable') != NULL
            );
        }else {
            $customer_data = array(
                'account_number' => 'C' . time(),
                'company_name' => $this->input->post('company_name') == '' ? NULL : $this->input->post('company_name'),
                'discount_percent' => $this->input->post('discount_percent') == '' ? 0.00 : $this->input->post('discount_percent'),
                'taxable' => $this->input->post('taxable') != NULL
            );
        }

		if($this->Customer->save_customer($person_data, $customer_data, $customer_id))
		{
			$person_data = $this->xss_clean($person_data);
			$customer_data = $this->xss_clean($customer_data);
			
			//New customer
			if($customer_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_adding').' '.
								$person_data['first_name'].' '.$person_data['last_name'], 'id' => $customer_data['person_id']));
			}
			else //Existing customer
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_updating').' '.
								$person_data['first_name'].' '.$person_data['last_name'], 'id' => $customer_id));
			}
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_error_adding_updating').' '.
							$person_data['first_name'].' '.$person_data['last_name'], 'id' => -1));
		}
	}
	
	public function check_account_number()
	{
		$exists = $this->Customer->account_number_exists($this->input->post('account_number'), $this->input->post('person_id'));

		echo !$exists ? 'true' : 'false';
	}
	
	/*
	This deletes customers from the customers table
	*/
	public function delete()
	{
		$customers_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Customer->delete_list($customers_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_deleted').' '.
							count($customers_to_delete).' '.$this->lang->line('customers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_cannot_be_deleted')));
		}
	}

}
?>