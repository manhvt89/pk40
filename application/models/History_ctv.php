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

		return ($this->db->get()->num_rows()==1);
	}

	public function update($sale_id, $sale_data, $payments, $employee_id,$customer_id,$amount_change,$points=0)
	{
		//$this->db->where('sale_id', $sale_id);
		//$success = $this->db->update('sales', $sale_data);

		// touch payment only if update sale is successful and there is a payments object otherwise the result would be to delete all the payments associated to the sale
		//if($success && !empty($payments))
		$success = 0;
		if(!empty($payments))
		{
			//Run these queries as a transaction, we want to make sure we do all or nothing
			$this->db->trans_start();

            $this->db->where('sale_id', $sale_id);
            $success = $this->db->update('sales', $sale_data);
            // first delete all payments
			// $this->db->delete('sales_payments', array('sale_id' => $sale_id));
			$cus_obj = $this->Customer->get_info($customer_id);
			// add new payments
			foreach($payments as $payment)
			{
				if($payment['payment_amount'] == 0) //Số tiền bằng 0 thì không thực hiện ghi vào db
				{
					continue;
				}
				$sales_payments_data = array(
					'sale_id' => $sale_id,
					'payment_type' => $payment['payment_type'],
					'payment_amount' => $payment['payment_amount']
				);

				$success = $this->db->insert('sales_payments', $sales_payments_data);
				$payment_id = $this->db->insert_id();

				if($payment['payment_type'] == $this->lang->line("sales_cash")) { // If tiền mặt then insert accounting
				 
					$data_total = array(
						'creator_personal_id' => $employee_id,
						'personal_id' => $customer_id, // this is a customer
						'amount' => $payment['payment_amount']
					);
					$data_total['payment_type'] = $payment['payment_type'];
					$data_total['kind'] = $payment['payment_kind'];
					$data_total['payment_id'] = $payment_id;
					$data_total['sale_id'] = $sale_id;
					$this->Accounting->save_income($data_total);
	
					if($amount_change > 0) {
						$out_data = array(
							'creator_personal_id' => $employee_id,
							'personal_id' => $customer_id, // this is a customer
							'amount' => $amount_change
						);
						$out_data['payment_type'] = $payment['payment_type'];
						$out_data['kind'] = 3;
						$out_data['payment_id'] = $payment_id;
						$out_data['sale_id'] = $sale_id;
	
	
						$this->Accounting->save_payout($out_data);
					}
				} elseif($this->lang->line("sales_check") == $payment['payment_type'] || $payment['payment_type'] == $this->lang->line("sales_debit")) {
					$data_total = array(
						'creator_personal_id' => $employee_id,
						'personal_id' => $customer_id, // this is a customer
						'amount' => $payment['payment_amount']
					);
					$data_total['payment_type'] = $payment['payment_type'];
					$data_total['kind'] = $payment['payment_kind'];
					$data_total['payment_id'] = $payment_id;
					$data_total['sale_id'] = $sale_id;
					$data_total['payment_method'] = 1; //Banking;
					$this->Accounting->save_income($data_total);
				}
			}

			if($points > 0) // Nếu sử dụng điểm thanh toán
			{
				//1. Update ppoint cua kh
				// Lấy thông tin của khách hàng này
				//$customer_info = $cus_obj;
				//var_dump($customer_info);die();
				$customer_info['points'] = $cus_obj->points - $points;
				$this->db->where('person_id',$customer_id);
				$this->db->update('customers',$customer_info);

				//2. insert ospos_history_points
				//$sale_info = $this->Sale->get_info($sale_id)->row_array();
				//var_dump($sale_id ); die();
				$_aHistoryPoint = array(
					'customer_id' =>$customer_id,
					'sale_id' => $sale_id,
					'sale_uuid' => '',
					'created_date' =>time(),
					'point' =>$points,
					'type' => 1,
					'note' =>'- '.$points . ' TT đơn hàng ID '. $sale_id
				);
				// Insert ospos_history_points
				$this->db->insert('history_points', $_aHistoryPoint);

				$data_total = array(
					'creator_personal_id' => $employee_id,
					'personal_id' => $customer_id, // this is a customer
					'amount' => $points
				);
				$data_total['payment_type'] = 'point';
				$data_total['kind'] = 'point';
				$data_total['payment_id'] = 0;
				$data_total['sale_id'] = $sale_id;
				$data_total['payment_method'] = 2; //use point to payment;
				$this->Accounting->save_income($data_total);
				
			}

			$this->db->trans_complete();
			
			$success &= $this->db->trans_status();
		}
		
		return $success;
	}
	
	public function save($history_ctv_data)
	{
		$this->db->trans_start(); // start transaction mysql

		$this->db->insert('history_ctv', $history_ctv_data);
		$_id = $this->db->insert_id();

		$sale_data['sync'] = 1;
		$this->db->where('sale_id', $history_ctv_data['sale_id']);
        $success = $this->db->update('sales', $sale_data);

		//Update total_sale of ctv
		$_ctv_info = $this->Employee->get_info($history_ctv_data['ctv_id']);
		$_ctv_data = [
			'total_sale'=>$_ctv_info->total_sale + $history_ctv_data['payment_amount'],
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

	public function delete($sale_id, $employee_id, $update_inventory = TRUE) 
	{
		// start a transaction to assure data integrity
		$this->db->trans_start();

		// first delete all payments
		$this->db->delete('sales_payments', array('sale_id' => $sale_id));
		// then delete all taxes on items
		$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id));

		if($update_inventory)
		{
			// defect, not all item deletions will be undone??
			// get array with all the items involved in the sale to update the inventory tracking
			$items = $this->get_sale_items($sale_id)->result_array();
			foreach($items as $item)
			{
				// create query to update inventory tracking
				$inv_data = array(
					'trans_date'      => date('Y-m-d H:i:s'),
					'trans_items'     => $item['item_id'],
					'trans_user'      => $employee_id,
					'trans_comment'   => 'Xóa đơn hàng ' . $sale_id,
					'trans_location'  => $item['item_location'],
					'trans_inventory' => $item['quantity_purchased']
				);
				// update inventory
				$this->Inventory->insert($inv_data);

				// update quantities
				$this->Item_quantity->change_quantity($item['item_id'], $item['item_location'], $item['quantity_purchased']);
			}
		}

		// delete all items
		$this->db->delete('sales_items', array('sale_id' => $sale_id));
		// delete sale itself
		$this->db->delete('sales', array('sale_id' => $sale_id));

		// execute transaction
		$this->db->trans_complete();
	
		return $this->db->trans_status();
	}
	public function get_sale_items($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->where('sale_id', $sale_id);
		return $this->db->get();
	}

	public function get_sale_payments($sale_id)
	{
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $sale_id);

		return $this->db->get();
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

	public function get_sale_by_code($code)
	{
		$this->db->from('sales');
		$this->db->where('code', $code);
		return $this->db->get()->row();
	}
	
}
?>
