<?php
class Attendance extends CI_Model
{
	
	
	public function get_all_employees()
    {
        		
		$this->db->select('employees.person_id AS employee_id, people.last_name, people.first_name,attendance_id,attendance_uuid,attendance.employee_uuid, attendance.fullname,people.person_uuid,
                        check_in_time , 
                        check_out_time ');
		$this->db->from('employees');
		$this->db->join('attendance', 'attendance.employee_id = employees.person_id AND attendance.shift_date = CURDATE() AND attendance.check_out_time = 0', 'left');
		$this->db->join('people', 'people.person_id = employees.person_id', 'left');
		$this->db->where('deleted', 0);
		//$this->db->group_by('employees.person_id');
		$this->db->order_by('people.first_name');
		return $this->db->get();
    }

	public function get_all_closed_attendance()
    {
        		
		$this->db->select('employees.person_id AS employee_id, people.last_name, people.first_name,attendance_id,attendance_uuid,attendance.employee_uuid, attendance.fullname,people.person_uuid,
                        check_in_time , 
                        check_out_time ');
		$this->db->from('employees');
		$this->db->join('attendance', 'attendance.employee_id = employees.person_id AND attendance.shift_date = CURDATE() AND attendance.check_out_time > 0', 'right');
		$this->db->join('people', 'people.person_id = employees.person_id', 'left');
		$this->db->where('deleted', 0);
		//$this->db->group_by('employees.person_id');
		$this->db->order_by('people.first_name');
		return $this->db->get();
    }

    public function check_in($employee_info)
    {
		$this->db->where('employee_id', $employee_info->person_id);
    	$this->db->where('check_out_time', 0);  // Chưa check-out
    	$open_shift = $this->db->get('attendance')->row();

    	if ($open_shift) {
			return false; // Không được phép tạo check-in khi chưa check-out
		} else {
			$data = [
				'employee_id' => $employee_info->person_id,
				'employee_uuid'=> $employee_info->person_uuid,
				'fullname' => $employee_info->last_name . ' ' . $employee_info->first_name,
				'hourly_wage' => $employee_info->hourly_wage,
				'check_in_time' => time(),
				'created_at'=>time(),
				'shift_date' => date('Y-m-d')  // Ngày hiện tại
			];
			return $this->db->insert('attendance', $data);
		}
    }

    public function check_out($attendance_uuid)
    {
		// Tìm bản ghi chưa có check-out cho ngày hôm nay
		$this->db->where('attendance_uuid', $attendance_uuid);
		$this->db->where('check_out_time', 0);  // Chưa check-out
		$attendance = $this->db->get('attendance')->row();
		if ($attendance) {
			// Cập nhật thời gian check-out
			$this->db->where('attendance_id', $attendance->attendance_id);
			return $this->db->update('attendance', ['check_out_time' => time()]);
		} else {
			// Xử lý trường hợp không tìm thấy bản ghi để check-out
			return false;
		}
    }

    public function generate_report()
    {
       
		$this->db->select('people.first_name, people.last_name, SUM(check_out_time - check_in_time) as total_minutes');
       
        $this->db->from('attendance');
        $this->db->join('employees', 'employees.person_id = attendance.employee_id');
		$this->db->join('people', 'employees.person_id = people.person_id', 'left');
        $this->db->group_by('employees.person_id');
        return $this->db->get()->result();
    }
	/**
	 * 
	 * Lấy danh sách nhân viên đã hoàn thành nhiệm vụ
	 * @return void
	 */
	public function open_shift_employees()
	{
		$this->db->where('check_out_time', 0);  // Chưa check-out
    	$open_shift_employees = $this->db->get('attendance')->result_array();
		return $open_shift_employees;
	}

	public function employee_columns()
	{
		return array(
            'summary' => [
                ['id' => '#','align'=>'center'],
                ['created_at' => 'Ngày tháng'],
                ['check_in_time' => 'Thời gian bắt đầu','footer-formatter'=>'iformatter'],
				['check_out_time' => 'Thời gian kết thúc','footer-formatter'=>'iformatter', 'align'=>'right'],
                ['duration' => 'Thời lượng (giờ)','align'=>'right','formatter'=>'currencyFormatter','footer-formatter'=>'totalformatter'],
                ['total'=>'TT']
			],
			'details' => [
				['stt'=>'STT'],
				['item_name'=>'Tên sản phẩm'],
				['quantity'=>'Số lượng','align'=>'right'],
				['item_unit_price'=>'Giá','align'=>'right'],
				['tong_tien'=>'Thành tiền','align'=>'right'],
				//['receiving_uuid'=>'Mã theo dõi','align'=>'left'],
			]
        );
	} 

	public function ajax_attendances(array $inputs)
	{
		$input_date = $inputs['start_date'];

		// Chuyển đổi ngày tháng đầu vào sang định dạng DateTime để lấy tháng và năm
		$date_obj = DateTime::createFromFormat('Y/m/d', $input_date);
		$month = $date_obj->format('m'); // Lấy tháng từ ngày đầu vào
		$year = $date_obj->format('Y');  // Lấy năm từ ngày đầu vào

		$this->db->select('attendance.*');
		$this->db->from('attendance');
		$this->db->where('attendance.employee_uuid', $inputs['uuid']);
		//$this->db->where('DATE(attendance.created_at) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']));
		$this->db->where('MONTH(shift_date)', $month); // Điều kiện lọc theo tháng
		$this->db->where('YEAR(shift_date)', $year);   // Điều kiện lọc theo năm
		$this->db->where('check_out_time > 0');   // Điều kiện lọc theo năm
		$this->db->order_by('attendance.created_at', 'DESC');

		$data = array();
		$data['summary'] = $this->db->get()->result();
		$data['details'] = [];
		//var_dump($data);
		return $data;

	}

	/**
	 * END ĐIểm Danh
	 */
	/*
	Determines if a given item_id is an item
	*/
	public function exists($oinc_id, $ignore_deleted = FALSE, $deleted = FALSE)
	{
		if (ctype_digit($oinc_id))
		{
			$this->db->from('oincs');
			$this->db->where('oinc_id', (int) $oinc_id);
			if ($ignore_deleted == FALSE)
			{
				$this->db->where('deleted', $deleted);
			}

			return ($this->db->get()->num_rows() == 1);
		}

		return FALSE;
	}

	/*
	Determines if a given item_number exists
	*/
	public function doc_entry_exists($doc_entry, $oinc_id = '')
	{
		$this->db->from('oincs');
		$this->db->where('doc_entry', (string) $doc_entry);
		if(ctype_digit($oinc_id))
		{
			$this->db->where('oinc_id !=', (int) $oinc_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}

	public function doc_num_exists($doc_num, $oinc_id = '')
	{
		$this->db->from('oincs');
		$this->db->where('doc_num', (string) $doc_num);
		if(ctype_digit($oinc_id))
		{
			$this->db->where('item_id !=', (int) $oinc_id);
		}
		return ($this->db->get()->num_rows() == 1);
	}

	/*
	get item in cart to check
	*/
	public function get_items_in_cart($_aItemNUmber)
	{
		$this->db->from('items');
		$this->db->where_in('item_number', $_aItemNUmber);
		return $this->db->get()->result();

	}

	/*
	Đếm số bản ghi
	*/
	public function get_total_rows()
	{
		$this->db->from('oincs');
		$this->db->where('deleted', 0);
		return $this->db->count_all_results();
	}

	/*
	Get number of rows
	*/
	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters)->num_rows();
	}

	/*
	Perform a search on items
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'oincs.oinc_id', $order = 'asc')
	{
		$this->db->from('oincs');
		
		//$this->db->where('DATE_FORMAT(trans_date, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

		if(!empty($search))
		{
			
			$this->db->group_start();
				$this->db->like('doc_num', $this->db->escape_like_str($search));
				$this->db->or_like('doc_entry', $search);
			$this->db->group_end();
			
		}
		//var_dump($filters);die();
		if($filters['O'] != FALSE)
		{
			$this->db->where('status', 'O');
		}
		if($filters['W'] != FALSE)
		{
			$this->db->or_where('status', 'W');
		}
		if($filters['C'] != FALSE)
		{
			$this->db->or_where('status', 'C');
		}
		if($filters['P'] != FALSE)
		{
			$this->db->or_where('status', 'P');
		}

		// avoid duplicated entries with same name because of inventory reporting multiple changes on the same item in the same date range
		$this->db->group_by('oincs.oinc_id');
		
		// order by name of item
		$this->db->order_by($sort, $order);

		if($rows > 0) 
		{	
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}
	
	/*
	Returns all the items
	*/
	public function get_all($stock_location_id = -1, $rows = 0, $limit_from = 0)
	{
		$this->db->from('oincs');
		
		$this->db->where('oincs.deleted', 0);

		// order by name of item
		$this->db->order_by('oincs.oinc_id', 'asc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	Gets information about a particular item
	*/
	public function get_info($oinc_id)
	{
		$this->db->select('oincs.*');
		$this->db->from('oincs');
		if(strlen($oinc_id) > 30)
		{
			$this->db->where('oinc_uuid', $oinc_id);
		} else {
			$this->db->where('oinc_id', $oinc_id);
		}

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj = new stdClass();

			//Get all the fields from items table
			foreach($this->db->list_fields('oincs') as $field)
			{
				$item_obj->$field = '';
			}
			$item_obj->unit_price = 0;
            $item_obj->cost_price = 0;

			return $item_obj;
		}
	}


	/*
	Inserts or updates a item
	Bổ sung created_time;updated_time;
	khi tạo mới created_time = updated_time = current time
	khi update; updated_time = current_time
	*/
	public function save(&$item_data, $oinc_id = FALSE)
	{
		$time = time();
		if(!$oinc_id || !$this->exists($oinc_id, TRUE))
		{
			
			if($this->db->insert('oincs', $item_data))
			{
				$item_data['oinc_id'] = $this->db->insert_id();
				
				return TRUE;
			}
			
			return FALSE;
		}
		$item_data['updated_time'] = $time;
		$this->db->where('oinc_id', $item_id);
		
		return $this->db->update('oincs', $item_data);
	}

	/**
	 *  Thêm nhiều bản ghi
	 */
	public function insert_batch($data)
	{
		return $this->db->insert_batch('oincs', $data);
	}
	/**
	 * Lưu tài liệu kiểm kê
	 */
	public function save_doc(&$oinc_data,$items_data)
	{
		$this->db->trans_start();

		$this->db->where('oinc_id', $oinc_data['oinc_id']);
		$this->db->update('oincs', $oinc_data);

		//Clean All record inc1 with oinc_id = $oinc_data['oinc_id']

		$this->db->where('oinc_id',$oinc_data['oinc_id']);
		$this->db->delete('inc1');	

		$this->db->insert_batch('inc1', $items_data);
		
		$this->db->trans_complete();
		
		$success = $this->db->trans_status();

		return $success;
	}

	public function post_doc(&$oinc_data,$items_data,$employee_id)
	{
	
		$this->db->trans_start();
		$this->db->where('oinc_id', $oinc_data['oinc_id']);
		$this->db->update('oincs', $oinc_data);

		//thực hiện từng phần tử
		foreach($items_data as $item)
		{
			if( $item['quantity'] != 0) // có lệch mới điều chỉnh kho, nếu không lệch thì ko làm gì
			{
				$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['whs_code']);
				$this->Item_quantity->save(
						[ 
							'quantity'		=> $item_quantity->quantity + $item['quantity'],
							'item_id'		=> $item['item_id'],
							'location_id'	=> $item['whs_code']], 
						$item['item_id'], 
						$item['whs_code']
					);

				// Inventory Count Details
				$sale_remarks = 'Điều chỉnh kho theo Kiểm kê ngày '. date('d/m/Y h:m',$oinc_data['count_at']);
				$inv_data = [
					'trans_date'		=> date('Y-m-d H:i:s'),
					'trans_items'		=> $item['item_id'],
					'trans_user'		=> $employee_id,
					'trans_location'	=> $item['whs_code'],
					'trans_comment'		=> $sale_remarks,
					'trans_inventory'	=> $item['quantity']
				];
				$this->Inventory->insert($inv_data);
			}

		}

		$this->db->trans_complete();
		
		$success = $this->db->trans_status();

		return $success;
	}

	

	public function get_oinc_items($oinc_id)
	{
		$this->db->select('inc1.*');
		$this->db->from('inc1');
		$this->db->where('oinc_id', $oinc_id);
		
		return $this->db->get();
	}
	/*
	Updates multiple items at once
	*/
	public function update_multiple($item_data, $item_ids)
	{
		$this->db->where_in('item_id', explode(':', $item_ids));

		return $this->db->update('items', $item_data);
	}

	/*
	Deletes one item
	*/
	public function delete($oinc_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where('oinc_id', $oinc_id);
		$this->db->where('status <>', 'P'); // Chỉ delete khi trạng thái chưa update vào kho;update rồi không cho phép xóa;
		$success = $this->db->update('oincs', array('deleted'=>1));
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}
	
	/*
	Undeletes one item
	*/
	public function undelete($oinc_id)
	{
		$this->db->where('oinc_id', $oinc_id);

		return $this->db->update('oincs', array('deleted'=>0));
	}

	/*
	Deletes a list of items
	*/
	public function delete_list($oinc_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->db->where_in('oinc_id', $oinc_ids);
		//$this->db->where('status <>', 'P'); // Chỉ delete khi trạng thái chưa update vào kho;update rồi không cho phép xóa;
		$success = $this->db->update('oincs', array('deleted'=>1));
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
 	}

	public function get_search_suggestions($search, $filters = array('is_deleted' => FALSE, 'search_custom' => FALSE), $unique = FALSE, $limit = 25)
	{
		$suggestions = array();
		$this->db->select('item_id, name,unit_price');
		$this->db->from('items');
		$this->db->where('deleted', $filters['is_deleted']);
		$this->db->like('name', $search);
        $this->db->or_like('unit_price',$search);
		$this->db->or_like('code',$search); //add by ManhVT 16.12.2022
		$this->db->or_like('item_number_new',$search); //add by ManhVT 22.04.2023
		$this->db->order_by('name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->item_id, 'label' => $row->name . ' | '.$row->unit_price);
		}

		$this->db->select('item_id, item_number');
		$this->db->from('items');
		$this->db->where('deleted', $filters['is_deleted']);
		$this->db->like('item_number', $search);
		$this->db->order_by('item_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->item_id, 'label' => $row->item_number);
		}

		if(!$unique)
		{
			//Search by category
			$this->db->select('category');
			$this->db->from('items');
			$this->db->where('deleted', $filters['is_deleted']);
			$this->db->distinct();
			$this->db->like('category', $search);
			$this->db->order_by('category', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('label' => $row->category);
			}

			//Search by supplier
			$this->db->select('company_name');
			$this->db->from('suppliers');
			$this->db->like('company_name', $search);
			// restrict to non deleted companies only if is_deleted is FALSE
			$this->db->where('deleted', $filters['is_deleted']);
			$this->db->distinct();
			$this->db->order_by('company_name', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('label' => $row->company_name);
			}

			//Search by description
			$this->db->select('item_id, name, description');
			$this->db->from('items');
			$this->db->where('deleted', $filters['is_deleted']);
			$this->db->like('description', $search);
			$this->db->order_by('description', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$entry = array('value' => $row->item_id, 'label' => $row->name);
				if(!array_walk($suggestions, function($value, $label) use ($entry) { return $entry['label'] != $label; } ))
				{
					$suggestions[] = $entry;
				}
			}

			//Search by custom fields
			if($filters['search_custom'] != FALSE)
			{
				$this->db->from('items');
				$this->db->group_start();
					$this->db->like('custom1', $search);
					$this->db->or_like('custom2', $search);
					$this->db->or_like('custom3', $search);
					$this->db->or_like('custom4', $search);
					$this->db->or_like('custom5', $search);
					$this->db->or_like('custom6', $search);
					$this->db->or_like('custom7', $search);
					$this->db->or_like('custom8', $search);
					$this->db->or_like('custom9', $search);
					$this->db->or_like('custom10', $search);
				$this->db->group_end();
				$this->db->where('deleted', $filters['is_deleted']);
				foreach($this->db->get()->result() as $row)
				{
					$suggestions[] = array('value' => $row->item_id, 'label' => $row->name);
				}
			}
		}

		//only return $limit suggestions
		//if(count($suggestions > $limit))
		if($suggestions > $limit)
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}

		return $suggestions;
	}

	public function get_category_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('category');
		$this->db->from('items');
		$this->db->like('category', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('category', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->category);
		}

		return $suggestions;
	}
	
	public function get_custom_suggestions($search, $field_no)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('custom'.$field_no);
		$this->db->from('items');
		$this->db->like('custom'.$field_no, $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('custom'.$field_no, 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$row_array = (array) $row;
			$suggestions[] = array('label' => $row_array['custom'.$field_no]);
		}
	
		return $suggestions;
	}

	public function get_categories()
	{
		$this->db->select('category');
		$this->db->from('items');
		$this->db->where('deleted', 0);
		$this->db->distinct();
		$this->db->order_by('category', 'asc');

		return $this->db->get();
	}
}
?>