<?php $this->load->view("partial/header"); ?>

<div id="page_title" class="rp_page_title"><?php echo $this->lang->line('reports_report_input'); ?></div>

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

	<div class="form-group form-group-sm">
		<?php echo form_label($this->lang->line('reports_contact_lens_category'), 'reports_contact_lens_category_label', array('class'=>'required control-label col-xs-2')); ?>
		<div id='report_item_count' class="col-xs-3">
			<?php echo form_dropdown('category',$item_count,'all','id="category" class="form-control"'); ?>
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
<button id="expExcel" style="display: none;"> Excel</button>
<div id="view_report_lens_category">

</div>

<?php $this->load->view("partial/footer"); ?>
<script src="dist/jquery.table2excel.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{

	$('#expExcel').click(function(){
		$(".table2excel").table2excel({
			exclude: ".noExl",
			name: "Excel Document Name",
			filename: "myFileName",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
	});

	$("#generate_report").click(function()
	{
		var csrf_ospos_v3 = csrf_token();
		var location_id = $('#location_id').val();
		var category = $('#category').val();
		$.ajax({
			method: "POST",
			url: "<?php echo site_url('reports/ajax_inventory_contact_lens')?>",
			data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
			dataType: 'json'
		})
			.done(function( msg ) {
				if(msg.result == 1)
				{
					var html = '';
					if(msg.data.category == 'Bách Quang') {
						var header = '<tr><td colspan="36" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="6" class="rp_company"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.ordered_date + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6" style="text-align: left">Số ....</td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_des"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.customer + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_brand"></td>' +
							'<td colspan="14" style="text-align: left">Địa chỉ: ' + msg.data.header.address + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';


						var header_rp = '<tr><td>STT</td><td>Tên hàng</td>' +
							'<td>0.00</td><td>0.25</td><td>0.50</td><td>0.75</td>' +
							'<td>1.00</td><td>1.25</td><td>1.50</td><td>1.75</td>' +
							'<td>2.00</td><td>2.25</td><td>2.50</td><td>2.75</td>' +
							'<td>3.00</td><td>3.25</td><td>3.50</td><td>3.75</td>' +
							'<td>4.00</td><td>4.25</td><td>4.50</td><td>4.75</td>' +
							'<td>5.00</td><td>5.25</td><td>5.50</td><td>5.75</td>' +
							'<td>6.00</td><td>6.50</td>' +
							'<td>7.00</td><td>7.50</td>' +
							'<td>8.00</td><td>8.50</td>' +
							'<td>9.00</td><td>9.50</td><td>10.00</td>' +
							'<td>Tổng</td></tr>';
						header = header + header_rp;

						var rp_body = '';
						var biomedics55 = msg.data.biomedics55;
						var html_biomedics55 = '<tr class="root"><td>1</td><td style="text-align: left">Biomedic 55UV</td>';
						for (i = 1; i < 34; i++) {
							html_biomedics55 = html_biomedics55 + '<td>' + biomedics55[i] + '</td>';
						}
						html_biomedics55 = html_biomedics55 + '<td>' + biomedics55["sum"] + '</td></tr>';
						var biomedic1day = msg.data.biomedic1day


						var html_biomedic1day = '<tr class="root"><td>2</td><td style="text-align: left">Biomedic 1 Day</td>';
						for (i = 1; i < 34; i++) {
							html_biomedic1day = html_biomedic1day + '<td>' + biomedic1day[i] + '</td>';
						}
						html_biomedic1day = html_biomedic1day + '<td>' + biomedic1day["sum"] + '</td></tr>';

						var maxim1day = msg.data.maxim1day;
						html_maxim1day = '<tr class="root"><td>3</td><td style="text-align: left">Maxim 1 Day</td>';
						for (i = 1; i < 34; i++) {
							html_maxim1day = html_maxim1day + '<td></td>';
						}
						html_maxim1day = html_maxim1day + '<td></td></tr>';


						for (var key in maxim1day) {
							html_maxim1day = html_maxim1day + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_maxim1day = html_maxim1day + '<td>' + maxim1day[key][i] + '</td>';
							}
							html_maxim1day = html_maxim1day + '<td>' + maxim1day[key]["sum"] + '</td></tr>';
						}

						rp_body = html_biomedics55 + html_biomedic1day + html_maxim1day;

						var rp_footer = '';
						html = '<table id="rp_inventory" class="table2excel">' + header + rp_body + rp_footer + '</table>';
					}
					else if(msg.data.category == 'Seed')
					{
						//alert('SEED');
						var header = '<tr><td colspan="36" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="6" class="rp_company"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.ordered_date + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6" style="text-align: left">Số ....</td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_des"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.customer + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_brand"></td>' +
							'<td colspan="14" style="text-align: left">Địa chỉ: ' + msg.data.header.address + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';


						var header_rp = '<tr><td>STT</td><td>Tên hàng</td>' +
							'<td>0.00</td><td>0.25</td><td>0.50</td><td>0.75</td>' +
							'<td>1.00</td><td>1.25</td><td>1.50</td><td>1.75</td>' +
							'<td>2.00</td><td>2.25</td><td>2.50</td><td>2.75</td>' +
							'<td>3.00</td><td>3.25</td><td>3.50</td><td>3.75</td>' +
							'<td>4.00</td><td>4.25</td><td>4.50</td><td>4.75</td>' +
							'<td>5.00</td><td>5.25</td><td>5.50</td><td>5.75</td>' +
							'<td>6.00</td><td>6.50</td>' +
							'<td>7.00</td><td>7.50</td>' +
							'<td>8.00</td><td>8.50</td>' +
							'<td>9.00</td><td>9.50</td><td>10.00</td>' +
							'<td>Tổng</td></tr>';
						header = header + header_rp;

						var rp_body = '';
						/*var soflens59monthly = msg.data.soflens59monthly;
						 var html_soflens59monthly = '<tr class="root"><td>1</td><td style="text-align: left">Soflens 59 Monthly</td>';
						 for (i = 1; i < 34; i++) {
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly[i] + '</td>';
						 }
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly["sum"] + '</td></tr>';*/

						/*var optimafw = msg.data.optimafw


						 var html_optimafw = '<tr class="root"><td>2</td><td style="text-align: left">Optima FW</td>';
						 for (i = 1; i < 34; i++) {
						 html_optimafw = html_optimafw + '<td>' + optimafw[i] + '</td>';
						 }
						 html_optimafw = html_optimafw + '<td>' + optimafw["sum"] + '</td></tr>';*/

						 	
						var fr1day = msg.data.fr1day;
						html_fr1day = '<tr class="root"><td>1</td><td style="text-align: left">SEED 1 DAY</td>';
						for (i = 1; i < 34; i++) {
							html_fr1day = html_fr1day + '<td></td>';
						}
						html_fr1day = html_fr1day + '<td></td></tr>';


						for (var key in fr1day) {
							html_fr1day = html_fr1day + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1day = html_fr1day + '<td>' + fr1day[key][i] + '</td>';
							}
							html_fr1day = html_fr1day + '<td>' + fr1day[key]["sum"] + '</td></tr>';
						}

						var fr1month = msg.data.fr1month;
						 html_fr1month = '<tr class="root"><td>2</td><td style="text-align: left">SEED 1 Month</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr1month = html_fr1month + '<td></td>';
						 }
						 html_fr1month = html_fr1month + '<td></td></tr>';


						 for (var key in fr1month) {
						 html_fr1month = html_fr1month + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr1month = html_fr1month + '<td>' + fr1month[key][i] + '</td>';
						 }
						 html_fr1month = html_fr1month + '<td>' + fr1month[key]["sum"] + '</td></tr>';
						 }



						//rp_body = html_soflens59monthly + html_optimafw + html_fr1day + html_fr1month;
						rp_body = html_fr1day + html_fr1month;

						var rp_footer = '';
						html = '<table id="rp_inventory" class="table2excel">' + header + rp_body + rp_footer + '</table>';

						var mywater = msg.data.mywater;
						var table2 = '<table><tr>' + '<td>STT</td><td>Tên sản phẩm</td><td>Số lượng</td>'+
							'</tr>';
						var i=1;
						for (var key in mywater) {
							table2 = table2 + '<tr><td>'+i+'</td><td>'+ key +'</td><td>'+ mywater[key] +'</td></tr>';
							i++;
						}
						table2 = table2 + '</table>';

						html = html + '<br/>' + table2;

					}
					else if(msg.data.category == 'Ann 365 len')
					{
						var header = '<tr><td colspan="36" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="6" class="rp_company"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.ordered_date + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6" style="text-align: left">Số ....</td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_des"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.customer + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_brand"></td>' +
							'<td colspan="14" style="text-align: left">Địa chỉ: ' + msg.data.header.address + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';


						var header_rp = '<tr><td>STT</td><td>Tên hàng</td>' +
							'<td>0.00</td><td>0.25</td><td>0.50</td><td>0.75</td>' +
							'<td>1.00</td><td>1.25</td><td>1.50</td><td>1.75</td>' +
							'<td>2.00</td><td>2.25</td><td>2.50</td><td>2.75</td>' +
							'<td>3.00</td><td>3.25</td><td>3.50</td><td>3.75</td>' +
							'<td>4.00</td><td>4.25</td><td>4.50</td><td>4.75</td>' +
							'<td>5.00</td><td>5.25</td><td>5.50</td><td>5.75</td>' +
							'<td>6.00</td><td>6.50</td>' +
							'<td>7.00</td><td>7.50</td>' +
							'<td>8.00</td><td>8.50</td>' +
							'<td>9.00</td><td>9.50</td><td>10.00</td>' +
							'<td>Tổng</td></tr>';
						header = header + header_rp;

						var rp_body = '';
						/*var soflens59monthly = msg.data.soflens59monthly;
						 var html_soflens59monthly = '<tr class="root"><td>1</td><td style="text-align: left">Soflens 59 Monthly</td>';
						 for (i = 1; i < 34; i++) {
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly[i] + '</td>';
						 }
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly["sum"] + '</td></tr>';*/

						/*var optimafw = msg.data.optimafw


						 var html_optimafw = '<tr class="root"><td>2</td><td style="text-align: left">Optima FW</td>';
						 for (i = 1; i < 34; i++) {
						 html_optimafw = html_optimafw + '<td>' + optimafw[i] + '</td>';
						 }
						 html_optimafw = html_optimafw + '<td>' + optimafw["sum"] + '</td></tr>';*/


						var fr1day = msg.data.fr1day;
						html_fr1day = '<tr class="root"><td>1</td><td style="text-align: left">ANN 365 1 DAY</td>';
						for (i = 1; i < 34; i++) {
							html_fr1day = html_fr1day + '<td></td>';
						}
						html_fr1day = html_fr1day + '<td></td></tr>';


						for (var key in fr1day) {
							html_fr1day = html_fr1day + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1day = html_fr1day + '<td>' + fr1day[key][i] + '</td>';
							}
							html_fr1day = html_fr1day + '<td>' + fr1day[key]["sum"] + '</td></tr>';
						}

						/*var fr1month = msg.data.fr1month;
						html_fr1month = '<tr class="root"><td>2</td><td style="text-align: left">SEED 1 Month</td>';
						for (i = 1; i < 34; i++) {
							html_fr1month = html_fr1month + '<td></td>';
						}
						html_fr1month = html_fr1month + '<td></td></tr>';*/


						/*for (var key in fr1month) {
							html_fr1month = html_fr1month + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1month = html_fr1month + '<td>' + fr1month[key][i] + '</td>';
							}
							html_fr1month = html_fr1month + '<td>' + fr1month[key]["sum"] + '</td></tr>';
						}
*/


						//rp_body = html_soflens59monthly + html_optimafw + html_fr1day + html_fr1month;
						//rp_body = html_fr1day + html_fr1month;
						rp_body = html_fr1day;

						var rp_footer = '';
						html = '<table id="rp_inventory" class="table2excel">' + header + rp_body + rp_footer + '</table>';

						var mywater = msg.data.mywater;
						var table2 = '<table><tr>' + '<td>STT</td><td>Tên sản phẩm</td><td>Số lượng</td>'+
							'</tr>';
						var i=1;
						for (var key in mywater) {
							table2 = table2 + '<tr><td>'+i+'</td><td>'+ key +'</td><td>'+ mywater[key] +'</td></tr>';
							i++;
						}
						table2 = table2 + '</table>';

						html = html + '<br/>' + table2;
					}
					else if(msg.data.category == 'CLEARLAB-USA')
					{
						//alert('SEED');
						var header = '<tr><td colspan="36" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="6" class="rp_company"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.ordered_date + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6" style="text-align: left">Số ....</td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_des"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.customer + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_brand"></td>' +
							'<td colspan="14" style="text-align: left">Địa chỉ: ' + msg.data.header.address + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';


						var header_rp = '<tr><td>STT</td><td>Tên hàng</td>' +
							'<td>0.00</td><td>0.25</td><td>0.50</td><td>0.75</td>' +
							'<td>1.00</td><td>1.25</td><td>1.50</td><td>1.75</td>' +
							'<td>2.00</td><td>2.25</td><td>2.50</td><td>2.75</td>' +
							'<td>3.00</td><td>3.25</td><td>3.50</td><td>3.75</td>' +
							'<td>4.00</td><td>4.25</td><td>4.50</td><td>4.75</td>' +
							'<td>5.00</td><td>5.25</td><td>5.50</td><td>5.75</td>' +
							'<td>6.00</td><td>6.50</td>' +
							'<td>7.00</td><td>7.50</td>' +
							'<td>8.00</td><td>8.50</td>' +
							'<td>9.00</td><td>9.50</td><td>10.00</td>' +
							'<td>Tổng</td></tr>';
						header = header + header_rp;

						var rp_body = '';
						/*var soflens59monthly = msg.data.soflens59monthly;
						 var html_soflens59monthly = '<tr class="root"><td>1</td><td style="text-align: left">Soflens 59 Monthly</td>';
						 for (i = 1; i < 34; i++) {
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly[i] + '</td>';
						 }
						 html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly["sum"] + '</td></tr>';*/

						/*var optimafw = msg.data.optimafw


						 var html_optimafw = '<tr class="root"><td>2</td><td style="text-align: left">Optima FW</td>';
						 for (i = 1; i < 34; i++) {
						 html_optimafw = html_optimafw + '<td>' + optimafw[i] + '</td>';
						 }
						 html_optimafw = html_optimafw + '<td>' + optimafw["sum"] + '</td></tr>';*/


						var fr1day = msg.data.fr1day;
						html_fr1day = '<tr class="root"><td>1</td><td style="text-align: left">DAILY</td>';
						for (i = 1; i < 34; i++) {
							html_fr1day = html_fr1day + '<td></td>';
						}
						html_fr1day = html_fr1day + '<td></td></tr>';


						for (var key in fr1day) {
							html_fr1day = html_fr1day + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1day = html_fr1day + '<td>' + fr1day[key][i] + '</td>';
							}
							html_fr1day = html_fr1day + '<td>' + fr1day[key]["sum"] + '</td></tr>';
						}

						var fr1month = msg.data.fr1month;
						 html_fr1month = '<tr class="root"><td>2</td><td style="text-align: left">MONTHLY</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr1month = html_fr1month + '<td></td>';
						 }
						 html_fr1month = html_fr1month + '<td></td></tr>';


						 for (var key in fr1month) {
						 html_fr1month = html_fr1month + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr1month = html_fr1month + '<td>' + fr1month[key][i] + '</td>';
						 }
						 html_fr1month = html_fr1month + '<td>' + fr1month[key]["sum"] + '</td></tr>';
						 }
						 // Three Month
						 var fr3month = msg.data.fr3month;
						 html_fr3month = '<tr class="root"><td>3</td><td style="text-align: left">3 MONTHS</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr3month = html_fr3month + '<td></td>';
						 }
						 html_fr3month = html_fr3month + '<td></td></tr>';


						 for (var key in fr3month) {
						 html_fr3month = html_fr3month + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
						 for (i = 1; i < 34; i++) {
						 html_fr3month = html_fr3month + '<td>' + fr3month[key][i] + '</td>';
						 }
						 html_fr3month = html_fr3month + '<td>' + fr3month[key]["sum"] + '</td></tr>';
						 }



						//rp_body = html_soflens59monthly + html_optimafw + html_fr1day + html_fr1month;
						rp_body = html_fr1day + html_fr1month + html_fr3month;

						var rp_footer = '';
						html = '<table id="rp_inventory" class="table2excel">' + header + rp_body + rp_footer + '</table>';

						var mywater = msg.data.mywater;
						var table2 = '<table><tr>' + '<td>STT</td><td>Tên sản phẩm</td><td>Số lượng</td>'+
							'</tr>';
						var i=1;
						for (var key in mywater) {
							table2 = table2 + '<tr><td>'+i+'</td><td>'+ key +'</td><td>'+ mywater[key] +'</td></tr>';
							i++;
						}
						table2 = table2 + '</table>';

						html = html + '<br/>' + table2;

					}
					else{
						var header = '<tr><td colspan="36" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="6" class="rp_company"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.ordered_date + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6" style="text-align: left">Số ....</td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_des"></td>' +
							'<td colspan="14" style="text-align: left">' + msg.data.header.customer + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';
						header = header + '<tr><td colspan="6" class="rp_brand"></td>' +
							'<td colspan="14" style="text-align: left">Địa chỉ: ' + msg.data.header.address + '</td>' +
							'<td colspan="10"></td>' +
							'<td colspan="6"></td>' +
							'</tr>';


						var header_rp = '<tr><td>STT</td><td>Tên hàng</td>' +
							'<td>0.00</td><td>0.25</td><td>0.50</td><td>0.75</td>' +
							'<td>1.00</td><td>1.25</td><td>1.50</td><td>1.75</td>' +
							'<td>2.00</td><td>2.25</td><td>2.50</td><td>2.75</td>' +
							'<td>3.00</td><td>3.25</td><td>3.50</td><td>3.75</td>' +
							'<td>4.00</td><td>4.25</td><td>4.50</td><td>4.75</td>' +
							'<td>5.00</td><td>5.25</td><td>5.50</td><td>5.75</td>' +
							'<td>6.00</td><td>6.50</td>' +
							'<td>7.00</td><td>7.50</td>' +
							'<td>8.00</td><td>8.50</td>' +
							'<td>9.00</td><td>9.50</td><td>10.00</td>' +
							'<td>Tổng</td></tr>';
						header = header + header_rp;

						var rp_body = '';
						var soflens59monthly = msg.data.soflens59monthly;
						var html_soflens59monthly = '<tr class="root"><td>1</td><td style="text-align: left">Soflens 59 Monthly</td>';
						for (i = 1; i < 34; i++) {
							html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly[i] + '</td>';
						}
						html_soflens59monthly = html_soflens59monthly + '<td>' + soflens59monthly["sum"] + '</td></tr>';

						var optimafw = msg.data.optimafw


						var html_optimafw = '<tr class="root"><td>2</td><td style="text-align: left">Optima FW</td>';
						for (i = 1; i < 34; i++) {
							html_optimafw = html_optimafw + '<td>' + optimafw[i] + '</td>';
						}
						html_optimafw = html_optimafw + '<td>' + optimafw["sum"] + '</td></tr>';


						var fr1day = msg.data.fr1day;
						html_fr1day = '<tr class="root"><td>3</td><td style="text-align: left">FreshKon 1 Day</td>';
						for (i = 1; i < 34; i++) {
							html_fr1day = html_fr1day + '<td></td>';
						}
						html_fr1day = html_fr1day + '<td></td></tr>';


						for (var key in fr1day) {
							html_fr1day = html_fr1day + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1day = html_fr1day + '<td>' + fr1day[key][i] + '</td>';
							}
							html_fr1day = html_fr1day + '<td>' + fr1day[key]["sum"] + '</td></tr>';
						}

						var fr1month = msg.data.fr1month;
						html_fr1month = '<tr class="root"><td>4</td><td style="text-align: left">FreshKon 3 Month</td>';
						for (i = 1; i < 34; i++) {
							html_fr1month = html_fr1month + '<td></td>';
						}
						html_fr1month = html_fr1month + '<td></td></tr>';


						for (var key in fr1month) {
							html_fr1month = html_fr1month + '<tr class="chilrent"><td></td><td style="text-align: left;padding-left: 10px;">' + key + '</td>';
							for (i = 1; i < 34; i++) {
								html_fr1month = html_fr1month + '<td>' + fr1month[key][i] + '</td>';
							}
							html_fr1month = html_fr1month + '<td>' + fr1month[key]["sum"] + '</td></tr>';
						}



						rp_body = html_soflens59monthly + html_optimafw + html_fr1day + html_fr1month;

						var rp_footer = '';
						html = '<table id="rp_inventory" class="table2excel">' + header + rp_body + rp_footer + '</table>';

						var mywater = msg.data.mywater;
						var table2 = '<table><tr>' + '<td>STT</td><td>Tên sản phẩm</td><td>Số lượng</td>'+
							'</tr>';
						var i=1;
						for (var key in mywater) {
							table2 = table2 + '<tr><td>'+i+'</td><td>'+ key +'</td><td>'+ mywater[key] +'</td></tr>';
							i++;
						}
						table2 = table2 + '</table>';

						html = html + '<br/>' + table2;

					}
					$('#view_report_lens_category').html(html);
					$('#expExcel').show();
				}else{
					$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
				}

			});
	});

});
</script>