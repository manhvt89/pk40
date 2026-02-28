<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Report.php");

class Specific_medical extends Report
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cấu hình cột cho report
     */
    public function getDataColumns()
    {
        return [
            'summary' => [
                array('id' => '#', 'align' => 'center'),
                array('test_date' => 'Ngày khám'),
                array('fullname' => 'Họ và tên'),
                array('age' => 'Năm sinh'),
                array('phone_number' => 'Điện thoại'),
                //array('prescription' => 'Chỉ định / Toa kính'),
                array('note' => 'Chuẩn đoán'),
            ]
        ];
    }

    /**
     * Lấy dữ liệu báo cáo theo năm
     * $inputs['year']
     */
    public function getData(array $inputs)
    {
        $year = (int) $inputs['year'];
        
        // 👉 Timestamp range – KHÔNG ghép chuỗi
        $from_ts = (new DateTimeImmutable())
            ->setDate($year, 1, 1)
            ->setTime(0, 0, 0)
            ->getTimestamp();

        $to_ts = (new DateTimeImmutable())
            ->setDate($year, 12, 31)
            ->setTime(23, 59, 59)
            ->getTimestamp();

        $this->db->select("
            t.test_id,
            t.test_time,
            t.prescription,
            t.reason,
            t.note,
            p.first_name,
            p.last_name,
            p.phone_number,
            p.age
        ");

        $this->db->from('ospos_test t');
        $this->db->join('ospos_customers c', 'c.person_id = t.customer_id', 'inner');
        $this->db->join('ospos_people p', 'p.person_id = c.person_id', 'inner');

        $this->db->where('t.test_time >=', $from_ts);
        $this->db->where('t.test_time <=', $to_ts);
        $this->db->where('c.deleted', 0);

        $this->db->order_by('t.test_time', 'DESC');

        $rows = $this->db->get()->result_array();

        // Format dữ liệu cho view
        foreach ($rows as &$row)
        {
            $row['fullname']  = trim($row['last_name'] . ' ' . $row['first_name']);
            $row['test_date'] = date('d/m/Y', $row['test_time']);
        }

        return array(
            'summary' => $rows,
            'details' => array()
        );
    }

    /**
     * Tổng hợp nhanh
     */
    public function getSummaryData(array $inputs)
    {
        $year = (int) $inputs['year'];

        $from_ts = (new DateTimeImmutable())->setDate($year, 1, 1)->setTime(0,0)->getTimestamp();
        $to_ts   = (new DateTimeImmutable())->setDate($year, 12, 31)->setTime(23,59,59)->getTimestamp();

        $this->db->select('COUNT(t.test_id) AS total_tests');
        $this->db->from('tests t');
        $this->db->where('t.test_time >=', $from_ts);
        $this->db->where('t.test_time <=', $to_ts);

        return $this->db->get()->row_array();
    }
}
