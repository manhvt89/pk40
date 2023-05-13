<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('account/save_payout/', array('id'=>'item_form', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('accounting_person'), 'item_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
					<?php echo form_input(array(
							'name'=>'accounting_person',
							'id'=>'accounting_person',
							'class'=>'form-control input-sm',
							'value'=>'')
					);?>

					<?php echo form_input(array(
							'name'=>'person_id',
							'id'=>'person_id',
							'class'=>'form-control input-sm',
							'value'=>'',
							'type'=>'hidden')
					);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('accounting_amount'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'accounting_amount',
						'id'=>'accounting_amount',
						'class'=>'form-control input-sm',
						'value'=>'')
						);?>
				<?php echo form_input(array(
						'name'=>'accounting_employee_id',
						'id'=>'accounting_employee_id',
						'class'=>'form-control input-sm',
						'value'=>$employee_id,
						'type'=>'hidden')

				);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label('Loại phiếu chi', 'kind', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'kind',
							'type'=>'radio',
							'id'=>'kind',
							'value'=>1,
							'checked'=>false)
							); ?> <?php echo 'Chi nội bộ'; ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'kind',
							'type'=>'radio',
							'id'=>'kind',
							'value'=>3,
							'checked'=>false)
							); ?> <?php echo 'Chi khác'; ?>
				</label>

					
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('accounting_note'), 'category', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<?php echo form_textarea(array(
							'name'=>'accounting_note',
							'id'=>'accounting_note',
							'class'=>'form-control input-sm',
							'value'=>'')
					);?>
				</div>
			</div>
		</div>

		

<?php echo form_close(); ?>
		<script src="/dist/jquery.number.min.js"></script>
<script type="text/javascript">
	//validation and submit handling
	$(document).ready(function()
	{
		$('#accounting_amount').number(true,0,',','.');
		$("#new").click(function() {
			stay_open = true;
			$("#item_form").submit();
		});

		$("#submit").click(function() {
			stay_open = false;
		});

		var no_op = function(event, data, formatted){};
		$("#accounting_person").autocomplete({
			source: "<?php echo site_url('account/suggest_customer');?>",
			delay:10,
			appendTo: '.modal-content',
			focus: function( event, ui ) {
				$( "#accounting_person" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#accounting_person" ).val( ui.item.label );
				$( "#person_id" ).val( ui.item.value );
				return false;
			}
		});


		$('#item_form').validate($.extend({
			submitHandler: function(form, event) {
				$(form).ajaxSubmit({
					success: function(response) {
						var stay_open = dialog_support.clicked_id() != 'submit';
						if (stay_open)
						{
							// set action of item_form to url without item id, so a new one can be created
							$("#item_form").attr("action", "<?php echo site_url("account/save_payout/")?>");
							// use a whitelist of fields to minimize unintended side effects
							$(':text, #accounting_note, #item_form').not('.quantity, ' +
								'#accounting_note, ').val('');
							// de-select any checkboxes, radios and drop-down menus
						}
						else
						{
							dialog_support.hide();
						}
						table_support.handle_submit('<?php echo site_url('account'); ?>', response, stay_open);
					},
					dataType: 'json'
				});
			},

			rules:
			{
				accounting_person:"required",
				accounting_amount:"required",
				kind:"required"
			},

			messages:
			{
				accounting_amount:"<?php echo $this->lang->line('accounting_amount_required'); ?>",
				accounting_person:"<?php echo $this->lang->line('accounting_name_required'); ?>",
				kind:"Bạn phải chọn loại phiếu chi"

			}
		}, form_support.error));
	});
</script>

