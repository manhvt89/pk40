<?php $this->load->view("partial/header"); ?>

<div id="page_title"><?php echo $this->lang->line('reports_report_input'); ?></div>

<?php
if(isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}
?>

<?php echo form_open('#', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>

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
			<?php echo form_input(array('name'=>'yearInput', 'placeholder'=>"Nhập năm (YYYY)", 'class'=>'form-control input-sm', 'id'=>'yearInput')); ?>
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

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
	$(document).ready(function()
	{
		<?php $this->load->view('partial/bootstrap_tables_locale'); ?>
		$("#generate_report").click(function()
		{
			var csrf_ospos_v3 = csrf_token();
			var location_id = 0;
			
			var _strDate = $("#yearInput").val().trim();

			// Regex: chỉ cho phép 4 số
			var yearRegex = /^(19|20)\d{2}$/;

			if (!_strDate) {
				alert("Vui lòng nhập năm");
				return;
			}

			if (!yearRegex.test(_strDate)) {
				alert("Năm không hợp lệ. Ví dụ: 2024");
				return;
			}

			var year = parseInt(_strDate, 10);

			// kiểm tra thêm nếu cần
			var currentYear = new Date().getFullYear();
			if (year < 1900 || year > currentYear + 1) {
				alert("Năm nằm ngoài phạm vi cho phép");
				return;
			}	
		
			$.ajax({
				method: "POST",
				url: "<?php echo site_url('reports/ajax_medical')?>",
				//data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
				data: { location_id: location_id, yearInput: year ,csrf_ospos_v3: csrf_ospos_v3 },
				dataType: 'json'
			})
				.done(function( msg ) {
					if(msg.result == 1)
					{
						var detail_data = msg.data.details_data;
						var header_summary = msg.data.headers_summary;
						var summary_data = msg.data.summary_data;
						var header_details = msg.data.headers_details;
						/*
						var init_dialog = function()
						{

						};
						*/
						header_summary[5].formatter = window[header_summary.formatter];
						$('#table').bootstrapTable('destroy');
						$('#table').bootstrapTable({
							//columns: header_summary,
							exportFooter: true,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: true,
							uniqueId: 'id',
							showExport: true,
							data: summary_data,
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: false,
							uniqueId: 'id',
							escape: false,
							/*onPageChange: init_dialog,*/
							onPostBody: function() {
								dialog_support.init("a.modal-dlg");
							},
							
						});

						//init_dialog();


					}else{
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}

				});
		});

		currencyFormatter = function (value)
		{
			console.log(value);
			var color = '#' + Math.floor(Math.random() * 6777215).toString(16)
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

		iformatter = function()
		{
			return '<b>Tổng cộng</b>';
		}

		totalformatter = function(data)
		{
			var field = this.field
			var result;
			console.log(field);
			result = data.map(function (row) {
				console.log(row[field]);
			return +row[field]
			}).reduce(function (sum, i) {
			return sum + i
			}, 0)
			result = Number(result).toLocaleString('en-US', { maximumFractionDigits: 0 });
			return result;
		}

		$('#table').bootstrapTable({
							//columns: header_summary,
							exportFooter: true,
							pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
							striped: true,
							pagination: true,
							sortable: true,
							uniqueId: 'id',
							showExport: true,
							data: [],
							iconSize: 'sm',
							paginationVAlign: 'bottom',
							detailView: false,
							uniqueId: 'id',
							escape: false,
							/*onPageChange: init_dialog,*/
							onPostBody: function() {
								dialog_support.init("a.modal-dlg");
							},
							
						});
		

	});
</script>