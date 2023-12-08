<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Detailed_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function create(array $inputs)
	{
		//Create our temp tables to work with the data in our report
		$this->Receiving->create_temp_table($inputs);
	}
	
	public function getDataColumns()
	{
        $CI =& get_instance();
        if($CI->Employee->has_grant('reports_detail_import_lens')) {
            $columns = array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_receiving_id')),
                    array('receiving_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_received_by')),
                    array('supplier' => $this->lang->line('reports_supplied_by')),
                    array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('reports_payment_type')),
                    array('reference' => $this->lang->line('receivings_reference')),
                    array('comment' => $this->lang->line('reports_comments')),
					array('mode' => 'Loại')),
                'details' => array(
                    $this->lang->line('reports_item_number'),
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_quantity'),
                    $this->lang->line('reports_price'),
                    $this->lang->line('reports_discount'))
            );
        }else{
            $columns = array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_receiving_id')),
                    array('receiving_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_received_by')),
                    array('supplier' => $this->lang->line('reports_supplied_by')),
                    array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('reports_payment_type')),
                    array('reference' => $this->lang->line('receivings_reference')),
                    array('comment' => $this->lang->line('reports_comments')),
					array('mode' => 'Loại')
				),
                'details' => array(
                    $this->lang->line('reports_item_number'),
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_quantity'),
                    $this->lang->line('reports_price'))
            );

        }
		return $columns;
	}
	
	public function getDataByReceivingId($receiving_id)
	{
		$this->db->select('receiving_id, receiving_date, SUM(quantity_purchased) AS items_purchased, CONCAT(employee.first_name, " ", employee.last_name) AS employee_name, supplier.company_name AS supplier_name, SUM(subtotal) AS subtotal, SUM(total) AS total, SUM(profit) AS profit, payment_type, comment, reference');
		$this->db->from('receivings_items_temp');
		$this->db->join('people AS employee', 'receivings_items_temp.employee_id = employee.person_id');
		$this->db->join('suppliers AS supplier', 'receivings_items_temp.supplier_id = supplier.person_id', 'left');
		$this->db->where('receiving_id', $receiving_id);

		return $this->db->get()->row_array();
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('receiving_id,receiving_uuid ,mode, receiving_date, receiving_time, SUM(quantity_purchased) AS items_purchased, CONCAT(employee.first_name," ",employee.last_name) AS employee_name, supplier.company_name AS supplier_name, SUM(total) AS total, SUM(profit) AS profit, payment_type, comment, reference');
		$this->db->from('receivings_items_temp');
		$this->db->join('people AS employee', 'receivings_items_temp.employee_id = employee.person_id');
		$this->db->join('suppliers AS supplier', 'receivings_items_temp.supplier_id = supplier.person_id', 'left');

        //var_dump($inputs);
        $categories = $this->config->item('iKindOfLens');

		if($inputs['category'] != 'all')
        {
            $this->db->where('category', $categories[$inputs['category']]);
        }
        if($inputs['location_id'] != 'all')
		{
			$this->db->where('item_location', $inputs['location_id']);
		}
		if($inputs['receiving_type'] == 'receiving')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif($inputs['receiving_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		elseif($inputs['receiving_type'] == 'requisitions')
		{
			$this->db->having('items_purchased = 0');
		}
		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_date');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
		
		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('name, item_number, items.unit_price as item_unit_price, items.category, quantity_purchased, serialnumber,total, discount_percent, item_location, receivings_items_temp.receiving_quantity');
			$this->db->from('receivings_items_temp');
			$this->db->join('items', 'receivings_items_temp.item_id = items.item_id');
			$this->db->where('receiving_id = '.$value['receiving_id']);
            if($inputs['category'] != 'all')
            {
                $this->db->where('items.category', $categories[$inputs['category']]);
            }
			$data['details'][$key] = $this->db->get()->result_array();
		}
		
		return $data;
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM(total) AS total');
		$this->db->from('receivings_items_temp');
        $categories = $this->config->item('iKindOfLens');

        if($inputs['category'] != 'all')
        {
            $this->db->where('category', $categories[$inputs['category']]);
        }


		if($inputs['location_id'] != 'all')
		{
			$this->db->where('item_location', $inputs['location_id']);
		}
		if($inputs['receiving_type'] == 'receiving')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif($inputs['receiving_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		elseif($inputs['receiving_type'] == 'requisitions')
		{
			$this->db->where('quantity_purchased = 0');
		}

		return $this->db->get()->row_array();
	}
    /**
     * returns the array for the dropdown-element item-count in the form for the inventory summary-report
     *
     * @return array
     */
    public function getCategoryDropdownArray()
    {
        $_aKindOfLens = $this->config->item('iKindOfLens');
        return $_aKindOfLens;
    }
	/**
	 * Lây danh sách cột hiển thị danh sách công nợ theo nhà cung cấp;
	 */
	public function getDataColumns1()
	{
        $CI =& get_instance();
        //if($CI->Employee->has_grant('reports_detail_import_lens')) {
            $columns = [
				'summary' => [
                    array('receiving_id' => $CI->lang->line('common_id'),'halign'=>'center', 'align'=>'right'),
					array('supplier_name' => 'Tên nhà cung cấp','halign'=>'center', 'align'=>'left'),
					array('total_amount' => 'Tổng tiền hàng','halign'=>'center', 'align'=>'right'),
					array('paid_amount' => 'Đã thanh toán','halign'=>'center', 'align'=>'right'),
					array('remain_amount' => 'Công nợ','halign'=>'center', 'align'=>'right'),
					array('supplier_uuid' => 'UUID','halign'=>'center', 'align'=>'right')
				],
				'details' => [
					['stt'=>'STT'],
					['receiving_time'=>'Ngày tháng'],
					['code'=>'Mã đơn'],
					['total_amount'=>'Thành tiền','align'=>'right'],
					['paid_amount'=>'Đã thanh toán','align'=>'right'],
					['remain_amount'=>'Công nợ','align'=>'right'],
					//['receiving_uuid'=>'Mã theo dõi','align'=>'left'],
				]
			];
                
                
			/*
        }else{
            $columns = array(
                'summary' => array(
                    array('id' => $this->lang->line('reports_receiving_id')),
                    array('receiving_date' => $this->lang->line('reports_date')),
                    array('quantity' => $this->lang->line('reports_quantity')),
                    array('employee_name' => $this->lang->line('reports_received_by')),
                    array('supplier' => $this->lang->line('reports_supplied_by')),
                    array('total' => $this->lang->line('reports_total'), 'sorter' => 'number_sorter'),
                    array('payment_type' => $this->lang->line('reports_payment_type')),
                    array('reference' => $this->lang->line('receivings_reference')),
                    array('comment' => $this->lang->line('reports_comments')),
					array('mode' => 'Loại')
				),
                'details' => array(
                    $this->lang->line('reports_item_number'),
                    $this->lang->line('reports_name'),
                    $this->lang->line('reports_category'),
                    $this->lang->line('reports_quantity'),
                    $this->lang->line('reports_price'))
            );

        }
		*/
		return $columns;
	}

	public function getData1(array $inputs)
	{
		$data['summary'] = array();
		
		$sql = 'SELECT
					sp.supplier_id,
					sp.company_name,
					sp.supplier_uuid,
					IFNULL(COUNT(rd.receiving_id), 0) AS tong_don_hang,
					IFNULL(SUM(IFNULL(rd.total_amount, 0)), 0) AS tong_cong,
					IFNULL(SUM(IFNULL(rd.paid_amount, 0)), 0) AS da_thanh_toan,
					IFNULL(SUM(IFNULL(rd.remain_amount, 0)), 0) AS con_lai
				FROM
				( SELECT
					p.person_id as supplier_id,
					s.company_name,
					s.supplier_uuid
					FROM 
					ospos_suppliers s
					LEFT JOIN ospos_people p ON p.person_id = s.person_id   
					WHERE s.deleted = 0
					) sp
				LEFT JOIN (
					SELECT
						r.supplier_id,
						r.receiving_id,
						r.total_amount,
						r.paid_amount,
						r.remain_amount
					FROM
						ospos_receivings r
					WHERE
					DATE(r.receiving_time) BETWEEN ? AND ?
				) rd ON sp.supplier_id = rd.supplier_id
				GROUP BY
					sp.company_name
				ORDER BY
					sp.company_name;';

			$query = $this->db->query($sql, [$inputs['fromDate'], $inputs['toDate']]);

			$data['summary'] = $query->result_array();
	
		//var_dump($data['summary']);
        return $data;
	}

	public function _getDetailData1(array $inputs,$uuid)
	{
		$data['details'] = array();
		
		$sql = 'SELECT r.receiving_id,r.code,r.receiving_time, r.total_amount, r.paid_amount, r.remain_amount,r.payment_type, r.receiving_uuid
			FROM ospos_receivings AS r
			RIGHT JOIN 
				(SELECT ss.person_id
				 FROM ospos_suppliers AS ss 
				 WHERE ss.supplier_uuid = ? ) AS s ON s.person_id = r.supplier_id
			WHERE DATE(r.receiving_time) BETWEEN ? AND ?

			ORDER BY r.receiving_time DESC';

			$query = $this->db->query($sql, [$uuid,$inputs['fromDate'], $inputs['toDate']]);

			$data['details'] = $query->result_array();
	
		
		//var_dump($data['summary']);
        return $data;
	}
}
?>