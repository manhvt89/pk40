<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use emberlabs\Barcode\BarcodeBase;
require APPPATH.'/views/barcodes/BarcodeBase.php';
require APPPATH.'/views/barcodes/Code39.php';
require APPPATH.'/views/barcodes/Code128.php';
require APPPATH.'/views/barcodes/Ean13.php';
require APPPATH.'/views/barcodes/Ean8.php';

class Barcode_lib
{
	private $CI;
	private $supported_barcodes = array('Code39' => 'Code 39', 'Code128' => 'Code 128', 'Ean8' => 'EAN 8', 'Ean13' => 'EAN 13');
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function get_list_barcodes()
	{
		return $this->supported_barcodes;
	}
	
	public function get_barcode_config()
	{
		$data['company'] = $this->CI->config->item('company');
		$data['barcode_content'] = $this->CI->config->item('barcode_content');
		$data['barcode_type'] = $this->CI->config->item('barcode_type');
		$data['barcode_font'] = $this->CI->config->item('barcode_font');
		$data['barcode_font_size'] = $this->CI->config->item('barcode_font_size');
		$data['barcode_height'] = $this->CI->config->item('barcode_height');
		$data['barcode_width'] = $this->CI->config->item('barcode_width');
		$data['barcode_quality'] = $this->CI->config->item('barcode_quality');
		$data['barcode_first_row'] = $this->CI->config->item('barcode_first_row');
		$data['barcode_second_row'] = $this->CI->config->item('barcode_second_row');
		$data['barcode_third_row'] = $this->CI->config->item('barcode_third_row');
		$data['barcode_num_in_row'] = $this->CI->config->item('barcode_num_in_row');
		$data['barcode_page_width'] = $this->CI->config->item('barcode_page_width');	  
		$data['barcode_page_cellspacing'] = $this->CI->config->item('barcode_page_cellspacing');
		$data['barcode_generate_if_empty'] = $this->CI->config->item('barcode_generate_if_empty');
		
		return $data;
	}

	public function validate_barcode($barcode)
	{
		$barcode_type = $this->CI->config->item('barcode_type');
		$barcode_instance = $this->get_barcode_instance($barcode_type);
		return $barcode_instance->validate($barcode);
	}

	public static function barcode_instance($item, $barcode_config)
	{
		$barcode_instance = Barcode_lib::get_barcode_instance($barcode_config['barcode_type']);
		$is_valid = empty($item['item_number']) && $barcode_config['barcode_generate_if_empty'] || $barcode_instance->validate($item['item_number']);

		// if barcode validation does not succeed,
		if (!$is_valid)
		{
			$barcode_instance = Barcode_lib::get_barcode_instance();
		}
		$seed = Barcode_lib::barcode_seed($item, $barcode_instance, $barcode_config);
		$barcode_instance->setData($seed);

		return $barcode_instance;
	}

	private static function get_barcode_instance($barcode_type='Code128')
	{
		switch($barcode_type)
		{
			case 'Code39':
				return new emberlabs\Barcode\Code39();
				break;
				
			case 'Code128':
			default:
				return new emberlabs\Barcode\Code128();
				break;
				
			case 'Ean8':
				return new emberlabs\Barcode\Ean8();
				break;
				
			case 'Ean13':
				return new emberlabs\Barcode\Ean13();
				break;
		}
	}

	private static function barcode_seed($item, $barcode_instance, $barcode_config)
	{
		$seed = $barcode_config['barcode_content'] !== "id" && !empty($item['item_number']) ? $item['item_number'] : $item['item_id'];

		if( $barcode_config['barcode_content'] !== "id" && !empty($item['item_number']))
		{
			$seed = $item['item_number'];
		}
		else
		{
			if ($barcode_config['barcode_generate_if_empty'])
			{
				// generate barcode with the correct instance
				$seed = $barcode_instance->generate($seed);
			}
			else
			{
				$seed = $item['item_id'];
			}
		}
		return $seed;
	}

	private function generate_barcode($item, $barcode_config)
	{
		try
		{
			$barcode_instance = Barcode_lib::barcode_instance($item, $barcode_config);
			$barcode_instance->setQuality($barcode_config['barcode_quality']);
			$barcode_instance->setDimensions($barcode_config['barcode_width'], $barcode_config['barcode_height']);

			$barcode_instance->draw();
			
			return $barcode_instance->base64();
		} 
		catch(Exception $e)
		{
			echo 'Caught exception: ', $e->getMessage(), "\n";		
		}
	}

	public function generate_receipt_barcode($barcode_content)
	{
		try
		{
			// Code128 is the default and used in this case for the receipts
			$barcode = $this->get_barcode_instance('Code128');

			// set the receipt number to generate the barcode for
			$barcode->setData($barcode_content);
			
			// image quality 100
			$barcode->setQuality(100);
			
			// width: 250, height: 50
			//$barcode->setDimensions(250, 45);
			$barcode->setDimensions(0, 45);

			// draw the image
			$barcode->draw();
			
			return $barcode->base64();
		} 
		catch(Exception $e)
		{
			echo 'Caught exception: '. $barcode_content . ' ', $e->getMessage(), "\n";
		}
	}
	

