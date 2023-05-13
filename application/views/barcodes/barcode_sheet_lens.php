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
	  <div class="print-page-barcode">
	<?php 
	if (!empty($items)) {
		$count = 0;
	  	foreach($items as $item)
		{ 
			if ($count % 3 == 0 and $count != 0)
			{
					?>
					<div class="pagebreak"></div>
					<?php
			}
				?>
			<div class="2" style=" width: 35mm; text-align: center;float: left; margin:0px; ">
				<?php echo $this->barcode_lib->_display_barcode_lens($item, $barcode_config); ?>
			</div>
		<?php ++$count; 
		} 
	} else { ?>
		Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.
	<?php }?>
	
	  </div>
</body>

</html>
