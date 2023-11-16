<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Customer_care extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		return [];
	}

	public function getData(array $inputs)
	{	
        return [];
	}

	/**
	 * calculates the total value of the given inventory summary by summing all sub_total_values (see Inventory_summary::getData())
	 * 
	 * @param array $inputs expects the reports-data-array which Inventory_summary::getData() returns
	 * @return array
	 */
	public function getSummaryData(array $inputs)
	{
		$return = array('total_inventory_value' => 0);

		foreach($inputs as $input)
		{
			$return['total_inventory_value'] += $input['sub_total_value'];
		}

		return $return;
	}

	/**
	 * returns the array for the dropdown-element item-count in the form for the inventory summary-report
	 * 
	 * @return array
	 */
	public function getItemCountDropdownArray()
	{
		return array('all' => $this->lang->line('reports_all'),
					'zero_and_less' => $this->lang->line('reports_zero_and_less'),
					'more_than_zero' => $this->lang->line('reports_more_than_zero'));
	}


	public function _getDataColumns()
	{
		return [

            'summary' => [
				array('id' => 'STT'),
				array('employee_code' => 'Mã nhân viên'),
				array('employee_name' => 'Tên'),
				array('total' => 'Số cuộc gọi','halign'=>'center', 'align'=>'right'),
			],
			'details' =>[
				array('id' => 'STT'),
				array('datetime' => 'Ngày'),
				array('customer_name' => 'Tên khách hàng'),
				array('customer_code' => 'Mã khách hàng'),
				array('content'=>'Ghi chú','halign'=>'center', 'align'=>'left'),
				array('status'=>'Trạng thái','halign'=>'center', 'align'=>'center'),
			]
		];
	}

	public function _getData2(array $inputs)
	{	
       
	    $this->db->select('hr.*,COUNT(history_reminder_id) as tt, e.code');
        $this->db->from('history_reminder as hr');
		$this->db->join('employees as e','e.person_id = hr.employeer_id');
		// should be corresponding to values Inventory_summary::getItemCountDropdownArray() returns...
		$this->db->where('DATE(FROM_UNIXTIME(created_time)) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
        $this->db->group_by('hr.employeer_id');
        $this->db->order_by('created_time');

        $data = array();
        $data['summary'] = $this->db->get()->result_array();

        $data['details'] = array();
		
        foreach($data['summary'] as $key=>$value)
        {
            $this->db->select('hr.*, c.account_number');
            $this->db->from('history_reminder as hr');
			$this->db->join('customers as c','c.person_id = hr.customer_id');
			$this->db->where('employeer_id', $value['employeer_id']);
			$this->db->where('DATE(FROM_UNIXTIME(created_time)) BETWEEN '. $this->db->escape($inputs['fromDate']).' AND '.$this->db->escape($inputs['toDate']));
            $this->db->order_by('hr.created_time');
            $data['details'][$key] = $this->db->get()->result_array();
        }
        return $data;

	}

	
}
?>