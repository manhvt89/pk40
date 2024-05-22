<?php
class Accounting extends CI_Model
{

	public function get_daily_total_info($date='')
	{
		$this->db->select('*');
		$this->db->from('daily_total');
		if($date != '') {
			if($date == 'yesterday') //LastDate
			{
				//$this->db->order_by('created_time','desc');
				//$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
				$this->db->where('DATE(FROM_UNIXTIME(created_time))', 'DATE(NOW() - INTERVAL 1 DAY)',false);
				$this->db->limit(1);
			}else {
				$this->db->where('DATE(FROM_UNIXTIME(created_time))', $date, false);
			}
		}else{
			$this->db->where('DATE(FROM_UNIXTIME(created_time))', 'CURDATE()',false);
		}
		$rs = $this->db->get()->result_array();
		if(!empty($rs))
		{
			return $rs[0];
		}else{
			return null;
		}
	}
	/**
	 * Summary of exists
	 * @param mixed $date Empty ís current Date
	 * @return bool
	 */
	public function exists($date='')
	{
		$this->db->from('daily_total');
		if($date != '') {
			$this->db->where('DATE(FROM_UNIXTIME(created_time))', "DATE(FROM_UNIXTIME($date))",false);
		}else{
			$this->db->where('DATE(FROM_UNIXTIME(created_time))', 'CURDATE()',false);
		}
		return ($this->db->get()->num_rows() > 0);
	}
	/*
	 * Insert new record to table
	 * data[created_time]
	 * data[begining_amount]
	 * data[ending_amount]
	 * data[increase_amount]
	 * data[decrease_amount]
	 *
	 */
	public function insert_daily_total($data)
	{
		if(!$this->exists()){
			$this->db->insert('daily_total', $data);
			$daily_total_id = $this->db->insert_id();
			return $daily_total_id;
		}else{
			return null;
		}
	}

