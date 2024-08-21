<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Count_lib
{
	private $CI;

  	public function __construct()
	{
		$this->CI =& get_instance();
	}
	// Begin Add properties of cart by ManhVT
	/////////////////
	public function set_item_name($sItemName)
	{
		$this->CI->session->set_userdata('item_name', $sItemName);
	}

	public function get_item_name()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_name = $this->CI->session->userdata('item_name');

    	return empty($item_name) ? '' : $item_name;
	}

	public function clear_item_name()
	{
		$this->CI->session->unset_userdata('item_name');
	}
    ///////////////////////
	public function set_item_category($sItemCategory)
	{
		$this->CI->session->set_userdata('item_category', $sItemCategory);
	}

	public function get_item_category()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_category = $this->CI->session->userdata('item_category');

    	return empty($item_category) ? '' : $item_category;
	}

	public function clear_item_category()
	{
		$this->CI->session->unset_userdata('item_category');
	}
	//////////////////////////////
	public function set_item_number($sItemNumber)
	{
		$this->CI->session->set_userdata('item_number', $sItemNumber);
	}

	public function get_item_number()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_number = $this->CI->session->userdata('item_number');

    	return empty($item_number) ? '' : $item_number;
	}

	public function clear_item_number()
	{
		$this->CI->session->unset_userdata('item_number');
	}
	///////////////////////////
	public function set_item_description($sItemDescription)
	{
		$this->CI->session->set_userdata('item_description', $sItemDescription);
	}

	public function get_item_description()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$item_description = $this->CI->session->userdata('item_description');

    	return empty($item_description) ? '' : $item_description;
	}

	public function clear_item_description()
	{
		$this->CI->session->unset_userdata('item_description');
	}
	// END: Add properties of cart by ManhVT
	/**-------------- */

	
	
	public function get_employee()
	{
		if(!$this->CI->session->userdata('sales_employee'))
		{
			$this->set_employee(-1);
		}

		return $this->CI->session->userdata('sales_employee');
	}

	public function set_employee($employee_id)
	{
		$this->CI->session->set_userdata('sales_employee', $employee_id);
	}

	public function remove_employee()
	{
		$this->CI->session->unset_userdata('sales_employee');
	}
    
	
	
	public function out_of_stock($item_id, $item_location)
	{
		//make sure item exists		
		if($item_id != -1)
		{
			$item_quantity = $this->CI->Item_quantity->get_item_quantity($item_id, $item_location)->quantity;
			$quantity_added = $this->get_quantity_already_added($item_id, $item_location);

			if($item_quantity - $quantity_added < 0)
			{
				return $this->CI->lang->line('sales_quantity_less_than_zero');
			}
			elseif($item_quantity - $quantity_added < $this->CI->Item->get_info_by_id_or_number($item_id)->reorder_level)
			{
				return $this->CI->lang->line('sales_quantity_less_than_reorder_level');
			}
		}

		return '';
	}
	
	public function get_quantity_already_added($item_id, $item_location)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach($items as $item)
		{
			if($item['item_id'] == $item_id && $item['item_location'] == $item_location)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		return $quanity_already_added;
	}
	
	public function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach($items as $line=>$item)
		{
			if($line == $line_to_get)
			{
				return $item['item_id'];
			}
		}
		
		return -1;
	}

	

	public function return_entire_sale($receipt_sale_id)
	{
		/* remove by manhvt to support code
		//POS #
		$pieces = explode(' ', $receipt_sale_id);
		$sale_id = $pieces[1];
		*/
		$sale_id = 0;
		$mode = $this->get_mode();
		$_oSale = $this->CI->Sale->get_sale_by_code($receipt_sale_id);
		//var_dump($_oSale);
		if(!empty($_oSale))
		{
			$sale_id = $_oSale->sale_id;
		}

		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			if ($mode == 'return') {
				$this->add_item($row->item_id, -$row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
			} else {
				$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
			}
		}
		
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}
	
	public function copy_entire_sale($sale_id)
	{
		$this->empty_cart();
		$this->remove_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id, $row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, TRUE);
		}

		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type, $row->payment_amount,$row->payment_kind,$row->payment_id);
		}

		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
		$this->set_employee($this->CI->Sale->get_employee($sale_id)->person_id);
		$this->set_partner_id($this->CI->Sale->get_ctv($sale_id)->person_id);
		$this->set_sale_id($sale_id);
	}
	
	
	
	public function get_item_total($quantity, $price, $discount_percentage, $include_discount = FALSE)  
	{
		$total = bcmul($quantity, $price);
		if($include_discount)
		{
			$discount_amount = $this->get_item_discount($quantity, $price, $discount_percentage);

			return bcsub($total, $discount_amount);
		}

		return $total;
	}
	
	public function get_item_discount($quantity, $price, $discount_percentage)
	{
		//echo locale_get_default();
		//$config = get_instance()->config;
		//locale_set_default($config->item('number_locale'));
		//echo locale_get_default();die();
		/*
		$total = bcmul($quantity, $price);
		$discount_fraction = bcdiv($discount_percentage, '100', 4);

		return bcmul($total, $discount_fraction);
		*/
		
		$total = bcmul($quantity, $price);
		//$discount_fraction = bcdiv($discount_percentage, 100);
		$discount_time = bcdiv($total, '100');
		$discount_percentage = str_replace(',','.', $discount_percentage);
		//var_dump($discount_time); die();
		return bcmul($discount_time, $discount_percentage);
	}
	
	public function get_item_tax($quantity, $price, $discount_percentage, $tax_percentage) 
	{
		$price = $this->get_item_total($quantity, $price, $discount_percentage, TRUE);
		if($this->CI->config->config['tax_included'])
		{
			$tax_fraction = bcadd(100, $tax_percentage);
			$tax_fraction = bcdiv($tax_fraction, 100);
			$price_tax_excl = bcdiv($price, $tax_fraction);

			return bcsub($price, $price_tax_excl);
		}
		$tax_fraction = bcdiv($tax_percentage, 100);

		return bcmul($price, $tax_fraction);
	}

	public function calculate_subtotal($include_discount = FALSE, $exclude_tax = FALSE) 
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
			if($exclude_tax && $this->CI->config->config['tax_included'])
			{
				$subtotal = bcadd($subtotal, $this->get_item_total_tax_exclusive($item['item_id'], $item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
			else 
			{
				$subtotal = bcadd($subtotal, $this->get_item_total($item['quantity'], $item['price'], $item['discount'], $include_discount));
			}
		}

		return $subtotal;
	}

	public function get_total()
	{
		$total = $this->calculate_subtotal(TRUE);		
		if(!$this->CI->config->config['tax_included'])
		{
			foreach($this->get_taxes() as $tax)
			{
				$total = bcadd($total, $tax);
			}
		}

		return $total;
	}

	
	public function get_customer_total()
	{
		if (!empty($this->CI->session->userdata('customer_total'))) {
			return $this->CI->session->userdata('customer_total');
		} else{
			return 0;
		}
	}

	public function set_customer_total($customer_total)
	{
		$this->CI->session->set_userdata('customer_total', $customer_total);
	}

	public function clear_customer_total()
	{
		$this->CI->session->unset_userdata('customer_total');
	}

	
	// by ManhVT 12/08/2024
	public function clearAll()
	{
		$this->clear_oinc_id();
		$this->clear_oinc_uuid();
		$this->clear_doc_entry();
		$this->clear_doc_num();
		$this->clear_zone();
		$this->clear_creator_name();
		$this->clear_creator_id();
		$this->clear_countor_id();
		$this->clear_countor_name();
		$this->clear_mode();
		$this->clear_type();
		$this->clear_status();
		$this->clear_created_at();
		$this->empty_cart();
		$this->clear_whs_code();
		$this->clear_count_at();
	}
	public function set_oinc_id($oinc_id)
	{
		$this->CI->session->set_userdata('oinc_id', $oinc_id);
	}
	public function get_oinc_id()
	{
		if(!$this->CI->session->userdata('oinc_id'))
		{
			$this->set_oinc_id(0); // ID = 0; không có bản ghi nào;
		}
		return $this->CI->session->userdata('oinc_id');
	}

	public function clear_oinc_id()
	{
		$this->CI->session->unset_userdata('oinc_id');
	}

	public function set_oinc_uuid($oinc_uuid)
	{
		$this->CI->session->set_userdata('oinc_uuid', $oinc_uuid);
	}
	public function get_oinc_uuid()
	{
		if(!$this->CI->session->userdata('oinc_uuid'))
		{
			$this->set_oinc_uuid(0); // ID = 0; không có bản ghi nào;
		}
		return $this->CI->session->userdata('oinc_uuid');
	}

	public function clear_oinc_uuid()
	{
		$this->CI->session->unset_userdata('oinc_uuid');
	}

	public function set_doc_entry($doc_entry)
	{
		$this->CI->session->set_userdata('doc_entry', $doc_entry);
	}

	public function get_doc_entry()
	{
		if(!$this->CI->session->userdata('doc_entry'))
		{
			$this->set_doc_entry(''); // ID = 0; không có bản ghi nào;
		}
		return $this->CI->session->userdata('doc_entry');
	}

	public function clear_doc_entry()
	{
		$this->CI->session->unset_userdata('doc_entry');
	}

	public function set_doc_num($doc_num)
	{
		$this->CI->session->set_userdata('doc_num', $doc_num);
	}

	public function get_doc_num()
	{
		if(!$this->CI->session->userdata('doc_num'))
		{
			$this->set_doc_num(''); // ID = 0; không có bản ghi nào;
		}
		return $this->CI->session->userdata('doc_num');
	}

	public function clear_doc_num()
	{
		$this->CI->session->unset_userdata('doc_num');
	}

	public function set_zone($zone){
		$this->CI->session->set_userdata('zone', $zone);
	
	}

	public function get_zone(){
		if(!$this->CI->session->userdata('zone'))
		{
			$this->set_zone(''); //
		}
		return $this->CI->session->userdata('zone');
	}

	public function clear_zone(){
		$this->CI->session->unset_userdata('zone');
	
	}

	public function set_creator_name($creator_name)
	{
		$this->CI->session->set_userdata('creator_name', $creator_name);
	}

	public function get_creator_name(){
		if(!$this->CI->session->userdata('creator_name'))
		{
			$this->set_creator_name(''); //
		}
		return $this->CI->session->userdata('creator_name');
	}

	public function clear_creator_name(){
		$this->CI->session->unset_userdata('creator_name');
	
	}


	public function set_creator_id($creator_id)
	{
		$this->CI->session->set_userdata('creator_id', $creator_id);
	}

	public function get_creator_id(){
		if(!$this->CI->session->userdata('creator_id'))
		{
			$this->set_creator_id(''); //
		}
		return $this->CI->session->userdata('creator_id');
	}

	public function clear_creator_id(){
		$this->CI->session->unset_userdata('creator_id');
	
	}

	public function set_countor_name($countor_name){
		$this->CI->session->set_userdata('countor_name', $countor_name);
	}
	public function get_countor_name(){
		if(!$this->CI->session->userdata('countor_name'))
		{
			$this->set_countor_name(''); //
		}
		return $this->CI->session->userdata('countor_name');
	}

	public function clear_countor_name(){
		$this->CI->session->unset_userdata('countor_name');
	
	}

	public function set_countor_id($countor_id){
		$this->CI->session->set_userdata('countor_id', $countor_id);
	}
	public function get_countor_id(){
		if(!$this->CI->session->userdata('countor_id'))
		{
			$this->set_countor_id(''); //
		}
		return $this->CI->session->userdata('countor_id');
	}

	public function clear_countor_id(){
		$this->CI->session->unset_userdata('countor_id');
	
	}

	public function set_mode($mode){
		$this->CI->session->set_userdata('mode', $mode);
	}

	public function get_mode(){
		if(!$this->CI->session->userdata('mode'))
		{
			$this->set_mode(''); //
		}
		return $this->CI->session->userdata('mode');
	}

	public function clear_mode(){
		$this->CI->session->unset_userdata('mode');
	
	}

	public function set_status($status){
		$this->CI->session->set_userdata('status', $status);
	}

	public function get_status(){
		if(!$this->CI->session->userdata('status'))
		{
			$this->set_status(''); //
		}
		return $this->CI->session->userdata('status');
	}

	public function clear_status(){
		$this->CI->session->unset_userdata('status');
	
	}
	public function set_created_at($created_at){
		$this->CI->session->set_userdata('created_at', $created_at);
	}


	public function get_created_at(){
		if(!$this->CI->session->userdata('created_at'))
		{
			$this->set_created_at(''); //
		}
		return $this->CI->session->userdata('created_at');
	}

	public function clear_created_at(){
		$this->CI->session->unset_userdata('created_at');
	
	}

	public function set_type($type){
		$this->CI->session->set_userdata('type', $type);

	}

	public function get_type(){
		if(!$this->CI->session->userdata('type'))
		{
			$this->set_type(''); //
		}
		return $this->CI->session->userdata('type');
	}

	public function clear_type(){
		$this->CI->session->unset_userdata('type');
	
	}

	public function add_item(&$item_id, $quantity = 1)
	{
		$item_info = $this->CI->Item->get_info_by_id_or_number($item_id);

		//make sure item exists		
		if(empty($item_info))
		{
			$item_id = -1;
            return FALSE;			
		}
		
		$item_id = $item_info->item_id;

		// Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey = 0;                       //Highest key so far
        $itemalreadyinsale = FALSE;        //We did not find the item yet.
		$insertkey = 0;                    //Key to use for new entry.
		$updatekey = 0;                    //Key to use to update(quantity)

		foreach($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id'] == $item_id)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
                if(!$item_info->is_serialized)
                {
                    $quantity = bcadd($quantity, $items[$updatekey]['quantity']);
                }
			}
		}

		$insertkey = $maxkey+1;
		
		//var_dump($item_info->is_serialized);die(); 

		if(!$itemalreadyinsale || $item_info->is_serialized)
		{
            $item = [
				$insertkey => [
                    'item_id' => $item_id,
                    'line' => $insertkey,
                    'name' => $item_info->name,
                    'item_number' => $item_info->item_number,
					'item_category'=>$item_info->category,
					'is_serialized' => $item_info->is_serialized,
                    'quantity' => $quantity,
					'in_whs_quantity'=>$this->cal_in_whs_quantity($item_id,$this->get_whs_code())
					
                ]
            ];
			//add to existing array
			$items += $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['quantity'] = $quantity;
        }

		$this->set_cart($items);
		$this->calculate_quantity();
		return TRUE;
	}

	public function get_cart()
	{
		if(!$this->CI->session->userdata('count_cart'))
		{
			$this->set_cart([]);
		}

		return $this->CI->session->userdata('count_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('count_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('count_cart');
		$this->set_quantity(0);
	}

	public function get_quantity()
	{
		if(!$this->CI->session->userdata('count_quantity'))
		{
			$this->set_quantity(0);
		}

		return $this->CI->session->userdata('count_quantity');
	}

	public function set_quantity($quantity)
	{
		$this->CI->session->set_userdata('count_quantity', $quantity);
	}

	public function clear_quantity()
	{
		$this->CI->session->unset_userdata('count_quantity');
	}

	public function get_whs_code()
	{
		if(!$this->CI->session->userdata('whs_code'))
		{
			$this->set_whs_code(1);
		}

		return $this->CI->session->userdata('whs_code');
	}

	public function set_whs_code($quantity)
	{
		$this->CI->session->set_userdata('whs_code', $quantity);
	}

	public function clear_whs_code()
	{
		$this->CI->session->unset_userdata('whs_code');
	}

	/**
	 * Summary of Lấy mã trạng thái
	 * 
	 * @return mixed
	 */
	public function get_state_code()
	{
		if(!$this->CI->session->userdata('state_code'))
		{
			$this->set_state_code(0);
			$this->set_state_id('');
		}

		return $this->CI->session->userdata('state_code');
	}

	public function set_state_code($state_code)
	{
		$this->CI->session->set_userdata('state_code', $state_code);
	}

	public function clear_state_code()
	{
		$this->CI->session->unset_userdata('state_code');
	}
	/**
	 * Lấy oinc_id trong session
	 * @return mixed
	 */
	public function get_state_id()
	{
		if(!$this->CI->session->userdata('state_id'))
		{
			$this->set_state_id(0);
		}

		return $this->CI->session->userdata('state_id');
	}
	/**
	 * Thiết lập oinc_id vào session
	 * Mặc định 0 khi mã trạng thái 0
	 * @param mixed $state_id
	 * @return void
	 */
	public function set_state_id($state_id)
	{
		$this->CI->session->set_userdata('state_id', $state_id);
	}

	public function clear_state_id()
	{
		$this->CI->session->unset_userdata('state_id');
	}


	public function cal_in_whs_quantity($item_id, $item_location)
	{
		//make sure item exists		
		if($item_id != -1)
		{
			$item_quantity = $this->CI->Item_quantity->get_item_quantity($item_id, $item_location)->quantity;
			return $item_quantity;
		}

		return 0;
	}


	public function get_count_at()
	{
		if(!$this->CI->session->userdata('count_at'))
		{
			$this->set_count_at(0);
			
		}

		return $this->CI->session->userdata('count_at');
	}

	public function set_count_at($count_at)
	{
		$this->CI->session->set_userdata('count_at', $count_at);
	}

	public function clear_count_at()
	{
		$this->CI->session->unset_userdata('count_at');
	}

	/**
	 * 
	 * Tính tổng số lượng sản phẩm kiểm kê
	 * @return bool
	 */
	public function calculate_quantity()
	{
		$items = $this->get_cart();
		$quantity = 0;
		foreach ($items as $item)
		{
			$quantity = $quantity + $item['quantity'];
		}
		$this->set_quantity($quantity);

		return false;
	}

	public function load_doc_to_cart($oinc_id)
	{
		$this->empty_cart();
		$items = [];
		foreach($this->CI->Oinc->get_oinc_items($oinc_id)->result() as $row)
		{
			//$this->add_item($row->item_id, $row->counted_quantity);
			$item = [
				$row->line_num => [
                    'item_id' => $row->item_id,
                    'line' => $row->line_num,
                    'name' => $row->item_name,
                    'item_number' => $row->item_number,
					'item_category'=>$row->item_category,
					'is_serialized' => $row->is_serialized,
                    'quantity' => $row->counted_quantity,
					'in_whs_quantity'=>$row->in_whs_quantity
                ]
            ];
			//add to existing array
			$items += $item;
		}
		$this->set_cart($items);

	}

	public function edit_item($line, $quantity)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))	
		{
			$line = &$items[$line];
			$line['quantity'] = $quantity;
			$this->set_cart($items);
			$this->calculate_quantity();
		}

		return FALSE;
	}

	public function delete_item($line)
	{
		$items = $this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
		$this->calculate_quantity();
	}

	
}

?>
