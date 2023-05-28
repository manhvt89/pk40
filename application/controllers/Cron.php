<?php

class Cron extends CI_Controller{

    private $url;
	public function __construct(){
		parent::__construct();
		//$this->load->library('email');
		//$this->load->model('Model_main');
        $this->load->model('Item');
        $this->load->model('cron/Product');
        $this->load->library('sms_lib');
        $this->url = 'https://tongkho.thiluc2020.com';
	}

    // Auto tong hop vao bang 1 AM daily daily_total

    public function daily_total()
    {
        echo 'Started';
        $this->load->model('Accounting');
        $this->Accounting->auto_create_daily_total();
        echo 'Completed';
    }

    // Import Sản phẩm mắt kính;
    public function import_lens()
    {
        $message = ' Bắt đầu import SP '. date('d/m/Y h:m:s',time());
        echo 	$message .PHP_EOL;
        
        $lfile =  str_replace('/public/','/',FCPATH).'log-lens.txt';
        //echo $lfile;exit();
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, $message.PHP_EOL);

        // Get list file voi bat dau lens
        $_aFiles = array();
        //$_handle = opendir(str_replace('/public_html/public/','/',FCPATH));
        $_aFiles = glob(str_replace('/public_html/public/','/',FCPATH).'lens.*');
        //var_dump($_aFiles); die();
        //1. Get All sản phẩm từ file csv
        foreach($_aFiles as $_file)
        {
            //$_file = str_replace('/public_html/public/','/',FCPATH)."lens.csv";
            //echo $_file;exit();
            if(($handle = fopen($_file, 'r')) !== FALSE)
            {
                fgetcsv($handle); // bỏ qua hàng đầu tiên không làm gì, chuyển đến dòng 2
                $i = 1;
                $failCodes = array();

                while(($data = fgetcsv($handle)) !== FALSE)
                {
                    
                        //$item_data = array();
                    if(sizeof($data) >= 0)
                    {
                        $item_data = array(
                            'name'					=> $data[0],
                            'description'			=> '',
                            'category'				=> $data[1],
                            'cost_price'			=> $data[5],
                            'unit_price'			=> $data[6],
                            'reorder_level'			=> 0,
                            'supplier_id'			=> 200278,
                            'allow_alt_description'	=> '0',
                            'is_serialized'			=> '0',
                            'custom1'				=> '',
                            'custom2'				=> '',
                            'custom3'				=> '',
                            'custom4'				=> '',
                            'custom5'				=> '',
                            'custom6'				=> '',
                            'custom7'				=> '',
                            'custom8'				=> '',
                            'custom9'				=> '',
                            'custom10'				=> ''
                        );
                        $item_number = $data[3];
                        $invalidated = FALSE;
                        if($item_number != '')
                        {
                            $item_data['item_number'] = $item_number;
                            $invalidated = $this->Item->item_number_exists($item_number);
                        }
                    } else {
                        $invalidated = TRUE;
                    }
                    
                    if(!$invalidated && $this->Item->save($item_data))
                    {
                        $items_taxes_data = NULL;
                            //tax 1

                        $items_taxes_data[] = array('name' => 'Tax', 'percent' => '10' );
                            // save tax values
                        if(count($items_taxes_data) > 0)
                        {
                            $this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
                        }

                        // quantities & inventory Info
                        $employee_id = 1; // Khởi tạo dữ liệu ban đầu;
                        $emp_info = $this->Employee->get_info($employee_id);
                        $comment =$this->lang->line('items_qty_file_import');
                        // array to store information if location got a quantity
                        $item_quantity_data = array(
                            'item_id' => $item_data['item_id'],
                            'location_id' => 1,
                            'quantity' => 0,
                        );
                        $this->Item_quantity->save($item_quantity_data, $item_data['item_id'], 1);

                        $excel_data = array(
                            'trans_items' => $item_data['item_id'],
                            'trans_user' => $employee_id,
                            'trans_comment' => $comment,
                            'trans_location' => 1,
                            'trans_inventory' => 0
                        );

                        $this->Inventory->insert($excel_data);

                    } 
                    else //insert or update item failure
                    {
                            $failCodes[$i] = $item_data['item_number'];
                            $message = "$i,". $item_data['item_number'];
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                    }

                    ++$i;
                }
                
            } else {
                $message = ' Lỗi đọc file sp.csv';
                echo 	$message .PHP_EOL;
            }
        }
        fclose($_flog);

    }

    //Import Sản phẩm
    public function import_products()
    {
        $message = ' Bắt đầu import SP '. date('d/m/Y h:m:s',time());
        echo 	$message .PHP_EOL;
        
        $lfile =  str_replace('/public/','/',FCPATH).'log.txt';
        //echo $lfile;exit();
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, $message.PHP_EOL);

        $_aFiles = array();
        //$_handle = opendir(str_replace('/public_html/public/','/',FCPATH));
        $_aFiles = glob(str_replace('/public_html/public/','/',FCPATH).'sp.*');
        //var_dump($_aFiles); die();
        //1. Get All sản phẩm từ file csv
        foreach($_aFiles as $_file)
        {
        //1. Get All sản phẩm từ file csv
        //$_file = str_replace('/public_html/public/','/',FCPATH)."sp.csv";
        //echo $_file;exit();
            if(($handle = fopen($_file, 'r')) !== FALSE)
            {
                fgetcsv($handle); // bỏ qua hàng đầu tiên không làm gì, chuyển đến dòng 2
                $i = 1;
                $failCodes = array();

                while(($data = fgetcsv($handle)) !== FALSE)
                {
                    
                        //$item_data = array();
                    if(sizeof($data) >= 0)
                    {
                        $item_data = array(
                            'name'					=> $data[0],
                            'description'			=> '',
                            'category'				=> $data[1],
                            'cost_price'			=> $data[5],
                            'unit_price'			=> $data[6],
                            'reorder_level'			=> 0,
                            'supplier_id'			=> 200278,
                            'allow_alt_description'	=> '0',
                            'is_serialized'			=> '0',
                            'custom1'				=> '',
                            'custom2'				=> '',
                            'custom3'				=> '',
                            'custom4'				=> '',
                            'custom5'				=> '',
                            'custom6'				=> '',
                            'custom7'				=> '',
                            'custom8'				=> '',
                            'custom9'				=> '',
                            'custom10'				=> ''
                        );
                        $item_number = $data[3];
                        $invalidated = FALSE;
                        if($item_number != '')
                        {
                            $item_data['item_number'] = $item_number;
                            $invalidated = $this->Item->item_number_exists($item_number);
                        }
                    } else {
                        $invalidated = TRUE;
                    }
                    
                    if(!$invalidated && $this->Item->save($item_data))
                    {
                        $items_taxes_data = NULL;
                            //tax 1

                        $items_taxes_data[] = array('name' => 'Tax', 'percent' => '10' );



                            // save tax values
                        if(count($items_taxes_data) > 0)
                        {
                            $this->Item_taxes->save($items_taxes_data, $item_data['item_id']);
                        }

                        // quantities & inventory Info
                        $employee_id = 1; // Khởi tạo dữ liệu ban đầu;
                        $emp_info = $this->Employee->get_info($employee_id);
                        $comment =$this->lang->line('items_qty_file_import');
                        // array to store information if location got a quantity
                        $item_quantity_data = array(
                            'item_id' => $item_data['item_id'],
                            'location_id' => 1,
                            'quantity' => 0,
                        );
                        $this->Item_quantity->save($item_quantity_data, $item_data['item_id'], 1);

                        $excel_data = array(
                            'trans_items' => $item_data['item_id'],
                            'trans_user' => $employee_id,
                            'trans_comment' => $comment,
                            'trans_location' => 1,
                            'trans_inventory' => 0
                        );

                        $this->Inventory->insert($excel_data);

                    } 
                    else //insert or update item failure
                    {
                            $failCodes[$i] = $item_data['item_number'];
                            $message = "$i,". $item_data['item_number'];
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                    }

                    ++$i;
                }
                
            } else {
                $message = ' Lỗi đọc file sp.csv';
                echo 	$message .PHP_EOL;
            }
        }
        fclose($_flog);
    }
    // Import đơn kính
    public function import_dk()
    {
        $message = ' Bắt đầu import đơn kính '. date('d/m/Y h:m:s',time());
        echo 	$message .PHP_EOL;
        
        $lfile =  str_replace('/public/','/',FCPATH).'log.txt';
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, $message.PHP_EOL);

        //2. Get All đơn kính từ file csv
        $_file = str_replace('/public_html/public/','/',FCPATH).'dk.csv';
        //echo $_file; die();dk
        if(($handle = fopen($_file, 'r')) !== FALSE)
		{
            // Skip the first row as it's the table description
				fgetcsv($handle);
				$i = 1;

				$failCodes = array();

				while(($data = fgetcsv($handle)) !== FALSE)
				{
					//$item_data = array();
					if(sizeof($data) >= 14)
					{
                        if($this->Testex->exists_by_code($data[1]))
                        {
                            $invalidated = TRUE; //do Nothing
                            $failCodes[$i] = $data[4];
                            $message = "$i,".$data[4].',ERR-EXIST';
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                        } else {

                            $reArray = array(); // right eye information
                            $leArray = array(); // left eye information

                            $leArray['ADD'] = $data[9];
                            $leArray['AX'] = $data[8];
                            $leArray['CYL'] = $data[7];
                            $leArray['PD'] = $data[11];
                            //$leArray['ADD'] = $this->input->post('l_add') ? $this->input->post('l_add');
                            $leArray['SPH'] = $data[6];
                            $leArray['VA'] = $data[10];

                            $reArray['ADD'] = $data[16];
                            $reArray['AX'] = $data[15];
                            $reArray['CYL'] = $data[14];
                            $reArray['PD'] = $data[18];
                            //$reArray['ADD'] = $this->input->post('r_add');
                            $reArray['SPH'] = $data[13];
                            $reArray['VA'] = $data[17];

                            $obj['note'] = '';
                            $obj['right_e'] = json_encode($reArray);
                            $obj['left_e'] = json_encode($leArray);
                            if($data[19]==1)
                            {
                                $obj['toltal'] = 'Nhìn xa';
                            }else{
                                $obj['toltal'] = '';
                            }
                            if($data[12]==1){
                                $obj['toltal'] = $obj['toltal'] . ';' . 'Nhìn gần';
                            }else{
                                $obj['toltal'] = $obj['toltal'] . ';' . '';
                            }

                            if($data[20]==1){
                                $obj['lens_type']= 'Đơn tròng';
                            }else{
                                $obj['lens_type']= '';
                            }
                            if($data[22]==1){
                                $obj['lens_type'] = $obj['lens_type'] . ';Hai tròng';
                            }else{
                                $obj['lens_type'] = $obj['lens_type'] . ';';
                            }
                            if($data[23]==1){
                                $obj['lens_type'] = $obj['lens_type'] . ';Đa tròng';
                            }else{
                                $obj['lens_type'] = $obj['lens_type'] . ';';
                            }
                            if($data[24]==1){
                                $obj['lens_type'] = $obj['lens_type'] . ';Mắt đặt';
                            }else{
                                $obj['lens_type'] = $obj['lens_type'] . ';';
                            }

                            $obj['type'] =  0;
                            if(!is_numeric(trim($data[32])))
                            {
                                $obj['duration'] = 0;
                            } else {
                                $obj['duration'] = trim($data[32]);
                            }
                            $obj['employeer_id'] = 1;
                            $obj['contact_lens_type'] = '';

                            //get customer_id via account_number
                            $customer = $this->Customer->get_info_by_account_number($data[4]);

                            $invalidated = FALSE;
                            if(!$customer)
                            {
                                $invalidated = TRUE;
                            }else {

                                $obj['customer_id'] = $customer->person_id;
                                $obj['code'] = $data[1]; // just only create new
                                $obj['test_time'] = strtotime(str_replace('/', '-', $data[2]));
                            }

                            if(!$invalidated && $this->Testex->save($obj))
                            //if(!$invalidated)
                            {
                                $failCodes[$i] = $data[4];
                                $message = "$i,".$data[4].',OK';
                                fwrite($_flog, $message.PHP_EOL);
                                echo 	$message .PHP_EOL;
                            }
                            else //insert or update item failure
                            {
                                $failCodes[$i] = $data[4];
                                $message = "$i,".$data[4].',ERR';
                                fwrite($_flog, $message.PHP_EOL);
                                echo 	$message .PHP_EOL;
                            }
                        }
					}
					else
					{
						$invalidated = TRUE;
					}

					//var_dump($obj);
					//Kiểm tra xem đã tồn tại đơn kính chưa?
					// if($this->Testex->exists_by_code($data[1]))
					// {
					// 	$invalidated = TRUE; //do Nothing
					// }

					// if(!$invalidated && $this->Testex->save($obj))
					// //if(!$invalidated)
					// {
                    //     $failCodes[$i] = $data[4];
                    //     $message = "$i,".$data[4].',OK';
                    //     fwrite($_flog, $message.PHP_EOL);
                    //     echo 	$message .PHP_EOL;
					// }
					// else //insert or update item failure
					// {
                    //     $failCodes[$i] = $data[4];
                    //     $message = "$i,".$data[4].',ERR';
                    //     fwrite($_flog, $message.PHP_EOL);
                    //     echo 	$message .PHP_EOL;
					// }

					++$i;
				}

				if(count($failCodes) > 0)
				{
					$message = $this->lang->line('items_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);

					fwrite($_flog, $message.PHP_EOL);
                    echo 	$message .PHP_EOL;
				}
				else
				{
					$message = $this->lang->line('items_excel_import_success');
                    fwrite($_flog, $message.PHP_EOL);
                    echo 	$message .PHP_EOL;
				}
        } else {
            $message = ' Lỗi đọc file sp.csv';
            echo 	$message .PHP_EOL;
        }
        // 3. 
        fclose($_flog);
    }
    // Import Khách hàng
    public function import_kh()
	{
        $_file = str_replace('/public_html/public/','/',FCPATH).'kh.csv';
	    //$_file = "/home/dev.thiluc2020.com/kh.csv";
	    if(($handle = fopen($_file, 'r')) !== FALSE)
		{
                // Skip the first row as it's the table description
				fgetcsv($handle);
				$i = 1;

				$failCodes = array();

				while(($data = fgetcsv($handle)) !== FALSE) 
				{
					// XSS file data sanity check
					//$data = $this->xss_clean($data);
                    echo '.';
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
					$message = 'So Luong Loi: ' . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);
					
					echo 	$message .PHP_EOL;
				}
				else
				{
				    $message = 'Thanh cong';
				    echo 	$message .PHP_EOL;
				}
			}
			else 
			{
                $message = 'Loi, khong tim thay file';
				echo 	$message .PHP_EOL;
			}
	}

	public function index(){
		if(!$this->input->is_cli_request()){
			//echo "This script can only be accessed via the command line" . PHP_EOL;
			//return;
		}
		//1. Get list tests to reminder

        $tests = $this->Testex->get_reminders();
        //var_dump($test->result());
        $data_rows = array();
        $ids = array();

        foreach ($tests->result() as $test) {
            $data_rows[$test->test_id] = $test;
            $ids[] = $test->test_id;
        }
        //var_dump($data_rows);
        $exits_reminders = $this->Reminder->get_reminders_in($ids);
        if($exits_reminders->result())
        {
            foreach ($exits_reminders->result() as $reminder)
            {
                //remove it from $data_rows
                unset($data_rows[$reminder->test_id]);
            }
        }
        if($data_rows) {
            foreach ($data_rows as $row) {
                $item['created_date'] = time();
                $item['test_id'] = $row->test_id;
                $item['name'] = $row->last_name . ' ' . $row->first_name;
                $item['tested_date'] = $row->test_time;
                $item['duration'] = $row->duration;
                $item['status'] = 0;
                $item['remain'] = 1;
                $item['des'] = $row->note;
                $item['customer_id'] = $row->customer_id;
                $item['action'] = '';
                $item['expired_date'] = $row->expired_date;
                $item['phone'] = $row->phone_number;
                $the_id = $this->Reminder->save($item);
                if($the_id)
                {
                    echo "Đã đồng bộ thành công " . $the_id . PHP_EOL;
                }
            }

        }
        else{
            echo "Không có bản ghi nào được đồng bộ" . PHP_EOL;
        }

        $reminders = $this->Reminder->get_reminders_sms();

		//$reminder = $this->Model_main->get_days_request_reminders($timestamp);

        // send sms
        if($this->sms_lib->init()) {
            $status = $this->sms_lib->send('0904991997', 'hello world');
            $content = "KINH MAT VIET HAN: Da den han kiem tra mat ban $reminder->name. 91 Truong Dinh, HBT, HN. LH:0969864555";
            foreach ($reminders->result() as $reminder)
            {
                if($reminder->phone) {
                    $status = $this->sms_lib->send($reminder->phone, $content);
                    if ($status) {
                        //update reminder with is_sms = 1
                        //$reminder->is_sms = 1;
                        $data['is_sms'] = 1;
                        $this->Reminder->update($reminder->id, $data);
                        $item['created_date'] = time();
                        $item['to'] = $reminder->phone;
                        $item['content'] = $content;
                        $item['type'] = 1;
                        $item['employee_id'] = 0;
                        $item['name'] = $reminder->name;
                        $this->Messages->save($item);

                        echo "Đã gửi sms thành công đến $reminder->name " . PHP_EOL;
                        sleep(30); //wait 30s before active to next task
                    }else{
                        $item['created_date'] = time();
                        $item['to'] = $reminder->phone;
                        $item['content'] = $content;
                        $item['type'] = 1;
                        $item['employee_id'] = 0;
                        $item['name'] = $reminder->name;
                        $item['status'] = 1;
                        $this->Messages->save($item);
                    }
                }
            }
            $this->sms_lib->close();
        }

	}

	public function send_sms_client()
    {
        $smses = $this->SmsSale->get_sms_sales();
        if($smses->result() == null)
        {
            echo "Không có bản ghi để gửi " . PHP_EOL;
            return;
        }
        //var_dump($smses->result());
        $content = "KINH MAT VIET HAN: Cam on quy khach da mua San Pham tai cua hang, quy khach can chinh sua gi vui long mang kinh den cua hang. Chinh sua MIEN PHI. LH:0969864555";
        if($this->sms_lib->init())
        {
            foreach ($smses->result() as $sms)
            {
                if(strlen($sms->phone) > 1) {
                    if((time() - $sms->saled_date) >= 600 ) {
                        $status = $this->sms_lib->send($sms->phone, $content);
                        if ($status) {
                            //update reminder with is_sms = 1
                            //$reminder->is_sms = 1;
                            $data['is_sms'] = 1;
                            $this->SmsSale->update($sms->id, $data);
                            $item['created_date'] = time();
                            $item['to'] = $sms->phone;
                            $item['content'] = $content;
                            $item['type'] = 0; //Gửi cảm ơn
                            $item['employee_id'] = 0;
                            $item['name'] = $sms->name;
                            $this->Messages->save($item);

                            echo "Đã gửi sms thành công đến $sms->name " . PHP_EOL;
                            sleep(30); //wait 30s before active to next task
                        } else {
                            $item['created_date'] = time();
                            $item['to'] = $sms->phone;
                            $item['content'] = $content;
                            $item['type'] = 0; //Gửi cảm ơn
                            $item['employee_id'] = 0;
                            $item['name'] = $sms->name;
                            $item['status'] = 1;//lỗi
                            $this->Messages->save($item);
                        }
                    }
                }else{
                    echo "$sms->name không có số điện thoại" . PHP_EOL;
                }
            }
        }else{
            echo "Chưa khởi tạo đc SMS MODEM" . PHP_EOL;
        }
    }

    public function generate_report_detail_sale()
    {
        ini_set('memory_limit', '-1');
        $sale_type = 'sales';
        $start_date = '2010-01-01';
        $end_date = date('Y-m-d');
        $location_id = 'all';
        $code = 0;
        $this->load->model('reports/Reports_detailed_sales');
        $report_detail = $this->Reports_detailed_sales;
        $code = $report_detail->getMax_code();
        //echo $code;die();
        $inputs = array('start_date' => $start_date, 'end_date' => $end_date, 'sale_type' => $sale_type, 'location_id' => $location_id, 'code'=>$code);

        $this->load->model('reports/Detailed_sales');
        $model = $this->Detailed_sales;

        $model->create($inputs);

        $report_data = $model->getData($inputs);

        foreach($report_data['summary'] as $key => $row)
        {
            $summary_data = array(
                'code' => $row['sale_id'],
                'sale_time' => $row['sale_time'],
                'amount' => to_quantity_decimals($row['items_purchased']),
                'saler' => $row['employee_name'],
                'buyer' => $row['customer_name'],
                'subtotal' => $row['subtotal'],
                'tax' => $row['tax'],
                'total' => $row['total'],
                'cost' => $row['cost'],
                'profit' => $row['profit'],
                'paid_customer' => $row['payment_type'],
                'kind' => $row['kind'],
                'sale_type' => $sale_type,
                'comment' => $row['comment']
            );

            //var_dump($summary_data); die();

            foreach($report_data['details'][$key] as $drow)
            {
                $quantity_purchased = to_quantity_decimals($drow['quantity_purchased']);

                //$quantity_purchased .= ' [' . $this->Stock_location->get_location_name($drow['item_location']) . ']';

                $details_data[$key][] = array(
                                    'name'     =>$drow['name'],
                                    'category' => $drow['category'],
                                    'serialnumber' => $drow['serialnumber'],
                                    'description' => $drow['description'],
                                    'quantity_purchased'=>$quantity_purchased,
                                    'subtotal'=>to_currency($drow['subtotal']),
                                    'tax'=>to_currency($drow['tax']),
                                    'total'=>to_currency($drow['total']),
                                    'cost'=>to_currency($drow['cost']),
                                    'profit'=>to_currency($drow['profit']),
                                    'item_location'=>$drow['item_location'],
                                    'discount_percent'=>$drow['discount_percent'].'%');
            }
            $summary_data['items'] = json_encode($details_data[$key]);



            $rs = $report_detail->insert($summary_data);
            if($rs == 2)
            {
                echo '-';
            }elseif($rs == 1){
                echo "Exist: {$summary_data['code']} \n";
            }else{
                echo "Error: {$summary_data['code']} \n";
                break;
            }

        }




    }
    /**
     * Thêm bới ManhVT
     * 02.03.2023
     * Thực hiện công việc tự động hóa lấy dữ liệu từ tổng kho bắt đầu từ sản phẩm thứ $id
     * Thực hiện cả kho id=0; đôngf bộ toàn bộ kho
     * 
     */
    public function b($bCanUpdate=false)
    {
        echo $bCanUpdate;
        $lfile =  str_replace('/public/','/',FCPATH).'log-lens.txt';
        //echo $lfile;exit();
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, 'Bat dau dong bo theo SP'.PHP_EOL);
        echo 'Bat dau dong bo';
        $input = $this->Product->get_max_ref_item_id();
        echo "INPUT ".$input;
        //var_dump($input);die();
        //$id = 15294;
        $_aProducts = $this->get_last_products($input);
        //echo 'manhvt';
        //var_dump($_aProducts);
        $i = 0;
        foreach($_aProducts as $_oProduct)
        {   echo '.';
            $i++;
            $item_number = $_oProduct->item_number;
            $invalidated = $this->Item->item_number_exists($item_number);
            if($invalidated == true) // update
            {
                if($bCanUpdate)
                {
                    echo 'update:'.$item_number.' \n';
                    fwrite($_flog, 'SP.Update'.PHP_EOL);
                    $_oItem = array();
                    $_oItem['unit_price'] = $_oProduct->unit_price;
                    $_oItem['name'] = $_oProduct->name;
                    $_oItem['cost_price'] = $_oProduct->cost_price;
                    $_oItem['ref_item_id'] = $_oProduct->item_id;
                    //var_dump($_oItem);
                    $this->Product->update_product($_oItem,$item_number);
                }

            } else{ // create mới
                if($_oProduct->name != '')
                {
                    echo 'Create: ' .$item_number.' \n';
                    $item_data = array(
                        'name'					=> $_oProduct->name,
                        'description'			=> $_oProduct->description,
                        'category'				=> $_oProduct->category,
                        'cost_price'			=> $_oProduct->cost_price,
                        'unit_price'			=> $_oProduct->unit_price,
                        'reorder_level'			=> $_oProduct->reorder_level,
                        'supplier_id'			=> $_oProduct->supplier_id,
                        'allow_alt_description'	=> $_oProduct->allow_alt_description,
                        'is_serialized'			=> $_oProduct->is_serialized,
                        'item_number'           => $_oProduct->item_number,
                        'ref_item_id'   =>$_oProduct->item_id,
                        'custom1'				=> '',
                        'custom2'				=> '',
                        'custom3'				=> '',
                        'custom4'				=> '',
                        'custom5'				=> '',
                        'custom6'				=> '',
                        'custom7'				=> '',
                        'custom8'				=> '',
                        'custom9'				=> '',
                        'custom10'				=> ''
                    );
                
                    if( $this->Product->save_item($item_data))
                    {
                        fwrite($_flog, 'SP.Add Thanh cong'.PHP_EOL);
                    } 
                    else //insert or update item failure
                    {
                            $failCodes[$i] = $item_data['item_number'];
                            $message = "". $item_data['item_number'];
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                    }
                }else {
                    $message = "Dữ liệu trống";
                    fwrite($_flog, $message.PHP_EOL);
                    echo 	$message .PHP_EOL;
                }

            }
        }
        echo 'Toal:'.$i;
    }
    /**
     * Thêm bới ManhVT
     * 02.03.2023
     * Thực hiện công việc tự động hóa lấy dữ liệu từ tổng kho với danh mục đã chọn
     * Thực hiện đồng bộ toàn bộ sản phẩm với anh mục đã chọn;
     * Danh mục hiện tại là các loại mắt bên tổng kho;
     * $category =0 -> x
     * $bCanUpdate: cho phép cập nhật không, true sẽ cho phép được cập nhật;
     * $bupdateCat: cho phép cập nhật category không, true cho phép cập nhật category giống tổng kho; nếu không chỉ cập nhật giá, tên sản phẩm;
     */
    public function c($category=0, $bCanUpdate = false, $bupdateCat = false)
    {
        echo $bCanUpdate;
        $message = ' Bắt đầu Synch SP '. date('d/m/Y h:m:s',time());
        echo 	$message .PHP_EOL;
        
        /* $_aCategory = array(
            "1.56 CHEMI",//0
            "1.56 CHEMI Crystal U2", //1
            "1.56 CHEMI Crystal U6", //2
            "1.56 CHEMI ASP PHOTO GRAY",//3
            "1.61 CHEMI Crystal U2",//4
            "1.60 CHEMI Crystal U6",//5
            "1.67 CHEMI Crystal U2",//6
            "1.67 CHEMI Crystal U6",//7
            "1.74 CHEMI Crystal U2",//8
            "FREEFORM",//9
            "1.56 KODAK Clean'N'CleAR".//10
            "1.60 KODAK Clean'N'CleAR",//1
            "1.67 KODAK Clean'N'CleAR",//12
            "1.60 KODAK UV400 BLUE",//13
            "1.60 HOYA NULUX SFT SV",//14
            "1.67 HOYA NULUX SFT SV", //15       
            "1.60 ESSILOR CRIZAL ALIZE",//16
            "1.56 NAHAMI CRYSTAL COATED",//17
            "1.60 NAHAMI SUPER HMC A+",//18
            "1.67 NAHAMI SUPER HMC",//19
            "1.60 U1 ECOVIS",//20
            "1.56 KOREA TC",
            "1.56 Đổi màu TC",
            "1.56 ĐM PQ Korea",
            "1.56 CR Korea",
            "1.56 Polaroid CR Korea",//25
            "1.60 U1 ECOVIS",
            "1.56 TRÁNG CỨNG",
            "ĐỔI MÀU KOREA",
            "1.49 CR Korea",
            "1.56 POLAROID KHÓI",//30
            "1.56 POLAROID XANH",
            "1.56 POLAROID TRÀ",
            "1.56 KHÓI 1 MÀU CR",
            "1.56 KHÓI 2 MÀU CR",
            "1.56 TRÀ 1 MÀU CR",//35
            "1.56 TRÀ 2 MÀU CR",
            "1.56 XANH 1 MÀU CR"
        ); */

        $_aCategory = $this->get_categories(); 

        //var_dump($_aCategory); die();

        $lfile =  str_replace('/public/','/',FCPATH).'log-lens.txt';
        //echo $lfile;exit();
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, $message.PHP_EOL);
        $cate = "1.56 CHEMI";
        if(isset($_aCategory[$category]))
        {
            $cate = vn_str_filter($_aCategory[$category]);//"1.56 KODAK Clean'N'CleAR";
        }
        $_str =  str_replace(' ','_',$cate);
        $_str =  str_replace("'",'',$_str);
        //echo $_str; die();
        //$_aLocalProducts = $this->Product->get_list_items_by_category_code($_str);
        $_aProducts = $this->get_products_by_category($_str);
        var_dump($_aProducts);
        echo $_str; die();
        foreach($_aProducts as $_oProduct)
        {
            $item_number = $_oProduct->item_number;
            $invalidated = $this->Item->item_number_exists($item_number);
            if($invalidated == true) // update
            {
                if($bCanUpdate) // Nếu update
                {
                    $_oItem = array();
                    $_oItem['unit_price'] = $_oProduct->unit_price;
                    $_oItem['name'] = $_oProduct->name;
                    $_oItem['cost_price'] = $_oProduct->cost_price;
                    $_oItem['ref_item_id'] = $_oProduct->item_id;
                    if($bupdateCat)
                    {
                        $_oItem['category'] = $_oProduct->category;
                    }
                    var_dump($_oItem);
                    $this->Product->update_product($_oItem,$item_number);
                }

            } else{ // create mới

                if($_oProduct->name != '')
                {
                    $_sProductCat = vn_str_filter($_oProduct->category);
                    $_sProductCateCode =  str_replace(' ','_',$_sProductCat);
                    $_sProductCateCode =  str_replace("'",'',$_sProductCateCode);
                    $item_data = array(
                        'name'					=> $_oProduct->name,
                        'description'			=> $_oProduct->description,
                        'category'				=> $_oProduct->category,
                        'category_code'			=> $_sProductCateCode,
                        'cost_price'			=> $_oProduct->cost_price,
                        'unit_price'			=> $_oProduct->unit_price,
                        'reorder_level'			=> $_oProduct->reorder_level,
                        'supplier_id'			=> $_oProduct->supplier_id,
                        'allow_alt_description'	=> $_oProduct->allow_alt_description,
                        'is_serialized'			=> $_oProduct->is_serialized,
                        'item_number'           => $_oProduct->item_number,
                        'ref_item_id'   =>$_oProduct->item_id,
                        'custom1'				=> '',
                        'custom2'				=> '',
                        'custom3'				=> '',
                        'custom4'				=> '',
                        'custom5'				=> '',
                        'custom6'				=> '',
                        'custom7'				=> '',
                        'custom8'				=> '',
                        'custom9'				=> '',
                        'custom10'				=> ''
                    );
                
                    if( $this->Product->save_item($item_data))
                    {
                        
                    } 
                    else //insert or update item failure
                    {
                            //$failCodes[$i] = $item_data['item_number'];
                            $message = "". $item_data['item_number'];
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                    }
                } else {
                            $message = "Dữ liệu trống";
                            fwrite($_flog, $message.PHP_EOL);
                            echo 	$message .PHP_EOL;
                }

            }
        }
        
    }

    private function get_last_products($id)
    {
        //insert data
        $url = $this->url."/api/item/last_products/$id";
        
        //create a new cURL resource
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:123456789');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $result = json_decode($body);
        //var_dump($result);
        curl_close($ch);
        if(empty($result))
        {
            return array();
        }
        if($result->status == TRUE)
        {
            return $result->data;
        } else {
            return array();
        }
        
    }

    private function get_products_by_category($category_code)
    {
        //insert data
        $url = $this->url."/api/item/products_category/$category_code";
        echo $url;
        
        
        //create a new cURL resource
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:123456789');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $result = json_decode($body);
        if(empty($result))
        {
            return array();
        }
        curl_close($ch);
        if($result->status == TRUE)
        {
            return $result->data;
        } else {
            return array();
        }
        
    }

    private function get_categories()
    {
        //insert data
        $url = $this->url."/api/item/the_lens_categories";
        //user information
        
        
        //create a new cURL resource
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:123456789');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $result = json_decode($body);
        //var_dump($result);
        curl_close($ch);
        if(empty($result))
        {
            return array();
        }
        if($result->status == TRUE)
        {
            return $result->data;
        } else {
            return array();
        }
        
    }

     /**
     * Thêm bới ManhVT
     * 02.03.2023
     * Thực hiện công việc tự động hóa lấy dữ liệu từ tổng kho với danh mục đã chọn
     * Thực hiện đồng bộ toàn bộ sản phẩm với anh mục đã chọn;
     * Danh mục hiện tại là các loại mắt bên tổng kho;
     * $category =0 -> x
     * $bCanUpdate: cho phép cập nhật không, true sẽ cho phép được cập nhật;
     * $bupdateCat: cho phép cập nhật category không, true cho phép cập nhật category giống tổng kho; nếu không chỉ cập nhật giá, tên sản phẩm;
     */
    public function d($category=0, $bCanUpdate = false)
    {
        echo $bCanUpdate;
        $message = ' Bắt đầu Synch SP '. date('d/m/Y h:m:s',time());
        echo 	$message .PHP_EOL;
        
        $_aCategory = array(
            "1.56 CHEMI",//0
            "1.56 CHEMI Crystal U2", //1
            "1.56 CHEMI Crystal U6", //2
            "1.56 CHEMI ASP PHOTO GRAY",//3
            "1.61 CHEMI Crystal U2",//4
            "1.60 CHEMI Crystal U6",//5
            "1.67 CHEMI Crystal U2",//6
            "1.67 CHEMI Crystal U6",//7
            "1.74 CHEMI Crystal U2",//8
            "FREEFORM",//9
            "1.56 KODAK Clean'N'CleAR".//10
            "1.60 KODAK Clean'N'CleAR",//1
            "1.67 KODAK Clean'N'CleAR",//12
            "1.60 KODAK UV400 BLUE",//13
            "1.60 HOYA NULUX SFT SV",//14
            "1.67 HOYA NULUX SFT SV", //15       
            "1.60 ESSILOR CRIZAL ALIZE",//16
            "1.56 NAHAMI CRYSTAL COATED",//17
            "1.60 NAHAMI SUPER HMC A+",//18
            "1.67 NAHAMI SUPER HMC",//19
            "1.60 U1 ECOVIS",//20
            "1.56 KOREA TC",
            "1.56 Đổi màu TC",
            "1.56 ĐM PQ Korea",
            "1.56 CR Korea",
            "1.56 Polaroid CR Korea",//25
            "1.60 U1 ECOVIS",
            "1.56 TRÁNG CỨNG",
            "ĐỔI MÀU KOREA",
            "1.49 CR Korea",
            "1.56 POLAROID KHÓI",//30
            "1.56 POLAROID XANH",
            "1.56 POLAROID TRÀ",
            "1.56 KHÓI 1 MÀU CR",
            "1.56 KHÓI 2 MÀU CR",
            "1.56 TRÀ 1 MÀU CR",//35
            "1.56 TRÀ 2 MÀU CR",
            "1.56 XANH 1 MÀU CR"
        );

        $_aCategory = $this->get_categories(); 

        //var_dump($_aCategory);

        $lfile =  str_replace('/public/','/',FCPATH).'log-lens.txt';
        //echo $lfile;exit();
        $_flog=fopen($lfile, 'a');
        fwrite($_flog, $message.PHP_EOL);
        $cate = "1.56 CHEMI";
        if(isset($_aCategory[$category]))
        {
            $cate = vn_str_filter($_aCategory[$category]);//"1.56 KODAK Clean'N'CleAR";
        }
        $_str =  str_replace(' ','_',$cate);
        $_str =  str_replace("'",'',$_str);
        //echo $_str; die();
        //$_aLocalProducts = $this->Product->get_list_items_by_category_code($_str);
        $_aProducts = $this->get_products_by_category($_str);
        //var_dump($_aProducts);
        //echo $_str; die();
        foreach($_aProducts as $_oProduct)
        {
            $item_number = $_oProduct->item_number_new;
            $invalidated = $this->Item->item_number_exists($item_number);
            if($invalidated == true) // update
            {
                if($bCanUpdate) // Nếu update
                {
                    $_oItem['item_number_new'] = $_oProduct->item_number_new;
                    $_oItem['item_number'] = $_oProduct->item_number;
                    
                    var_dump($_oItem);
                    $this->Product->update_product($_oItem,$item_number);
                }

            } else{ // khong lam gi

            }
        }
        
    }

}
