<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->lang->line('items_generate_barcodes'); ?></title>
	<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>dist/barcode_font.css" />
	<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>dist/barcode_print.css" />
</head>
<style>
</style>

<body class=<?php echo "font_".$this->barcode_lib->get_font_name($barcode_config['barcode_font']); ?> 
      style="font-size:<?php echo $barcode_config['barcode_font_size']; ?>px">
	  <div class="buttonpr no-print">
				<button onclick="window.print()" class="bt-print-barcode">Print</button>
	  </div>
	  <?php print_barcode($items,$this->config->item('GBarcode')['template'],$barcode_config);?>
</body>

</html>