	public function display_barcode($item, $barcode_config)
	{
		$display_table = "<table>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</td></tr>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<tr><td align='center'><img src='data:image/png;base64,$barcode' /></td></tr>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</td></tr>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</td></tr>";
		$display_table .= "</table>";
		
		return $display_table;
	}

	public function _display_barcode($item, $barcode_config) // @gong
	{
		//var_dump($item);die();
		$item['unit_price'] = $item['price'];
		$barcode_config['barcode_width'] = 0;
		$display_table = "<div class='print-barcode_1'>";
		$display_table .= "<div align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<div align='center'><img src='data:image/png;base64,$barcode' /></div></tr>";
		$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</b> </div>";
		$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</b>- <b class='category-barcode'>".$item['item_category']."</b></div>";
		$display_table .= "</div>";

		$display_table .= "<div class='print-barcode_2'>";
		$display_table .= "<div class='store_name' align='center'><b>".$barcode_config['store_name']."</b></div>";
		
		//$display_table .= "<div class='headline' align='center'>Chăm sóc đôi mắt bạn</div>";
		$display_table .= "<div align='center'>".$barcode_config['store_address']."</div>";
		$display_table .= "</div>";
		
		return $display_table;
	}

	public function _display_barcode_1x75($item, $barcode_config) // @gong 1x75
	{
		//var_dump($item);die();
		$item['unit_price'] = $item['price'];
		$barcode_config['barcode_width'] = 0;
		$display_table = "<div class='print-barcode_1' style='width:52mm; height:14mm'>";
		$display_table .= "<div align='center' style='font-size:9px'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<div align='center' style='font-size:14px'><img src='data:image/JPEG;base64,$barcode' /></div></tr>";
		/*if($item['item_number'] != '') {
			$display_table .= "<div align='center' style='font-size:29px' class='LibreBarcode128'>" . $item['item_number'] . "</div></tr>";
		}*/
		$display_table .= "<div align='center' style='font-size:9px'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</div>";
		$display_table .= "<div align='center' style='font-size:9px'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . " </b></div>";
		$display_table .= "</div>";

		$display_table .= "<div class='print-barcode_2' style='width:50mm; height:15mm; margin: 0mm 0mm 0mm 0mm; padding-bottom: 0mm'>";
		$display_table .= "<div style='font-size: 11px; font-family: 'Arial' !important;' align='center'><b>".$barcode_config['store_name']."</b></div>";
		//$display_table .= "<div class='headline' align='center'>Chăm sóc đôi mắt bạn</div>";
		$display_table .= "<div align='center' style='font-size:9px'>".$barcode_config['store_address']."</div>";
		$display_table .= "<div align='center' style='font-size:9px'>0904642141</div>";
		$display_table .= "</div>";
		
		return $display_table;
	}

