<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{

	// when any filter is clicked and the dropdown window is closed

	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>
    // set the beginning of time as starting date
    $('#daterangepicker').data('daterangepicker').setStartDate("<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>");
	// update the hidden inputs with the selected dates before submitting the search data
    //var start_date = "<?php echo date('Y-m-d', mktime(0,0,0,01,01,2010));?>";
	$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
        table_support.refresh();
    });

    <?php $this->load->view('partial/bootstrap_tables_locale'); ?>

    table_support.init({
        employee_id: <?php echo $this->Employee->get_logged_in_employee_info()->person_id; ?>,
        resource: '<?php echo site_url($controller_name);?>',
        headers: <?php echo $table_headers; ?>,
        pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
        uniqueId: 'total_id',
        showExport: <?php echo $permission_admin ?>,

        queryParams: function() {
            return $.extend(arguments[0], {
                start_date: start_date,
                end_date: end_date,
                filters: $("#filters").val() || [""]
            });
        },
        onLoadSuccess: function(response) {
            if($("#table tbody tr").length > 1) {
                $("#payment_summary").html(response.account_summary);
                $("#table tbody tr:last td:first").html("");
            }else{
                $("#payment_summary").html(response.account_summary);
            }
        }
    });
});
</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('common_new_income') ?>' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/viewi"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new_income'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_income'); ?>
    </button>
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('common_new') ?>' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new_payout'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_payout'); ?>
    </button>
</div>

<div id="toolbar">
    <div class="pull-left form-inline" role="toolbar" style="display: <?php if($permission_admin==1){ }else{ echo 'none';}?>">
        <?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
    </div>
</div>
<div id="payment_summary"></div>
<div id="table_holder">
    <?php if($permission_admin==1): ?>
        <table id="table" data-sort-order="desc" data-sort-name="created_time" data-search="false" ></table>
    <?php else : ?>
        <table id="table" data-sort-order="desc" data-sort-name="created_time" data-search="false" data-show-columns="false"></table>
    <?php endif; ?>
</div>

<?php $this->load->view("partial/footer"); ?>
