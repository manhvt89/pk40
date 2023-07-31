<?php $this->load->view("partial/header"); ?>

<?php
if (isset($error))
{
	echo "<div class='alert alert-dismissible alert-danger'>".$error."</div>";
}

if (!empty($warning))
{
	echo "<div class='alert alert-dismissible alert-warning'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='alert alert-dismissible alert-success'>".$success."</div>";
}
?>

<div id="register_wrapper_test">
<!-- Top register controls -->
	<?php echo form_open($controller_name."/change_mode", array('id'=>'mode_form', 'class'=>'form-horizontal panel panel-default')); ?>
	<div class="panel-body form-group">
			<ul>
				<?php
				if ($this->Employee->has_grant('test_create', $this->session->userdata('person_id')))
				{
				?>
				<li class="pull-right">
					<?php echo anchor($controller_name."/", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('test_new'),
						array('class'=>'btn btn-primary btn-sm', 'id'=>'sales_takings_button', 'title'=>$this->lang->line('test_new'))); ?>
				</li>
				<?php } ?>
				<?php
				if ($this->Employee->has_grant('test_manage', $this->session->userdata('person_id')) || $this->session->userdata('type') == 2)
				{
				?>
					<li class="pull-right">
						<?php echo anchor($controller_name."/manage", '<span class="glyphicon glyphicon-list-alt">&nbsp</span>' . $this->lang->line('test_takings'),
									array('class'=>'btn btn-primary btn-sm', 'id'=>'sales_takings_button', 'title'=>$this->lang->line('test_takings'))); ?>
					</li>
				<?php
				}
				?>
			</ul>
	</div>
	<?php echo form_close(); ?>
	<?php $tabindex = 0; ?>
<!-- Sale Items List -->
	<?php if(isset($customer)): ?>
		
	<?php $this->load->view("test/form"); ?>
	<?php else: ?>
		<ul id="error_message_box" class="error_message_box">Chưa nhập thông tin khách hàng</ul>
	<?php endif; ?>
</div>
<!-- Overall Test -->

<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">
		<?php
		if(isset($customer))
		{
		?>
			<table class="sales_table_100">
				<tr>
					<th style='width: 55%;'><?php echo $this->lang->line("test_customer"); ?></th>
					<th style="width: 45%; text-align: right;">
						<?php if($this->session->userdata('type') != 2): ?>
						<a target="_blank" href="/customer_info/index/<?php echo $customer_phone; ?>"><?php echo $customer; ?></a>
						<?php else: ?>
							<?=$customer; ?>
						<?php endif; ?>
					</th>
				</tr>
				<?php
				if(!empty($customer_address))
				{
				?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("test_customer_address"); ?></th>
						<th style="width: 45%; text-align: right;"><?php echo $customer_address; ?></th>
					</tr>
				<?php
				}
				?>
				<?php
				if(!empty($customer_phone))
				{
					?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("test_customer_phone"); ?></th>
						<th style="width: 45%; text-align: right;"><?php echo $customer_phone; ?></th>
					</tr>
					<?php
				}
				?>
				<?php
				if($age != 0)
				{
					?>
					<tr>
						<th style='width: 55%;'><?php echo $this->lang->line("test_customer_age"); ?></th>
						<th style="width: 45%; text-align: right;"><?php echo $age; ?></th>
					</tr>
					<?php
				}
				?>
				<tr>
					<th colspan="2" style='width: 55%;'><?php echo $this->lang->line("test_customer_old_data"); ?></th>

				</tr>
				<tr>
					<th colspan="2" style="width: 45%; text-align: right;"><?php echo $customer_old_data; ?></th>
				</tr>
			</table>

			<?php echo anchor($controller_name."/remove_customer", '<span class="glyphicon glyphicon-remove">&nbsp</span>' . 'Đối thông tin',
								array('class'=>'btn btn-danger btn-sm', 'id'=>'remove_customer_button', 'title'=>'Đối thông tin')); ?>
		<?php
		}
		else
		{
		?>
			<?php if(count($tests)): //Hiển thị danh sách khách hàng khám trong ngày; ?>
			<table id="list_tested_today" class="table table-hover table-striped" style="background-color: #fff;">

				<tr style="text-align: left; background-color: #e4e4d7">
					<td>Họ và tên</td>
					<td>
						Số điện thoại
					</td>
					<td>

					</td>
				</tr>
				<?php foreach ($tests as $test): ?>
					<tr style="text-align: left" id="list_<?=$test['test_id']?>">
						<td>
							<?php echo $test['last_name']. ' ' . $test['first_name']; ?>
						</td>
						<td>
							<?php echo $test['phone_number']; ?>
						</td>
						<td>
							<span class="glyphicon glyphicon-ok"></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
			<?php echo form_open($controller_name."/select_customer", array('id'=>'select_customer_form', 'class'=>'form-horizontal')); ?>
				<div class="form-group" id="select_customer">
					<label id="customer_label" for="customer" class="control-label" style="margin-bottom: 1em; margin-top: -1em;"><?php echo $this->lang->line('test_select_customer'); ?></label>
					<?php echo form_input(array('name'=>'customer', 'id'=>'customer', 'class'=>'form-control input-sm', 'value'=>$this->lang->line('test_start_typing_customer_name')));?>

					<button id="dlg_form" class='btn btn-info btn-sm modal-dlg' data-value="" data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url("customers/view"); ?>'
							title='<?php echo $this->lang->line($controller_name. '_new_customer'); ?>'>
						<span class="glyphicon glyphicon-user">&nbsp</span><?php echo $this->lang->line($controller_name. '_new_customer'); ?>
					</button>

				</div>
			<?php echo form_close(); ?>
		<?php
		}
		?>

		<?php
		// Only show this part if there are Items already in the sale.
		//if(count($cart) > 0)
		if(!empty($cart))
		{
		?>

			<div id="payment_details">
				<div class='btn btn-sm btn-success pull-right' id='finish_sale_button' ><span class="glyphicon glyphicon-ok">&nbsp</span><?php echo $this->lang->line('test_print_test'); ?></div>
			</div>
		<?php
		}
		?>
	</div>
	<?php echo form_open($controller_name."/view_test", array('id'=>'view_test_form', 'class'=>'form-horizontal')); ?>
	<?php echo form_input(array(
			'name'=>'test_id',
			'id'=>'hdd_test_id',
			'class'=>'input-test',
			'type'=>'hidden',
			'value'=>'')
	);?>
	<?php echo form_close(); ?>
