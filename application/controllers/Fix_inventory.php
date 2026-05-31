<?php
class Fix_inventory extends CI_Controller {
    public function index() {
        $this->output->enable_profiler(FALSE);
        header('Content-Type: text/plain; charset=utf-8');

        $items_to_fix = [
            ['item_number' => 'JM22395', 'name' => 'LAIKA JM22395'],
            ['item_number' => 'RB2389', 'name' => 'RAYBAN RB2389'],
            ['item_number' => 'B70152', 'name' => 'BLAKE 70152'],
            ['item_number' => '805C69', 'name' => 'CHNKELUOXIN 805'],
            ['item_number' => 'T10000', 'name' => 'UNI-ATROPIN'],
            ['item_number' => 'T05', 'name' => 'Natri clorid 0.9%'],
            ['item_number' => 'T5000', 'name' => 'MOXIEYE'],
            ['item_number' => 'T0120', 'name' => 'Cravit'],
            ['item_number' => '', 'name' => 'Dropstar'],
            ['item_number' => '', 'name' => 'Sanlein'],
            ['item_number' => 'T6000', 'name' => 'Dexamoxi'],
        ];

        $employee_id = 1; // Admin
        $location_id = 1; // Kho chính

        $this->db->trans_start();
        $count = 0;

        foreach($items_to_fix as $req) {
            $this->db->from('items');
            if (!empty($req['item_number'])) {
                $this->db->where("item_number = '{$req['item_number']}' OR name LIKE '%{$req['name']}%'");
            } else {
                $this->db->like('name', trim($req['name']));
            }
            $item = $this->db->get()->row();

            if ($item) {
                $item_id = $item->item_id;
                
                // 1. Force item_quantities to 0
                $iq = $this->db->get_where('item_quantities', ['item_id' => $item_id, 'location_id' => $location_id])->row();
                if ($iq) {
                    $this->db->where(['item_id' => $item_id, 'location_id' => $location_id]);
                    $this->db->update('item_quantities', ['quantity' => 0]);
                } else {
                    $this->db->insert('item_quantities', ['item_id' => $item_id, 'location_id' => $location_id, 'quantity' => 0]);
                }

                // 2. Balance the inventory history to 0
                $this->db->select_sum('trans_inventory');
                $inv = $this->db->get_where('inventory', ['trans_items' => $item_id, 'trans_location' => $location_id])->row();
                $history_qty = $inv ? (float)$inv->trans_inventory : 0;
                
                if ($history_qty != 0) {
                    $diff = 0 - $history_qty;
                    
                    $inv_data = array(
                        'trans_date'      => date('Y-m-d H:i:s'),
                        'trans_items'     => $item_id,
                        'trans_user'      => $employee_id,
                        'trans_comment'   => 'Điều chỉnh tồn kho về 0 (Clear lịch sử âm)',
                        'trans_location'  => $location_id,
                        'trans_inventory' => $diff
                    );
                    $this->db->insert('inventory', $inv_data);
                    
                    echo "[OK] {$item->name} (Lịch sử: {$history_qty} -> Đã bù thêm {$diff} để về 0)\n";
                    $count++;
                } else {
                    echo "[SKIP] {$item->name} (Lịch sử đã = 0, Tồn kho hiển thị đã ép về 0)\n";
                }
            } else {
                echo "[LỖI] Không tìm thấy sản phẩm: " . ($req['item_number'] ? $req['item_number'] : $req['name']) . "\n";
            }
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "\nLỖI NGHIÊM TRỌNG: Không thể cập nhật Database (Đã Rollback).";
        } else {
            echo "\nTHÀNH CÔNG: Đã đồng bộ Lịch sử về 0 cho $count sản phẩm.";
        }
    }
}
