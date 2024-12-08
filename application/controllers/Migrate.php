<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Đảm bảo chỉ chạy qua CLI
        if (!is_cli()) {
            exit("Chỉ chạy trong CLI\n");
        }

        // Load thư viện Migration của CodeIgniter
        $this->load->library('migration');
    }

    // Phương thức chạy migration
    public function index() {
        if ($this->migration->current() === FALSE) {
            echo $this->migration->error_string() . "\n";
        } else {
            echo "Migration thành công.\n";
        }
    }

    // Phương thức tạo file migration
    public function create($name) {
        $timestamp = date('YmdHis');
        $filename = $timestamp . "_" . strtolower($name) . ".php";
        $filepath = APPPATH . "migrations/" . $filename;

        $template = "<?php\n
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_" . ucfirst($name) . " extends CI_Migration {

    public function up() {
        // Thêm logic ở đây
    }

    public function down() {
        // Rollback logic ở đây
    }
}";

        if (file_put_contents($filepath, $template)) {
            echo "Tạo file migration $filename thành công.\n";
        } else {
            echo "Không thể tạo file migration.\n";
        }
    }
}
