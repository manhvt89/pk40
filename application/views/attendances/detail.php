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
    Thông tin khách hàng
</div>
<?php echo form_open('customers/save/'.$person_info->person_id, array('id'=>'customer_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="customer_basic_info">
		
		<div class="form-group form-group-sm">
			<?php echo form_label('Họ và tên', 'first_name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'first_name',
						'id'=>'first_name',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>$person_info->first_name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_gender'), 'gender', !empty($basic_version) ? array('class'=>'control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-2">
				<?php echo form_input(array(
							'name'=>'gender',
							'id'=>'gender',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$person_info->gender == '1' ? $this->lang->line('common_gender_male'):$this->lang->line('common_gender_female'))
							);?>
			</div>
			<?php echo form_label($this->lang->line('common_age'), 'age', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-age"></span></span>
					<?php echo form_input(array(
							'name'=>'age',
							'id'=>'age',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$person_info->age)
					);?>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm" style="display: none">
			<?php echo form_label($this->lang->line('common_email'), 'email', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
					<?php

					if($person_info->email=='NULL')
					{
						$person_info->email = '';
					}

					echo form_input(array(
							'name'=>'email',
							'id'=>'email',
							'class'=>'form-control input-sm',
							'value'=>$person_info->email)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_phone_number'), 'phone_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-2'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
					<?php echo form_input(array(
							'name'=>'phone_number',
							'id'=>'phone_number',
							'class'=>'form-control input-sm',
							'disabled'=>'',
							'value'=>$person_info->phone_number)
							);?>
				</div>
			</div>
			<?php echo form_label($this->lang->line('customers_account_number'), 'account_number', array('class' => 'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'account_number',
						'id'=>'account_number',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>$person_info->code)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_address_1'), 'address_1', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'address_1',
						'id'=>'address_1',
						'class'=>'form-control input-sm',
						'disabled'=>'',
						'value'=>$person_info->address_1 .', '.$city)
						);?>
			</div>
		</div>	
	
	</fieldset>
<?php echo form_close(); ?>

</br>
<ul class="nav nav-tabs" data-tabs="tabs">   
    <li class="active" role="presentation">
        <a data-toggle="tab" href="#attendance" title="Chấm công">Chấm công</a>
    </li>
	<li class="" role="presentation">
        <a data-toggle="tab" href="#" title="...">...</a>
    </li>
</ul>

<div class="tab-content">

    <div class="tab-pane fade in active" id="attendance">
		
		<div id="table_holder">
			<div id="toolbar">
				<div class="pull-left form-inline" role="toolbar">
					<!--
					<button id="delete" class="btn btn-default btn-sm print_hide">
						<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
					</button>
					-->
					<?php echo form_input(array('name'=>'daterangepicker_att', 'class'=>'form-control input-sm', 'id'=>'daterangepicker_att')); ?>
					<?php //echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'class'=>'selectpicker show-menu-arrow', 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
				</div>
			</div>
			<table id="table"
				data-export-types="['excel']"
				data-show-footer="false"
				data-export-footer="false"
				data-show-columns="false"
				data-row-style="rowStyle">
			
				<thead>
					<tr>
					<?=$headers?>
					</tr>
				</thead>
			</table>
		</div>
    </div>
	<div class="tab-pane" id="tests">
		<div id="table_holder_test">
				<table id="tbl_tests"
				data-header-style="headerStyle"
				
				>
					
				</table>
		</div>
    </div>
   
</div>
<script src="/dist/autoNumeric.min.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		var expandedRowIndex = -1; // Sử dụng để theo dõi dòng nào đã được mở rộng
		var oParentTotal = {remain_total:0,paid_total:0};
		var currentYear = new Date().getFullYear();
		var start_date = "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")));?>";
		var end_date   = "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")+1,1,date("Y"))-1);?>";
		

		$('#daterangepicker_att').daterangepicker({
			"startDate": start_date,
    		"endDate": end_date,
			
			"ranges": {
				"<?php echo $this->lang->line("datepicker_this_month"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")+1,1,date("Y"))-1);?>"
				],
				"<?php echo 'Tháng trước'; ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-1,1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y"))-1);?>"
				],
				"<?php echo '2 rháng trước'; ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-2,1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-1,1,date("Y"))-1);?>"
				],
				"<?php echo '3 tháng trước'; ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-3,1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-2,1,date("Y"))-1);?>"
				],
				
			},
			"locale": {
				"format": '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>',
				"separator": " - ",
				"applyLabel": "<?php echo $this->lang->line("datepicker_apply"); ?>",
				"cancelLabel": "<?php echo $this->lang->line("datepicker_cancel"); ?>",
				"fromLabel": "<?php echo $this->lang->line("datepicker_from"); ?>",
				"toLabel": "<?php echo $this->lang->line("datepicker_to"); ?>",
				"customRangeLabel": "<?php echo $this->lang->line("datepicker_custom"); ?>",
				"daysOfWeek": [
					"<?php echo $this->lang->line("cal_su"); ?>",
					"<?php echo $this->lang->line("cal_mo"); ?>",
					"<?php echo $this->lang->line("cal_tu"); ?>",
					"<?php echo $this->lang->line("cal_we"); ?>",
					"<?php echo $this->lang->line("cal_th"); ?>",
					"<?php echo $this->lang->line("cal_fr"); ?>",
					"<?php echo $this->lang->line("cal_sa"); ?>",
					"<?php echo $this->lang->line("cal_su"); ?>"
				],
				"monthNames": [
					"<?php echo $this->lang->line("cal_january"); ?>",
					"<?php echo $this->lang->line("cal_february"); ?>",
					"<?php echo $this->lang->line("cal_march"); ?>",
					"<?php echo $this->lang->line("cal_april"); ?>",
					"<?php echo $this->lang->line("cal_may"); ?>",
					"<?php echo $this->lang->line("cal_june"); ?>",
					"<?php echo $this->lang->line("cal_july"); ?>",
					"<?php echo $this->lang->line("cal_august"); ?>",
					"<?php echo $this->lang->line("cal_september"); ?>",
					"<?php echo $this->lang->line("cal_october"); ?>",
					"<?php echo $this->lang->line("cal_november"); ?>",
					"<?php echo $this->lang->line("cal_december"); ?>"
				],
				"firstDay": <?php echo $this->lang->line("datepicker_weekstart"); ?>
			},
			"alwaysShowCalendars": false,
			showCustomRangeLabel: false,
			autoApply: true,
			//"startDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
			//"endDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
			"minDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>",
			"maxDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
		}, function(start, end, label) {
			start_date = start.format('YYYY-MM-DD');
			end_date = end.format('YYYY-MM-DD');
		});

		$("#daterangepicker_att").on('apply.daterangepicker', function(ev, picker) {

			var csrf_ospos_v3 = csrf_token();
			var uuid = '<?=$person_info->person_uuid?>';
			var _strDate = $("#daterangepicker_att").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];

			$.ajax({
				method: "POST",
				url: "<?php echo site_url('attendances/ajax_attendances')?>",
				data: { uuid: uuid, fromDate:fromDate,toDate:toDate ,csrf_ospos_v3: csrf_ospos_v3 },
				dataType: 'json'
			})
				.done(function( msg ) {
					if(msg.result == 1)
					{
						var detail_data = msg.data.details_data;
						var header_summary = msg.data.headers_summary;
						var summary_data = msg.data.summary_data;
						var header_details = msg.data.headers_details;
						// Thêm dữ liệu chi tiết vào nội dung
						// Hiển thị nội dung chi tiết trong dòng đã mở rộng

						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: false,
							showColumns: false,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: false,
							uniqueId: 'id',
							escape: false,
							
						});

					
						
					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});

		});
		<?php $this->load->view('partial/bootstrap_tables_locale'); ?>
		init = function()
		{
			var currentYear = new Date().getFullYear();

			// Initialize DateRangePicker
			
			var csrf_ospos_v3 = csrf_token();
			var uuid = '<?=$person_info->person_uuid?>';
			//var category = $('#category').val();
			
			var _strDate = $("#daterangepicker_att").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];
			var currentYear = new Date().getFullYear();
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('attendances/ajax_attendances')?>",
				//data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
				data: { uuid: uuid, fromDate:fromDate,toDate:toDate ,csrf_ospos_v3: csrf_ospos_v3 },
				dataType: 'json'
			})
				.done(function( msg ) {
					if(msg.result == 1)
					{

						var detail_data = msg.data.details_data;
						var header_summary = msg.data.headers_summary;
						var summary_data = msg.data.summary_data;
						var header_details = msg.data.headers_details;
						// Thêm dữ liệu chi tiết vào nội dung
						
						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: false,
							showColumns: false,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: false,
							uniqueId: 'id',
							escape: false,
						});

					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		};
		init();
		
		
		currencyFormatter = function (value)
		{
			// Tạo một span chứa giá trị cần định dạng
			var span = document.createElement('span');
            span.textContent = value;

            // Khởi tạo AutoNumeric trên span này
            new AutoNumeric(span, {
                digitGroupSeparator: ',',  // Dấu phân cách hàng nghìn
				decimalCharacter: '.',     // Dấu phân cách thập phân
				decimalPlaces: 0,          // Số chữ số thập phân
				currencySymbol: ' (vnd)',       // Ký hiệu tiền tệ (nếu cần)
				currencySymbolPlacement: 's', // Đặt ký hiệu tiền tệ trước số ('p' là trước, 's' là sau)
            });

            // Trả về HTML của span đã định dạng
            return span.outerHTML;
		}

		durationFormatter = function (value)
		{
			
			// Tạo một span chứa giá trị cần định dạng
			var span = document.createElement('span');
            span.textContent = value;

            // Khởi tạo AutoNumeric trên span này
            new AutoNumeric(span, {
                digitGroupSeparator: ',',  // Dấu phân cách hàng nghìn
				decimalCharacter: '.',     // Dấu phân cách thập phân
				decimalPlaces: 2,          // Số chữ số thập phân
				currencySymbol: ' (giờ)',       // Ký hiệu tiền tệ (nếu cần)
				currencySymbolPlacement: 's', // Đặt ký hiệu tiền tệ trước số ('p' là trước, 's' là sau)
            });

            // Trả về HTML của span đã định dạng
            return span.outerHTML;
			
		}

		rowStyle = function (row, index) {
			console.log(index);
			if (index === 0) { // Kiểm tra nếu đây là dòng đầu tiên
				return {
					classes: 'bg-primary', // CSS class để định dạng
					css: { "font-weight": "bold", 'color':'#000000' }   // Các thuộc tính CSS khác
				};
			}
			return {};
	}

		

		headerStyle = function (column) 
		{
			
			return {
			id: {
				classes: 'uppercase'
			},
			test_date: {
				css: {background: 'yellow'}
			},
			don_kham: {
				css: {color: 'red'}
			}
			}[column.field]
		}

		

	});
	
	
</script>
<style>
	#paymentModal {
  
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	
}
.payment-btn{
	padding 5px;
}
.edited-row {
    background-color: #FFFFCC; /* Màu highlight của bạn */
    transition: background-color 0.5s; /* Thời gian chuyển đổi hiệu ứng */
}
</style>

<?php $this->load->view("partial/footer"); ?>