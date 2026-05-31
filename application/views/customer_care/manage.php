<?php $this->load->view("partial/header"); ?>

<div id="page_title" style="margin-bottom:8px;">
    Chăm sóc khách hàng
</div>

<div id="page_subtitle" style="margin-bottom:15px;">
    Quản lý danh sách khách hàng cần liên hệ chăm sóc. Khách hàng chưa liên hệ quá 3 tháng sẽ tự động trở lại tab "Chờ liên hệ".
</div>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab_new" aria-controls="tab_new" role="tab" data-toggle="tab" onclick="refreshTable('new')">
                    <span class="glyphicon glyphicon-time"></span> Chờ liên hệ
                </a>
            </li>
            <li role="presentation">
                <a href="#tab_contacted" aria-controls="tab_contacted" role="tab" data-toggle="tab" onclick="refreshTable('contacted')">
                    <span class="glyphicon glyphicon-ok-sign"></span> Đã liên hệ
                </a>
            </li>
        </ul>

        <div class="tab-content" style="padding-top: 15px; background: #fff; padding: 15px; border: 1px solid #ddd; border-top: none;">
            
            <table id="table"
                   data-toggle="table"
                   data-url="<?php echo site_url('customer_care/search?status=new'); ?>"
                   data-pagination="true"
                   data-side-pagination="server"
                   data-sort-name="total_amount"
                   data-sort-order="desc"
                   data-search="true"
                   data-show-refresh="true"
                   data-page-list="[10, 25, 50, 100]">
                <thead>
                    <tr>
                        <th data-field="name" data-sortable="true">Tên khách hàng</th>
                        <th data-field="phone_number" data-sortable="true">Số điện thoại</th>
                        <th data-field="address" data-sortable="false">Địa chỉ</th>
                        <th data-field="total_amount" data-sortable="true">Tổng tiền đã mua</th>
                        <th data-field="last_contact_time" data-sortable="true">Lần liên hệ gần nhất</th>
                        <th data-field="action" data-align="center" data-sortable="false">Thao tác</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>

<script type="text/javascript">
    var currentStatus = 'new';

    function refreshTable(status) {
        currentStatus = status;
        $('#table').bootstrapTable('refresh', {
            url: '<?php echo site_url("customer_care/search"); ?>?status=' + status
        });
    }

    function viewCustomerDetails(person_id) {
        var url = '<?php echo site_url("customers/view_detail"); ?>/' + person_id;
        
        if (typeof BootstrapDialog !== 'undefined') {
            BootstrapDialog.show({
                title: 'Lịch sử mua hàng & Chi tiết',
                message: $('<iframe src="' + url + '" width="100%" height="600" frameborder="0"></iframe>'),
                cssClass: 'modal-dlg',
                size: BootstrapDialog.SIZE_WIDE || 'size-wide'
            });
        } else {
            // Fallback nếu không có BootstrapDialog
            window.open(url, '_blank', 'width=1000,height=700');
        }
    }

    function markContacted(person_id) {
        if (confirm("Xác nhận đã liên hệ với khách hàng này?")) {
            $.ajax({
                url: '<?php echo site_url("customer_care/mark_contacted"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    customer_id: person_id
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Thành công');
                        $('#table').bootstrapTable('refresh');
                    } else {
                        toastr.error(response.message, 'Lỗi');
                    }
                },
                error: function() {
                    toastr.error('Không thể kết nối đến máy chủ.', 'Lỗi mạng');
                }
            });
        }
    }
</script>

<?php $this->load->view("partial/footer"); ?>
