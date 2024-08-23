<?php $this->load->view("partial/header"); ?>
<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
    
	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
        table_support.refresh();
    });

	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>
    // set the beginning of time as starting date
    $('#daterangepicker').data('daterangepicker').setStartDate("<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>");
	// update the hidden inputs with the selected dates before submitting the search data
    var start_date = "<?php echo date('Y-m-d', mktime(0,0,0,01,01,2010));?>";
	$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
        table_support.refresh();
    });

    $("#stock_location").change(function() {
       table_support.refresh();
    });

    <?php $this->load->view('partial/bootstrap_tables_locale'); ?>

    var _headers = <?php echo $table_headers; ?>;
    <?php if(has_grant('is_show_count','oincs')): //Phân quyền kiểm kê ?>
    $_obt = {
                field: 'view_button',
                title: 'Kiểm kê',
                formatter: viewFormatter,
                events: {
                    'click .view-recipe-btn': redirectToForm,
                },
            };

    _headers.push($_obt);
    <?php endif; ?>
    <?php if(has_grant('is_show_post','oincs')): //can so sánh số lượng hệ thống và số lượng kiểm kê ?>
    $_obt = {
                field: 'post_button',
                title: 'Kiểm tra',
                formatter: CheckFormatter,
                events: {
                    'click .view-recipe-btn': redirectToCheckForm,
                },
            };
    _headers.push($_obt);
    <?php endif; ?>

    table_support.init({
        employee_id: <?php echo $this->Employee->get_logged_in_employee_info()->person_id; ?>,
        resource: '<?php echo site_url($controller_name);?>',
        headers: _headers,
        pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
        uniqueId: 'items.item_id',
        showExport: true,
        queryParams: function() {
            return $.extend(arguments[0], {
                start_date: start_date,
                end_date: end_date,
                stock_location: $("#stock_location").val(),
                filters: $("#filters").val() || [""]
            });
        },
        onLoadSuccess: function(response) {
            $('a.rollover').imgPreview({
				imgCSS: { width: 200 },
				distanceFromCursor: { top:10, left:-210 }
			})
        }
    });
});
function viewFormatter(value, row, index) {
        console.log(row);
        console.log(value);
        if(row.raw_status == 'O')
        {
            
            return '<button class="btn btn-warning view-recipe-btn btn-sm">Bắt đầu</button>';
        } 
        else if(row.raw_status == 'W')
        {
            return '<button class="btn btn-info view-recipe-btn btn-sm">Sửa</button>';
        }
        else if(row.raw_status == 'P' || row.raw_status == 'C')
        {   
            return '<button class="btn btn-success view-recipe-btn btn-sm">Xem</button>';
        } else {

        }
}


function redirectToForm(event, value, row, index) {
    // Thay đổi đường dẫn theo nhu cầu của bạn
    var url = '';
    if(row.raw_status == 'P' || row.raw_status == 'C')
    {
        url = '<?php echo site_url($controller_name . "/check"); ?>/' + row.uuid; 
    } else {
        url = '<?php echo site_url($controller_name . "/count"); ?>/' + row.uuid; 
    }
    window.location.href = url;
}

function CheckFormatter(value, row, index) {
        console.log(row);
        console.log(value);
        if(row.raw_status == 'O')
        {
            
            return '';
        } 
        else if(row.raw_status == 'C')
        {
            return '<button class="btn btn-warning view-recipe-btn btn-sm">Kiểm tra</button>';
        }
        else if(row.raw_status == 'P')
        {   
            return '<button class="btn btn-success view-recipe-btn btn-sm">Xem</button>';
        } else {

        }
}


function redirectToCheckForm(event, value, row, index) {
    // Thay đổi đường dẫn theo nhu cầu của bạn
    var url = '<?php echo site_url($controller_name . "/check"); ?>/' + row.uuid; 
    window.location.href = url;
}
    

</script>

<div id="title_bar" class="btn-toolbar print_hide">
    <?php if ($this->Employee->has_grant($controller_name.'_excel_import')) { ?>
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/excel_import"); ?>'
            title='<?php echo $this->lang->line('items_import_items_excel'); ?>'>
        <span class="glyphicon glyphicon-import">&nbsp</span><?php echo $this->lang->line('common_import_excel'); ?>
    </button>
    <?php } ?>
    <?php if ($this->Employee->has_grant($controller_name.'_is_show_view')) { ?>
    <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-new='<?php echo $this->lang->line('oincs_btn_cancel') ?>' data-btn-submit='<?php echo $this->lang->line('oincs_create_new') ?>' data-href='<?php echo site_url($controller_name . "/view"); ?>'
            title='<?php echo $this->lang->line($controller_name . '_new'); ?>'>
        <span class="glyphicon glyphicon-tag">&nbsp</span><?php echo $this->lang->line($controller_name . '_new'); ?>
    </button>
    <?php } ?>
    <?php if ($this->Employee->has_grant($controller_name.'_run_synchro')) { ?>
    
    <button class='btn btn-info btn-sm pull-right' id="runCronBtn" title='Đồng bộ sản phẩm'>
        <span class="glyphicon glyphicon-tag">&nbsp</span>Đồng bộ sản phẩm
    </button>
    <?php } ?>
</div>

<div id="toolbar">
    <div class="pull-left form-inline" role="toolbar">
    <?php if ($this->Employee->has_grant($controller_name.'_delete')) { ?>
        <button id="delete" class="btn btn-default btn-sm print_hide">
            <span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete"); ?>
        </button>
    <?php } ?>
    <?php if ($this->Employee->has_grant($controller_name.'_bulk_edit')) { ?>
        <button id="bulk_edit" class="btn btn-default btn-sm modal-dlg print_hide", data-btn-submit='<?php echo $this->lang->line('common_submit') ?>', data-href='<?php echo site_url($controller_name."/bulk_edit"); ?>'
				title='<?php echo $this->lang->line('items_edit_multiple_items'); ?>'>
            <span class="glyphicon glyphicon-edit">&nbsp</span><?php echo $this->lang->line("items_bulk_edit"); ?>
        </button>
    <?php } ?>
    <?php if ($this->Employee->has_grant($controller_name.'_generate_barcodes')) {?>
        <button id="add_barcodes" class="btn btn-default btn-sm print_hide" data-href='<?php echo site_url($controller_name."/add_barcodes"); ?>' title='<?php echo $this->lang->line('items_generate_barcodes');?>'>
            <span class="glyphicon glyphicon-barcode">&nbsp</span><?php echo 'Thêm SP Tạo barocde'; ?>
        </button>
    <?php } ?>    
        <?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
        <?php echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'class'=>'selectpicker show-menu-arrow', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
        <?php
        if (count($stock_locations) > 1)
        {
            echo form_dropdown('stock_location', $stock_locations, $stock_location, array('id'=>'stock_location', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit'));
        }
        ?>
    </div>
</div>

<div id="table_holder">
    <table 
        id="table" 
        data-sort-order="desc" 
        data-sort-name="oinc_id"
        data-search="true" 
        data-export-types="['excel']">
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="frmView" tabindex="-1" role="dialog" aria-labelledby="OincModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header print_hide">
        <h5 class="modal-title" id="RecipeModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="body-recipe-view-modal" class="modal-body">
        <!-- Form để nhập số tiền và chọn phương thức thanh toán -->
      </div>
      <div class="modal-footer print_hide">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" id="CreateBtn">Tạo mới</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<?php $this->load->view("partial/footer"); ?>
