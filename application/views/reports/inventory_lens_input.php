<?php $this->load->view("partial/header"); ?>

<div id="page_title"><?php echo $this->lang->line('reports_report_input'); ?></div>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>

<?php echo form_open('#', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>

	<div class="form-group form-group-sm">
		<?php echo form_label($this->lang->line('reports_stock_location'), 'reports_stock_location_label', array('class'=>'required control-label col-xs-2')); ?>
		<div id='report_stock_location' class="col-xs-3">
			<?php echo form_dropdown('stock_location',$stock_locations,'all','id="location_id" class="form-control"'); ?>
		</div>
	</div>

	<?php
	echo form_button(array(
		'name'=>'generate_report',
		'id'=>'generate_report',
		'content'=>$this->lang->line('common_submit'),
		'class'=>'btn btn-primary btn-sm')
	);
	?>
<?php echo form_close(); ?>
<div id="view_report_lens_category">

</div>
<div id="table_holder">
	<div id="toolbar">
		<div class="pull-left form-inline" role="toolbar">
			<!--
			<button id="delete" class="btn btn-default btn-sm print_hide">
				<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
			</button>
			-->
			<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
		
		</div>
	</div>
	<table 
		id="table" 
		data-export-types="['excel']">
	</table>
</div>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
	$(document).ready(function()
	{
		<?php $this->load->view('partial/daterangepicker'); ?>

		$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {

			var csrf_ospos_v3 = csrf_token();
			var location_id = $('#location_id').val();
			var _strDate = $("#daterangepicker").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];

			$.ajax({
				method: "POST",
				url: "<?php echo site_url('reports/ajax_inventory_cat_lens')?>",
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

						var init_dialog = function()
						{

						};
						//$('#table').bootstrapTable('refresh');
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
							escape: false,
							onExpandRow: function (index, row, $detail) {
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var category = row.cat;
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
									url: '<?php echo site_url('reports/ajax_inventory_lens')?>',
									method: 'get',
									data: {
										category: category,
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
											// Thêm dữ liệu chi tiết vào nội dung
											// Hiển thị nội dung chi tiết trong dòng đã mở rộng
											
											$detail.html('<table></table>').find("table").bootstrapTable({
												columns: header_details,
												data: detail_data,
												sortable: true,
												showExport: true,
												exportTypes: ['excel'],
											});
											// Lưu trạng thái dòng đã mở rộng
											expandedRowIndex = index;
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
							}
							
						});
						//$('#table').bootstrapTable('load',{data: summary_data});
					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});

		});
		var expandedRowIndex = -1; // Sử dụng để theo dõi dòng nào đã được mở rộng
		<?php $this->load->view('partial/bootstrap_tables_locale'); ?>
		$("#generate_report").click(function()
		{
			var csrf_ospos_v3 = csrf_token();
			var location_id = $('#location_id').val();
			var _strDate = $("#daterangepicker").val();
			var _aDates = _strDate.split(" - ");			
			var fromDate = _aDates[0];
			var toDate = _aDates[1];
			
			//var category = $('#category').val();
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('reports/ajax_inventory_cat_lens')?>",
				data: { location_id: location_id, fromDate:fromDate,toDate:toDate ,csrf_ospos_v3: csrf_ospos_v3 },
				dataType: 'json'
			})
				.done(function( msg ) {
					if(msg.result == 1)
					{

						
						var header_summary = msg.data.headers_summary;
						var summary_data = msg.data.summary_data;
						var header_details = msg.data.headers_details;

						var init_dialog = function()
						{

						};
						//$('#table').bootstrapTable('refresh');
						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							columns: header_summary,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: true,
							showColumns: true,
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: true,
							uniqueId: 'id',
							escape: false,
							onExpandRow: function (index, row, $detail) {
								// Khi người dùng mở rộng một dòng (danh mục), tải dữ liệu chi tiết
								var category = row.cat;
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
								url: '<?php echo site_url('reports/ajax_inventory_lens')?>',
								method: 'get',
								data: {
									category: category,
									fromDate:fromDate,
									toDate:toDate
								},
								dataType: 'json',
								success: function (data) {
									console.log(data);
									// Xây dựng nội dung chi tiết
									if(data.result == 1)
									{
									//var detail_data = msg.data.details_data;
										var detail_data = data.data.details_data;
										// Thêm dữ liệu chi tiết vào nội dung
										// Hiển thị nội dung chi tiết trong dòng đã mở rộng
										
										$detail.html('<table></table>').find("table").bootstrapTable({
											columns: header_details,
											data: detail_data,
											sortable: true,
											showExport: true,
											exportTypes: ['excel'],
										});
										// Lưu trạng thái dòng đã mở rộng
										expandedRowIndex = index;
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
							}
							
						});
						//$('#table').bootstrapTable('load',{data: summary_data});
					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		});

	});
</script>