<?php
class History_ctv extends CI_Model
{

	public function get_info($history_id)
	{
		$this->db->select('history_ctv.*');
		$this->db->from('history_ctv');
		$this->db->where('history_ctv.history_ctv_id', $history_id);
		return $this->db->get();
	}

	/*
	 Get number of rows for the takings (sales/manage) view
	*/
	public function get_found_rows($search, $filters, $bUser_type = 0, $iUser_Id = 0)
	{
		return $this->search($search, $filters,0,0,'sale_date','desc',$bUser_type, $iUser_Id)->num_rows();
	}

	/*
	 Get the sales data for the takings (sales/manage) view
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'sale_date', $order = 'desc', $bUser_type = 0, $iUser_Id = 0)
	{
		

		$this->db->select('history_ctv.*');
		$this->db->from('history_ctv');
		
		if($bUser_type == 2)
		{
			$this->db->where('DATE(FROM_UNIXTIME(history_ctv.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
			$this->db->where('history_ctv.ctv_id',$iUser_Id);
		}else{
			// Sử dụng filter
			//if(!$filters['pending'])
			//{
				$this->db->where('DATE(FROM_UNIXTIME(history_ctv.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
				$this->db->where('status', 0);
			//} else {
				//$this->db->where('status', 1);
			//}
		}
		
		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ', $search);
				$this->db->where('history_ctv.ctv_id', $pieces[1]);
			}
			else
			{			
				$this->db->group_start();
					// customer last name
					$this->db->like('history_ctv.ctv_name', $search);
					// customer first name
					$this->db->or_like('history_ctv.employee_name', $search);
					// customer first and last name
					
					// customer company name
					$this->db->or_like('history_ctv.customer_name', $search);
					$this->db->or_like('history_ctv.ctv_phone', $search);
				$this->db->group_end();
			}
		}
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	 Get the payment summary for the takings (sales/manage) view
	*/
	public function get_payments_summary($search, $filters,$bUser_type=0, $iUser_id=0)
	{
		// get payment summary
		$this->db->select('payment_type, count(*) AS count, SUM(payment_amount) AS payment_amount');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales_payments.sale_id = sales.sale_id');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');

		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		
		if($bUser_type == 2)
		{
			$this->db->where('sales.ctv_id', $iUser_id);
		}
		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ',$search);
				$this->db->where('sales.sale_id', $pieces[1]);
			}
			else
			{
				$this->db->group_start();
					// customer last name
					$this->db->like('customer_p.last_name', $search);
					// customer first name
					$this->db->or_like('customer_p.first_name', $search);
					// customer first and last name
					$this->db->or_like('CONCAT(customer_p.first_name, " ", customer_p.last_name)', $search);
					// customer company name
					$this->db->or_like('customer.company_name', $search);
				$this->db->group_end();
			}
		}

		if($filters['sale_type'] == 'sales')
		{
			$this->db->where('payment_amount > 0');
		}
		elseif($filters['sale_type'] == 'returns')
		{
			$this->db->where('payment_amount < 0');
		}

		if($filters['only_invoices'] != FALSE)
		{
			$this->db->where('invoice_number IS NOT NULL');
		}
		
		if($filters['only_cash'] != FALSE)
		{
			$this->db->like('payment_type', $this->lang->line('sales_cash'), 'after');
		}

		$this->db->group_by('payment_type');

		$payments = $this->db->get()->result_array();

		// consider Gift Card as only one type of payment and do not show "Gift Card: 1, Gift Card: 2, etc." in the total
		$gift_card_count = 0;
		$gift_card_amount = 0;
		foreach($payments as $key=>$payment)
		{
			if( strstr($payment['payment_type'], $this->lang->line('sales_giftcard')) != FALSE )
			{
				$gift_card_count  += $payment['count'];
				$gift_card_amount += $payment['payment_amount'];

				// remove the "Gift Card: 1", "Gift Card: 2", etc. payment string
				unset($payments[$key]);
			}
		}

		if($gift_card_count > 0)
		{
			$payments[] = array('payment_type' => $this->lang->line('sales_giftcard'), 'count' => $gift_card_count, 'payment_amount' => $gift_card_amount);
		}

		return $payments;
	}

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('history_ctv');

		return $this->db->count_all_results();
	}


	
	public function is_valid_receipt(&$receipt_sale_id)
	{
		if(!empty($receipt_sale_id))
		{
			//Added by ManhVT 05.02.2023
			return $this->exists_by_code($receipt_sale_id);

			// end added
			//POS #
			$pieces = explode(' ', $receipt_sale_id);

			if(count($pieces) == 2 && preg_match('/(POS)/', $pieces[0]))
			{
				return $this->exists($pieces[1]);
			}
			elseif($this->config->item('invoice_enable') == TRUE)
			{
				$sale_info = $this->get_sale_by_invoice_number($receipt_sale_id);
				if($sale_info->num_rows() > 0)
				{
					$receipt_sale_id = 'POS ' . $sale_info->row()->sale_id;

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	public function exists($ctv_id)
	{
		$this->db->from('history_ctv');
		$this->db->where('ctv_id', $ctv_id);

		return ($this->db->get()->num_rows()==1);
	}
	/**
	 * Kiểm tra tồn tại mã bán hàng
	 */
	public function exists_by_code($sale_code)
	{
		$this->db->from('history_ctv');
		$this->db->where('sale_code', $sale_code);
		$this->db->where('status', 0);
		return ($this->db->get()->num_rows()==1);
	}

	public function save($history_ctv_data)
	{
		$this->db->trans_start(); // start transaction mysql

		$this->db->insert('history_ctv', $history_ctv_data);
		$_id = $this->db->insert_id();

		$sale_data['sync'] = 3; //Lần sau bỏ qu
		$this->db->where('sale_id', $history_ctv_data['sale_id']);
        $success = $this->db->update('sales', $sale_data);

		//Update total_sale of ctv
		$_ctv_info = $this->Employee->get_info($history_ctv_data['ctv_id']);
		//var_dump($_ctv_info);
		//var_dump($history_ctv_data['payment_amount']);
		$_ctv_data = [
			'total_sale'=>(float) $_ctv_info->total_sale + $history_ctv_data['payment_amount'],
		];
		$this->db->where('person_id', $history_ctv_data['ctv_id']);
		$success = $this->db->update('employees', $_ctv_data);
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return -1;
		}
		else
		{
			$this->db->trans_commit();
			return $_id;
		}
	}

	public function delete_list($sale_ids, $employee_id, $update_inventory = TRUE)
	{
		$result = TRUE;

		foreach($sale_ids as $sale_id)
		{
			$result &= $this->delete($sale_id, $employee_id, $update_inventory);
		}

		return $result;
	}

	public function get_customer($history_id)
	{
		$this->db->from('history_ctv');
		$this->db->where('history_ctv_id', $history_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}

	public function get_employee($history_id)
	{
		$this->db->from('history_ctv');
		$this->db->where('history_ctv_id', $history_id);
		return $this->Employee->get_info($this->db->get()->row()->employee_id);
	}

	public function get_ctv($history_id)
	{
		$this->db->from('history_ctv');
		$this->db->where('history_ctv_id', $history_id);

		return $this->Employee->get_info($this->db->get()->row()->ctv_id);
	}
	public function get_sale($history_id)
	{
		$this->db->from('history_ctv');
		$this->db->where('history_ctv_id', $history_id);
		return $this->Sale->get_info($this->db->get()->row()->sale_id);
	}


	//public function

	public function get_info_by_uuid($uuid)
	{
		$this->db->from('history_ctv');
		$this->db->where('history_ctv.history_ctv_uuid', $uuid);
		$this->db->order_by('history_ctv.created_time', 'asc');
		return $this->db->get();
	}


	public function get_the_last_record($input)
	{
		$this->db->select('*');

		// Chọn bảng
		$this->db->from('history_ctv');
		$this->db->where('history_ctv.sale_id',$input['sale_id']);
		$this->db->where('history_ctv.ctv_id',$input['ctv_id']);
		// Sắp xếp theo cột timestamp (giả sử đây là cột chứa thời gian)
		$this->db->order_by('created_time', 'DESC');

		// Giới hạn số lượng bản ghi trả về là 1 (bản ghi cuối cùng)
		$this->db->limit(1);

		// Thực hiện truy vấn và lấy dữ liệu
		$query = $this->db->get();

		// Kiểm tra xem có kết quả hay không
		if ($query->num_rows() > 0) {
			// Lấy bản ghi cuối cùng
			$result = $query->row();

			// Xử lý dữ liệu ở đây
			return $result;
		} else {
			// Không có bản ghi nào
			return null;
		}
	}

	public function get_sync_ctvs($status=0)
	{
		$this->db->from('sales');
		$this->db->where('sales.sync', $status);
		$this->db->where('sales.ctv_id > ', 0);
		return $this->db->get();
	}

	public function is_synchroed($oSale)
	{
		return $this->exists_by_code($oSale->code);
	}

	public function do_update($oSale)
	{
		$_aRecord = [
			'sync'=>3 //Đã đồng bộ, lần tiếp theo bỏ qua
		];
		$this->db->where('sales.sale_id', $oSale->sale_id);
		$this->db->update('sales',$_aRecord);
	}

	public function do_synch($aSale)
	{
		return $this->save($aSale);
	}
}
?>
