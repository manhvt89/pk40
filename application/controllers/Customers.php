<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Customers extends Persons
{
    public $person_id;
    public function __construct()
	{
		parent::__construct('customers');
        $this->person_id = $this->session->userdata('person_id');
		$this->logedUser_type = $this->session->userdata('type');
	}
	/* 
		Require permissions: customer_view
	*/
	public function index()
	{
		if($this->logedUser_type != 2)
		{
			$data['table_headers'] = $this->xss_clean(get_people_manage_table_headers());
			$this->load->view('people/manage', $data);
			
		} else { // Bác sĩ type = 2 laf bac si;
			$data['table_headers'] = $this->xss_clean(get_people_manage_table_headers());

				$this->load->view('people/manage', $data);
		}
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	Exclude permissions
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$customers = $this->Customer->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Customer->get_found_rows($search);

		$data_rows = array();
		foreach($customers->result() as $person)
		{
			$data_rows[] = get_person_data_row($person, $this);
		}

		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for, Call Ajax
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->get('term'), TRUE));
		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->post('term'), TRUE));

		echo json_encode($suggestions);
	}
	
	/*
	    Loads the customer edit form
		Require permissions: customer_view
	*/
	public function view($customer_id = -1)
	{
		$info = $this->Customer->get_info($customer_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$city_ = get_cities_list();
        $cities = array();
        foreach ($city_ as $key=>$value)
        {
            $cities[$value] = $value;
        }
		$data['city'] = $this->config->item('default_city');//'Bình Thuận';
        /* if($data['city'] == '' || $data['city'] == 'HN')
        {
            $data['city'] = 'Bình Thuận';
        } */
		if($info->age == '')
		{
			$info->age = 30;
		}
		//var_dump($info);
		$info->first_name = get_fullname($info->first_name, $info->last_name);
		$data['person_info'] = $info;
        $data['cities'] = $cities;
		$data['total'] = $this->xss_clean($this->Customer->get_totals($customer_id)->total);
		$this->load->view("customers/form", $data);
	}
	
	/*
		Inserts/updates a customer
	*/
	public function save($customer_id = -1)
	{
		$_firstname = $this->input->post('first_name');
		$_aName = extract_fullname($_firstname);
		$person_data = array(
			'first_name' => mb_convert_case($_aName['firstname'], MB_CASE_TITLE, "UTF-8"),
			'last_name' => mb_convert_case($_aName['lastname'], MB_CASE_TITLE, "UTF-8"),
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
            'age'=>$this->input->post('age')==''?0:$this->input->post('age'),
            'facebook'=>$this->input->post('facebook')
		);
		//var_dump($this->input->post('age'));
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
	Require permissions: customer_delete
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

	/*
	Customers import from excel spreadsheet
	*/
	public function excel()
	{
		$name = 'import_customers.csv';
		$data = file_get_contents('../' . $name);
		force_download($name, $data);
	}
	/*
	This deletes customers from the customers table
	Require permissions: customer_excel_import
	*/
	public function excel_import()
	{
		$this->load->view('customers/form_excel_import', NULL);
	}
    // Import KH từ phần mềm quản lý cũ
	public function do_excel_import()
	{
		if($_FILES['file_path']['error'] != UPLOAD_ERR_OK)
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_failed')));
		}
		else
		{
			if(($handle = fopen($_FILES['file_path']['tmp_name'], 'r')) !== FALSE)
			{
                // Skip the first row as it's the table description
				fgetcsv($handle);
				$i = 1;

				$failCodes = array();

				while(($data = fgetcsv($handle)) !== FALSE) 
				{
					// XSS file data sanity check
					$data = $this->xss_clean($data);

					if(sizeof($data) >= 16)
					{
						$fullname = $data[3];
                        $names = explode(' ', $fullname);
                        $firstname = $names[count($names) - 1];
                        unset($names[count($names) - 1]);
                        $lastname = join(' ', $names);
                        $firstname = mb_convert_case($firstname, MB_CASE_TITLE, "UTF-8");
                        $lastname = mb_convert_case($lastname, MB_CASE_TITLE, "UTF-8");

					    $person_data = array(
							'first_name'	=> $firstname,
							'last_name'		=> $lastname,
							'gender'		=> 0,
							'email'			=> $data[10],
							'phone_number'	=> $data[8],
							'address_1'		=> $data[7],
							'address_2'		=> '',
							'city'			=> 'HN',
							'state'			=> 'HN',
							'zip'			=> '100000',
							'country'		=> 'VN',
							'comments'		=> '',
                            'age'           => 0
						);
						
						$customer_data = array(
							'company_name'		=> '',
							'discount_percent'	=> 0,
							'taxable'			=> 1
						);
						
						$account_number = $data[1];
						$invalidated = FALSE;
						if($account_number != '') 
						{
							$customer_data['account_number'] = $account_number;
							$invalidated = $this->Customer->account_number_exists($account_number);
						}
					}
					else 
					{
						$invalidated = TRUE;
					}

					if($invalidated || !$this->Customer->save_customer($person_data, $customer_data))
					{	
						$failCodes[] = $i;
					}
					
					++$i;
				}
				
				if(count($failCodes) > 0)
				{
					$message = $this->lang->line('customers_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);
					
					echo json_encode(array('success' => FALSE, 'message' => $message));
				}
				else
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_excel_import_success')));
				}
			}
			else 
			{
                echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_nodata_wrongformat')));
			}
		}
	}
    // import của hệ thống mới
	public function do_excel_import_bk()
    {
        if($_FILES['file_path']['error'] != UPLOAD_ERR_OK)
        {
            echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_failed')));
        }
        else
        {
            if(($handle = fopen($_FILES['file_path']['tmp_name'], 'r')) !== FALSE)
            {
                // Skip the first row as it's the table description
                fgetcsv($handle);
                $i = 1;

                $failCodes = array();

                while(($data = fgetcsv($handle)) !== FALSE)
                {
                    // XSS file data sanity check
                    $data = $this->xss_clean($data);

                    if(sizeof($data) >= 16)
                    {
                        $person_data = array(
                            'first_name'	=> $data[0],
                            'last_name'		=> $data[1],
                            'gender'		=> $data[2],
                            'email'			=> $data[3],
                            'phone_number'	=> $data[4],
                            'address_1'		=> $data[5],
                            'address_2'		=> $data[6],
                            'city'			=> $data[7],
                            'state'			=> $data[8],
                            'zip'			=> $data[9],
                            'country'		=> $data[10],
                            'comments'		=> $data[11],
                            'age'           => $data[16]
                        );

                        $customer_data = array(
                            'company_name'		=> $data[12],
                            'discount_percent'	=> $data[14],
                            'taxable'			=> $data[15] == '' ? 0 : 1
                        );

                        $account_number = $data[13];
                        $invalidated = FALSE;
                        if($account_number != '')
                        {
                            $customer_data['account_number'] = $account_number;
                            $invalidated = $this->Customer->account_number_exists($account_number);
                        }
                    }
                    else
                    {
                        $invalidated = TRUE;
                    }

                    if($invalidated || !$this->Customer->save_customer($person_data, $customer_data))
                    {
                        $failCodes[] = $i;
                    }

                    ++$i;
                }

                if(count($failCodes) > 0)
                {
                    $message = $this->lang->line('customers_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);

                    echo json_encode(array('success' => FALSE, 'message' => $message));
                }
                else
                {
                    echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_excel_import_success')));
                }
            }
            else
            {
                echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_nodata_wrongformat')));
            }
        }
    }

	public function phonenumber_hide()
	{
		exit();
	}
}
?>