	public function auto_create_daily_total(){
		if(!$this->exists()){
			$yesterday_totol_data = $this->get_daily_total_info('yesterday'); // Lấy bản ghi ngày hôm qua
			if($yesterday_totol_data)
			{

				$_LastDate = $yesterday_totol_data['created_time'];

				$filters['start_date'] = date('Y-m-d', $_LastDate);
				$filters['end_date'] = date('Y-m-d', $_LastDate);
				$_rsTotal = $this->get_accounting_summary($filters);

				//var_dump($_rsTotal);

				$begin = $_rsTotal['starting'] + $_rsTotal['in'] - $_rsTotal['po'];
				$end = 0;

				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => $begin,
					'ending_amount' => $end,
					'increase_amount' => 0,
					'decrease_amount' => 0 );

				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => $begin,
					'ending_amount' => $end,
					'increase_amount' => 0,
					'decrease_amount' => 0 );

			}else{
				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => 0,
					'ending_amount' => 0,
					'increase_amount' => 0,
					'decrease_amount' => 0 );
			}
			$daily_total_id = $this->insert_daily_total($daily_total_data);
		}
	}
		/*
         * Insert new payout record to _total table
         * data[created_time]-
         * data[daily_total_id]-
         * data[amount]
         * data[payment_type]-
	 	* data[payment_id]-
	 	* 		data[code]:- code of pa
	 	*  data[type]:- type of payment: 0: in; 1: out
	 	* data[creator_personal_id]
	 	* data[personal_id]
	 	* data[sale_id]
	 	* data[kind]    : 0:paid; 1:prepaid
         *
         */
	public function save_income($income_data)
	{
		$daily_total_id = 0;
		if(!$this->exists()){
			//$yesterday_totol_data = $this->get_daily_total_info('yesterday');
			$the_last_total_data = $this->get_the_last_total_daily();
			$yesterday_totol_data = $the_last_total_data;
			if($yesterday_totol_data)
			{
				$_LastDate = $yesterday_totol_data['created_time'];

				$filters['start_date'] = date('Y-m-d', strtotime($_LastDate));
				$filters['end_date'] = date('Y-m-d', strtotime($_LastDate));
				$_rsTotal = $this->get_accounting_summary($filters);

				$begin = $_rsTotal['starting'] + $_rsTotal['in'] - $_rsTotal['po'];
				$end = 0;

				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => $begin,
					'ending_amount' => $end,
					'increase_amount' => 0,
					'decrease_amount' => 0 );

			}else{
				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => 0,
					'ending_amount' => 0,
					'increase_amount' => 0,
					'decrease_amount' => 0 );
			}
			$daily_total_id = $this->insert_daily_total($daily_total_data);
		}else{
			$today_rs = $this->get_daily_total_info();
			$daily_total_id = $today_rs['daily_total_id'];
		}

		$daily_rs = $this->get_daily_total_info();
		if(!empty($daily_rs))
		{
			$income_data['daily_total_id']=$daily_total_id;
			if(!isset($income_data['created_time'])) {
				$income_data['created_time'] = time();
			}
			$income_data['type'] = 0; //income
			$income_data['code'] = 'IC'.$income_data['created_time'];
			//$payout_data['payment_type'] = 'Tiền mặt'; used {cash, banking}
			//$payout_data['payment_id'] = 0;//used
			//$payout_data['sale_id'] = 0;// used
			//$payout_data['kind'] = 0; //used
			//var_dump($payout_data);
			$this->db->trans_start();
			//1. Insert new record
			$this->insert_data($income_data);
			//2. Update daily_record
			//- ending_amount = ending_amount - $payout_data['amount']
			// - decrease_amount = decrease_amount + $payout_data['amount']
			//$daily_rs['ending_amount'] = $daily_rs['ending_amount'] + $income_data['amount'] ;
			//$daily_rs['increase_amount'] = $daily_rs['increase_amount'] + $income_data['amount'];
			//$this->db->where('daily_total_id', $daily_total_id);
			//$this->db->update('daily_total', $daily_rs);
			$this->db->trans_complete();
			$success = $this->db->trans_status();
			return $success;
		}else{
			return false;
		}

	}

	public function save_payout($payout_data)
	{
		$daily_total_id = 0;
		if(!$this->exists()){
			// Nếu ngày hôm nay chưa tồn tại, lấy ngày gần nhất (fix bug nghỉ nhiều ngày);
			//$yesterday_totol_data = $this->get_daily_total_info('yesterday');
			$the_last_total_data = $this->get_the_last_total_daily();
			$yesterday_totol_data = $the_last_total_data;
			if($yesterday_totol_data)
			{
				$_LastDate = $yesterday_totol_data['created_time'];

				$filters['start_date'] = date('Y-m-d', $_LastDate);
				$filters['end_date'] = date('Y-m-d', $_LastDate);
				$_rsTotal = $this->get_accounting_summary($filters);

				$begin = $_rsTotal['starting'] + $_rsTotal['in'] - $_rsTotal['po'];
				$end = 0;

				$daily_total_data = array(
					'created_time' =>time(),
					'begining_amount' => $begin,
					'ending_amount' => $end,
					'increase_amount' => 0,
					'decrease_amount' => 0 );

			}else{
				$daily_total_data = array(
								'created_time' =>time(),
								'begining_amount' => 0,
								'ending_amount' => 0,
								'increase_amount' => 0,
								'decrease_amount' => 0 );
			}
			$daily_total_id = $this->insert_daily_total($daily_total_data);
		}else{
			$today_rs = $this->get_daily_total_info();
			$daily_total_id = $today_rs['daily_total_id'];
		}

		$daily_rs = $this->get_daily_total_info();
		// Insert payout
		if($daily_total_id)
		{
			$payout_data['daily_total_id']=$daily_total_id;
			$payout_data['created_time'] = time();
			$payout_data['type'] = 1; //payout
			$payout_data['code'] = 'PO'.$payout_data['created_time'];
			$payout_data['payment_type'] = 'Tiền mặt';
			if($payout_data['kind']==1) // Các khoản chi cho hoạt động cửa hàng
			{
				$payout_data['code'] = 'NB-'.$payout_data['created_time'];
				$payout_data['payment_id'] = 0;//don't user
				$payout_data['sale_id'] = 0;//don't user
			}elseif($payout_data['kind'] == 2) {
				//$payout_data['kind'] = 0; //don't user
				$payout_data['payment_id'] = 0;//don't user
				$payout_data['sale_id'] = 0;//don't user
			} else{

			}
			//var_dump($payout_data);
			$this->db->trans_start();
			//1. Insert new record
			$rs_id = $this->insert_data($payout_data);
			//2. Update daily_record
			//- ending_amount = ending_amount - $payout_data['amount']
			// - decrease_amount = decrease_amount + $payout_data['amount']
			//$daily_rs['ending_amount'] = $daily_rs['ending_amount'] - $payout_data['amount'] ;
			//$daily_rs['decrease_amount'] = $daily_rs['decrease_amount'] + $payout_data['amount'];
			//$this->db->where('daily_total_id', $daily_total_id);
			//$this->db->update('daily_total', $daily_rs);
			$this->db->trans_complete();
			$success = $this->db->trans_status();
			if($success)
			{
				return $rs_id;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/*
         * Insert new payout record to _total table
         * data[created_time]-
         * data[daily_total_id]-
         * data[amount]
         * data[payment_type]-
	 	* data[payment_id]-
	 	* 		data[code]:- code of pa
	 	*  data[type]:- type of payment: 0: in; 1: out
	 	* data[creator_personal_id]
	 	* data[personal_id]
	 	* data[sale_id]
	 	* data[kind]    : 0:paid; 1:prepaid
         *
         */
	public function insert_data($data)
	{
		$this->db->insert('total', $data);
		$total_id = $this->db->insert_id();
		return $total_id;
	}

	/*
	 Get the sales data for the takings (sales/manage) view
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'created_time', $order = 'asc')
	{
		$this->db->select('
				total.total_id as total_id,
				total.kind as kind,
				total.code as code,
				total.amount as amount,
				total.payment_method,
				total.type as type,
				total.payment_type,				
				CONCAT(people.last_name," ",people.first_name) as person,
				CONCAT(people1.last_name," ",people1.first_name) as employee,
				total.note as note,
				total.created_time as created_time
		');

		$this->db->from('total AS total');
		$this->db->join('people AS people', 'total.personal_id = people.person_id', 'left');
		$this->db->join('people AS people1', 'total.creator_personal_id = people1.person_id', 'left');


		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

		if(!empty($search))
		{

				$this->db->group_start();
				// customer last name
				$this->db->like('people.last_name', $search);
				// customer first name
				$this->db->or_like('people.first_name', $search);
				// customer first and last name
				$this->db->or_like('CONCAT(people.first_name, " ", people.last_name)', $search);
				// customer company name
				$this->db->group_end();

		}

		//$this->db->group_by('test.sale_id');
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}
		return $this->db->get();
	}

	/*
	 Get number of rows for the takings (sales/manage) view
	*/
	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters)->num_rows();
	}

	public function get_info($id)
	{
		$this->db->select('
				total.total_id as total_id,
				total.amount as amount,
				total.type as type,				
				people.first_name as person,
				people1.first_name as employee,
				total.note as note,
				total.created_time as created_time
		');

		$this->db->from('total AS total');
		$this->db->join('people AS people', 'total.personal_id = people.person_id', 'left');
		$this->db->join('people AS people1', 'total.creator_personal_id = people1.person_id', 'left');


		$this->db->where('total_id',$id);

		return $this->db->get();
	}

	public function get_accounting_summary($filters)
	{
		// get payment summary
		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',0); // Thu
		$this->db->where('payment_method ',0); //Tiền mặt

		$income_amount = $this->db->get()->result_array();

		$starting_amount = $this->get_daily_total_info($this->db->escape($filters['start_date']));
		if($income_amount)
		{
			$income = $income_amount[0]['amount'];
		}else{
			$income = 0;
		}

		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',0); // Thu
		$this->db->where('sale_id',0); //thu từ phiếu thu

		$income_amount_nb = $this->db->get()->result_array();

		if($income_amount_nb)
		{
			$income_nb = $income_amount_nb[0]['amount'];
		}else{
			$income_nb = 0;
		}

		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$payout_amount = $this->db->get()->result_array();

		$payout = $payout_amount[0]['amount'];
		if($starting_amount) {
			$starting = $starting_amount['begining_amount'];
		}else{
			$starting = 0;
		}
		$ending = $starting + $income - $payout;

		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$this->db->where('kind',1); // Nội bộ
		$payout_amount_nb = $this->db->get()->result_array();

		$payout_nb = $payout_amount_nb[0]['amount'];

		$this->db->select('SUM(amount) AS pc');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$this->db->where('sale_id >',0);
		$payout_customer = $this->db->get()->result_array();
		//var_dump($payout_customer);
		$payout_c = $payout_customer[0]['pc'];
		// consider Gift Card as only one type of payment and do not show "Gift Card: 1, Gift Card: 2, etc." in the total
		$payments = array('in' => $income, 'in_nb' => $income_nb, 'po' => $payout, 'starting' => $starting,'ending'=>$ending,'nb'=>$payout_nb,'pc'=>$payout_c);

		return $payments;
	}

	public function get_revenue_summary($filters)
	{
		$this->db->select('payment_type, count(*) AS count, SUM(payment_amount) AS payment_amount');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales_payments.sale_id = sales.sale_id');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');

		//$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('DATE(payment_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
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
	

	// - OLD CODE TO REFERENCE -------------




	/*
	 Get the payment summary for the takings (sales/manage) view
	*/
	public function get_payments_summary($search, $filters)
	{
		// get payment summary
		$this->db->select('payment_type, count(*) AS count, SUM(payment_amount) AS payment_amount');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales_payments.sale_id = sales.sale_id');
		$this->db->join('people AS customer_p', 'sales.customer_id = customer_p.person_id', 'left');
		$this->db->join('customers AS customer', 'sales.customer_id = customer.person_id', 'left');

		$this->db->where('DATE(sale_time) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));

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
			$this->db->or_like('company_name', $search);
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



	public function update($sale_id, $sale_data, $payments)
	{
		$this->db->where('sale_id', $sale_id);
		$success = $this->db->update('sales', $sale_data);

		// touch payment only if update sale is successful and there is a payments object otherwise the result would be to delete all the payments associated to the sale
		if($success && !empty($payments))
		{
			//Run these queries as a transaction, we want to make sure we do all or nothing
			$this->db->trans_start();
			
			// first delete all payments
			$this->db->delete('sales_payments', array('sale_id' => $sale_id));

			// add new payments
			foreach($payments as $payment)
			{
				$sales_payments_data = array(
					'sale_id' => $sale_id,
					'payment_type' => $payment['payment_type'],
					'payment_amount' => $payment['payment_amount']
				);

				$success = $this->db->insert('sales_payments', $sales_payments_data);
			}
			
			$this->db->trans_complete();
			
			$success &= $this->db->trans_status();
		}
		
		return $success;
	}

	public function save($items, $customer_id, $employee_id, $comment, $invoice_number, $payments, $sale_id = FALSE)
	{
		//if(count($items) == 0)
		if(!is_array($items) || count($items) == 0) // Nếu không có sản phẩm nào php 7.4, hoặc $items phải chắc chắn truyền vào một mảng, hoặc là mảng trống; không được truyền null;
		{
			return -1;
		}

		$sales_data = array(
			'sale_time'		 => date('Y-m-d H:i:s'),
			'customer_id'	 => $this->Customer->exists($customer_id) ? $customer_id : null,
			'employee_id'	 => $employee_id,
			'comment'		 => $comment,
			'invoice_number' => $invoice_number
		);

		// Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('sales', $sales_data);
		$sale_id = $this->db->insert_id();

		foreach($payments as $payment_id=>$payment)
		{
			if( substr( $payment['payment_type'], 0, strlen( $this->lang->line('sales_giftcard') ) ) == $this->lang->line('sales_giftcard') )
			{
				// We have a gift card and we have to deduct the used value from the total value of the card.
				$splitpayment = explode( ':', $payment['payment_type'] );
				$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
				$this->Giftcard->update_giftcard_value( $splitpayment[1], $cur_giftcard_value - $payment['payment_amount'] );
			}

			$sales_payments_data = array(
				'sale_id'		 => $sale_id,
				'payment_type'	 => $payment['payment_type'],
				'payment_amount' => $payment['payment_amount']
			);
			$this->db->insert('sales_payments', $sales_payments_data);
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
				'item_location'		=> $item['item_location']
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
			$sale_remarks = 'POS '.$sale_id;
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
					'trans_comment'   => 'Deleting sale ' . $sale_id,
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

	public function get_payment_options($giftcard = TRUE)
	{
		$payments = array();
		
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

		$payments[$this->lang->line('sales_check')] = $this->lang->line('sales_check');

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
					GROUP_CONCAT(CONCAT(payments.payment_type, " ", payments.payment_amount) SEPARATOR ", ") AS payment_type
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
					sales.comment,
					sales.invoice_number,
					sales.customer_id,
					CONCAT(customer_p.first_name, " ", customer_p.last_name) AS customer_name,
					customer_p.first_name AS customer_first_name,
					customer_p.last_name AS customer_last_name,
					customer_p.email AS customer_email,
					customer_p.comments AS customer_comments, 
					customer.company_name AS customer_company_name,
					sales.employee_id,
					CONCAT(employee.first_name, " ", employee.last_name) AS employee_name,
					items.item_id,
					items.name,
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

	public function get_the_last_total_daily()
	{
		$query = $this->db->query("SELECT * FROM ospos_daily_total ORDER BY daily_total_id DESC LIMIT 1");
		$result = $query->result_array();
		if(!empty($result))
		{
			return $result[0];
		}else{
			return null;
		}
	}

	public function get_totals_of_the_day($date)
	{
		$filters['start_date'] = $date;
		$filters['end_date'] = $date;
		// get payment summary
		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',0); // Thu

		$income_amount = $this->db->get()->result_array();

		//$starting_amount = $this->get_daily_total_info($this->db->escape($filters['start_date']));
		if($income_amount)
		{
			$income = $income_amount[0]['amount'];
		}else{
			$income = 0;
		}


		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$payout_amount = $this->db->get()->result_array();

		$payout = $payout_amount[0]['amount'];
		/*
		if($starting_amount) {
			$starting = $starting_amount['begining_amount'];
		}else{
			$starting = 0;
		}
		*/
		$starting = 0;
		$ending = $starting + $income - $payout;

		$this->db->select('SUM(amount) AS amount');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$this->db->where('kind',1); // Nội bộ
		$payout_amount_nb = $this->db->get()->result_array();

		$payout_nb = $payout_amount_nb[0]['amount'];

		$this->db->select('SUM(amount) AS pc');
		$this->db->from('total as total');
		$this->db->where('DATE(FROM_UNIXTIME(total.created_time)) BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		$this->db->where('type',1); //Chi
		$this->db->where('sale_id >',0);
		$payout_customer = $this->db->get()->result_array();
		//var_dump($payout_customer);
		$payout_c = $payout_customer[0]['pc'];
		// consider Gift Card as only one type of payment and do not show "Gift Card: 1, Gift Card: 2, etc." in the total
		$payments = array('in' => $income, 'po' => $payout, 'starting' => $starting,'ending'=>$ending,'nb'=>$payout_nb,'pc'=>$payout_c);


		return $payments;
	}
}
?>
