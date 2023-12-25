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
			<?php echo form_label('Họ và tên', 'first_name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'first_name',
						'id'=>'first_name',
						'class'=>'form-control input-sm',
						'value'=>$person_info->first_name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_gender'), 'gender', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-4">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'gender',
							'type'=>'radio',
							'id'=>'gender',
							'value'=>1,
							'checked'=>$person_info->gender === '1')
							); ?> <?php echo $this->lang->line('common_gender_male'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'gender',
							'type'=>'radio',
							'id'=>'gender',
							'value'=>0,
							'checked'=>$person_info->gender === '0')
							); ?> <?php echo $this->lang->line('common_gender_female'); ?>
				</label>

			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_age'), 'age', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-age"></span></span>
					<?php echo form_input(array(
							'name'=>'age',
							'id'=>'age',
							'class'=>'form-control input-sm',
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
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
					<?php echo form_input(array(
							'name'=>'phone_number',
							'id'=>'phone_number',
							'class'=>'form-control input-sm',
							'value'=>$person_info->phone_number)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_address_1'), 'address_1', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'address_1',
						'id'=>'address_1',
						'class'=>'form-control input-sm',
						'value'=>$person_info->address_1)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_city'), 'city', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php

					echo form_dropdown('city', $cities, $city, array('class'=>'form-control','id'=>'city')); ?>

			</div>
		</div>
				
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('customers_account_number'), 'account_number', array('class' => 'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<?php echo form_input(array(
						'name'=>'account_number',
						'id'=>'account_number',
						'class'=>'form-control input-sm',
						'value'=>$person_info->account_number)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('customers_total'), 'total', array('class' => 'control-label col-xs-3')); ?>
			<div class="col-xs-4">
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'total',
							'id'=>'total',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($total),
							'disabled'=>'')
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('customers_discount'), 'discount_percent', array('class' => 'control-label col-xs-3')); ?>
			<div class='col-xs-3'>
				<div class="input-group input-group-sm">
					<?php echo form_input(array(
							'name'=>'discount_percent',
							'id'=>'discount_percent',
							'class'=>'form-control input-sm',
							'value'=>$person_info->discount_percent)
							);?>
					<span class="input-group-addon input-sm"><b>%</b></span>
				</div>
			</div>	
		</div>
	</fieldset>
<?php echo form_close(); ?>

</br>
<ul class="nav nav-tabs" data-tabs="tabs">   
    <li class="active" role="presentation">
        <a data-toggle="tab" href="#sales" title="danh sách đơn hàng">Danh sách đơn hàng</a>
    </li>
	<li class="" role="presentation">
        <a data-toggle="tab" href="#tests" title="Danh sách đơn kính & đơn thuốc">Danh sách đơn kính</a>
    </li>
</ul>

<div class="tab-content">

    <div class="tab-pane fade in active" id="sales">
		
		<div id="table_holder">
			<div id="toolbar">
				<div class="pull-left form-inline" role="toolbar">
					<!--
					<button id="delete" class="btn btn-default btn-sm print_hide">
						<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
					</button>
					-->

					<?php echo form_input(array('name'=>'daterangepicker_sales', 'class'=>'form-control input-sm', 'id'=>'daterangepicker_sales')); ?>
					<?php //echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'class'=>'selectpicker show-menu-arrow', 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
				</div>
			</div>
			<table id="table"
				data-export-types="['excel']"
				data-show-footer="false"
				data-export-footer="false"
				data-show-columns="false"
			> 
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

