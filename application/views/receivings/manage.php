<?php $this->load->view("partial/header"); ?>
<script src="/dist/jquery.number.min.js"></script>


<?php $this->load->view('partial/print_receipt', array('print_after_sale'=>false, 'selected_printer'=>'takings_printer')); ?>

<?php if($is_created == 1): ?>
<div id="title_bar" class="print_hide btn-toolbar">
	<button onclick="javascript:printdoc()" class='btn btn-info btn-sm pull-right'>
		<span class="glyphicon glyphicon-print">&nbsp</span><?php echo $this->lang->line('common_print'); ?>
	</button>
	<?php echo anchor("sales", '<span class="glyphicon glyphicon-shopping-cart">&nbsp</span>' . $this->lang->line('sales_register'), array('class'=>'btn btn-info btn-sm pull-right', 'id'=>'show_sales_button')); ?>
</div>
<?php endif; ?>
<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<!--
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
		-->

		<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		<?php echo form_multiselect('filters[]', $filters, '', array('id'=>'filters', 'data-none-selected-text'=>$this->lang->line('common_none_selected_text'), 'class'=>'selectpicker show-menu-arrow', 'data-selected-text-format'=>'count > 1', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
	</div>
</div>

<div id="table_holder">
	<table id="table" data-sort-order="desc" data-sort-name="receiving_time"></table>
</div>

<div id="payment_summary">
</div>
<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Thanh Toán Đơn Hàng</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form để nhập số tiền và chọn phương thức thanh toán -->
        <form id="paymentForm">
		<div class="form-group">
			<span style="color:red" id="errorDisplay"></span>
		</div>
          <div class="form-group">
            <label for="amount" id="lblRemainAmount">Số tiền thanh toán ():</label>
            <input type="text" class="form-control" id="amount" placeholder="Nhập số tiền">
			<input type="hidden" class="form-control" id="hdd_receiving_id" name="hdd_receiving_id" value="0">
			<input type="hidden" class="form-control" id="hdd_remain_amount" name="hdd_remain_amount" value="0">
			<input type="hidden" class="form-control" id="hdd_row_index" name="hdd_row_index" value="0">
			<input type="hidden" class="form-control" id="hdd_total_amount" name="hdd_total_amount" value="0">
			<input type="hidden" class="form-control" id="hdd_paid_amount" name="hdd_paid_amount" value="0">
			
          </div>
          <div class="form-group">
            <label for="paymentMethod">Phương Thức Thanh Toán:</label>
            <select class="form-control" id="paymentMethod">
              <option value="Chuyển khoản">Chuyển Khoản</option>
              <option value="Tiền mặt">Tiền Mặt</option>
              
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" id="confirmPaymentBtn">Thanh Toán</button>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">
	$(document).ready(function()
	{
		var expandedRowIndex = -1; // Sử dụng để theo dõi dòng nào đã được mở rộng
		var oParentTotal = {remain_total:0,paid_total:0};
		var currentYear = new Date().getFullYear();
		var start_date = "<?php echo '01/01/'.date('Y') ?>";
		var end_date   = "<?php echo date('d/m/Y') ?>";
		$('#amount').number(true,0,',','.');

		$('#daterangepicker').daterangepicker({
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

		$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {

			var csrf_ospos_v3 = csrf_token();
			var location_id = 1;
			var _strDate = $("#daterangepicker").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];

			$.ajax({
				method: "POST",
				url: "<?php echo site_url('receivings/ajax_receivings')?>",
				data: { location_id: location_id, fromDate:fromDate,toDate:toDate ,csrf_ospos_v3: csrf_ospos_v3 },
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
											$_obt = {
														field: 'payment_button',
														title: 'Thanh Toán',
														formatter: paymentFormatter,
														events: {
															'click .payment-btn': openPaymentPopup,
														},
													};
											header_details.push($_obt);

						var init_dialog = function()
						{

						};
						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: true,
							showColumns: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
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
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var category = row.supplier_uuid;
								console.log('expandedRowIndex:'+expandedRowIndex);
								console.log('category:'+ category);
								var _strDate = $("#daterangepicker").val();
								var _aDates = _strDate.split(" - ");			
								var fromDate = _aDates[0];
								var toDate = _aDates[1];
								// Ẩn dữ liệu chi tiết của dòng đã mở rộng trước đó (nếu có)
								if (expandedRowIndex !== -1 && expandedRowIndex !== index) {
									$('#table').bootstrapTable('collapseRow', expandedRowIndex);
								}

								$.ajax({
									url: '<?php echo site_url('receivings/ajax_receivings_detail')?>',
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

						init_dialog();
						
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
			var location_id = 1;
			//var category = $('#category').val();
			
			var _strDate = $("#daterangepicker").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];
			var currentYear = new Date().getFullYear();
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('receivings/ajax_receivings')?>",
				//data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
				data: { location_id: location_id, fromDate:fromDate,toDate:toDate ,csrf_ospos_v3: csrf_ospos_v3 },
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
						$_obt = {
									field: 'payment_button',
									title: 'Thanh Toán',
									formatter: paymentFormatter,
									events: {
										'click .payment-btn': openPaymentPopup,
									},
								};
						header_details.push($_obt);

						var init_dialog = function()
						{

						};
						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: true,
							showColumns: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
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
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var category = row.supplier_uuid;
								console.log('expandedRowIndex:'+expandedRowIndex);
								console.log('category:'+ category);
								var _strDate = $("#daterangepicker").val();
								var _aDates = _strDate.split(" - ");			
								var fromDate = _aDates[0];
								var toDate = _aDates[1];
								// Ẩn dữ liệu chi tiết của dòng đã mở rộng trước đó (nếu có)
								if (expandedRowIndex !== -1 && expandedRowIndex !== index) {
									$('#table').bootstrapTable('collapseRow', expandedRowIndex);
								}

								$.ajax({
									url: '<?php echo site_url('receivings/ajax_receivings_detail')?>',
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

						init_dialog();


					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		};
		init();

		$('#confirmPaymentBtn').click(function() {
			// Lấy thông tin từ popup
			console.log('Start...');
			
			var receiving_id = $('#hdd_receiving_id').val();
			var paymentAmount = $('#amount').val();
			var paymentMethod = $('#paymentMethod').val();
			var remain_amount = $('#hdd_remain_amount').val();
			var currentRowIndex = $('#hdd_row_index').val();
			var total_amount = $('#hdd_total_amount').val();
			var paidAmount = $('#hdd_paid_amount').val();
			
			remain_amount = parseInt(remain_amount.replace(/,/g, ''));
			paymentAmount = parseInt(paymentAmount.replace(/,/g, ''));
			paidAmount = parseInt(paidAmount.replace(/,/g, ''));

			var remain_total = oParentTotal.remain_total;
			var paid_total = oParentTotal.paid_total;
			remain_total = parseInt(remain_total.replace(/,/g, ''));
			paid_total = parseInt(paid_total.replace(/,/g, ''));
			
			console.log(paymentAmount);
			console.log(oParentTotal);
			if(paymentAmount > remain_amount)
			{
				$('#errorDisplay').html('Lỗi! Giá trị thanh toán lớn nhất bằng số công nợ');
				$('#amount').focus();
				return false;
			}
			remain_amount = remain_amount - paymentAmount; //sau khi thanh toán
			paidAmount = paidAmount + paymentAmount; //sau khi thanh toán
			remain_total = remain_total - paymentAmount;
			paid_total = paid_total + paymentAmount;
			var detailView = $('#table').find('.detail-view');
			var detailTable = detailView.find("table");
			//var rowToUpdate = detailTable.find('tr[data-index="' + 0+ '"]');
			//rowToUpdate.remove();
			//console.log(detailTable.html());
					// Cập nhật dòng trong bảng chi tiết
					
					//detailTable.bootstrapTable('highlightRow', currentRowIndex);
			//$('#paymentModal').modal('hide');
			//return false;
			// Thực hiện AJAX để gửi thông tin thanh toán đến máy chủ
			$.ajax({
				url: '<?php echo site_url('receivings/process_payment')?>',
				method: 'post',
				data: {
					csrf_ospos_v3: csrf_token,
					receiving_id: receiving_id, // Truyền ID đơn hàng cần thanh toán
					paymentAmount: paymentAmount,
					paymentMethod: paymentMethod,
					remainAmount: remain_amount
				},
				dataType: 'json',
				success: function(msg) {
				// Xử lý kết quả thanh toán
				// ...
					/*
					$('#table').find("table")bootstrapTable('updateRow', {
						index: currentRowIndex,
						row: {
							remain_amount: remain_amount -  paymentAmount,// Thay 'paymentAmount' bằng trường tương ứng trong dòng của bạn
							paid_amount: total_amount - (remain_amount -  paymentAmount)
						}
					});
					*/
					if(msg.result == 1)
					{
						
						detailTable.bootstrapTable('updateRow', {
							index: currentRowIndex,
							row: {
								// Cập nhật các trường cần thiết
								//'stt': '1111',
								//'code':'Ahhh22233',
								//'receiving_id': '1233',
								'remain_amount': remain_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
								'paid_amount': paidAmount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
								//'receiving_time': '20/11/2023',
								'payment_type': paymentMethod,
								'total_amount': total_amount
								// ...
							}
						});
						
						//var element = detailTable.find('tbody tr').eq(currentRowIndex).addClass('edited-row');
						var element = detailTable.find('tbody tr').eq(currentRowIndex);
						var color = null;
						var original = element.css('backgroundColor');
						element.animate({ backgroundColor: color || '#e1ffdd' }, "slow", "linear")
							.animate({ backgroundColor: color || '#e1ffdd' }, 5000)
							.animate({ backgroundColor: original }, "slow", "linear");
						/*
						$('#table').bootstrapTable('updateRow', {
							index: expandedRowIndex,
							row: {
								
								'remain_amount': remain_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
								'paid_amount': paid_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
							
							}
						});
						$('#table').bootstrapTable('expandRow', expandedRowIndex);

						
						detailView = $('#table').find('.detail-view');
						detailTable = detailView.find("table");
						detailTable.find('tbody tr').eq(currentRowIndex).addClass('edited-row');
						*/
						//console.log(detailTable.find('tbody tr').eq(currentRowIndex));
						//console.log('End..'+currentRowIndex);
						$('#hdd_total_amount').val(0);
						$('#hdd_receiving_id').val(0);
						$('#amount').val(0);
						//$('#paymentMethod').val('');
						$('#hdd_remain_amount').val(0);
						$('#hdd_row_index').val(-1);
						$('#hdd_paid_amount').val(0);
						// Đóng popup
						$('#paymentModal').modal('hide');
					}
				},
				error: function(error) {
				console.log('Error processing payment:', error);
				},
			});
			});	

			function paymentFormatter(value, row, index) {
				//console.log(row.remain_amount);
				if(row.remain_amount == "0") {
					return 'Đã thanh toán';
				}
				return '<button class="btn btn-info payment-btn btn-sm">Thanh Toán</button>';
			}

			function openPaymentPopup(e, value, row, index) {
				// Hiển thị popup và truyền thông tin đơn hàng (row) vào popup
				// ...
				console.log(index);
				$('#paymentModalLabel').html('Thanh Toán Đơn Hàng <b>'+row.code+'</b>');
				$('#lblRemainAmount').html('Số tiền thanh toán (<b>'+row.remain_amount+'</b>)');
				$('#hdd_remain_amount').val(row.remain_amount);
				$('#hdd_receiving_id').val(row.receiving_uuid);
				$('#hdd_row_index').val(index);
				$('#hdd_total_amount').val(row.total_amount);
				$('#hdd_paid_amount').val(row.paid_amount);
				// Ví dụ sử dụng Bootstrap Modal
				$('#paymentModal').modal('show');
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