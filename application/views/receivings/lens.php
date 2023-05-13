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


<div id="view_report_lens_category">
	<table id="rp_inventory" class="table2excel" width="100%">
		<thead>
			<tr id="_row_m_1">
				<td style="padding: 0 9px 0 9px;">SPH</td>
				<td colspan="25">CYL(-)</td>
			</tr>
			<tr id="_row_m_2">
				<?php 
					foreach($cyls  as $cyl):
						?>
					<td><?php echo $cyl ?></td>
						<?php
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
		</tbody>
	</table>
	<table id="rp_inventory_hyo" class="table2excel" width="100%">
		
			<tr id="_row_m_1"><td style="padding: 0 9px 0 9px;"><b>SPH</b></td><td colspan="17"><b>CYL(-)</b></td></tr>
		
		
		
		<tr id="_row_h_2">
			<td>+</td>
			<?php 
				foreach($cyls  as $k=>$cyl):
					if($k > 0):
					?>
				<td><b><?php echo $cyl ?></b></td>
					<?php
					endif;
				endforeach;
			?>
			
		</tr>

		<?php 
			foreach($hysphs as $key=>$sph):
				if($key > 0)
				{
					$tr = '<tr id="_row_hyo_'.$key.'">';
					$tr = $tr . '<td><b>'.$sph.'</b></td>';
					foreach($cyls as $k=>$cyl):
						if($k > 0)
						{
							if($k < 10)
							{
								$k = '0'.$k;
							}
							$tr = $tr . '<td>'.'<input type="text" name="hyo'.$key.$k.'"  value="'.set_value('hyo'.$key.$k).'">'.'</td>';
						}
					endforeach;
					$tr = $tr . '</tr>';
					echo $tr;
				}
			endforeach;
		?>
	</table>
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
			$( "#target" ).submit();
		});

	});
	jspreadsheet(document.getElementById('rp_inventory'),{
		columns:<?=$columns?>,
		updateTable: function(el, cell, x, y, source, value, id) {
        if (x == 0) {
            cell.classList.add('readonly');
        }
    } 
	});
</script>
<?php $this->load->view("partial/footer"); ?>