</div>

<div id="principle_print">
	<!-- Sale Items List -->
	<?php if(isset($customer)): ?>
		<?php $this->load->view("test/form_print"); ?>
	<?php endif; ?>
</div>
<?php $this->load->view('partial/print_prescription.php',array('selected_printer'=>'precription_printer')); ?>
<script type="text/javascript">


	function view(test_id)
	{
		$('#hdd_test_id').val(test_id);
		$('#view_test_form').submit();
	}

$(document).ready(function()
{
	$(window).keydown(function(event){
		if((event.keyCode == 13) && ($(event.target)[0]!=$("textarea")[0])) {


		//if(event.keyCode == 13) {
		event.preventDefault();
		return false;
		}
	});
	<?php if(!$this->Employee->has_grant('test_step_one')): ?>
		
	$("#list_tested_today tr").click(function(){
		//alert($(this).attr('id'));
		var strId = $(this).attr('id');
		var Ids = strId.split('_');
		var test_id = Ids[1];
		//alert(strId);
		$('#hdd_test_id').val(test_id);
		$('#view_test_form').submit();
	});
	$("#old_data_view tr").click(function(){
		//alert($(this).attr('id'));
		var strId = $(this).attr('id');
		if(undefined != strId )
		{
			console.log(strId);
			var Ids = strId.split('_');
			var test_id = Ids[1];
			//alert(strId);
			$('#hdd_test_id').val(test_id);
			$('#view_test_form').submit();
		}
	});
	<?php endif; ?>

	$("#clear_test_button").click(function(){
		$('#hdd_test_id').val(0);
		$('#view_test_form').submit();
		//$("#clear_test_button").hide();
	});

	var clear_fields = function()
    {
        if ($(this).val().match("<?php echo $this->lang->line('test_start_typing_item_name') . '|' . $this->lang->line('test_start_typing_customer_name'); ?>"))
        {
            $(this).val('');
        }
    };

	$('#customer').keypress(function (e) {
		if (e.which == 13) {
			$('#select_customer_form').submit();
			return false;
		}
	});

    $("#customer").autocomplete(
    {
		source: '<?php echo site_url("customers/suggest"); ?>',
    	minChars: 0,
    	delay: 10,
		select: function (a, ui) {
			$(this).val(ui.item.value);
			$("#select_customer_form").submit();
		}
    });

	$('#customer').click(clear_fields).dblclick(function(event)
	{
		$(this).autocomplete("search");
	});

	$('#customer').blur(function()
    {
    	$(this).val("<?php echo $this->lang->line('test_start_typing_customer_name'); ?>");
    });
	$('#customer').keyup(function()
	{
		$('#dlg_form').attr('data-value', $('#customer').val());
	});

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url($controller_name."/set_comment");?>', {comment: $('#comment').val()});
	});

    $("#finish_sale_button").click(function()
    {
    	printdoc();
    });

	$("#update_test_button").click(function()
	{
		$('#done_test_form').attr('action', '<?php echo site_url($controller_name."/complete"); ?>');
		$('#done_test_form').submit();
	});

	dialog_support.init("a.modal-dlg, button.modal-dlg");

	table_support.handle_submit = function(resource, response, stay_open)
	{
		if(response.success) {
			if (resource.match(/customers$/))
			{
				$("#customer").val(response.id);
				$("#select_customer_form").submit();
			}
			else
			{
				var $stock_location = $("select[name='stock_location']").val();
				$("#item_location").val($stock_location);
				$("#item").val(response.id);
				if (stay_open)
				{
					$("#add_item_form").ajaxSubmit();
				}
				else
				{
					$("#add_item_form").submit();
				}
			}
		}
	}

	// Danh sách thuốc đã lưu trữ dưới dạng JSON
	var medicationList = <?=$json_pres_a?>; 
	var prescription_list = <?=$json_prescription_list?>;

	prescription_list.forEach(myFunction);

	function myFunction(medication, index, array) {
		var $newPrescriptionItem = $('<tr></tr>');
		var $col_stt = $('<td></td>');
		var $col_name = $('<td><input name="pres_name[]" type="hidden" value="'+medication.name+'"/>'+medication.name+'</td>');
		var $col_dvt = $('<td><input name="pres_dvt[]" type="hidden" value="'+medication.dvt+'"/>'+medication.dvt+'</td>');
		var $col_sl = $('<td><input name="pres_amount[]" type="number" maxlength="4" size="4" value=""/></td>');
		var $col_hdsd = $('<td><input name="pres_hdsd[]" type="text" maxlength="250" size="45" value="'+medication.hdsd+'"/></td>');
		$newPrescriptionItem.append($col_stt);
		$newPrescriptionItem.append($col_name);
		$newPrescriptionItem.append($col_dvt);
		$newPrescriptionItem.append($col_sl);
		$newPrescriptionItem.append($col_hdsd);
		//$newPrescriptionItem.text(medication.name);

		var $deleteButton = $('<button class="delete-button">Xóa</button>');
		$deleteButton.on('click', function() {
			$('#done_test_form').keypress(function(e){
		if (e.keyCode == 13) {
				e.preventDefault()
				return false;
			}
		});
			var cf = confirm('Bạn chắc muốn xóa');
			if(cf == true)
			{
				$newPrescriptionItem.remove();
			}
		});

		$newPrescriptionItem.append($deleteButton);
		$('#cart_contents2').append($newPrescriptionItem);
		
	}
  // Tìm kiếm thuốc
  $('#search-input').on('input', function() {
    var searchQuery = $(this).val().toLowerCase();
    var searchResults = [];

    if (searchQuery.length > 0) {
      searchResults = medicationList.filter(function(medication) {
        return medication.name.toLowerCase().includes(searchQuery);
      });
    }

    displaySearchResults(searchResults);
  });

  // Thêm thuốc vào đơn thuốc
  $('#search-results').on('click', 'li', function() {
    var medicationName = $(this).text();
    var medication = medicationList.find(function(medication) {
      return medication.name === medicationName;
    });

    if (!isMedicationAlreadyAdded(medicationName)) {
      addToPrescription(medication);
    }

    clearSearch();
  });

  // Kiểm tra nếu thuốc đã được thêm vào đơn thuốc
  function isMedicationAlreadyAdded(medicationName) {
    var isAlreadyAdded = false;

    $('#prescription-list li').each(function() {
      if ($(this).text() === medicationName) {
        isAlreadyAdded = true;
        return false;
      }
    });

    return isAlreadyAdded;
  }

  // Thêm thuốc vào đơn thuốc và hiển thị danh sách đơn thuốc
  function addToPrescription(medication) {
    var $newPrescriptionItem = $('<tr></tr>');
	var $col_stt = $('<td></td>');
	var $col_name = $('<td><input name="pres_name[]" type="hidden" value="'+medication.name+'"/>'+medication.name+'</td>');
	var $col_dvt = $('<td><input name="pres_dvt[]" type="hidden" value="'+medication.dvt+'"/>'+medication.dvt+'</td>');
	var $col_sl = $('<td><input name="pres_amount[]" type="number" maxlength="4" size="4" value=""/></td>');
	var $col_hdsd = $('<td><input name="pres_hdsd[]" type="text" maxlength="250" size="45" value="'+medication.hdsd+'"/></td>');
	$newPrescriptionItem.append($col_stt);
	$newPrescriptionItem.append($col_name);
	$newPrescriptionItem.append($col_dvt);
	$newPrescriptionItem.append($col_sl);
	$newPrescriptionItem.append($col_hdsd);
    //$newPrescriptionItem.text(medication.name);

    var $deleteButton = $('<button class="delete-button">Xóa</button>');
    $deleteButton.on('click', function() {
		$('#done_test_form').keypress(function(e){
			console.log(e)
		if (e.keyCode == 13) {
				e.preventDefault()
				return false;
			}
		});
			var cf = confirm('Bạn chắc muốn xúa');
			if(cf == true)
			{
				$newPrescriptionItem.remove();
			}
    });

    $newPrescriptionItem.append($deleteButton);
    $('#cart_contents2').append($newPrescriptionItem);
  }

  // Hiển thị kết quả tìm kiếm
  function displaySearchResults(searchResults) {
    var $searchResults = $('#search-results');
    $searchResults.empty();

    searchResults.forEach(function(result) {
      var $resultItem = $('<li></li>');
      $resultItem.text(result.name);
      $searchResults.append($resultItem);
    });
  }

  // Xóa nội dung ô tìm kiếm
  function clearSearch() {
    $('#search-input').val('');
    $('#search-results').empty();
  }
});

</script>

<?php $this->load->view("partial/footer"); ?>
