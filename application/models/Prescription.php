<?php
class Prescription extends CI_Model
{
	
	/*
	Returns all the items
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$tenthuoc = $this->config->item('tenthuoc');
		$_aItem = [];
		$_aRS = [];
		if(empty($tenthuoc))
		{
			return [];
		}
		foreach($tenthuoc as $ten)
		{
			$_aItem[] = $ten['id'];
		}
		$this->db->select('*');
		$this->db->from('item_quantities');
		$this->db->where_in('item_id',$_aItem);
		$_aRecords = $this->db->get()->result_array();
		if(!empty($_aRecords)) {
			foreach($_aRecords as $key=>$value) {
				$_aRS[$value['item_id']] = $value['quantity'];
			}
		}
		foreach($tenthuoc as $key=>$value)
		{
			
			$_sSL = empty($_aRS[$value['id']])==true? 0 : $_aRS[$value['id']]; // Thêm số lượng
			$value['sl'] = number_format($_sSL,0);
			$tenthuoc[$key] = $value;
		}
		
		//var_dump($tenthuoc);
		return $tenthuoc;
	}

	
}
?>
