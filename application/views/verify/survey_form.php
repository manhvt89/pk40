
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<title> He thong thong tin</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
	<!-- start css template tags -->
	<style>
		/*
		CSS-Tricks Example
		by Chris Coyier
		http://css-tricks.com
		*/

		#company_name {
			font-size: 150%;
			font-weight: 700;
		}
		.address, .phone {
			width: 100%;
			text-align: left;
			font-size: 16px;
		}
		* {
			margin: 0;
			padding: 0;
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 13px;
		}

		#page-wrap {
			max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Arial', 'Helvetica', sans-serif;
				color: #555;
		}

		pre {
			font-family: Arial;
			font-size: 13px;
		}

		#page-wrap {
			font-family: Arial;
		}

		#page-wrap table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			#page-wrap table td {
				padding: 5px;
				vertical-align: top;
			}

			#page-wrap table tr td:nth-child(2) {
				text-align: right;
			}

			#page-wrap table tr.top table td {
				padding-bottom: 20px;
			}

			#page-wrap table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			#page-wrap table tr.information table td {
				padding-bottom: 5px;
			}

			#page-wrap table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			#page-wrap table tr.details td {
				padding-bottom: 20px;
			}

			#page-wrap table tr.item td {
				border-bottom: 1px solid #eee;
			}

			#page-wrap table tr.item.last td {
				border-bottom: none;
			}

			#page-wrap table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				#page-wrap table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				#page-wrap table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			#page-wrap.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			#page-wrap.rtl table {
				text-align: right;
			}

			#page-wrap.rtl table tr td:nth-child(2) {
				text-align: left;
			}

		#page-wrap table {
			border-collapse: collapse;
		}

		#page-wrap table#items td {
			padding: 10px;
		}

		#page-wrap #meta table td,
		#page-wrap table th {
			border: 1px solid black;
			padding: 5px;
		}

		#sale_receipt {
  text-transform: uppercase;
  font-size: 20px;
  font-weight: bold;
  line-height: 110%;
  padding-top: 5px;
}
.time {
  font-size: 15px;
  font-style: italic;
}
.number_inc{
	font-size: 13px;
}

		#header {
			height: 30px;
			width: 100%;
			margin: 20px 0;
			background-color: #222222;
			text-align: center;
			color: white;
			font-weight: bold;
			font-size: 26px;
			letter-spacing: 4px;
			padding: 8px 0px;
		}


		/* first row */

		#info {
			width: 100%;
			margin: 10px 0 30px 0;
		}

		#logo {
			width: 50%;
			text-align: left;
			border: 1px solid #ffffff;
			overflow: hidden;
		}

		.logo {
			float: left;
			width: 25%;
			margin-right: 2px;
		}

		#image {
			height: 120px;
			width: 163px;
		}

		#logoctr {
			display: none;
		}

		#logo:hover #logoctr,
		#logo.edit #logoctr {
			display: block;
			text-align: right;
			line-height: 25px;
			background: #eee;
			padding: 0 5px;
		}

		#logohelp {
			text-align: left;
			display: none;
			font-style: italic;
			padding: 10px 5px;
		}

		#logohelp input {
			margin-bottom: 5px;
		}

		.edit #logohelp {
			display: block;
		}

		.edit #save-logo,
		.edit #cancel-logo {
			display: inline;
		}

		.edit #image,
		#save-logo,
		#cancel-logo,
		.edit #change-logo,
		.edit #delete-logo {
			display: none;
		}

		#customer-title {
			text-align: right;
		}

		#terms div {
			width: 100%;
			text-align: center;
			margin-bottom: 10px;
		}


		/* second row */

		#company-title {
			width: 50%;
			padding-left: 20px;
		}

		#meta td {
			text-align: right;
		}

		#meta td.meta-head {
			text-align: left;
			background: #eee;
		}

		#items {
			width: 100%;
			border: 1px solid black;
		}

		#items th {
			background: #eee;
		}

		#items tr.item-row td {
			border: 0;
			vertical-align: top;
		}

		#items td {
			font-family: DejaVu Sans;
		}

		#items td.description {
			width: 300px;
		}

		#items td.item-name {
			width: 175px;
		}

		#items td.total-line {
			text-align: right;
			border-width: 1px 0 1px 1px;
			border-style: solid;
		}

		#items td.total-value {
			text-align: right;
			border-width: 1px 0px 1px 0;
			border-style: solid;
		}

		#items td.centered-value {
			text-align: center;
		}

		#items td.balance {
			background: #eeeeee;
		}

		#items td.blank {
			border: 0;
		}

		#terms {
			text-align: center;
			margin: 20px 0;
		}

		#terms h5 {
			border-bottom: 1px solid black;
			font: 13px Helvetica, Sans-Serif;
			padding: 8px 0;
			margin: 8px 0;
			line-height: 1.3em;
		}

		.delete-wpr {
			position: relative;
		}

		.delete {
			display: block;
			color: #000;
			text-decoration: none;
			position: absolute;
			background: #EEEEEE;
			font-weight: bold;
			padding: 0px 3px;
			border: 1px solid;
			top: -6px;
			left: -22px;
			font-family: Verdana;
			font-size: 12px;
		}
		.range-wrap {
			position: relative;
			margin: 0 auto 3rem;
			}
		.range {
			width: 100%;
			}
		.bubble {
			background: red;
			color: white;
			padding: 4px 12px;
			position: absolute;
			border-radius: 4px;
			left: 50%;
			transform: translateX(-50%);
			}
		.bubble::after {
			content: "";
			position: absolute;
			width: 2px;
			height: 2px;
			background: red;
			top: -1px;
			left: 50%;
			}

		@media (min-width: 1200px){
			.container {
				width: 100%;
			}
		}

		@media (min-width: 992px){
			.container {
				width: 100%;
			}
		}
		.survey-form {
		background-color: #e9ecef;
		padding: 20px;
		}
		.modal-content textarea.form-control {
		max-width: 100%;
		}
		.container {
			width: 100%;
			padding-right: 0px;
			padding-left: 0px; 
			margin-right: auto;
			margin-left: auto;
		}
	</style>
	<!-- end css template tags -->
