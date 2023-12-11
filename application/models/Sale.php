<?php
class Sale extends CI_Model
{

	public function get_info($sale_id)
	{
		// NOTE: temporary tables are created to speed up searches due to the fact that are ortogonal to the main query
		// create a temporary table to contain all the payments per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_payments_temp') . 
			'(
				SELECT payments.sale_id AS sale_id, 
					IFNULL(SUM(payments.payment_amount), 0) AS sale_payment_amount,
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", payments.payment_amount) SEPARATOR ", ") AS payment_type
				FROM ' . $this->db->dbprefix('sales_payments') . ' AS payments
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = payments.sale_id
				WHERE sales.sale_id = ' . $this->db->escape($sale_id) . '
				GROUP BY sale_id
			)'
		);

		// create a temporary table to contain all the sum of taxes per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_items_taxes_temp') . 
			'(
				SELECT sales_items_taxes.sale_id AS sale_id,
					sales_items_taxes.item_id AS item_id,
					SUM(sales_items_taxes.percent) AS percent
				FROM ' . $this->db->dbprefix('sales_items_taxes') . ' AS sales_items_taxes
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = sales_items_taxes.sale_id
				INNER JOIN ' . $this->db->dbprefix('sales_items') . ' AS sales_items
					ON sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.line = sales_items_taxes.line
				WHERE sales.sale_id = ' . $this->db->escape($sale_id) . '
				GROUP BY sales_items_taxes.sale_id, sales_items_taxes.item_id
			)'
		);

		if($this->config->item('tax_included'))
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (100 / (100 + sales_items_taxes.percent)))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 - 100 / (100 + sales_items_taxes.percent)))';
		}
		else
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 + (sales_items_taxes.percent / 100)))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (sales_items_taxes.percent / 100))';
		}

		$decimals = totals_decimals();

		$this->db->select('
				sales.sale_id AS sale_id,
				sales.code as code,
				sales.ctv_id as ctv_id,
				sales.sync as sync,
				sales.status as status,
				sales.sale_uuid as sale_uuid,
				customer.account_number as account_number,
				DATE(sales.sale_time) AS sale_date,
				sales.sale_time AS sale_time,
				sales.comment AS comment,
				sales.invoice_number AS invoice_number,
				sales.employee_id AS employee_id,
				sales.customer_id AS customer_id,
				sales.ctv_id AS sale_man_id,
				CONCAT(customer_p.first_name, " ", customer_p.last_name) AS customer_name,
				customer_p.first_name AS first_name,
				customer_p.last_name AS last_name,
				customer_p.email AS email,
				customer_p.comments AS comments,
				customer_p.phone_number AS phone_number,
				' . "
				IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS amount_due,
				payments.sale_payment_amount AS amount_tendered,
				(payments.sale_payment_amount - IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals))) AS change_due,
				" . '
				payments.payment_type AS payment_type
		');

		$this->db->from('sales_items AS sales_items');
		$this->db->join('sales AS sales', 'sales_items.sale_id = sales.sale_id', 'inner');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');
		$this->db->join('sales_payments_temp AS payments', 'sales.sale_id = payments.sale_id', 'left outer');
		$this->db->join('sales_items_taxes_temp AS sales_items_taxes', 'sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.item_id = sales_items_taxes.item_id', 'left outer');

		$this->db->where('sales.sale_id', $sale_id);

		$this->db->group_by('sales.sale_id');
		$this->db->order_by('sales.sale_time', 'asc');

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
		// NOTE: temporary tables are created to speed up searches due to the fact that are ortogonal to the main query
		// create a temporary table to contain all the payments per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_payments_temp') . 
			' (PRIMARY KEY(sale_id), INDEX(sale_id))
			(
				SELECT payments.sale_id AS sale_id, 
					IFNULL(SUM(payments.payment_amount), 0) AS sale_payment_amount,
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", FORMAT(payments.payment_amount,0,"vi_VN")) SEPARATOR ", ") AS payment_type
				FROM ' . $this->db->dbprefix('sales_payments') . ' AS payments
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = payments.sale_id
				WHERE DATE(sales.sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']) . '
				GROUP BY sale_id
			)'
		);

		// create a temporary table to contain all the sum of taxes per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_items_taxes_temp') . 
			' (INDEX(sale_id), INDEX(item_id))
			(
				SELECT sales_items_taxes.sale_id AS sale_id,
					sales_items_taxes.item_id AS item_id,
					SUM(sales_items_taxes.percent) AS percent
				FROM ' . $this->db->dbprefix('sales_items_taxes') . ' AS sales_items_taxes
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = sales_items_taxes.sale_id
				INNER JOIN ' . $this->db->dbprefix('sales_items') . ' AS sales_items
					ON sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.line = sales_items_taxes.line
				WHERE DATE(sales.sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']) . ' OR sales.status = 1
				
				GROUP BY sales_items_taxes.sale_id, sales_items_taxes.item_id
			)'
		);

		if($this->config->item('tax_included'))
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (100 / (100 + sales_items_taxes.percent)))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 - 100 / (100 + sales_items_taxes.percent)))';
		}
		else
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 + (sales_items_taxes.percent / 100)))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (sales_items_taxes.percent / 100))';
		}

		$sale_cost = 'SUM(sales_items.item_cost_price * sales_items.quantity_purchased)';

		$decimals = totals_decimals();

		$this->db->select('
				sales.sale_id AS sale_id,
				sales.ctv_id AS bacsi_id,
				sales.status AS status,
				sales.sale_uuid AS sale_uuid,
				DATE(sales.sale_time) AS sale_date,
				sales.sale_time AS sale_time,
				sales.invoice_number AS invoice_number,
				SUM(sales_items.quantity_purchased) AS items_purchased,
				CONCAT(customer_p.last_name, " ", customer_p.first_name) AS customer_name,
				customer.company_name AS company_name,
				' . "
				ROUND($sale_subtotal, $decimals) AS subtotal,
				IFNULL(ROUND($sale_tax, $decimals), 0) AS tax,
				IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS total,
				ROUND($sale_cost, $decimals) AS cost,
				ROUND($sale_total - IFNULL($sale_tax, 0) - $sale_cost, $decimals) AS profit,
				IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS amount_due,
				payments.sale_payment_amount AS amount_tendered,
				(payments.sale_payment_amount - IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals))) AS change_due,
				" . '
				payments.payment_type AS payment_type
		');

		$this->db->from('sales_items AS sales_items');
		$this->db->join('sales AS sales', 'sales_items.sale_id = sales.sale_id', 'inner');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');
		$this->db->join('sales_payments_temp AS payments', 'sales.sale_id = payments.sale_id', 'left outer');
		$this->db->join('sales_items_taxes_temp AS sales_items_taxes', 'sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.item_id = sales_items_taxes.item_id', 'left outer');
		$this->db->where('current', 1); //added by ManhVT hỗ trợ việc chỉ xét các bản ghi có hiệu lực;
		if($bUser_type == 2)
		{
			$this->db->where('DATE(sales.sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
			$this->db->where('sales.ctv_id',$iUser_Id);
		}else{
			if(!$filters['pending'])
			{
				$this->db->where('DATE(sales.sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
				$this->db->where('status', 0);
			} else {
				$this->db->where('status', 1);
			}
		}
		
		if(!empty($search))
		{
			if($filters['is_valid_receipt'] != FALSE)
			{
				$pieces = explode(' ', $search);
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

		if($filters['location_id'] != 'all')
		{
			$this->db->where('sales_items.item_location', $filters['location_id']);
		}

		if($filters['sale_type'] == 'sales')
        {
            $this->db->where('sales_items.quantity_purchased > 0');
        }
        elseif($filters['sale_type'] == 'returns')
        {
            $this->db->where('sales_items.quantity_purchased < 0');
        }

		if($filters['only_invoices'] != FALSE)
		{
			$this->db->where('sales.invoice_number IS NOT NULL');
		}
	
		
		//var_dump($this->db);
		if($filters['only_cash'] != FALSE)
		{
			$this->db->group_start();
				$this->db->like('payments.payment_type', $this->lang->line('sales_cash'), 'after');
				$this->db->or_where('payments.payment_type IS NULL');
			$this->db->group_end();
		}

		$this->db->group_by('sales.sale_id');
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
		$this->db->from('sales');

		return $this->db->count_all_results();
	}

	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		if(!$this->is_valid_receipt($search))
		{
			$this->db->distinct();
			$this->db->select('first_name, last_name');
			$this->db->from('sales');
			$this->db->join('people', 'people.person_id = sales.customer_id');
			$this->db->like('last_name', $search);
			$this->db->or_like('first_name', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
			//$this->db->or_like('company_name', $search);
			$this->db->order_by('last_name', 'asc');

			foreach($this->db->get()->result_array() as $result)
			{
				$suggestions[] = array('label' => $result['first_name'] . ' ' . $result['last_name']);
			}
		}
		else
		{
			$suggestions[] = array('label' => $search);
		}

		return $suggestions;
	}

	/*
	Gets total of invoice rows
	*/
	public function get_invoice_count()
	{
		$this->db->from('sales');
		$this->db->where('invoice_number IS NOT NULL');

		return $this->db->count_all_results();
	}

	public function get_sale_by_invoice_number($invoice_number)
	{
		$this->db->from('sales');
		$this->db->where('invoice_number', $invoice_number);

		return $this->db->get();
	}

	public function get_invoice_number_for_year($year = '', $start_from = 0) 
	{
		$year = $year == '' ? date('Y') : $year;
		$this->db->select('COUNT( 1 ) AS invoice_number_year');
		$this->db->from('sales');
		$this->db->where('DATE_FORMAT(sale_time, "%Y" ) = ', $year);
		$this->db->where('invoice_number IS NOT NULL');
		$result = $this->db->get()->row_array();

		return ($start_from + $result['invoice_number_year']);
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

	public function exists($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);

		return ($this->db->get()->num_rows()==1);
	}

	public function exists_by_code($code)
	{
		$this->db->from('sales');
		$this->db->where('code', $code);

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
			if($sale_data['ctv_id'] == 0) // Nếu không có CTV thì không đồng bộ vào bảng history_ctv
			{
				$sale_data['sync'] = 1;
			} else {
				$sale_data['sync'] = 0;
			}
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

	//Import old_data
	public function import_sale($obj)
	{
		if(!isset($obj))
		{
			return -1;
		}

		$sales_data = array(
			'sale_time'		 => date('Y-m-d h:m:s',$obj['sale_time']),
			'customer_id'	 => $obj['customer_id'],
			'employee_id'	 => $obj['employee_id'],
			'comment'		 => $obj['comment'],
			'status'=>$obj['status'],
			'test_id'=>$obj['test_id'],
			'code'=>$obj['code']
		);

		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();

		$sales_payments_data = array(
			'sale_id'		 => $sale_id,
			'payment_type'	 => 'Tiền mặt',
			'payment_amount' => $obj['tongtien'],
			'payment_kind'=>'Thanh Toán'
		);
		$this->db->insert('sales_payments', $sales_payments_data);
		$payment_id = $this->db->insert_id();

		$data_total = array(
			'creator_personal_id'=>$obj['employee_id'],
			'personal_id'=>$obj['customer_id'], // this is a customer
			'amount'=>$obj['tongtien']
		);
		$data_total['payment_type'] = 'Tiền mặt';
		$data_total['kind'] = 'Thanh toán';
		$data_total['payment_id'] = $payment_id;
		$data_total['sale_id'] = $sale_id;
		$data_total['created_time'] = $obj['sale_time'];
		$this->Accounting->save_income($data_total);
		if(isset($obj['items'])) {
			$items = $obj['items'];
			foreach ($items as $line => $item) {
				$cur_item_info = $this->Item->get_info($item['item_id']);

				$sales_items_data = array(
					'sale_id' => $sale_id,
					'item_id' => $item['item_id'],
					'description' => '',
					'quantity_purchased' => $item['quantity_purchased'],
					'discount_percent' => $item['discount_percent'],
					'item_cost_price' => $item['item_cost_price'],
					'item_unit_price' => $item['item_unit_price'],
					'item_location' => $item['item_location']
				);

				$this->db->insert('sales_items', $sales_items_data);
			}
		}else{
			echo $obj['code'];
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $sale_id;
	}

	public function save($items, $customer_id, $employee_id, $comment, $invoice_number, $payments,$amount_change,$suspended_sale_id=null, $ctv_id = 0 ,$status = 0,$test_id=0, $kxv_id = 0,$doctor_id=0,$points=0,$code = '')
	{
		//var_dump($items);die();
		if(count($items) == 0) // if cart is empty
		{
			return -1;
		}
		$now = time();
		if($code == '')
		{
			//$code = 'STD' . time();
			$code = 'STD' . $now;
		}
		$sync = 0;
		if($ctv_id == 0) // Nếu không có cộng tác vieenl sync = 1; không đồng bộ vào bảng history_ctv
		{
			$sync = 1;
		}

		$sales_data = array(
			'sale_time'		 => date('Y-m-d H:i:s',$now),//'sale_time'		 => date('Y-m-d H:i:s'),
			'customer_id'	 => $this->Customer->exists($customer_id) ? $customer_id : null,
			'employee_id'	 => $employee_id,
			'comment'		 => $comment,
			'invoice_number' => $invoice_number,
			'status'=>$status,
			'test_id'=>$test_id,
			'ctv_id' =>$ctv_id,
			'kxv_id' => $kxv_id,
			'doctor_id'=>$doctor_id,
			'paid_points'=>$points,
			'code'=>$code,
			'sync'=>$sync,
			'created_at'=>$now //added 01/07/2023
		);
		//var_dump($sales_data);die();
		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();
		// Suport send SMS
		$cus_obj = $this->Customer->get_info($customer_id);
		$sms['sale_id'] = $sale_id;
		$sms['is_sms'] = 0;
		$sms['name'] = $cus_obj->last_name . ' '. $cus_obj->first_name;
		$sms['phone'] = $cus_obj->phone_number;
		$sms['customer_id'] = $cus_obj->person_id;
		$sms['saled_date'] = $sales_data['sale_time'];
		$this->db->insert('sms_sale', $sms);
		// Suport send SMS
		// Make Payment
		foreach($payments as $payment_id=>$payment)
		{
			if( substr( $payment['payment_type'], 0, strlen( $this->lang->line('sales_giftcard') ) ) == $this->lang->line('sales_giftcard') )
			{
				// We have a gift card and we have to deduct the used value from the total value of the card.
				$splitpayment = explode( ':', $payment['payment_type'] );
				$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
				$this->Giftcard->update_giftcard_value( $splitpayment[1], $cur_giftcard_value - $payment['payment_amount'] );
			}
			if($payment['payment_amount'] != 0)
			{
				$sales_payments_data = array(
					'sale_id'		 => $sale_id,
					'payment_type'	 => $payment['payment_type'],
					'payment_amount' => $payment['payment_amount'],
					'payment_kind'=>$payment['payment_kind']
				);
				//var_dump($sales_payments_data); die();
				$this->db->insert('sales_payments', $sales_payments_data);
				$payment_id = $this->db->insert_id();
				//var_dump($payment);die();
				//if($payment['payment_type'] == 'Tiền mặt') { // If tiền mặt then insert accounting
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

					if ($amount_change > 0) {
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
				} elseif($this->lang->line("sales_check") == $payment['payment_type']){
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

				} elseif( $payment['payment_type'] == $this->lang->line("sales_debit")) {
					$data_total = array(
						'creator_personal_id' => $employee_id,
						'personal_id' => $customer_id, // this is a customer
						'amount' => $payment['payment_amount']
					);
					$data_total['payment_type'] = $payment['payment_type'];
					$data_total['kind'] = $payment['payment_kind'];
					$data_total['payment_id'] = $payment_id;
					$data_total['sale_id'] = $sale_id;
					$data_total['payment_method'] = 2; //Fire (tặng khách hàng);
					$this->Accounting->save_income($data_total);
				}
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

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$sales_items_data = array(
				'sale_id'			=> $sale_id,
				'item_id'			=> $item['item_id'],
				'line'				=> $item['line'],
				'description'		=> character_limiter($item['description'], 30),
				'serialnumber'		=> character_limiter($item['serialnumber'], 30),
				'quantity_purchased'=> $item['quantity'],
				'discount_percent'	=> $item['discount'],
				'item_cost_price'	=> $cur_item_info->cost_price,
				'item_unit_price'	=> $item['price'],
				'item_location'		=> $item['item_location'],
				'item_name'			=>$item['name'],
				'item_category'     => $item['item_category'],
				'item_supplier_id'  =>$item['item_supplier_id'],
				'item_number'		=>$item['item_number'],
				'item_description'	=>$item['description']
			);

			$this->db->insert('sales_items', $sales_items_data);

			// Update stock quantity
			$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
			$this->Item_quantity->save(array('quantity'		=> $item_quantity->quantity - $item['quantity'],
				'item_id'		=> $item['item_id'],
				'location_id'	=> $item['item_location']), $item['item_id'], $item['item_location']);

			// if an items was deleted but later returned it's restored with this rule
			if($item['quantity'] < 0)
			{
				$this->Item->undelete($item['item_id']);
			}

			// Inventory Count Details
			$sale_remarks = 'Bán '.$sale_id;
			$inv_data = array(
				'trans_date'		=> date('Y-m-d H:i:s'),
				'trans_items'		=> $item['item_id'],
				'trans_user'		=> $employee_id,
				'trans_location'	=> $item['item_location'],
				'trans_comment'		=> $sale_remarks,
				'trans_inventory'	=> -$item['quantity']
			);
			$this->Inventory->insert($inv_data);

			$customer = $this->Customer->get_info($customer_id);
			if($customer_id == -1 || $customer->taxable)
			{
				foreach($this->Item_taxes->get_info($item['item_id']) as $row)
				{
					$this->db->insert('sales_items_taxes', array(
						'sale_id' 	=> $sale_id,
						'item_id' 	=> $item['item_id'],
						'line'      => $item['line'],
						'name'		=> $row['name'],
						'percent' 	=> $row['percent']
					));
				}
			}
		}
		//echo $suspended_sale_id;
		if($suspended_sale_id) {
			$this->Sale_suspended->locksuspend($suspended_sale_id);
		}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $sale_id;
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

	public function get_payment_options($giftcard = TRUE, $cfPoint = TRUE)
	{
		$payments = array();
		/*
		
		if($this->config->item('payment_options_order') == 'debitcreditcash')
		{
			$payments[$this->lang->line('sales_debit')] = $this->lang->line('sales_debit');
			$payments[$this->lang->line('sales_credit')] = $this->lang->line('sales_credit');
			$payments[$this->lang->line('sales_cash')] = $this->lang->line('sales_cash');
		}
		elseif($this->config->item('payment_options_order') == 'debitcashcredit')
		{
			$payments[$this->lang->line('sales_debit')] = $this->lang->line('sales_debit');
			$payments[$this->lang->line('sales_cash')] = $this->lang->line('sales_cash');
			$payments[$this->lang->line('sales_credit')] = $this->lang->line('sales_credit');
		}
		else // default: if($this->config->item('payment_options_order') == 'cashdebitcredit')
		{
			$payments[$this->lang->line('sales_cash')] = $this->lang->line('sales_cash');
			$payments[$this->lang->line('sales_debit')] = $this->lang->line('sales_debit');
			$payments[$this->lang->line('sales_credit')] = $this->lang->line('sales_credit');
		}

		//$payments[$this->lang->line('sales_check')] = $this->lang->line('sales_check');
		*/
		$payments[$this->lang->line('sales_check')] = $this->lang->line('sales_check');
		$payments[$this->lang->line('sales_debit')] = $this->lang->line('sales_debit');
		//$payments[$this->lang->line('sales_credit')] = $this->lang->line('sales_credit');
		$payments[$this->lang->line('sales_cash')] = $this->lang->line('sales_cash');
		if($cfPoint)
		{
			$payments[$this->lang->line('sales_point')] = $this->lang->line('sales_point');
		}

		if($giftcard)
		{
			$payments[$this->lang->line('sales_giftcard')] = $this->lang->line('sales_giftcard');
		}

		return $payments;
	}

	public function get_customer($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}

	public function get_employee($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);

		return $this->Employee->get_info($this->db->get()->row()->employee_id);
	}

	public function get_ctv($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id', $sale_id);

		return $this->Employee->get_info($this->db->get()->row()->ctv_id);
	}

	public function check_invoice_number_exists($invoice_number, $sale_id = '')
	{
		$this->db->from('sales');
		$this->db->where('invoice_number', $invoice_number);
		if(!empty($sale_id))
		{
			$this->db->where('sale_id !=', $sale_id);
		}
		
		return ($this->db->get()->num_rows() == 1);
	}

	public function get_giftcard_value($giftcardNumber)
	{
		if(!$this->Giftcard->exists($this->Giftcard->get_giftcard_id($giftcardNumber)))
		{
			return 0;
		}
		
		$this->db->from('giftcards');
		$this->db->where('giftcard_number', $giftcardNumber);

		return $this->db->get()->row()->value;
	}

	//We create a temp table that allows us to do easy report/sales queries
	public function create_temp_table(array $inputs)
	{
		if($this->config->item('tax_included'))
		{
			$sale_total = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_subtotal = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (100 / (100 + SUM(sales_items_taxes.percent))))';
			$sale_tax = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 - 100 / (100 + SUM(sales_items_taxes.percent))))';
		}
		else
		{
			$sale_total = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 + (SUM(sales_items_taxes.percent) / 100)))';
			$sale_subtotal = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_tax = '(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (SUM(sales_items_taxes.percent) / 100))';
		}

		$sale_cost  = '(sales_items.item_cost_price * sales_items.quantity_purchased)';

		$decimals = totals_decimals();

		if(empty($inputs['sale_id']))
		{
			$where = 'WHERE DATE(sales.sale_time) BETWEEN ' . $this->db->escape($inputs['start_date']) . ' AND ' . $this->db->escape($inputs['end_date']);
		}
		else
		{
			$where = 'WHERE sales.sale_id = ' . $this->db->escape($inputs['sale_id']);
		}

		// create a temporary table to contain all the payment types and amount
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_payments_temp') . 
			' (PRIMARY KEY(sale_id), INDEX(sale_id))
			(
				SELECT payments.sale_id AS sale_id, 
					IFNULL(SUM(payments.payment_amount), 0) AS sale_payment_amount,
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", FORMAT(payments.payment_amount,0,"vi_VN")) SEPARATOR ", ") AS payment_type
				FROM ' . $this->db->dbprefix('sales_payments') . ' AS payments
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = payments.sale_id
				' . "
				$where
				" . '
				GROUP BY payments.sale_id
			)'
		);

		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_items_temp') . 
			' (INDEX(sale_date), INDEX(sale_id))
			(
				SELECT
					DATE(sales.sale_time) AS sale_date,
					sales.sale_time,
					sales.sale_id,
					sales.sale_uuid,
					sales.comment,
					sales.invoice_number,
					sales.customer_id,
					sales.ctv_id,
					sales.kind,
					CONCAT(customer_p.last_name, " ", customer_p.first_name) AS customer_name,
					customer_p.first_name AS customer_first_name,
					customer_p.last_name AS customer_last_name,
					customer_p.email AS customer_email,
					customer_p.comments AS customer_comments, 
					customer.company_name AS customer_company_name,
					sales.employee_id,
					CONCAT(employee.last_name, " ", employee.first_name) AS employee_name,
					items.item_id,
					items.name,
					items.item_number,
					items.category,
					items.supplier_id,
					sales_items.quantity_purchased,
					sales_items.item_cost_price,
					sales_items.item_unit_price,
					sales_items.discount_percent,
					sales_items.line,
					sales_items.serialnumber,
					sales_items.item_location,
					sales_items.description,
					payments.payment_type,
					payments.sale_payment_amount,
					IFNULL(SUM(sales_items_taxes.percent), 0) AS item_tax_percent,
					' . "
					ROUND($sale_subtotal, $decimals) AS subtotal,
					IFNULL(ROUND($sale_tax, $decimals), 0) AS tax,
					IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS total,
					ROUND($sale_cost, $decimals) AS cost,
					ROUND($sale_total - IFNULL($sale_tax, 0) - $sale_cost, $decimals) AS profit
					" . '
				FROM ' . $this->db->dbprefix('sales_items') . ' AS sales_items
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales_items.sale_id = sales.sale_id
				INNER JOIN ' . $this->db->dbprefix('items') . ' AS items
					ON sales_items.item_id = items.item_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('sales_payments_temp') . ' AS payments
					ON sales_items.sale_id = payments.sale_id		
				LEFT OUTER JOIN ' . $this->db->dbprefix('suppliers') . ' AS supplier
					ON items.supplier_id = supplier.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('people') . ' AS customer_p
					ON sales.customer_id = customer_p.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('customers') . ' AS customer
					ON sales.customer_id = customer.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('people') . ' AS employee
					ON sales.employee_id = employee.person_id
				LEFT OUTER JOIN ' . $this->db->dbprefix('sales_items_taxes') . ' AS sales_items_taxes
					ON sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.item_id = sales_items_taxes.item_id AND sales_items.line = sales_items_taxes.line
				' . "
				$where
				" . '
				GROUP BY sales.sale_id, items.item_id, sales_items.line
			)'
		);

		// drop the temporary table to contain memory consumption as it's no longer required
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS ' . $this->db->dbprefix('sales_payments_temp'));
	}

	//public function

	public function get_info_by_uuid($uuid)
	{
		// NOTE: temporary tables are created to speed up searches due to the fact that are ortogonal to the main query
		// create a temporary table to contain all the payments per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_payments_temp') . 
			'(
				SELECT payments.sale_id AS sale_id, 
					IFNULL(SUM(payments.payment_amount), 0) AS sale_payment_amount,
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", payments.payment_amount) SEPARATOR ", ") AS payment_type
				FROM ' . $this->db->dbprefix('sales_payments') . ' AS payments
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = payments.sale_id
				WHERE sales.sale_uuid = ' . $this->db->escape($uuid) . '
				GROUP BY sale_id
			)'
		);

		// create a temporary table to contain all the sum of taxes per sale item
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . $this->db->dbprefix('sales_items_taxes_temp') . 
			'(
				SELECT sales_items_taxes.sale_id AS sale_id,
					sales_items_taxes.item_id AS item_id,
					SUM(sales_items_taxes.percent) AS percent
				FROM ' . $this->db->dbprefix('sales_items_taxes') . ' AS sales_items_taxes
				INNER JOIN ' . $this->db->dbprefix('sales') . ' AS sales
					ON sales.sale_id = sales_items_taxes.sale_id
				INNER JOIN ' . $this->db->dbprefix('sales_items') . ' AS sales_items
					ON sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.line = sales_items_taxes.line
				WHERE sales.sale_uuid = ' . $this->db->escape($uuid) . '
				GROUP BY sales_items_taxes.sale_id, sales_items_taxes.item_id
			)'
		);

		if($this->config->item('tax_included'))
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (100 / (100 + sales_items_taxes.percent)))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 - 100 / (100 + sales_items_taxes.percent)))';
		}
		else
		{
			$sale_total = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (1 + (sales_items_taxes.percent / 100)))';
			$sale_subtotal = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100))';
			$sale_tax = 'SUM(sales_items.item_unit_price * sales_items.quantity_purchased * (1 - sales_items.discount_percent / 100) * (sales_items_taxes.percent / 100))';
		}

		$decimals = totals_decimals();

		$this->db->select('
				sales.sale_id AS sale_id,
				sales.code as code,
				sales.confirm as confirm,
				sales.sale_uuid as sale_uuid,
				DATE(sales.sale_time) AS sale_date,
				sales.sale_time AS sale_time,
				sales.comment AS comment,
				sales.invoice_number AS invoice_number,
				sales.employee_id AS employee_id,
				sales.customer_id AS customer_id,
				sales.ctv_id AS sale_man_id,
				sales.kxv_id as kxv_id,
				sales.doctor_id as doctor_id,
				sales.paid_points as paid_points,
				CONCAT(customer_p.first_name, " ", customer_p.last_name) AS customer_name,
				customer_p.first_name AS first_name,
				customer_p.last_name AS last_name,
				customer_p.email AS email,
				customer_p.comments AS comments,
				customer.customer_uuid AS c_uuid,
				customer_p.phone_number AS phone_number,
				customer.account_number as account_number,
				customer.points as points,
				' . "
				IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals)) AS amount_due,
				payments.sale_payment_amount AS amount_tendered,
				(payments.sale_payment_amount - IFNULL(ROUND($sale_total, $decimals), ROUND($sale_subtotal, $decimals))) AS change_due,
				" . '
				payments.payment_type AS payment_type
		');

		$this->db->from('sales_items AS sales_items');
		$this->db->join('sales AS sales', 'sales_items.sale_id = sales.sale_id', 'inner');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');
		$this->db->join('sales_payments_temp AS payments', 'sales.sale_id = payments.sale_id', 'left outer');
		$this->db->join('sales_items_taxes_temp AS sales_items_taxes', 'sales_items.sale_id = sales_items_taxes.sale_id AND sales_items.item_id = sales_items_taxes.item_id', 'left outer');

		$this->db->where('sales.sale_uuid', $uuid);

		$this->db->group_by('sales.sale_id');
		$this->db->order_by('sales.sale_time', 'asc');

		return $this->db->get();
	}

	public function update_confirm($confirm, $q1,$q2,$q3, $sale_info)
	{
		$this->db->trans_start(); // start Transaction
		$success = 0;
		$rate = 0.1; // load in setting;
		//var_dump($sale_info); die();
		$_paidAmount = bcsub($sale_info['amount_due'],$sale_info['paid_points']);
		$point = bcmul($rate,$_paidAmount );
		$sale_data['confirm'] = $confirm;
		$sale_uuid = $sale_info['sale_uuid'];
		//1. Update the sale with sale_uuid
		$this->db->where('sale_uuid', $sale_uuid);
		$success = $this->db->update('sales', $sale_data);

		//2. Update point of customer

		$customer_info['points'] = $sale_info['points'] + $point;
		$this->db->where('person_id',$sale_info['customer_id']);
		$this->db->update('customers',$customer_info);


		//3. Insert ospos_short_survey table

		$_aServey = array(
			'customer_id'=>$sale_info['customer_id'],
			'sale_id' =>$sale_info['sale_id'],
			'sale_uuid' => $sale_uuid,
			'nvbh_id' =>$sale_info['sale_man_id'],
			'kxv_id' =>$sale_info['kxv_id'],
			'created_date' => time(),
			'q1' => $q1,
			'q2' => $q2,
			'q3' => $q3
		);
		// Insert Short_Survey
		$this->db->insert('short_survey', $_aServey);


		//4. insert ospos_history_points

		$_aHistoryPoint = array(
			'customer_id' =>$sale_info['customer_id'],
			'sale_id' => $sale_info['sale_id'],
			'sale_uuid' => $sale_info['sale_uuid'],
			'created_date' =>time(),
			'point' =>$point,
			'type' => 1,
			'note' =>'+ '.$point . ' boi '. $sale_info['sale_uuid']
		);
		// Insert ospos_history_points
		$this->db->insert('history_points', $_aHistoryPoint);

		
		$this->db->trans_complete(); // execute transaction
	
		return $this->db->trans_status(); // end Transaction
	}

	public function get_sale_by_code($code)
	{
		$this->db->from('sales');
		$this->db->where('code', $code);
		return $this->db->get()->row();
	}
	//public function save($items, $customer_id, $employee_id, $comment, $invoice_number, $payments,$amount_change,$suspended_sale_id=null, $ctv_id = 0 ,$status = 0,$test_id=0, $kxv_id = 0,$doctor_id=0,$points=0,$sale_id = FALSE)
	public function edit(&$sale_id, $items, $customer_id, $employee_id, $comment, $invoice_number, $payments,$amount_change,$suspended_sale_id=null, $ctv_id = 0 ,$status = 0,$test_id=0, $kxv_id = 0,$doctor_id=0,$update_inventory=TRUE,$points=0)
	{
		// Chỉnh sửa đơn hàng gồm: Các sản phẩm; và đã tạm ứng tiền;
		// Chú ý hoạt động chỉnh sửa chỉ có thể chỉnh sửa trong thời gian là ngày hiện tại;
	
		$sale_info = $this->get_info($sale_id)->row();

		if(empty($sale_info))
		{
			return 0; 
		} 
		if($sale_info->status == 0)
		{
			return 0; //Không làm gì
		}
		// Kiểm tra xem đơn hàng này có phải ngày hiện tại không. Nếu không phải thì không làm gì return 0
		$sale_time = $sale_info->sale_time;

		$_dSaleDate = date('Y-m-d',strtotime($sale_time));
		$_dToday = date('Y-m-d');
		if($_dSaleDate != $_dToday)
		{
			return 0; // Không làm gì cả nếu không phải đơn hàng hôm nay
		}

		$code = $sale_info->code;
		$success = 0;
		$_iNewSaleID = 0;
		if(!empty($payments) && !empty($items))
		{
			//Run these queries as a transaction, we want to make sure we do all or nothing
			$this->db->trans_start();
			//1.Xóa sale với sale_id () && status = 0; đơn hàng hoàn thành không cho phép xóa;
			
			//$this->db->delete('sales_payments', array('sale_id' => $sale_id));
			// then delete all taxes on items
			$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id));

			if($update_inventory)
			{
				// defect, not all item deletions will be undone??
				// get array with all the items involved in the sale to update the inventory tracking
				$_aItems = $this->get_sale_items($sale_id)->result_array();
				foreach($_aItems as $item)
				{
					// create query to update inventory tracking
					$inv_data = array(
						'trans_date'      => date('Y-m-d H:i:s'),
						'trans_items'     => $item['item_id'],
						'trans_user'      => $employee_id,
						'trans_comment'   => 'Hoàn kho (cập nhật đơn hàng) ' . $sale_info->code,
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
			//$this->db->delete('sales', array('sale_id' => $sale_id));

			//1.4 delete acctoing with sale_id
			//$this->db->delete('total', array('sale_id' => $sale_id));
			
			//2. Tạo mới sale
			/* 01/07/2023
			$_iNewSaleID = $this->save(
				$items,
				$customer_id,
				$employee_id,
				$comment,
				$invoice_number,
				$payments,
				$amount_change,
				$suspended_sale_id,
				$ctv_id,
				$status,
				$test_id,
				$kxv_id,
				$doctor_id,
				$points,
				$code
			);
			*/
			//2 Update item on sale
			foreach($items as $line=>$item)
			{
				$cur_item_info = $this->Item->get_info($item['item_id']);

				$sales_items_data = array(
					'sale_id'			=> $sale_id,
					'item_id'			=> $item['item_id'],
					'line'				=> $item['line'],
					'description'		=> character_limiter($item['description'], 30),
					'serialnumber'		=> character_limiter($item['serialnumber'], 30),
					'quantity_purchased'=> $item['quantity'],
					'discount_percent'	=> $item['discount'],
					'item_cost_price'	=> $cur_item_info->cost_price,
					'item_unit_price'	=> $item['price'],
					'item_location'		=> $item['item_location'],
					'item_name'			=>$item['name'],
					'item_category'     => $item['item_category'],
					'item_supplier_id'  =>$item['item_supplier_id'],
					'item_number'		=>$item['item_number'],
					'item_description'	=>$item['description']
				);

				$this->db->insert('sales_items', $sales_items_data);

				// Update stock quantity
				$item_quantity = $this->Item_quantity->get_item_quantity($item['item_id'], $item['item_location']);
				$this->Item_quantity->save(array('quantity'		=> $item_quantity->quantity - $item['quantity'],
					'item_id'		=> $item['item_id'],
					'location_id'	=> $item['item_location']), $item['item_id'], $item['item_location']);

				// if an items was deleted but later returned it's restored with this rule
				if($item['quantity'] < 0)
				{
					$this->Item->undelete($item['item_id']);
				}

				// Inventory Count Details
				$sale_remarks = 'Bán '.$sale_id;
				$inv_data = array(
					'trans_date'		=> date('Y-m-d H:i:s'),
					'trans_items'		=> $item['item_id'],
					'trans_user'		=> $employee_id,
					'trans_location'	=> $item['item_location'],
					'trans_comment'		=> $sale_remarks,
					'trans_inventory'	=> -$item['quantity']
				);
				$this->Inventory->insert($inv_data);

				$customer = $this->Customer->get_info($customer_id);
				if($customer_id == -1 || $customer->taxable)
				{
					foreach($this->Item_taxes->get_info($item['item_id']) as $row)
					{
						$this->db->insert('sales_items_taxes', array(
							'sale_id' 	=> $sale_id,
							'item_id' 	=> $item['item_id'],
							'line'      => $item['line'],
							'name'		=> $row['name'],
							'percent' 	=> $row['percent']
						));
					}
				}
			}




            // first delete all payments
			// $this->db->delete('sales_payments', array('sale_id' => $sale_id));
			$cus_obj = $this->Customer->get_info($customer_id);
			// Thêm thanh toán
			/*
			** Tạm không dùng (được update ngày 05.02.2023)
			foreach($payments as $payment)
			{
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
						$out_data['kind'] = 2;
						$out_data['payment_id'] = $payment_id;
						$out_data['sale_id'] = $sale_id;
	
	
						$this->Accounting->save_payout($out_data);
					}
				}
			}
			End
			*/
			//Add by ManhVT: sử dụng điểm thanh toán
			/* Da duoc thuc hien trong ham save
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
				
			}
			*/

			$this->db->trans_complete();
			
			$success &= $this->db->trans_status();
		}
		//$sale_id = $_iNewSaleID; 01/07/2023 tương đương 17/06/2023 ()
		return $success;
	}

	/**
	 * Update thông tin đơn hàng;
	 * 12/12/2023
	 * ManhVT89@gmail.com
	 */
	public function ajax_save_info($sale_id, $sale_data, $payments=[])
	{
		
		$success = 0;
		$customer_id = $sale_data['customer_id'];
		$employee_id = $sale_data['employee_id'];
		if(empty($payments))
		{
			$this->db->where('sale_id', $sale_id);
            $success = $this->db->update('sales', $sale_data);
		} else {
			if($sale_data['ctv_id'] == 0) // Nếu không có CTV thì không đồng bộ vào bảng history_ctv
			{
				$sale_data['sync'] = 1;
			} else {
				$sale_data['sync'] = 0;
			}
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

			$this->db->trans_complete();
			
			$success &= $this->db->trans_status();
		} 
		
		return $success;
	}
}
?>
