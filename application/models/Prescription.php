<?php
class Prescription extends CI_Model
{
	
	/*
	Returns all the items
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$tenthuoc = $this->config->item('tenthuoc');
		//var_dump($tenthuoc);
		return $tenthuoc;
	}

	
}
?>
