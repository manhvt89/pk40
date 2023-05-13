<?php $this->load->view("partial/header"); ?>

<div id="page_title" class="rp_page_title"><?php echo $this->lang->line('reports_import_input'); ?></div>

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
		<?php echo form_label($this->lang->line('reports_lens_category'), 'reports_lens_category_label', array('class'=>'required control-label col-xs-2')); ?>
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
<div id="view_report_lens_category">

</div>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	$("#generate_report").click(function()
	{
		var csrf_ospos_v3 = csrf_token();
		var location_id = $('#location_id').val();
		var category = $('#category').val();
		$.ajax({
			method: "POST",
			url: "<?php echo site_url('reports/ajax_import_inventory_summary')?>",
			data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
			dataType: 'json'
		})
			.done(function( msg ) {
				if(msg.result == 1)
				{
					var html = '';
					var header = '<tr><td colspan="26" class="rp_title">'+ msg.data.header.title +'</td></tr>';
					header = header + '<tr><td colspan="5" class="rp_company">Công ty</td>' +
						'<td colspan="4">'+ msg.data.header.company_name +'</td>' +
						'<td colspan="7">Group</td><td colspan="4">Số lượng (miếng)</td><td colspan="6">Thành tiền</td>'+
						'</tr>';

					header = header + '<tr><td colspan="5" class="rp_des">Mô tả</td>' +
						'<td colspan="4">'+ msg.data.header.description +'</td>' +
						'<td colspan="7">-0.00/-0.00 ~ -6.00/-2.00</td><td colspan="4">'+ msg.data.sub_group[0]+'</td><td colspan="6"></td>'+
						'</tr>';

					header = header + '<tr><td colspan="5" class="rp_brand">Thương hiệu</td>' +
						'<td colspan="4">'+ msg.data.header.brand +'</td>' +
						'<td colspan="7">-6.25/-0.00 ~ -8.00/-2.00</td><td colspan="4">'+ msg.data.sub_group[1]+'</td><td colspan="6"></td>'+
						'</tr>';

					header = header + '<tr><td colspan="5" class="rp_rmk">RMK</td>' +
						'<td colspan="4">'+'</td>' +
						'<td colspan="7">-8.25/-0.00 ~ -15.00/-2.00</td><td colspan="4">'+ msg.data.sub_group[2]+'</td><td colspan="6"></td>'+
						'</tr>';

					header = header + '<tr><td colspan="5">Khách hàng</td>' +
						'<td colspan="4">'+ msg.data.header.customer +'</td>' +
						'<td colspan="7">+0.00/-0.00 ~ +6.00/-2.00</td><td colspan="4">'+ msg.data.sub_group[3]+'</td><td colspan="6"></td>'+
						'</tr>';
					header = header + '<tr><td colspan="5">Ngày đặt hàng</td>' +
						'<td colspan="4">'+ msg.data.header.ordered_date +'</td>' +
						'<td colspan="7">-0.00/-2.25 ~ -10.00/-3.00</td><td colspan="4">'+ msg.data.sub_group[4]+'</td><td colspan="6"></td></tr>';
					header = header +'<tr><td colspan="5">MS đơn hàng</td>' +
						'<td colspan="4"></td>' +
						'<td colspan="7">-0.00/-3.25 ~ -10.00/-4.00</td><td colspan="4">'+ msg.data.sub_group[5]+'</td><td colspan="6"></td></tr>';
					header = header +'<tr><td colspan="5">Tổng số lượng (miếng)</td>' +
						'<td colspan="4">'+msg.data.total+'</td>' +
						'<td colspan="7">+0.25/-0.50 ~ +1.75/-2.00</td><td colspan="4">'+ msg.data.sub_group[6]+'</td><td colspan="6"></td></tr>';

					header = header +'<tr><td colspan="5"></td>' +
						'<td colspan="4"></td>' +
						'<td colspan="7">+0.25/-2.25 ~ +1.75/-4.00</td><td colspan="4">'+ msg.data.sub_group[7]+'</td><td colspan="6"></td></tr>';

					header = header +'<tr><td colspan="26" style="height: 15px"></td></tr>';


					header = header;

					var table_header = '<table>'+ header + '</table>';

					var header_rp = '<tr><td class="rp_header">SPH</td>' +
						'<td colspan="25">CYL (-)</td>' +
						'</tr>';
					header_rp = header_rp + '<tr class="rp_header_table_len"><td>(-)</td>' +
						'<td>-0.00</td><td>-0.25</td><td>-0.50</td><td>-0.75</td>' +
						'<td>-1.00</td><td>-1.25</td><td>-1.50</td><td>-1.75</td>' +
						'<td>-2.00</td><td>-2.25</td><td>-2.50</td><td>-2.75</td>' +
						'<td>-3.00</td><td>-3.25</td><td>-3.50</td><td>-3.75</td>' +
						'<td>-4.00</td><td>-4.25</td><td>-4.50</td><td>-4.75</td>' +
						'<td>-5.00</td><td>-5.25</td><td>-5.50</td><td>-5.75</td><td>-6.00</td>' +
						'</tr>';

					

					var rp_body = '';
					var myopia = msg.data.myopia;
					var hyperopia = msg.data.hyperopia;
					var re_map = msg.data.re_map;
					var sub_myopia = msg.data.sub_myopia;
					var sub_hyperopia = msg.data.sub_hyperopia;

					for(i=1;i<56;i++)
					{
						var row = '<tr class="rp_number"><td> -'+ re_map[i] +'</td>';
						for(j=1;j<26;j++)
						{
							row = row + '<td>' + myopia[i][j] + '</td>';
						}
						row = row + '</tr>';
						rp_body = rp_body + row;
					}

					var row57 = '<tr class="rp_header_table_len"><td>Sub</td>'
					for(i=1;i<26;i++)
					{
						if(sub_myopia[i] != null) {
							row57 = row57 + '<td>' + sub_myopia[i] + '</td>';
						}else{
							row57 = row57 + '<td></td>';
						}
					}
					rp_body = rp_body + row57 + '</tr>';



					var row57 = '<tr><td colspan="26" height="15px"></td>';


					rp_body = rp_body + row57 + '</tr>';

					var rp_footer = '<tr><td>Sub</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>' +
						'<td>'+ sub_hyperopia[10]+ '</td>' +
						'<td>'+ sub_hyperopia[11]+ '</td>' +
						'<td>'+ sub_hyperopia[12]+ '</td>' +
						'<td>'+ sub_hyperopia[13]+ '</td>' +
						'<td>'+ sub_hyperopia[14]+ '</td>' +
						'<td>'+ sub_hyperopia[15]+ '</td>' +
						'<td>'+ sub_hyperopia[16]+ '</td>' +
						'<td>'+ sub_hyperopia[17]+ '</td>' +
						'<td colspan="2"></td></tr>';
					rp_footer = '<tr><td colspan="2">Minus Low</td><td colspan="2"></td>' +
						'<td colspan="2">Minus High</td><td colspan="2"></td>' +
						'<td colspan="2">Plus</td><td colspan="2"></td>' +
						'<td colspan="2">Mix</td><td colspan="2"></td>' +
						'<td colspan="2">High Cyl</td><td colspan="8"></td></tr>';
					/*
					rp_footer = rp_footer + '<tr><td width="4%"></td><td width="4%"></td><td width="4%"></td><td width="4%"></td><td width="4%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td>' +
						'<td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%"></td><td width="5%">' +
						'</td><td width="5%"></td><td width="5%"></td><td width="5%"></td></tr>';
					*/
					var table1 = '<table id="rp_inventory" style="margin-bottom: 130px;">'+ header_rp + rp_body + rp_footer+'</table>';

					rp_footer = '';
					for(i=1;i<26;i++)
					{
						if(sub_hyperopia[i] != null) {
							rp_footer = rp_footer + '<td>' + sub_hyperopia[i] + '</td>';
						}else{
							rp_footer = rp_footer + '<td></td>';
						}
					}
					rp_footer = '<tr><td>Sub</td>'+rp_footer+'</tr>';
					//next-----------
					rp_body = '';
					for(i=1;i<26;i++)
					{
						var row = '<tr class="pr_number"><td> +'+re_map[i]+'</td>';
						for(j=1;j<26;j++)
						{
							row = row + '<td>'+hyperopia[i][j]+'</td>';
						}
						row = row + '</tr>';
						rp_body = rp_body + row;
					}

					var table2 = '<table id="rp_inventory_2" style="margin-bottom: 900px; margin-top: 20px;">' + header_rp + rp_body + rp_footer+'</table>';

					html = table_header + table1 + table2;
					$('#view_report_lens_category').html(html);
				}else{
					$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
				}

			});
	});

});
</script>