</head>

<body>
<div id="page-wrap">
	<table cellpadding="0" cellspacing="0">
					<tr class="top">
						<td colspan="2">
							<table>
								<tr>
									<td class="title">
									<?php
									if ($this->config->item('company_logo') != '') 
									{ 
									?>
										<div id="logo" class="_logo" style="width: 100%; max-width: 300px"><img id="image" src="<?php echo base_url('uploads/' . $this->config->item('company_logo')); ?>" alt="company_logo" /></div>
									<?php
									}
									?>
									</td>

									<td>
									<?php echo $this->config->item('company'); ?><br />
									<?php echo nl2br($this->config->item('address')); ?><br/>
										Điện thoại: <?php echo $this->config->item('phone'); ?><br />
										Website: <?php echo $this->config->item('website'); ?>
																			
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr class="information">
						<td colspan="2">
							<table>
								<tr>
									<td align="center">
									<div id="sale_receipt"><?php echo $receipt_title; ?></div>
									<div id="" class="number_inc">Số hóa đơn: <?=$code?></div>
									<div id="" class="time">Ngày <?php echo $transaction_time ?></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
	</table>
	<div id="receipt_header">
		<div class="clearboth"></div>
		
		
		<div class="clearboth"></div>
	</div>

	<div id="receipt_general_info">

		<?php
		if(isset($customer))
		{
		?>
			<div id="customer_number" class="customer_number">Mã KH: <?php echo $account_number; ?></div>
			<div id="customer" class="customer"><?php echo $this->lang->line('customers_customer').": ". mb_convert_case($customer, MB_CASE_TITLE, "UTF-8"); ?></div>
			<?php
		}
		?>
	</div>
	<table id="items">
	    <thead>
		<tr>
			<th style="width:40%;"><?php echo $this->lang->line('sales_description_abbrv'); ?></th>
			<th style="width:20%;"><?php echo $this->lang->line('sales_price'); ?></th>
			<th style="width:10%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="width:10%;">[KM]</th>
			<th style="width:20%;" class="total-value"><?php echo $this->lang->line('sales_total_amount'); ?></th>
		</tr>
		</thead>
		<?php
		$i = 0;
		foreach(array_reverse($cart, true) as $line=>$item)
		{ $i++;
		    $data_th = '';
		?>
			<tr>
				<th><?php echo $i .'. '. ucfirst($item['name']); ?></th>
				<td><?php echo number_format($item['price']); $data_th = number_format($item['price']); ?></td>
				<td><?php echo to_quantity_decimals($item['quantity']); $data_th = $data_th . ' x '.to_quantity_decimals($item['quantity']);?></td>
				<?php
				if ($item['discount'] > 0)
				{
					?>
						<td class="discount"><?php echo number_format($item['discount'], 2) . "%"; $data_th = $data_th.'[-'.number_format($item['discount'], 2) . "%]";?></td>
					<?php
				}else{ ?>
					<td class="discount">0</td>
				<?php
				}
				    $data_th = $data_th . '=';
				?>
				<td class="total-value" data-th="<?=$data_th?> "><?php echo number_format($item[($this->config->item('receipt_show_total_discount') ? 'total' : 'discounted_total')]); ?></td>
			</tr>
			<!-- <tr>
				<?php
				if($this->config->item('receipt_show_description'))
				{
				?>
					<td colspan="2"><?php echo $item['description']; ?></td>
				<?php
				}
				?>
				<?php
				if($this->config->item('receipt_show_serialnumber'))
				{
				?>
					<td><?php echo $item['serialnumber']; ?></td>
				<?php
				}
				?>
			</tr> -->

		<?php
		}
		?>
	
		<?php
		if ($this->config->item('receipt_show_total_discount') && $discount > 0)
		{
		?> 
			<tr>
				<td colspan="4" style='text-align:right;border-top:1px dashed black;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
				<td style='text-align:right;border-top:1px dashed black;' data-th="<?php echo $this->lang->line('sales_sub_total'); ?> "><?php echo number_format($subtotal); ?></td>
			</tr>
			<tr>
				<td colspan="4" class="total-value"><?php echo $this->lang->line('sales_discount'); ?>:</td>
				<td class="total-value" data-th="<?php echo $this->lang->line('sales_discount'); ?>: "><?php echo number_format($discount * 1); ?></td>
			</tr>
		<?php
		}
		?>

		<?php
		if ($this->config->item('receipt_show_taxes'))
		{
		?> 
			<tr>
				<td colspan="4" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
				<td style='text-align:right;border-top:2px solid #000000;'><?php echo number_format($this->config->item('tax_included') ? $tax_exclusive_subtotal : $discounted_subtotal); ?></td>
			</tr>
			<?php
			foreach($taxes as $name=>$value)
			{
			?>
				<tr>
					<td colspan="4" class="total-value"><?php echo $name; ?>:</td>
					<td class="total-value"><?php echo number_format($value); ?></td>
				</tr>
			<?php
			}
			?>
		<?php
		}
		?>
		<?php $border = (!$this->config->item('receipt_show_taxes') && !($this->config->item('receipt_show_total_discount') && $discount > 0)); ?> 
		<tr class="total">
			<td colspan="4" style="text-align:right;<?php echo $border? 'border-top: 1px dashed black;' :''; ?>"><?php echo $this->lang->line('sales_total'); ?></td>
			<td style="text-align:right;<?php echo $border? 'border-top: 1px dashed black;' :''; ?>" data-th="<?php echo $this->lang->line('sales_total'); ?> "><?php echo number_format($total); ?></td>
		</tr>

		<tr class="total_words">
			<td colspan="5">Bằng chữ:&nbsp;<?php echo convert_number_to_words(number_format($total,0,'.','')); ?> đồng</td>
		</tr>

		<?php
		$only_sale_check = FALSE;
		$show_giftcard_remainder = FALSE;

		if(!empty($payments[$this->lang->line('sales_reserve_money')]))
		{
			$old_payments = $payments[$this->lang->line('sales_reserve_money')];
			foreach ($old_payments as $payment_id => $payment) {
				$only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
				$splitpayment = explode(':', $payment['payment_type']);
				$show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');
				?>
				<tr class="total_reserve">
					<td colspan="4" style="text-align:right;">Đặt trước</td>
					<td class="total-value" data-th="Đặt trước: "><?php echo number_format($payment['payment_amount'] * 1); ?></td>
				</tr>
				<?php
			}
		}

		if(!empty($payments[$this->lang->line('sales_paid_money')])) {
			$new_payments = $payments[$this->lang->line('sales_paid_money')];
			foreach ($new_payments as $payment_id => $payment) {
				$only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
				$splitpayment = explode(':', $payment['payment_type']);
				$show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');
				$_sTitleDisplayPayment = $this->lang->line('sales_paid_rev').' ('.$payment['payment_type'].')';
				?>
				<tr class="total_paid">
					<td colspan="4" style="text-align:right;"><?php echo $_sTitleDisplayPayment; ?> </td>
					<td class="total-value" data-th="<?php echo $_sTitleDisplayPayment; ?> "><?php echo number_format($payment['payment_amount'] * 1); ?></td>
				</tr>
				<?php
			}
		}
		?>
		<?php 
		if (isset($cur_giftcard_value) && $show_giftcard_remainder)
		{
		?>
		<tr>
			<td colspan="4" style="text-align:right;"><?php echo $this->lang->line('sales_giftcard_balance'); ?></td>
			<td class="total-value" data-th="<?php echo $this->lang->line('sales_giftcard_balance'); ?> "><?php echo number_format($cur_giftcard_value); ?></td>
		</tr>
		<?php 
		}
		?>
		<tr class="total_blance">
			<td colspan="4" style="text-align:right;"> <?php echo $this->lang->line($amount_change >= 0 ? ($only_sale_check ? 'sales_check_balance' : 'sales_change_due') : 'sales_amount_due_1') ; ?> </td>
			<td class="total-value" data-th="<?php echo $this->lang->line($amount_change >= 0 ? ($only_sale_check ? 'sales_check_balance' : 'sales_change_due') : 'sales_amount_due_1') ; ?> "><?php echo number_format($amount_change >= 0 ? $amount_change : $amount_change * -1); ?></td>
		</tr>
	</table>
	<div class="clearboth"></div>
	<div class="barcode">
	</div>
	<div class="qrcode" style="text-align: center;">
			
	</div>
	<main role="main" class="container">
        <div class="jumbotron">
            <!-- User Story #1: I can see a title with id="title" in H1 sized text. -->
            <h1 class="text-center" id="title">Đánh giá đơn hàng</h1>
            <!-- User Story #2: I can see a short explanation with id="description" in P sized text. -->
            <p id="description">Bạn hoàn thành đánh giá này, bạn sẽ được tặng 10% giá trị đơn hàng cho lần mua hàng tiếp theo.</p>
        </div>
        <!-- User Story #3: I can see a form with id="survey-form". -->
        <?php echo form_open('verify/check') ?>
            <div id="formarea">
                
                <div class="row">
                    <div class="col-md-4 d-flex justify-content-end">
                        <div class="form-group">
                            <p>Bạn có hài lòng với nhân viên bán hàng không? (Bạn cho điểm từ 1 (thấp) đến 5 (cao)</p>
                        </div>
                    </div>
                    <div class="col-md-8">
						<div class="range-wrap">
                            <input type="range" id="q1" class="form-control range" name="q1" min="1" max="5" step="1">
							<output class="bubble"></output>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-md-4 d-flex justify-content-end">
                        <div class="form-group">
                            <p>Bạn có hài lòng với nhân viên đo mắt không? (Bạn cho điểm từ 1 (thấp) đến 5 (cao)</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="range-wrap">
                            <input type="range" id="q2" class="form-control range" name="q2" min="1" max="5" step="1">
							<output class="bubble"></output>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-md-4 d-flex justify-content-end">
                        <div class="form-group">
                            <p>Bạn có hài lòng với sản phẩm không? (Bạn cho điểm từ 1 (thấp) đến 5 (cao)</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="range-wrap">
                            <input type="range" id="q3" class="form-control range" name="q3" min="1" max="5" step="1">
							<output class="bubble"></output>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 d-flex justify-content-end">
                        <div class="form-group">
                            <p>Đơn hàng có chính xác không</p>
                        </div>
                    </div>
                    <!-- User Story #13: Inside the form element, I can select a field from one or more groups of radio buttons. Each group should be grouped using the name attribute. -->
                    <div class="col-md-8">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="confirm" id="varopinion" value="1" checked="checked">
                            <label class="form-check-label" for="confirm">
                                Đúng
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="confirm" id="varopinion" value="2">
                            <label class="form-check-label" for="confirm">
                                Sai
                            </label>
                        </div>
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-center mb-5">
                        <!-- User Story #16: Inside the form element, I am presented with a button with id="submit" to submit all my inputs. -->
                        <button type="submit" id="submit" class="btn btn-outline-secondary btn-lg btn-block">OK</button>
						<input class="form-check-input" type="hidden" name="sale_uuid" id="hdd_sale_uuid" value="<?=$sale_uuid ?>">
						<input class="form-check-input" type="hidden" name="c_uuid" id="hdd_c_uuid" value="<?=$c_uuid ?>">
                    </div>
                </div>
            </div>
		<?php echo form_close(); ?>
    </main>
</div>
</body>
</html>

<script type="">
	const allRanges = document.querySelectorAll(".range-wrap");
	allRanges.forEach(wrap => {
	const range = wrap.querySelector(".range");
	const bubble = wrap.querySelector(".bubble");

	range.addEventListener("input", () => {
		setBubble(range, bubble);
	});
	setBubble(range, bubble);
	});

	function setBubble(range, bubble) {
	const val = range.value;
	const min = range.min ? range.min : 0;
	const max = range.max ? range.max : 100;
	const newVal = Number(((val - min) * 100) / (max - min));
	bubble.innerHTML = val;

	// Sorta magic numbers based on size of the native UI thumb
	bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
	}
</script>