<?php $this->load->view("partial/header"); ?>
<div id="page_title" class="rp_page_title" style="text-align: center;"><?php echo $page_title; ?></div>

<div style="color:red; align-content: center"><?php echo validation_errors(); ?></div>
<?php echo form_open('receivings/lens', array('id'=>'target')); ?>
<div class="form-group form-group-sm">
	<?php echo form_label($this->lang->line('reports_lens_category'), 'reports_lens_category_label', array('class'=>'required control-label col-xs-2')); ?>
	<div id='report_item_count' class="col-xs-3">
		<?php echo form_dropdown('category',$item_count,'all','id="category" class="form-control"'); ?>
	</div>
</div>

<div id="view_report_lens_category12">
	<input id="hhmyo" name="hhmyo" type="hidden" value="" />
	<table id="input_grid_data_myo" width="100%">
		<thead>
			<tr id="_row_m_1">
				<td style="padding: 0 9px 0 9px;">SPH</td>
				<td colspan="25">CYL(-)</td>
			</tr>
			<tr id="_row_m_2">
				<?php 
					$i = 0;
					foreach($cyls  as $cyl):
						?>
					<td id='col-<?=$i?>'><?php echo $cyl ?></td>
						<?php
						$i++;
					endforeach;
				?>
				
			</tr>
		</thead>
		<tbody>	
			<?php 
				foreach($mysphs as $key=>$sph):
					if($key > 0)
					{
						$tr = '<tr id="_row_myo_'.$key.'">';
						$tr = $tr . '<td>'.$sph.'</td>';
						foreach($cyls as $k=>$cyl):
							if($k > 0)
							{
								if($k < 10)
								{
									$k = '0'.$k;
								}
								//$tr = $tr . '<td>'.'<input type="text" name="myo'.$key.$k.'" value="'.set_value('myo'.$key.$k).'">'.'</td>';
								$tr = $tr . '<td></td>';
							}
						endforeach;
						$tr = $tr . '</tr>';
						echo $tr;
					}
				endforeach;
			?>
		<tbody>	
		</table>
		<input id="hhhyo" name="hhhyo" type="hidden" value="" />
	<table id="input_grid_data_hyo" class="" width="100%">
		<thead>
			<tr id="_row_m_1"><td style="padding: 0 9px 0 9px;">SPH</td><td colspan="25">CYL(-)</td></tr>
			<tr id="_row_h_2">
				<td>+</td>
				<?php 
					foreach($cyls  as $k=>$cyl):
						if($k > 0):
						?>
							<td><?php echo $cyl ?></td>
						<?php
						endif;
					endforeach;
				?>
				
			</tr>
		</thead>
	<tbody>
	<?php 
		foreach($hysphs as $key=>$sph):
			if($key > 0)
			{
				$tr = '<tr id="_row_hyo_'.$key.'">';
				$tr = $tr . '<td>'.$sph.'</td>';
				foreach($cyls as $k=>$cyl):
					if($k > 0)
					{
						if($k < 10)
						{
							$k = '0'.$k;
						}
						$tr = $tr . '<td></td>';
						//$tr = $tr . '<td>'.'<input type="text" name="hyo'.$key.$k.'"  value="'.set_value('hyo'.$key.$k).'">'.'</td>';
					}
				endforeach;
				$tr = $tr . '</tr>';
				echo $tr;
			}
		endforeach;
	?>
	</tbody>
</table>
</div>
<div id="view_report_lens_category">
	
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
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#generate_report').click(function()
		{
			$('#hhmyo').val(JSON.stringify(myo.getJson()));
			$('#hhhyo').val(JSON.stringify(hyo.getJson()));
			$( "#target" ).submit();
		});

	});

	myo = jspreadsheet(document.getElementById('input_grid_data_myo'),{
		onbeforeinsertrow: function(){			
			return false;
			
		},
		onbeforeinsertcolumn: function (){
			return false;
		},	
		updateTable: function(el, cell, x, y, source, value, id) {
			if (x == 0) {
				cell.classList.add('readonly');
				$(cell).css('font-weight','bold');
				$(cell).css('color','black');
				$(cell).css('background-color', '#dcdcdc');	
			} else {
				//$(cell).value(0);
			}
			if (y % 2) {
				if(x != 0)
				{
            		$(cell).css('background-color', '#edf3ff');	
				}
        	}

		},
		
		
	}); 
	hyo = jspreadsheet(document.getElementById('input_grid_data_hyo'),{
		onbeforeinsertrow: function(){			
			return false;
			
		},
		onbeforeinsertcolumn: function (){
			return false;
		},	
		updateTable: function(el, cell, x, y, source, value, id) {
			if (x == 0) {
				cell.classList.add('readonly');
				$(cell).css('font-weight','bold');
				$(cell).css('color','black');
				$(cell).css('background-color', '#dcdcdc');	
			}
			if (y % 2) {
				if(x != 0)
				{
            		$(cell).css('background-color', '#edf3ff');	
				}
        	}

		},
		
	}); 
	console.log(myo.getJson());
</script>
<?php $this->load->view("partial/footer"); ?>