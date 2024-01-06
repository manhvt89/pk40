<?php
class Prescription extends CI_Model
{
	
	/*
	Returns all the items
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$filter = $this->config->item('filter_other'); //define in app.php
		if(empty($filter))
		{
			return [];
		}
		$tenthuoc = [];//$this->config->item('tenthuoc');
		$this->db->select('*');
		$this->db->from('items');
		$this->db->where_in('category',$filter);
		$this->db->where('deleted',0);
		$_aTenThuocs = $this->db->get()->result_array();
		//var_dump($_aTenThuocs);
		//["id"]=> string(4) "6290" ["name"]=> string(21) "Bột Cefalexin 250mg" ["price"]=> string(4) "1500" ["dvt"]=> string(4) "Gói" ["hdsd"]=> string(18) "2 gói mỗi ngày" ["price_display"]=> string(7) "1.500đ" }
		$_aItem = [];
		$_aRS = [];
		if(empty($_aTenThuocs))
		{
			return [];
		}
		foreach($_aTenThuocs as $ten)
		{
			$_aTenthuoc['id'] = $ten['item_id'];
			$_aTenthuoc['name'] = $ten['name'];
			$_aTenthuoc['price'] = $ten['unit_price'];
			$_aTenthuoc['dvt'] = $ten['custom1'];
			$_aTenthuoc['hdsd'] = $ten['description'];
			$_aTenthuoc['price_display']=number_format($ten['unit_price'],0,',','.').'đ';
			$tenthuoc[] = $_aTenthuoc;
		}
		foreach($tenthuoc as $ten)
		{
			$_aItem[] = $ten['id'];
		}
		//var_dump($_aItem);die();
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
		
		//var_dump($tenthuoc); die();
		return $tenthuoc;
	}

	
}
?>
