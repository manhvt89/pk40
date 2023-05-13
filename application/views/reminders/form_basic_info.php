<div class="form-group form-group-sm">
	<?php echo form_label($this->lang->line('common_last_name'), 'last_name', array('class'=>'required control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_input(array(
				'name'=>'last_name',
				'id'=>'last_name',
				'class'=>'form-control input-sm',
				'value'=>$person_info->last_name)
		);?>
	</div>
</div>
<div class="form-group form-group-sm">
	<?php echo form_label($this->lang->line('common_first_name'), 'first_name', array('class'=>'required control-label col-xs-3')); ?>
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

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_email'), 'email', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<div class="input-group">
			<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
			<?php echo form_input(array(
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
		<?php echo form_input(array(
				'name'=>'address_2',
				'id'=>'address_2',
				'class'=>'form-control input-sm',
				'type'=>'hidden',
				'value'=>'HN')
				);?>

		<?php echo form_input(array(
				'name'=>'city',
				'id'=>'city',
				'class'=>'form-control input-sm',
				'type'=>'hidden',
				'value'=>'HN')
				);?>

		<?php echo form_input(array(
				'name'=>'state',
				'id'=>'state',
				'class'=>'form-control input-sm',
				'type'=>'hidden',
				'value'=>'HN')
				);?>

		<?php echo form_input(array(
				'name'=>'zip',
				'id'=>'postcode',
				'class'=>'form-control input-sm',
				'type'=>'hidden',
				'value'=>'10001')
				);?>

		<?php echo form_input(array(
				'name'=>'country',
				'id'=>'country',
				'class'=>'form-control input-sm',
				'type'=>'hidden',
				'value'=>'Việt Nam')
				);?>

<div class="form-group form-group-sm">	
	<?php echo form_label($this->lang->line('common_comments'), 'comments', array('class'=>'control-label col-xs-3')); ?>
	<div class='col-xs-8'>
		<?php echo form_textarea(array(
				'name'=>'comments',
				'id'=>'comments',
				'class'=>'form-control input-sm',
				'value'=>$person_info->comments)
				);?>
	</div>
</div>