	public function _display_barcode_lens($item, $barcode_config)
	{
		//$barcode_config['barcode_width'] = 145;
		$barcode_config['barcode_width'] = 0;
		$display_table = "<div class='' style='width:100%; '>";
		$display_table .= "<div style='width:100%; font-size:9px; padding-bottom: 5px;' align='center'>" . $this->manage_display_layout_lens($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<div style='width:100%; font-size:9px;' align='center'><img src='data:image/png;base64,$barcode' /></div>";
		$display_table .= "<div style='width:100%; font-size:9px;' align='center'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</div>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout_lens('location', $item, $barcode_config) . "</td></tr>";
		$display_table .= "</div>";
		
		return $display_table;
	}
	public function _display_barcode_lens_bak($item, $barcode_config)
	{
		$barcode_config['barcode_width'] = 150;
		$display_table = "<table class='print-barcode_1' with='35mm'>";
		$display_table .= "<tr><td style='width:130px' align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</td></tr>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<tr><td style='width:130px' align='center'><img style='width:130px' src='data:image/png;base64,$barcode' /></td></tr>";
		$display_table .= "<tr><td style='width:130px' align='center'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</td></tr>";
		//$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</td></tr>";
		$display_table .= "</table>";
		
		return $display_table;
	}
	
	
	private function manage_display_layout($layout_type, $item, $barcode_config)
	{
		$result = '';
		
		if($layout_type == 'name')
		{
			$result = $item['name'];
		}
		elseif($layout_type == 'category' && isset($item['category']))
		{
			$result = $item['category'];
		}
		elseif($layout_type == 'cost_price' && isset($item['cost_price']))
		{
			$result = to_currency($item['cost_price']);
		}
		elseif($layout_type == 'unit_price' && isset($item['unit_price']))
		{
			$result = to_currency($item['unit_price']);
		}
		elseif($layout_type == 'company_name')
		{
			$result = $barcode_config['company'];
		}
		elseif($layout_type == 'item_code')
		{
			$result = $barcode_config['barcode_content'] !== "id" && isset($item['item_number']) ? $item['item_number'] : $item['item_id'];
		}
		return character_limiter($result, 25);
	}
	private function manage_display_layout_lens($layout_type, $item, $barcode_config)
	{
		$result = '';
		
		if($layout_type == 'name')
		{
			$result = $item['name'];
		}
		elseif($layout_type == 'category' && isset($item['category']))
		{
			$result = $item['category'];
		}
		elseif($layout_type == 'cost_price' && isset($item['cost_price']))
		{
			$result = to_currency($item['cost_price']);
		}
		elseif($layout_type == 'unit_price' && isset($item['unit_price']))
		{
			$result = to_currency($item['unit_price']);
		}
		elseif($layout_type == 'company_name')
		{
			$result = $barcode_config['company'];
		}
		elseif($layout_type == 'item_code')
		{
			$result = $barcode_config['barcode_content'] !== "id" && isset($item['item_number']) ? $item['item_number'] : $item['item_id'];
		}
		elseif($layout_type == 'location')
		{
			if(!empty($barcode_config['location']))
			{
				$result = $barcode_config['location'];
			} else {
				$result = '';
			}
		}
		return character_limiter($result, 50);
	}
	private function _manage_display_layout($layout_type, $item, $barcode_config)
	{
		$result = '';
		
		if($layout_type == 'name')
		{
			$result = $this->CI->lang->line('items_name') . " " . $item['name'];
		}
		elseif($layout_type == 'category' && isset($item['category']))
		{
			$result = $this->CI->lang->line('items_category') . " " . $item['category'];
		}
		elseif($layout_type == 'cost_price' && isset($item['cost_price']))
		{
			$result = $this->CI->lang->line('items_cost_price') . " " . to_currency($item['cost_price']);
		}
		elseif($layout_type == 'unit_price' && isset($item['unit_price']))
		{
			$result = $this->CI->lang->line('items_unit_price') . " " . to_currency($item['unit_price']);
		}
		elseif($layout_type == 'company_name')
		{
			$result = $barcode_config['company'];
		}
		elseif($layout_type == 'item_code')
		{
			$result = $barcode_config['barcode_content'] !== "id" && isset($item['item_number']) ? $item['item_number'] : $item['item_id'];
		}

		return character_limiter($result, 40);
	}
	public function listfonts($folder) 
	{
		$array = array();

		if (($handle = opendir($folder)) !== false)
		{
			while (($file = readdir($handle)) !== false)
			{
				if(substr($file, -4, 4) === '.ttf')
				{
					$array[$file] = $file;
				}
			}
		}

		closedir($handle);

		array_unshift($array, $this->CI->lang->line('config_none'));

		return $array;
	}

	public function get_font_name($font_file_name)
	{
		return substr($font_file_name, 0, -4);
	}

	public function _display_barcode1($items, $barcode_config) // @gong 1: 2x2x150
	{
		//var_dump($item);die();
		if(count($items)==2) {
			$item = $items[0];
			$item['unit_price'] = $item['price'];
			$barcode_config['barcode_width'] = 0;
			$display_table = "<div class='print-barcode_1'>";
			$display_table .= "<div align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
			$barcode = $this->generate_barcode($item, $barcode_config);
			$display_table .= "<div align='center'><img src='data:image/png;base64,$barcode' /></div></tr>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</b> </div>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</b>- <b class='category-barcode'>".$item['item_category']."</b></div>";
			$display_table .= "</div>";
			$item = $items[1];
			$display_table .= "<div class='print-barcode_2' style='width: 50mm; transform: rotate(180deg); padding-bottom: 1px; border-spacing: 1px; height: 20mm;'>";
			$display_table .= "<div align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
			$barcode = $this->generate_barcode($item, $barcode_config);
			$display_table .= "<div align='center'><img src='data:image/png;base64,$barcode' /></div></tr>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</b> </div>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</b>- <b class='category-barcode'>".$item['item_category']."</b></div>";
			$display_table .= "</div>";
		} else {
			$item = $items[0];
			$item['unit_price'] = $item['price'];
			$barcode_config['barcode_width'] = 0;
			$display_table = "<div class='print-barcode_1'>";
			$display_table .= "<div align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
			$barcode = $this->generate_barcode($item, $barcode_config);
			$display_table .= "<div align='center'><img src='data:image/png;base64,$barcode' /></div></tr>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</b> </div>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</b>- <b class='category-barcode'>".$item['item_category']."</b></div>";
			$display_table .= "</div>";
			$item = $items[0];
			$display_table .= "<div class='print-barcode_2' style='width: 50mm; transform: rotate(180deg); padding-bottom: 1px; border-spacing: 1px; height: 20mm;'>";
			$display_table .= "<div align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config) . "</div>";
			$barcode = $this->generate_barcode($item, $barcode_config);
			$display_table .= "<div align='center'><img src='data:image/png;base64,$barcode' /></div></tr>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config) . "</b> </div>";
			//$display_table .= "<div align='center'><b style='font-size:10px;'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config) . "</b>- <b class='category-barcode'>".$item['item_category']."</b></div>";
			$display_table .= "</div>";
		}
		
		return $display_table;
	}

}

?>