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
			filename: "Ton_kho_mat",
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
			url: "<?php echo site_url('reports/ajax_inventory_summary')?>",
			data: { location_id: location_id, category: category, csrf_ospos_v3: csrf_ospos_v3 },
			dataType: 'json'
		})
			.done(function( msg ) {
				if(category != '1.56 TC') {
					if (msg.result == 1) {
						var html = '';
						var header = '<tr><td colspan="18" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="3" class="rp_company">Công ty</td>' +
							'<td colspan="4">' + msg.data.header.company_name + '</td>' +
							'<td colspan="2"></td>' +
							'<td colspan="4"></td>' +
							'<td colspan="3">Khách hàng</td>' +
							'<td colspan="2">' + msg.data.header.customer + '</td></tr>';
						header = header + '<tr><td colspan="3" class="rp_des">Mô tả</td>' +
							'<td colspan="4">' + msg.data.header.description + '</td>' +
							'<td colspan="2"></td>' +
							'<td colspan="4"></td>' +
							'<td colspan="3">Ngày đặt hàng</td>' +
							'<td colspan="2">' + msg.data.header.ordered_date + '</td></tr>';
						header = header + '<tr><td colspan="3" class="rp_brand">Thương hiệu</td>' +
							'<td colspan="4">' + msg.data.header.brand + '</td>' +
							'<td colspan="2"></td>' +
							'<td colspan="4"></td>' +
							'<td colspan="3">MS đơn hàng</td>' +
							'<td colspan="2"></td></tr>';

						header = header + '<tr><td colspan="3" class="rp_rmk">RMK</td>' +
							'<td colspan="4">' + '</td>' +
							'<td colspan="2"></td>' +
							'<td colspan="4"></td>' +
							'<td colspan="3">Tổng số lượng (miếng)</td>' +
							'<td colspan="2"><b>' + msg.data.total + '</b></td></tr>';

						var header_rp1 = '<tr><td class="rp_header">SPH</td>' +
							'<td colspan="17">CYL (-)</td>' +
							'</tr>';

						var header_rp2 = '<tr><td>(-)</td>' +
							'<td>-0.00</td><td>-0.25</td><td>-0.50</td><td>-0.75</td>' +
							'<td>-1.00</td><td>-1.25</td><td>-1.50</td><td>-1.75</td>' +
							'<td>-2.00</td><td>-2.25</td><td>-2.50</td><td>-2.75</td>' +
							'<td>-3.00</td><td>-3.25</td><td>-3.50</td><td>-3.75</td>' +
							'<td>-4.00</td></tr>';
						//header = header + header_rp1 + header_rp2; // Header of table;

						var rp_body = '';
						var myopia = msg.data.myopia;
						var hyperopia = msg.data.hyperopia;
						var re_map = msg.data.re_map;
						var sub_myopia = msg.data.sub_myopia;
						var sub_hyperopia = msg.data.sub_hyperopia;
						var _row_index = 0;

						for (i = 1; i < 62; i++) {
							var row = '<tr class="rp_number" data-row="'+i+'"><td> -' + re_map[i] + '</td>';
							for (j = 1; j < 18; j++) {
								var _mypD = myopia[i][j];
								console.log(myopia[i][j]);
								if( _mypD != '')
								{
									if(_mypD.substring(0,1) == '-' && _mypD != '-')
									{
										row = row + '<td style="background-color:orange"><p style="background-color:orange">' + myopia[i][j] + '</p></td>';
									} else {
										row = row + '<td><p>' + myopia[i][j] + '</p></td>';
									}
								} else {
									row = row + '<td><p>' + myopia[i][j] + '</p></td>';
								}
								//row = row + '<td>' + myopia[i][j] + '</td>';
							}
							row = row + '</tr>';
							rp_body = rp_body + row;
							_row_index = i; 
						}
						console.log(_row_index);
						_row_index++; //Next row
						
						var row_sub = '<tr row="'+_row_index+'"><td>Sub</td>';
						for (i = 1; i < 18; i++) {
							row_sub = row_sub + '<td>' + sub_myopia[i] + '</td>';
						}
						
						rp_body = rp_body + row_sub + '</tr>';

						html = '<table id="rp_inventory" class="table2excel">' + header + header_rp1 + header_rp2 + rp_body;
						
						/********************************* END  */ 

						rp_body = '';
						header_rp1 = '<tr class=""><td class="rp_header">SPH</td>' +
							'<td colspan="17">CYL (+)</td>' +
							'</tr>';
							var header_rp2 = '<tr><td>(+)</td>' +
							'<td>-0.00</td><td>-0.25</td><td>-0.50</td><td>-0.75</td>' +
							'<td>-1.00</td><td>-1.25</td><td>-1.50</td><td>-1.75</td>' +
							'<td>-2.00</td><td>-2.25</td><td>-2.50</td><td>-2.75</td>' +
							'<td>-3.00</td><td>-3.25</td><td>-3.50</td><td>-3.75</td>' +
							'<td>-4.00</td></tr>';

						_row_index = 1; //The first row
						for (i = 2; i < 26; i++) {
							var row = '<tr class="rp_number" data-row="'+i+'"><td> +' + re_map[i] + '</td>';
							for (j = 1; j < 18; j++) {
								var _hypD = hyperopia[i][j];
								console.log(hyperopia[i][j]);
								if( _hypD != '')
								{
									if(_hypD.substring(0,1) == '-' && _hypD != '-')
									{
										row = row + '<td style="background-color:orange"><p style="background-color:orange">' + hyperopia[i][j] + '</p></td>';
									} else {
										row = row + '<td><p>' + hyperopia[i][j] + '</p></td>';
									}
								} else {
									row = row + '<td><p>' + hyperopia[i][j] + '</p></td>';
								}
							}
							row = row + '</tr>';
							rp_body = rp_body + row;
							_row_index = i; 
						}

						console.log(_row_index);

						var row_sub = '<tr row="'+_row_index+'"><td>Sub</td>';
						for (i = 1; i < 18; i++) {
							row_sub = row_sub + '<td>' + sub_hyperopia[i] + '</td>';
						}
						
						rp_body = rp_body + row_sub + '</tr>';

						html = html + '<tr class="break-page" style="page-break-after: always;"><td colspan="18"></td></tr>'+header + header_rp1 + header_rp2 + rp_body + '</table>';
						
						
						html = html + '<div style="height: 50px;"></div>'
						$('#view_report_lens_category').html(html);
						$('#expExcel').show();
					} else {
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}
				}else{ //display for 1.56 TC
					if (msg.result == 1) {
						var html = '';
						var header = '<tr><td colspan="28" class="rp_title">' + msg.data.header.title + '</td></tr>';
						header = header + '<tr><td colspan="4" class="rp_company">Công ty</td>' +
							'<td colspan="5">' + msg.data.header.company_name + '</td>' +
							'<td colspan="3"></td>' +
							'<td colspan="5"></td>' +
							'<td colspan="4">Khách hàng</td>' +
							'<td colspan="7">' + msg.data.header.customer + '</td></tr>';
						header = header + '<tr><td colspan="4" class="rp_des">Mô tả</td>' +
							'<td colspan="5">' + msg.data.header.description + '</td>' +
							'<td colspan="3"></td>' +
							'<td colspan="5"></td>' +
							'<td colspan="4">Ngày đặt hàng</td>' +
							'<td colspan="7">' + msg.data.header.ordered_date + '</td></tr>';
						header = header + '<tr><td colspan="4" class="rp_brand">Thương hiệu</td>' +
							'<td colspan="5">' + msg.data.header.brand + '</td>' +
							'<td colspan="3"></td>' +
							'<td colspan="5"></td>' +
							'<td colspan="4">MS đơn hàng</td>' +
							'<td colspan="7"></td></tr>';

						header = header + '<tr><td colspan="4" class="rp_rmk">RMK</td>' +
							'<td colspan="5">' + '</td>' +
							'<td colspan="3"></td>' +
							'<td colspan="5"></td>' +
							'<td colspan="4">Tổng số lượng (miếng)</td>' +
							'<td colspan="7">' + msg.data.total + '</td></tr>';

						header = header + '<tr><td class="rp_header">SPH</td>' +
							'<td colspan="25">CYL (-)</td>' +
							'</tr>';

						var header_rp = '<tr><td>(-)</td>' +
							'<td>-0.00</td><td>-0.25</td><td>-0.50</td><td>-0.75</td>' +
							'<td>-1.00</td><td>-1.25</td><td>-1.50</td><td>-1.75</td>' +
							'<td>-2.00</td><td>-2.25</td><td>-2.50</td><td>-2.75</td>' +
							'<td>-3.00</td><td>-3.25</td><td>-3.50</td><td>-3.75</td>' +
							'<td>-4.00</td><td>-4.25</td><td>-4.50</td><td>-4.75</td>' +
							'<td>-5.00</td><td>-5.25</td><td>-5.50</td><td>-5.75</td>' +
							'<td>-6.00</td>'+
							'</tr>';
						header = header + header_rp;
						//header = '' + header_rp;

						var rp_body = '';
						var myopia = msg.data.myopia;
						var hyperopia = msg.data.hyperopia;
						var re_map = msg.data.re_map;
						var sub_myopia = msg.data.sub_myopia;
						var sub_hyperopia = msg.data.sub_hyperopia;

						for (i = 1; i < 56; i++) {
							var row = '<tr class="rp_number"><td> -' + re_map[i] + '</td>';
							for (j = 1; j < 26; j++) {
								row = row + '<td>' + myopia[i][j] + '</td>';
							}
							row = row + '</tr>';
							rp_body = rp_body + row;
						}


						var row57 = '<tr row="57"><td>Sub</td>';
						for (i = 1; i < 26; i++) {
							row57 = row57 + '<td>' + sub_myopia[i] + '</td>';
						}

						rp_body = rp_body + row57 + '</tr>';

                        /*
						var row58 = '<tr row="58"><td colspan="8">Group</td><td colspan="4">Số lượng (miếng)</td><td colspan="6">Thành tiền</td><td rowspan="9" colspan="2"></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';


						row58 = '<tr><td colspan="8">-0.00/-0.00 ~ -6.00/-2.00</td><td colspan="4">' + msg.data.sub_group[0] + '</td><td colspan="6"></td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						//--------------------
						row58 = '<tr><td colspan="8">-6.25/-0.00 ~ -8.00/-2.00</td><td colspan="4">' + msg.data.sub_group[1] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">-8.25/-0.00 ~ -15.00/-2.00</td><td colspan="4">' + msg.data.sub_group[2] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">+0.00/-0.00 ~ +6.00/-2.00</td><td colspan="4">' + msg.data.sub_group[3] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td> ' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">-0.00/-2.25 ~ -10.00/-3.00</td><td colspan="4">' + msg.data.sub_group[4] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">-0.00/-3.25 ~ -10.00/-4.00</td><td colspan="4">' + msg.data.sub_group[5] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">+0.25/-0.50 ~ +1.75/-2.00</td><td colspan="4">' + msg.data.sub_group[6] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						row58 = '<tr><td colspan="8">+0.25/-2.25 ~ +1.75/-4.00</td><td colspan="4">' + msg.data.sub_group[7] + '</td><td colspan="6">Thành tiền</td></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 8; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';

						//---------------------
						row58 = '<tr><td colspan="8"></td><td colspan="4"></td><td colspan="4"></td><td colspan="6"></td>';
						row58 = row58 + '<td>' + '' + '</td>';
						for (j = 1; j < 10; j++) {
							row58 = row58 + '<td>' + '' + '</td>';
						}
						rp_body = rp_body + row58 + '</tr>';
                        */

						html = html + '<table id="rp_inventory" class="table2excel">' + header + rp_body + '</table>';
						html = html + '<div style="height: 50px;"></div>';


                        header = '';
                        header = header + '<tr><td class="rp_header">SPH</td>' +
                            '<td colspan="25">CYL (-)</td>' +
                            '</tr>';


                        header_rp = '<tr><td>(+)</td>' +
                            '<td>-0.00</td><td>-0.25</td><td>-0.50</td><td>-0.75</td>' +
                            '<td>-1.00</td><td>-1.25</td><td>-1.50</td><td>-1.75</td>' +
                            '<td>-2.00</td><td>-2.25</td><td>-2.50</td><td>-2.75</td>' +
                            '<td>-3.00</td><td>-3.25</td><td>-3.50</td><td>-3.75</td>' +
                            '<td>-4.00</td><td>-4.25</td><td>-4.50</td><td>-4.75</td>' +
                            '<td>-5.00</td><td>-5.25</td><td>-5.50</td><td>-5.75</td>' +
                            '<td>-6.00</td>'+
                            '</tr>';
                        header = header + header_rp;
                        rp_body = '';
                        for (i = 1; i < 34; i++) {
                            var row = '<tr class="rp_number"><td> +' + re_map[i] + '</td>';
                            for (j = 1; j < 26; j++) {
                                row = row + '<td>' + hyperopia[i][j] + '</td>';
                            }
                            row = row + '</tr>';
                            rp_body = rp_body + row;
                        }

                        row57 = '<tr row="57"><td>Sub</td>';
                        for (i = 1; i < 26; i++) {
                            row57 = row57 + '<td>' + sub_hyperopia[i] + '</td>';
                        }

                        rp_body = rp_body + row57 + '</tr>';

                        html = html + '<table id="rp_inventory_hyp" class="table2excel">' + header + rp_body + '</table>';
                        html = html + '<div style="height: 50px;"></div>';

						$('#view_report_lens_category').html(html);
						$('#expExcel').show();
					} else {
						$('#view_report_lens_category').html('<strong>Không tìm thấy báo cáo phù hợp, hãy thử lại</strong>');
					}
				}

			});
	});

});
</script>