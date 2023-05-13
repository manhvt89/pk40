
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
									</td>
								</tr>
							</table>
						</td>
					</tr>
	</table>
	<div id="receipt_general_info">
		<?php
		if(isset($customer))
		{
			$_sCustomerName = $customer->last_name. ' ' .$customer->first_name;
		?>
			<div id="customer_number" class="customer_number">Mã KH: <?=$customer->account_number?></div>
			<div id="customer" class="customer"><?php echo $this->lang->line('customers_customer').": ". mb_convert_case($_sCustomerName, MB_CASE_TITLE, "UTF-8"); ?></div>
			<?php
		}
		?>
	</div>
	<div class="clearboth"></div>
	<div class="barcode">
	</div>
	<div class="qrcode" style="text-align: center;">	
	</div>
	<main role="main" class="container">
        <div class="jumbotron">
            <!-- User Story #1: I can see a title with id="title" in H1 sized text. -->
            <h1 class="text-center" id="title">Điểm tích của bạn: <?=to_currency_no_money($customer->points)?></h1>
            <!-- User Story #2: I can see a short explanation with id="description" in P sized text. -->
            <p id="description">
				Cảm ơn bạn đã dùng dịch vụ của chúng tôi. Bạn có thể sử dụng điểm tích lũy này để đổi lấy các thẻ giảm giá lần tiếp theo;<br/>
				Mỗi lần sử dụng bạn có thể chọn loại thẻ giảm giá theo số chẵn 50.000 vnđ, 100.000 vnđ, 150.000 vnđ, ..;<br/>
				Mỗi lần đổi bạn sẽ bị trừ số điểm tích lũy tương đương 1 điểm với 1 vnđ;
			</p>
			<p id="description">Chú ý: Điểm này không quy đổi ra tiền mặt.</p>
        </div>
        <!-- User Story #3: I can see a form with id="survey-form". -->
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