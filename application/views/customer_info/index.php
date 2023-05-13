<?php
/**
 * Created by PhpStorm.
 * User: MANHVT
 * Date: 11-Mar-17
 * Time: 3:42 PM
 */
?>
<?php $this->load->view("partial/header"); ?>
<div class="panel-info">
    Hãy nhập thông tin của khách hàng, Mã số KH, Số điện thoại, hay tên của khách hàng
</div>
<form method="post" action="customer_info/index">
    <div class="form-group">
        <label class="col-md-2 control-label" for="textinput">Thông tin khách hàng</label>
        <div class="col-md-4">
            <input id="textinput" name="textinput" class="form-control input-md" type="text">
            <input id="hddinput" name="hddinput" value="1" type="hidden" />
        </div>
        <div class="col-md-4">
            <button id="button1id" name="button1id" class="btn btn-success">Tìm</button>
        </div>
    </div>
    <?php if(!empty($message))
    {
        ?>
        <div class="warning col-md-12" style="color: red;"><?php echo $message; ?></div>
        <?php
    }
    ?>
</form>
</br>
<?php if($report_data):?>
<div id="table_holder">
    <table id="table"></table>
</div>
<script type="text/javascript">

    $(document).ready(function()
    {
        <?php $this->load->view('partial/bootstrap_tables_locale'); ?>

        var detail_data = <?php echo json_encode($details_data); ?>;

        var init_dialog = function()
        {

            <?php if (isset($editable)): ?>
            table_support.submit_handler('<?php echo site_url("reports/get_detailed_" . $editable . "_row")?>');
            dialog_support.init("a.modal-dlg");
            <?php endif; ?>
        };

        $('#table').bootstrapTable({
            columns: <?php echo transform_headers($headers['summary'], TRUE); ?>,
            pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
            striped: true,
            pagination: true,
            sortable: true,
            showColumns: true,
            uniqueId: 'id',
            showExport: true,
            data: <?php echo json_encode($summary_data); ?>,
            iconSize: 'sm',
            paginationVAlign: 'bottom',
            detailView: true,
            uniqueId: 'id',
            escape: false,
            onPageChange: init_dialog,
            onPostBody: function() {
                dialog_support.init("a.modal-dlg");
            },
            onExpandRow: function (index, row, $detail) {
                $detail.html('<table></table>').find("table").bootstrapTable({
                    columns: <?php echo transform_headers_readonly($headers['details']); ?>,
                    data: detail_data[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
                });
            }
        });

        init_dialog();
    });
</script>
<?php  endif; ?>

<?php $this->load->view("partial/footer"); ?>