<script type="text/javascript">
	$(document).ready(function()
	{
		var expandedRowIndex = -1; // Sử dụng để theo dõi dòng nào đã được mở rộng
		var oParentTotal = {remain_total:0,paid_total:0};
		var currentYear = new Date().getFullYear();
		var start_date = "<?php echo '01/01/'.date('Y') ?>";
		var end_date   = "<?php echo date('d/m/Y') ?>";
		

		$('#daterangepicker_sales').daterangepicker({
			"startDate": start_date,
    		"endDate": end_date,
			"ranges": {
				"<?php echo $this->lang->line("datepicker_this_year"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,1,1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")+1)-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_today"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d"),date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_today_last_year"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d"),date("Y")-1));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y")-1)-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_yesterday"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")-1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d"),date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_last_7"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")-6,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_last_30"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")-29,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_this_month"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")+1,1,date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_same_month_to_same_day_last_year"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")-1));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y")-1)-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_this_month_last_year"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y")-1));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")+1,1,date("Y")-1)-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_last_month"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m")-1,1,date("Y")));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),1,date("Y"))-1);?>"
				],
				
				"<?php echo $this->lang->line("datepicker_last_year"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,1,1,date("Y")-1));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,1,1,date("Y"))-1);?>"
				],
				"<?php echo $this->lang->line("datepicker_all_time"); ?>": [
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>",
					"<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
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
			"alwaysShowCalendars": true,
			//"startDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
			//"endDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>",
			"minDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,01,01,2010));?>",
			"maxDate": "<?php echo date($this->config->item('dateformat'), mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1);?>"
		}, function(start, end, label) {
			start_date = start.format('YYYY-MM-DD');
			end_date = end.format('YYYY-MM-DD');
		});

		$("#daterangepicker_sales").on('apply.daterangepicker', function(ev, picker) {

			var csrf_ospos_v3 = csrf_token();
			var uuid = '<?=$person_info->customer_uuid?>';
			var _strDate = $("#daterangepicker_sales").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];

			$.ajax({
				method: "POST",
				url: "<?php echo site_url('customers/ajax_saleings')?>",
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
							showColumns: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: true,
							uniqueId: 'id',
							escape: false,
							onExpandRow: function (index, row, $detail) {
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var category = row.supplier_uuid;
								console.log('expandedRowIndex:'+expandedRowIndex);
								
								var _strDate = $("#daterangepicker_sales").val();
								var _aDates = _strDate.split(" - ");			
								var fromDate = _aDates[0];
								var toDate = _aDates[1];
								// Ẩn dữ liệu chi tiết của dòng đã mở rộng trước đó (nếu có)
								if (expandedRowIndex !== -1 && expandedRowIndex !== index) {
									$('#table').bootstrapTable('collapseRow', expandedRowIndex);
								}

								$.ajax({
									url: '<?php echo site_url('customers/ajax_saleings_detail')?>',
									method: 'get',
									data: {
										supplier_uuid: category,
										fromDate:fromDate,
										toDate:toDate
									},
									dataType: 'json',
									success: function (data) {
										//console.log(data);
										// Xây dựng nội dung chi tiết
										if(data.result == 1)
										{
										//var detail_data = msg.data.details_data;
											var detail_data = data.data.details_data;
											$detail.html('<table></table>').find("table").bootstrapTable({
												columns: header_details,
												data: detail_data,
												sortable: true,
												showExport: true,
												exportTypes: ['excel'],
											});
											// Lưu trạng thái dòng đã mở rộng
											expandedRowIndex = index;
											oParentTotal.remain_total = row.remain_amount;
											oParentTotal.paid_total = row.paid_amount;
										}
									},
									error: function (error) {
										console.log('Error fetching product details:', error);
									}
									});		
								//alert(JSON.stringify(header_details));
								
							},
							onCollapseRow: function (index, row) {
								// Khi người dùng thu gọn dòng, đặt lại trạng thái dòng đã mở rộng
								expandedRowIndex = -1;
								oParentTotal.remain_total = 0;
								oParentTotal.paid_total = 0;
							}
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
			var uuid = '<?=$person_info->customer_uuid?>';
			//var category = $('#category').val();
			
			var _strDate = $("#daterangepicker_sales").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];
			var currentYear = new Date().getFullYear();
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('customers/ajax_saleings')?>",
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
							showColumns: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: true,
							uniqueId: 'id',
							escape: false,
							
							onExpandRow: function (index, row, $detail) {
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var sale_uuid = row.sale_uuid;
								console.log('expandedRowIndex:'+expandedRowIndex);
								console.log('sale_uuid:'+ sale_uuid);
								var _strDate = $("#daterangepicker_sales").val();
								var _aDates = _strDate.split(" - ");			
								var fromDate = _aDates[0];
								var toDate = _aDates[1];
								// Ẩn dữ liệu chi tiết của dòng đã mở rộng trước đó (nếu có)
								if (expandedRowIndex !== -1 && expandedRowIndex !== index) {
									$('#table').bootstrapTable('collapseRow', expandedRowIndex);
								}

								$.ajax({
									url: '<?php echo site_url('customers/ajax_saleings_detail')?>',
									method: 'get',
									data: {
										sale_uuid: sale_uuid,
										fromDate:fromDate,
										toDate:toDate
									},
									dataType: 'json',
									success: function (data) {
										//console.log(data);
										// Xây dựng nội dung chi tiết
										if(data.result == 1)
										{
										//var detail_data = msg.data.details_data;
											var detail_data = data.data.details_data;
											$detail.html('<table></table>').find("table").bootstrapTable({
												columns: header_details,
												data: detail_data,
												sortable: false,
												showExport: true,
												exportTypes: ['excel'],
											});
											// Lưu trạng thái dòng đã mở rộng
											expandedRowIndex = index;
											oParentTotal.remain_total = row.remain_amount;
											oParentTotal.paid_total = row.paid_amount;
										}
									},
									error: function (error) {
										console.log('Error fetching product details:', error);
									}
									});		
								//alert(JSON.stringify(header_details));
								
							},
							onCollapseRow: function (index, row) {
								// Khi người dùng thu gọn dòng, đặt lại trạng thái dòng đã mở rộng
								expandedRowIndex = -1;
								oParentTotal.remain_total = 0;
								oParentTotal.paid_total = 0;
							}
						});

					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		};
		init();
		init_tblTest = function()
		{
			

			// Initialize DateRangePicker
			
			var csrf_ospos_v3 = csrf_token();
			var uuid = '<?=$person_info->customer_uuid?>';
			//var category = $('#category').val();
	
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('customers/ajax_tests')?>",
				//data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
				data: { uuid: uuid,csrf_ospos_v3: csrf_ospos_v3 },
				dataType: 'json'
			})
				.done(function( msg ) {
					if(msg.result == 1)
					{
						var header_summary = msg.data.headers_summary;
						var summary_data = msg.data.summary_data;
						// Thêm dữ liệu chi tiết vào nội dung
						$('#tbl_tests').bootstrapTable('destroy');
						$('#tbl_tests').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: false,
							showColumns: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: false,
							uniqueId: 'id',
							escape: false,
							exportTypes: ['excel'],
							
						});

					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		};

		init_tblTest();
		//$('#tbl_tests').bootstrapTable({});
		

		currencyFormatter = function (value)
		{
			console.log(value);
			//var color = '#' + Math.floor(Math.random() * 6777215).toString(16)
			var color = '#000000';
			$_return = Number(value).toLocaleString('en-US', { maximumFractionDigits: 0 });
			if($_return == '0')
			{
				$_return = '-';
			}
			return '<div style="color: ' + color + '">' +
			'<i class="fa fa-dollar-sign"></i>' +
			$_return +
			'</div>'
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