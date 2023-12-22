<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_lib
{
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function get_quantity()
	{
		if(!$this->CI->session->userdata('purchase_quantity'))
		{
			$this->set_quantity(0);
		}

		return $this->CI->session->userdata('purchase_quantity');
	}

	public function set_quantity($quantity)
	{
		$this->CI->session->set_userdata('purchase_quantity', $quantity);
	}

	public function clear_quantity()
	{
		$this->CI->session->unset_userdata('purchase_quantity');
	}

	public function get_cart()
	{
		if(!$this->CI->session->userdata('purchase_cart'))
		{
			$this->set_cart(array());
		}

		return $this->CI->session->userdata('purchase_cart');
	}

	public function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('purchase_cart', $cart_data);
	}

	public function empty_cart()
	{
		$this->CI->session->unset_userdata('purchase_cart');
		$this->set_quantity(0);
	}

	public function get_supplier()
	{
		if(!$this->CI->session->userdata('purchase_supplier'))
		{
			$this->set_supplier(-1);
		}

		return $this->CI->session->userdata('purchase_supplier');
	}

	public function set_supplier($supplier_id)
	{
		$this->CI->session->set_userdata('purchase_supplier', $supplier_id);
	}

	public function remove_supplier()
	{
		$this->CI->session->unset_userdata('purchase_supplier');
	}

	public function get_stock_source()
	{
		if(!$this->CI->session->userdata('purchase_stock_source'))
		{
			$this->set_stock_source($this->CI->Stock_location->get_default_location_id());
		}

		return $this->CI->session->userdata('purchase_stock_source');
	}
	
	public function get_name()
	{
		// avoid returning a NULL that results in a 0 in the comment if nothing is set/available
		$comment = $this->CI->session->userdata('purchase_name');

		return empty($comment) ? '' : $comment;
	}
	
	public function set_name($comment)
	{
		$this->CI->session->set_userdata('purchase_name', $comment);
	}
	
	public function clear_name()
	{
		$this->CI->session->unset_userdata('purchase_name');
	}
   
	public function get_reference()
	{
		return $this->CI->session->userdata('purchase_reference');
	}
	
	
	public function set_stock_source($stock_source)
	{
		$this->CI->session->set_userdata('purchase_stock_source', $stock_source);
	}
	
	public function clear_stock_source()
	{
		$this->CI->session->unset_userdata('purchase_stock_source');
	}
	
	public function get_stock_destination()
	{
		if(!$this->CI->session->userdata('purchase_stock_destination'))
		{
			$this->set_stock_destination($this->CI->Stock_location->get_default_location_id());
		}

		return $this->CI->session->userdata('purchase_stock_destination');
	}

	public function set_stock_destination($stock_destination)
	{
		$this->CI->session->set_userdata('purchase_stock_destination', $stock_destination);
	}
	
	public function clear_stock_destination()
	{
		$this->CI->session->unset_userdata('purchase_stock_destination');
	}
	/** END PROPERITES */
	/**
	 * Summary of calculate_quantity
	 * @return bool
	 */
	public function calculate_quantity()
	{
		$items = $this->get_cart();
		$quantity = 0;
		foreach ($items as $item)
		{
			
			if(is_numeric($item['item_quantity'])){
				$quantity = $quantity + $item['item_quantity'];
			} else {
				$quantity = $quantity + 0;
			}
			
			
		}
		$this->set_quantity($quantity);

		return false;
	}

	/**
	 * Summary of add_item
	 * @param mixed $item_number = barcode
	 * @param mixed $item_name
	 * @param mixed $unit_price
	 * @param mixed $cost_price
	 * @param mixed $quantity
	 * @param mixed $category
	 * @return bool
	 */
	public function add_item($_aItem,$item_id=0)
	{
		$item_number = strtoupper(trim($_aItem['item_number']));
		$item_name = trim($_aItem['item_name']);
		$cost_price = trim($_aItem['cost_price']);
		$unit_price = trim($_aItem['unit_price']);
		$quantity = trim($_aItem['quanlity']);
		$category = trim($_aItem['category']);
		$custom1 = trim($_aItem['custom1']);
		$custom2 = trim($_aItem['custom2']);
		$custom3 = trim($_aItem['custom3']);
		$custom4 = trim($_aItem['custom4']);
		$custom5 = trim($_aItem['custom5']);
		$custom6 = trim($_aItem['custom6']);
		$custom7 = trim($_aItem['custom7']);
		$custom8 = trim($_aItem['custom8']);
		$custom9 = trim($_aItem['custom9']);
		$custom10 = trim($_aItem['custom10']);
		$description = trim($_aItem['description']);
	
		$status = isset($_aItem['status'])? trim($_aItem['status']):0 ;
		$_iMaxKey = 0;
		//Get items in the receiving so far.
		if ($status == 0) {
			if (!is_numeric($quantity)) {
				$quantity = 0;
				$status = 1;
			}
			if (!is_numeric($cost_price)) {
				$cost_price = 0;
				$status = 1;
			}
			if (!is_numeric($unit_price)) {
				$unit_price = 0;
				$status = 1;
			}
		}
		
		$_cItems = $this->get_cart();
		//var_dump($_cItems);
		$_iIsOnCart = 0;
		$_iEditLine = 0; 
		foreach($_cItems as $k=>$v)
		{
			if($k >= $_iMaxKey)
			{
				$_iMaxKey = $k;

			}
			if($item_number == $v['item_number'])
			{
				$_iIsOnCart = 1; // Đã tồn tại
				$quantity = $quantity + $v['item_quantity'];
				$_iEditLine = $k;
			}
		}
		$_iMaxKey++;
		if($_iIsOnCart == 1)
		{
			$_aEditItem = $_cItems[$_iEditLine];
			
			$_aEditItem['item_quantity'] = $quantity;
			
			$_cItems[$_iEditLine] = $_aEditItem;

		} else {
			$insertkey = $_iMaxKey;
			$item = array(
				'item_id'=>$item_id,
				'item_number' => $item_number,
				'item_name' => $item_name,
				'item_quantity' => $quantity,
				'item_price' => $cost_price,
				'item_u_price' => $unit_price,
				'item_category' => $category,
				'line' => $insertkey,
				'total' => $this->get_item_total($quantity, $cost_price, 0),
				'status' => $status,
				'custom1'=>$custom1,
				'description'=>$description,
				'custom2'=>$custom2,
				'custom3'=>$custom3,
				'custom4'=>$custom4,
				'custom5'=>$custom5,
				'custom6'=>$custom6,
				'custom7'=>$custom7,
				'custom8'=>$custom8,
				'custom9'=>$custom9,
				'custom10'=>$custom10,
			);


			$_cItems[$insertkey] = $item;
		}
		$this->set_cart($_cItems);
		$this->calculate_quantity();
		return TRUE;
	}

	public function add_item_by_itemID(&$item_id, $quantity = 1)
	{
		$item_info = $this->CI->Item->get_info_by_id_or_number($item_id);
		//var_dump($item_info );
		
		//make sure item exists		
		if(empty($item_info))
		{
			$item_id = -1;
            return FALSE;			
		}
		
		$item_id = $item_info->item_id;
		$item_number = $item_info->item_number;
		// Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();
		//var_dump($items);
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

			if($item['item_number'] == $item_number)
			{
				$itemalreadyinsale = TRUE;
				$updatekey = $item['line'];
                if(!$item_info->is_serialized)
                {
                    $quantity = bcadd($quantity, $items[$updatekey]['item_quantity']);
                }
			}
		}

		$insertkey = $maxkey+1;
		//array/cart records are identified by $insertkey and item_id is just another field.
		$price = $item_info->cost_price;
		$unit_price = $item_info->unit_price;
		$total = $this->get_item_total($quantity, $price, 0);
		//Item already exists and is not serialized, add to quantity

		if(!$itemalreadyinsale || $item_info->is_serialized)
		{
            $item = array(
				'item_id'=>$item_id,
				'item_number' => $item_info->item_number,
				'item_name' => $item_info->name,
				'item_quantity' => $quantity,
				'item_price' => $price,
				'item_u_price'=>$unit_price,
				'item_category' => $item_info->category,
				'line' => $insertkey,
				'total' => $this->get_item_total($quantity, $price,0),
				'status' => 9, //Item từ kho (không cho sửa barcode)
				'custom1'=>$item_info->custom1,
				'description'=>$item_info->description,
				'custom2'=>$item_info->custom2,
				'custom3'=>$item_info->custom3,
				'custom4'=>$item_info->custom4,
				'custom5'=>$item_info->custom5,
				'custom6'=>$item_info->custom6,
				'custom7'=>$item_info->custom7,
				'custom8'=>$item_info->custom8,
				'custom9'=>$item_info->custom9,
				'custom10'=>$item_info->custom10,
			);
			//add to existing array
			$items[$insertkey] = $item;
		}
        else
        {
            $line = &$items[$updatekey];
            $line['item_quantity'] = $quantity;
            $line['total'] = $total;
        }

		$this->set_cart($items);
		$this->calculate_quantity();
		return TRUE;
	}
	public function edit_item($line, $item_number, $item_name,$status=0,$unit_price = 0,$cost_price = 0,$quantity = 1, $category='')
	{
		$_cItems = $this->get_cart();
		if(isset($_cItems[$line]))
		{
			$_theItem = &$_cItems[$line];
			$_theItem['item_number'] = strtoupper($item_number);
			$_theItem['item_name'] = $item_name;
			$_theItem['item_quantity'] = $quantity;
			$_theItem['item_price'] = $cost_price;
			$_theItem['item_u_price'] = $unit_price;
			$_theItem['item_category'] = $category;
			$_theItem['status'] = $status;
			$_theItem['total'] = $this->get_item_total($quantity, $cost_price, 0);
			$this->set_cart($_cItems);
		}
		$this->calculate_quantity();
		return FALSE;
	}

	public function delete_item($line)
	{
		$_cItems = $this->get_cart();
		unset($_cItems[$line]);
		$this->set_cart($_cItems);
		$this->calculate_quantity();
	}
	/**
	 * Summary of set_check
	 * @param mixed $check = 0: chưa kiểm tra; 1: đã kiểm tra 
	 * @return void
	 */
	public function set_check($check)
	{
		$this->CI->session->set_userdata('purchase_check', $check);
	}

	public function get_check()
	{
		if(!$this->CI->session->userdata('purchase_check'))
		{
			$this->set_check(0);
		}

		return $this->CI->session->userdata('purchase_check');
	}
	public function clear_check()
	{
		$this->CI->session->unset_userdata('purchase_check');
	}
	/*
	public function return_entire_receiving($receipt_receiving_id)
	{
		//RECV #
		$pieces = explode(' ', $receipt_receiving_id);
		if(preg_match("/(RECV|KIT)/", $pieces[0]))
		{
			$receiving_id = $pieces[1];
		} 
		else 
		{
			$receiving_id = $this->CI->Receiving->get_receiving_by_reference($receipt_receiving_id)->row()->receiving_id;
		}

		$this->empty_cart();
		$this->remove_supplier();
		$this->clear_comment();

		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$this->add_item($row->item_id, -$row->quantity_purchased, $row->item_location, $row->discount_percent, $row->item_unit_price, $row->description, $row->serialnumber, $row->receiving_quantity, TRUE);
		}

		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);
	}

	public function add_item_kit($external_item_kit_id, $item_location)
	{
		//KIT #
		$pieces = explode(' ',$external_item_kit_id);
		$item_kit_id = $pieces[1];
		
		foreach($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$this->add_item($item_kit_item['item_id'],$item_kit_item['quantity'], $item_location);
		}
	}
		*/
	public function copy_entire_purchase($purchase_id,$full=0)
	{
		$this->empty_cart();
		$this->remove_supplier();

		foreach($this->CI->Purchase->get_purchase_items($purchase_id)->result() as $row)
		{
			
			$_aItem['item_number'] = $row->item_number;
			$_aItem['item_name'] = $row->item_name;
			$_aItem['cost_price'] = $row->item_price;
			$_aItem['unit_price'] = $row->item_u_price;
			$_aItem['quanlity'] = (int) $row->item_quantity;
			$_aItem['category'] = $row->item_category;
			$_aItem['custom1'] = $row->custom1;
			$_aItem['description'] = $row->description;
			$_aItem['custom2'] = $row->custom2;
			$_aItem['custom3'] = $row->custom3;
			$_aItem['custom4'] = $row->custom4;
			$_aItem['custom5'] = $row->custom5;
			$_aItem['custom6'] = $row->custom6;
			$_aItem['custom7'] = $row->custom7;
			$_aItem['custom8'] = $row->custom8;
			$_aItem['custom9'] = $row->custom9;
			$_aItem['custom10'] = $row->custom10;

			if($row->type == 0 || $row->type == 2)
			{
				$_aItem['status'] = 9;
			} else {
				$_aItem['status'] = 1;
			}

			//var_dump($_aItem);
			//var_dump($_aItem);
			if ($full == 0) {

				$this->add_item($_aItem, $row->item_id);
			} else {

			}
		}

		$this->set_supplier($this->CI->Purchase->get_supplier($purchase_id)->person_id);

	}

	public function clear_all()
	{
		$this->empty_cart();
		$this->remove_supplier();
		$this->clear_name();
		$this->clear_check();
		$this->clear_purchase_id();
	}

	public function get_item_total($quantity, $price, $discount_percentage)
	{
		$total = bcmul($quantity, $price);
		$discount_fraction = bcdiv($discount_percentage, 100);
		$discount_amount = bcmul($total, $discount_fraction);

		return bcsub($total, $discount_amount);
	}

	public function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
			$total = bcadd($total, $this->get_item_total($item['item_quantity'], $item['item_price'], 0));
		}
		
		return $total;
	}

	public function set_status_by_item_number($item_number,$status =0)
	{
		$_aCart = $this->get_cart();
		if(!empty($_aCart))
		{
			//var_dump($_aCart);die();
			foreach($_aCart as $k=>$v)
			{
				if($v['status'] != 9)
				{
					if($v['item_number'] == $item_number)
					{
						$v['status'] = $status;
						$_aCart[$k] = $v;
					}
				}
			}
			$this->set_cart($_aCart);
			return true;
		} else{
			return false;
		}
	}
	/**
	 * Summary of validate_cart
	 * Kiểm tra xem trong cart các dữ liệu đã sẵn sàng nhập kho chưa;
	 * Sẵn sàng nhập kho khi sản phẩm đã có trong kho itemts tables;
	 * @return bool
	 */
	public function validate_cart()
	{
		$_aCart = $this->get_cart();
		foreach ($_aCart as $k => $v) {
			if ($v['item_id'] == 0)
				return false;
		}
		return true;
	}

	public function get_purchase_id()
	{
		if(!$this->CI->session->userdata('purchase_id'))
		{
			$this->set_purchase_id(0);
		}

		return $this->CI->session->userdata('purchase_id');
	}

	public function set_purchase_id($id)
	{
		$this->CI->session->set_userdata('purchase_id', $id);
	}

	public function clear_purchase_id()
	{
		$this->CI->session->unset_userdata('purchase_id');
	}
}

?>
