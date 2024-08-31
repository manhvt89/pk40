<?php
class Inventory extends CI_Model
{
	
	public function get_transactions($offset,$limit)
	{
		//echo $limit; echo '|'.$offset;
		$this->db->from('inventory');
		$this->db->limit($limit,$offset);
		// order by name of item
		$this->db->order_by('inventory.trans_id', 'asc');
		return $this->db->get()->result();
	}

	public function get_records_to_sync($last_sync_time) {
        // Giả sử bạn lưu trữ thời gian đồng bộ cuối cùng
        // Thay đổi điều kiện lọc theo yêu cầu của bạn
        $this->db->select('*');
        $this->db->from('items');
        $this->db->where('updated_at >', $last_sync_time);
        $query = $this->db->get();

        return $query->result_array();
    }
    
    // Cập nhật thời gian đồng bộ cuối cùng
    public function update_last_sync_time($time) {
        $data = array(
            'last_sync_time' => $time
        );
        $this->db->where('id', 1); // Giả sử bạn có một bản ghi duy nhất để lưu thời gian đồng bộ
        return $this->db->update('sync_meta', $data);
    }
}
?>
