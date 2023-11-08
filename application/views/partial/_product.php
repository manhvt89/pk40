<div class="col-lg-2" style="display:block;">
<div style="text-align: center; padding-bottom: 10px;">
	<?php
		$key = $_products['key'];
		$value = $_products['value'];
		if($key == 'iKindOfLens')
		{
			echo 'Mắt kính';
		} 
		elseif('filter_contact_lens' == $key)
		{
			echo 'Áp tròng';
		} 
		elseif($key == 'filter')
		{
			echo 'Gọng kính';
		} 
		elseif($key == 'filter_sun_glasses')
		{
			echo 'Kính râm';
		}
		elseif($key == 'other_filter')
		{
			echo 'Thuốc';
		}
		else {
			echo $key;
		}
	?>
</div>

	<div class="form-group form-group-sm" style="display:block;">
		<div class='col-xs-12'>
			<?php
			if (empty($value)) {
				$_sValue = '';
			} else {
				$_sValue = implode("\n", $value);
			}
				$_data = array(
					'name'        => $key,
					'id'          => $key,
					'value'       => $_sValue == ''? set_value($key):$_sValue,
					'rows'        => '50',
					'cols'        => '40',					
					'class'       => 'form-control',
					'width'=>'100%'
				);
			
				echo form_textarea($_data);
			?>
		</div>
	</div>
</div>
