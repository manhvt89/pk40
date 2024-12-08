<?php

defined('BASEPATH') OR exit('No direct script access allowed');

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_fields_to_tables extends CI_Migration {

    public function up() {
        // Chỉnh sửa bảng ospos_people
        $this->dbforge->modify_column('ospos_people', [
            'age' => [
                'type' => 'VARCHAR',
                'constraint' => 12
            ],
            'facebook' => [
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => TRUE,
                'default' => "''"
            ]
        ]);

        // Tạo bảng ospos_oincs
        $this->dbforge->add_field([
            'oinc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ],
            'oinc_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'default' => 'uuid()'
            ],
            'doc_entry' => [
                'type' => 'VARCHAR',
                'constraint' => 25
            ],
            'created_at' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => 'O',
                'comment' => '{O: Open; W: Working; C: Closed; P: đã update kho;}'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'on_update' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('oinc_id', TRUE);
        $this->dbforge->create_table('ospos_oincs');

        // Tạo bảng ospos_inc1
        $this->dbforge->add_field([
            'inc1_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'counted_quantity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ]
        ]);
        $this->dbforge->add_key('inc1_id', TRUE);
        $this->dbforge->create_table('ospos_inc1');
    }

    public function down() {
        // Rollback: Xóa các bảng và cột vừa thêm
        $this->dbforge->drop_table('ospos_oincs', TRUE);
        $this->dbforge->drop_table('ospos_inc1', TRUE);
        $this->dbforge->modify_column('ospos_people', [
            'age' => [
                'type' => 'INT',
                'constraint' => 11
            ]
        ]);
    }
}
