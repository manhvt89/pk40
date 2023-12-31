<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->lang->line('items_generate_barcodes'); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Alegreya+Sans:400,700|Libre+Barcode+128+Text|Libre+Barcode+39+Text&display=swap" rel="stylesheet">
	<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>dist/barcode_font.css" />
	
</head>
<style>
	.barcode-print-area {
	background-color: transparent;
	outline: 1px dashed;
	width: 188.9px;
	height: 151.2px;
	margin: 0px 3px 0px 3px;
	/*transform: rotate(45deg);*/
}
.barcode-item-unit_price{
	font-weight: bold;
	line-height: 14px;
}

.print-barcode_2 {
	width: 50mm;
	transform: rotate(180deg);
	padding-top: 2px;
	border-spacing: 1px;
	height: 19mm;
}

.print-barcode_1 {
	width: 50mm;
	/*outline: 1px dashed;*/
	padding-top: 2px;
	border-spacing: 1px;
	height: 19mm;
}

.print-page-barcode {
	width: 105mm;
	/*
outline: 1px dashed;
border-spacing: 1px;
*/
	/*width: 420px;*/
	margin: auto;
}

.category-barcode {
	transform: rotate(90deg);
}

.buttonpr {
	width: 105mm;
	margin: auto;
	padding: 25px;
	text-align: center;
}

.bt-print-barcode {
	width: 40mm;
	margin: auto;
	height: 12mm;
	font-size: 25px;
	background-color: gray;
	font-family: 'Times New Roman', Times, serif;
}

.store_name {
	font-size: 12px;
	font-family: initial;
}

@media print {
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	.buttonpr {
		display: none;
	}
	body {
		margin: 0;
	}
	#register_wrapper {
		display: none;
	}
}
</style>

<body class=<?php echo "font_".$this->barcode_lib->get_font_name($barcode_config['barcode_font']); ?>
 
      style="font-size:<?php echo $barcode_config['barcode_font_size']; ?>px">
	<?php if(!empty($this->config->item('GBarcode'))): ?>
	  <div class="buttonpr no-print">
				<button onclick="window.print()" class="bt-print-barcode">Print</button>
	  </div>
	  <div id="main_barcode_printer" class="<?php echo "font_".$this->barcode_lib->get_font_name($barcode_config['barcode_font']); ?>" style="font-size:<?php echo $barcode_config['barcode_font_size']; ?>px">
	  
	  <?php print_barcode($items,$this->config->item('GBarcode')['template'],$barcode_config);?>
	  </div>
	  <?php else : ?>
		<div>
			Hiện tại chưa thiết lập mẫu in barcode gọng kính. Hãy liên hệ với người hỗ trợ.
		</div>  
	<?php endif; ?> 
</body>

</html>
