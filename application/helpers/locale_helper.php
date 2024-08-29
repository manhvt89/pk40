<?php

/*
 * Get url tracking shipper
 */

function get_url_tracking_shipping($shipping_key)
{
    $arr = array(
        'vnp'=>'http://www.vnpost.vn/vi-vn/dinh-vi/buu-pham?key=',
        'vtp'=>'https://www.viettelpost.com.vn/Tracking?KEY=',
        'ghn'=>'#'
    );
    if(isset($arr[$shipping_key])) {
        return $arr[$shipping_key];
    }else{
        return '#';
    }
}


/*
 * Get resource to buy
 */

function get_order_completeds($completed)
{
    $completeds = array();

    if($completed == 0)
    {
        $completeds = array(
            1=> 'Đặt hàng',
            -1=>'Hủy đơn'
        );
    }elseif ($completed == 1)
    {
        $completeds = array(
            1=> 'Đặt hàng',
            2=> 'Đang chuyển',
            -1=>'Hủy đơn'
        );
    }elseif ($completed == 2)
    {
        $completeds = array(
            3=> 'Nhận hàng',
            -1=>'Hủy đơn'
        );
    }else{
        $completeds = array(
            4=> 'Hoàn thành',
            -1=>'Hủy đơn'
        );
    }
    return $completeds;
}
function get_status_list()
{
    $CI =& get_instance();
    $arr = array(
        -1=>$CI->lang->line('order_return'),
        0=>$CI->lang->line('order_info'),
        1=>$CI->lang->line('order_ordered'),
        2=>$CI->lang->line('order_shipping'),
        3=>$CI->lang->line('order_received'),
        4=>$CI->lang->line('order_completed')
    );
    return $arr;
}
function get_sources_to_buy()
{
    $sourses = array(
        'fb'=>'facebook',
        'sp'=>'shopee',
        'ad'=>'adayroi',
        'ld'=>'lazada',
    );
    return $sourses;
}


/*
 * Get shiping_methods
 */

function get_shipping_methods()
{
    $shipping_methods = array(
        'vnp'=>'VN POST',
        'vtp'=>'Viettel Post',
        'ghn'=>'Giao Hàng Nhanh'
    );

    return $shipping_methods;
}

/*
 * City list
 */
function get_cities_list()
{
    $cities = array(
        'An Giang',
        'Bà Rịa - Vũng Tàu',
        'Bắc Giang',
        'Bắc Kạn',
        'Bạc Liêu',
        'Bắc Ninh',
        'Bến Tre',
        'Bình Định',
        'Bình Dương',
        'Bình Phước',
        'Bình Thuận',
        'Cà Mau',
        'Cao Bằng',
        'Đắk Lắk',
        'Đắk Nông',
        'Điện Biên',
        'Đồng Nai',
        'Đồng Tháp',
        'Gia Lai',
        'Hà Giang',
        'Hà Nam',
        'Hà Tĩnh',
        'Hải Dương',
        'Hậu Giang',
        'Hòa Bình',
        'Hưng Yên',
        'Khánh Hòa',
        'Kiên Giang',
        'Kon Tum',
        'Lai Châu',
        'Lâm Đồng',
        'Lạng Sơn',
        'Lào Cai',
        'Long An',
        'Nam Định',
        'Nghệ An',
        'Ninh Bình',
        'Ninh Thuận',
        'Phú Thọ',
        'Quảng Bình',
        'Quảng Nam',
        'Quảng Ngãi',
        'Quảng Ninh',
        'Quảng Trị',
        'Sóc Trăng',
        'Sơn La',
        'Tây Ninh',
        'Thái Bình',
        'Thái Nguyên',
        'Thanh Hóa',
        'Thừa Thiên Huế',
        'Tiền Giang',
        'Trà Vinh',
        'Tuyên Quang',
        'Vĩnh Long',
        'Vĩnh Phúc',
        'Yên Bái',
        'Phú Yên',
        'Cần Thơ',
        'Đà Nẵng',
        'Hải Phòng',
        'Hà Nội',
        'TP HCM',
    );
    return $cities;
}

/*
 * Currency locale
 */

function current_language_code()
{
    return get_instance()->config->item('language_code');
}

function current_language()
{
    return get_instance()->config->item('language');
}

function currency_side()
{
    $config = get_instance()->config;
    $fmt = new \NumberFormatter($config->item('number_locale'), \NumberFormatter::CURRENCY);
    $fmt->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $config->item('currency_symbol'));
    return !preg_match('/^¤/', $fmt->getPattern());
}

function quantity_decimals()
{
    $config = get_instance()->config;
    return $config->item('quantity_decimals') ? $config->item('quantity_decimals') : 0;
}

function totals_decimals()
{
	$config = get_instance()->config;
	return $config->item('currency_decimals') ? $config->item('currency_decimals') : 0;
}

function to_currency($number)
{
    return to_decimals($number, 'currency_decimals', \NumberFormatter::CURRENCY);
}

function to_currency_no_money($number)
{
    return to_decimals($number, 'currency_decimals');
}

function to_tax_decimals($number)
{
	// taxes that are NULL, '' or 0 don't need to be displayed
	// NOTE: do not remove this line otherwise the items edit form will show a tax with 0 and it will save it
    if(empty($number))
    {
        return $number;
    }
	
    return to_decimals($number, 'tax_decimals');
}

function to_quantity_decimals($number)
{
    return to_decimals($number, 'quantity_decimals');
}

function to_decimals($number, $decimals, $type=\NumberFormatter::DECIMAL)
{
	// ignore empty strings and return
	// NOTE: do not change it to empty otherwise tables will show a 0 with no decimal nor currency symbol
    if(!isset($number))
    {
        return $number;
    }
	
    $config = get_instance()->config;
    $fmt = new \NumberFormatter($config->item('number_locale'), $type);
	if($decimals == '2')
    {
        $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);
        $fmt->setAttribute(NumberFormatter::DECIMAL_ALWAYS_SHOWN, 2);
        if (empty($config->item('thousands_separator')))
        {
            $fmt->setAttribute(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
        }
        $fmt->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $config->item('currency_symbol'));
    
    } else {
        
        $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $config->item($decimals));
        $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $config->item($decimals));
        if (empty($config->item('thousands_separator')))
        {
            $fmt->setAttribute(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
        }
        $fmt->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $config->item('currency_symbol'));
    }
    return $fmt->format($number);
}

function parse_decimals($number)
{
    // ignore empty strings and return
    if(empty($number))
    {
        return $number;
    }

    $config = get_instance()->config;
    $fmt = new \NumberFormatter( $config->item('number_locale'), \NumberFormatter::DECIMAL );
    if (empty($config->item('thousands_separator')))
    {
        $fmt->setAttribute(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
    }
    return $fmt->parse($number);
}

/*
 * Time locale conversion utility
 */

function dateformat_momentjs($php_format)
{
    $SYMBOLS_MATCHING = array(
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version $1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X'
    );

    return strtr($php_format, $SYMBOLS_MATCHING);
}

function dateformat_bootstrap($php_format)
{
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'dd',
        'D' => 'd',
        'j' => 'd',
        'l' => 'dd',
        'N' => '',
        'S' => '',
        'w' => '',
        'z' => '',
        // Week
        'W' => '',
        // Month
        'F' => 'MM',
        'm' => 'mm',
        'M' => 'M',
        'n' => 'm',
        't' => '',
        // Year
        'L' => '',
        'o' => '',
        'Y' => 'yyyy',
        'y' => 'yy',
        // Time
        'a' => 'p',
        'A' => 'P',
        'B' => '',
        'g' => 'H',
        'G' => 'h',
        'h' => 'HH',
        'H' => 'hh',
        'i' => 'ii',
        's' => 'ss',
        'u' => ''
    );

    return strtr($php_format, $SYMBOLS_MATCHING);
}

function convert_number_to_words($number) {

    $hyphen      = ' ';
    $conjunction = '  ';
    $separator   = ' ';
    $negative    = 'âm ';
    $decimal     = ' phẩy ';
    $dictionary  = array(
        0                   => 'Không',
        1                   => 'Một',
        2                   => 'Hai',
        3                   => 'Ba',
        4                   => 'Bốn',
        5                   => 'Năm',
        6                   => 'Sáu',
        7                   => 'Bảy',
        8                   => 'Tám',
        9                   => 'Chín',
        10                  => 'Mười',
        11                  => 'Mười một',
        12                  => 'Mười hai',
        13                  => 'Mười ba',
        14                  => 'Mười bốn',
        15                  => 'Mười lăm',
        16                  => 'Mười sáu',
        17                  => 'Mười bảy',
        18                  => 'Mười tám',
        19                  => 'Mười chín',
        20                  => 'Hai mươi',
        21                  => 'Hai mốt',
        22                  => 'Hai hai',
        23                  => 'Hai ba',
        24                  => 'Hai bốn',
        25                  => 'Hai lăm',
        26                 => 'Hai sáu',
        27                  => 'Hai bẩy',
        28                  => 'Hai tám',
        29                  => 'Hai chín',
        30                  => 'Ba mươi',
        31                  => 'Ba mốt',
        32                  => 'Ba hai',
        33                  => 'Ba ba',
        34                  => 'Ba bốn',
        35                  => 'Ba lăm',
        36                  => 'Ba sáu',
        37                  => 'Ba bẩy',
        38                  => 'Ba tám',
        39                  => 'Ba chín',
        40                  => 'Bốn mươi',
        41                  => 'Bốn mốt',
        42                  => 'Bốn hai',
        43                  => 'Bốn ba',
        44                  => 'Bốn bốn',
        45                  => 'Bốn lăm',
        46                  => 'Bốn sáu',
        47                  => 'Bốn bẩy',
        48                  => 'Bốn tám',
        49                  => 'Bốn chín',
        50                  => 'Năm mươi',
        51                  => 'Năm mốt',
        52                  => 'Năm hai',
        53                  => 'Năm ba',
        54                  => 'Năm bốn',
        55                  => 'Năm lăm',
        56                  => 'Năm sáu',
        57                  => 'Năm bẩy',
        58                  => 'Năm tám',
        59                  => 'Năm chín',
        60                  => 'Sáu mươi',
        61                  => 'Sáu mốt',
        62                  => 'Sáu hai',
        63                  => 'Sáu ba',
        64                  => 'Sáu bốn',
        65                  => 'Sáu lăm',
        66                  => 'Sáu sáu',
        67                  => 'Sáu bẩy',
        68                  => 'Sáu tám',
        69                  => 'Sáu chín',
        70                  => 'Bảy mươi',
        71                  => 'Bảy mốt',
        72                  => 'Bảy hai',
        73                  => 'Bảy ba',
        74                  => 'Bảy bốn',
        75                  => 'Bảy lăm',
        76                  => 'Bảy sáu',
        77                  => 'Bảy bẩy',
        78                  => 'Bảy tám',
        79                  => 'Bảy chín',
        80                  => 'Tám mươi',
        81                  => 'Tám mốt',
        82                  => 'Tám hai',
        83                  => 'Tám ba',
        84                  => 'Tám tư',
        85                  => 'Tám lăm',
        86                  => 'Tám sáu',
        87                  => 'Tám bẩy',
        88                  => 'Tám tám',
        89                  => 'Tám chín',
        90                  => 'Chín mươi',
        91                  => 'Chín mốt',
        92                  => 'Chín hai',
        93                  => 'Chín ba',
        94                  => 'Chín bốn',
        95                  => 'Chín lăm',
        96                  => 'Chín sáu',
        97                  => 'Chín bẩy',
        98                  => 'Chín tám',
        99                  => 'Chín chín',
        100                 => 'trăm',
        1000                => 'ngìn',
        1000000             => 'triệu',
        1000000000          => 'tỷ',
        1000000000000       => 'nghìn tỷ',
        1000000000000000    => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
// overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 100:
            $string = $dictionary[$number];
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return mb_ucfirst($string,'UTF-8');
}
/*function mb_ucfirst($string, $encoding)
{
    $string = mb_convert_case($string, MB_CASE_LOWER, 'UTF-8');
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}*/

// Lấy tất cả các controllers, các file trong thư mục controller
function get_all_controllers()
{
    $CI =& get_instance();
    $CI->load->helper('file');

    $controllers = get_filenames( APPPATH . 'controllers/' ); 

    foreach( $controllers as $k => $v )
    {
        if( strpos( $v, '.php' ) === FALSE)
        {
            unset( $controllers[$k] );
        }
    }
    return $controllers; //Controllername.php
}

function get_all_actions_of_the_controller($controller) // This is controler like that Controllername.php
{
     //if (!@include(APPPATH . 'controllers/' . $controller)) {
    if (!class_exists(str_replace( '.php', '', $controller ))) {
        include_once APPPATH . 'controllers/' . $controller;
    }
    //}

    //echo str_replace( '.php', '', $controller );
    $methods = get_class_methods( str_replace( '.php', '', $controller ) );
    if($methods == null)
    {
        return array();
    }
    return $methods;
}

function get_all_permissions_of_the_module($module_key) //controllername = module_key
{
    $controller = ucfirst($module_key).'.php';
    $methods = get_all_actions_of_the_controller($controller);
    //var_dump($methods);
    $_astrReturn = array();
    if(count($methods) == 0) return array();
    foreach($methods as $method)
    {
        $_astrReturn[] = $module_key.'_'.$method; //controllername_action
    }
    return $_astrReturn;
}

function get_all_modules()
{
    $_aControllers = get_all_controllers();

    //if($_aControllers) return array();
    $_astrReturn = array();
    foreach($_aControllers as $controller)
    {
        $_astrReturn[] = str_replace('.php','',strtolower($controller));
    }
    return $_astrReturn;
}

function extract_fullname($sFullName = '')
{
    $_sFullName = normalizeFullName($sFullName); // Chuẩn họ và tên người Việt    
    $_aWords = explode(' ',$_sFullName);
    $_sFirstName = array_pop($_aWords);
    $_sLastName = implode(' ',$_aWords);
    return array(
        'firstname'=>$_sFirstName,
        'lastname'=>$_sLastName
    );
}

function get_fullname($firstname='',$lastname='')
{
    $_sFullName = $lastname .' '.$firstname;
    $_sFullName = trim($_sFullName, ' '); // bỏ khoảng trắng trước và sau chuỗi
    $_sFullName = preg_replace('/\s+/', ' ', $_sFullName); // loại bỏ khoảng trắng thừa trong

    return normalizeFullName($_sFullName);
}

function normalizeFullName($fullName) {
    // Chuyển đổi chữ đầu của mỗi từ thành chữ hoa
    $normalized = ucwords(strtolower($fullName));
    $normalized = trim($normalized, ' '); // bỏ khoảng trắng trước và sau chuỗi
    // Thay thế các khoảng trắng liên tục bằng một khoảng trắng duy nhất
    $normalized = preg_replace('/\s+/', ' ', $normalized);

    return $normalized;
}
/**
 * Summary of chuan_hoa_array
 * @param mixed $a
 * @return array<int>
 */
function chuan_hoa_array($a,$field)
{
	if(!is_array($a))
		return array();
	$b = $a;
	$aR = array();
	$i = 0;
	$n =0;
	foreach($a as $k=>$v)
	{
		$i++;
		
		unset($b[$k]);
		foreach($b as $key=>$value)
		{
			if(($value[$field] === $v[$field]))
			{
				$_sK = strval($b[$key][$field]);
				$aR[$_sK] = 0;
				unset($b[$key]);
			}
		
			$n++;
		}
	}
	return $aR;
}
/**
 * Summary of check_dup
 * @param mixed $a
 * @return array
 */
function check_dup(&$a, $field)
{
	if(!is_array($a)) 
		return array();
	$_aR = chuan_hoa_array($a, $field);
    if(empty($_aR))
    {
        return true; //không $field trùng
    }
	foreach($_aR as $k=>$v)
	{
		foreach($a as $key=>$value)
		{
            $_theItem = &$a[$key];            
			if((string)$value[$field] === (string)$k)
			{
                if ($_theItem['status'] != 9) {
                    $_theItem['status'] = 6;
                }
			}
		}
	}
	return false; // có trùng và đã xử lý
}
/**
 * Summary of isEmptyRow  check row in excel is empty
 * @param array $row
 * @param int $maxCols
 * @return bool
 */
function isEmptyRow($row,$maxCols) {
    for($i =0; $i < $maxCols; $i++){

        if ('' !== $row[$i]) return false;
    }
    return true;
}

function extract_price_excel_to_vnd($value)
{
    //1 remove .00
    $_aTmp = explode('.', $value);
    $_strTmp = $_aTmp[0];
    //2 replace ',' to ''
    $_strTmp = str_replace(',', '', $_strTmp);
    if(is_numeric($_strTmp))
    {
        return (int) $_strTmp;
    } else {
        return 0;
    }
}



function vn_str_filter ($str){
    $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd'=>'đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D'=>'Đ',
        'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
        'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );
   foreach($unicode as $nonUnicode=>$uni){
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
   }
return $str;
}

function print_barcode_gong_2x105($items,$barcode_config)
{
    $CI =& get_instance();
    
    $_sHtml = '<div style=" width: '.$barcode_config['barcode_page_width'].'mm; margin:auto; ">';
    //$_sHtml = $_sHtml . '<div class="print-page-barcode">';
	
    if (!empty($items)) {
			$count = 0;
	        $columns = 2;
			foreach ($items as $item) {
				if ($count % $columns == 0 and $count != 0) {
					
					$_sHtml = $_sHtml . '<div class="pagebreak"></div>';
						
				}
				if($count % $columns == 0){
                    $_sHtml = $_sHtml . '<div class="2" style=" width: '.$barcode_config['barcode_width'].'mm; text-align: center;float: left; margin:0mm 0mm 0mm 1mm;">';
					$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode($item, $barcode_config);
					$_sHtml = $_sHtml .'</div>';
				} else {
					$_sHtml = $_sHtml . '<div class="1" style=" width: '.$barcode_config['barcode_width'].'mm; text-align: center;float: left; margin:0mm 0mm 0mm '.$barcode_config['barcode_page_cellspacing'].'mm;">';
					$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode($item, $barcode_config); 
					$_sHtml = $_sHtml .'</div>';

				} 
			
				$count++;
			}
		} else { 
		    $_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
		}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}

function print_barcode_gong_1x75($items,$barcode_config)
{
    $CI =& get_instance();
    $_sHtml = '<div style=" width: 52mm; margin:auto; border: green 0px solid; overflow: hidden;">';

    if (!empty($items)) {
			$count = 0;
			foreach ($items as $item) {

                $_sHtml = $_sHtml . '<div class="2" style="border: green 0px solid;  width: 52mm; height:35mm; text-align: center; transform: rotate(0deg);margin:0mm 0mm 0mm 0mm;">';
                $_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode_1x75($item, $barcode_config);
                $_sHtml = $_sHtml .'</div>';
                $_sHtml = $_sHtml . '<div style="clear:right"></div><div class="pagebreak"></div>';
			
				$count++;
			}
		} else { 
		    $_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
		}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}

function print_barcode_mat_2x75($items,$barcode_config)
{
    $CI =& get_instance();
    $_sHtml = '<div style=" width: 75mm; margin:auto; ">';

	if (!empty($items)) {
		$count = 0;
	  	foreach($items as $item)
		{ 
			if ($count % 2 == 0 and $count != 0)
			{
				$_sHtml = $_sHtml . '<div class="pagebreak"></div>';	
			}
			//$_sHtml = $_sHtml . '<div class="2" style=" width: 33mm; text-align: center;float: left; margin:0px; padding: 0mm 1mm 0mm 2mm ">';
            $_sHtml = $_sHtml . '<div class="2" style=" width: 35mm; text-align: center;float: left; margin:0mm 1mm 0mm 1mm; padding: 0mm 0mm 0mm 0mm ">';
			$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode_lens($item, $barcode_config);
			$_sHtml = $_sHtml . '</div>';
		    ++$count; 
		} 
	} else {
		$_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
	}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}
function print_barcode_mat_3x105($items,$barcode_config)
{
    $CI =& get_instance();
    $_sHtml = '<div style=" width: '.$barcode_config['barcode_page_width'].'mm; margin:auto; ">';

	if (!empty($items)) {
		$count = 0;
	  	foreach($items as $item)
		{ 
			if ($count % 3 == 0 and $count != 0)
			{
				$_sHtml = $_sHtml . '<div class="pagebreak"></div>';	
			}
			$_sHtml = $_sHtml . '<div class="2" style=" width: '.$barcode_config['barcode_width'].'mm; text-align: center;float: left; margin:0px; ">';
			$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode_lens($item, $barcode_config);
			$_sHtml = $_sHtml . '</div>';
		    ++$count; 
		} 
	} else {
		$_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
	}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}
function print_barcode($items,$type,$barcode_config)
{
    switch ($type)
    {
        case "M2X75":
            print_barcode_mat_2x75($items,$barcode_config);
            break;
        case "M3X105":
            print_barcode_mat_3x105($items,$barcode_config);
            break;
        case "G1X75":
            print_barcode_gong_1x75($items,$barcode_config);
            break;
        case "G2X105":
            print_barcode_gong_2x105($items,$barcode_config);
            break;
        case "G2X2X105":
            print_barcode_gong_2x2x105($items,$barcode_config);
            break;
        case "T3X105":
                print_barcode_thuoc_3x105($items,$barcode_config);
                break;
        default: 
            break;
    }
}
function print_barcode_gong_2x2x105($items,$barcode_config)
{
  
    $CI =& get_instance();
    
    $_sHtml = '<div style=" width: 105mm; margin:auto; ">';
    $_sHtml = $_sHtml . '<div class="print-page-barcode">';
	
    if (!empty($items)) {
			$count = 0;
            $index = 0;
	        $columns = 2;
            $_aItems = [];
            

            foreach ($items as $item) {
                
                if ($count % $columns == 0 and $count != 0) {
					$index++;
				}
                $_aItems[$index][] = $item;
                $count++;
            }
            $count = 0;
			foreach ($_aItems as $item) {
				if ($count % $columns == 0 and $count != 0) {
					
					$_sHtml = $_sHtml . '<div class="pagebreak"></div>';
						
				}
				if($count % $columns == 0){
                    $_sHtml = $_sHtml . '<div class="2" style=" width: 50mm; text-align: center;float: left; margin:0mm 0mm 0mm 1mm;">';
					$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode1($item, $barcode_config);
					$_sHtml = $_sHtml .'</div>';
				} else {
					$_sHtml = $_sHtml . '<div class="1" style=" width: 50mm; text-align: center;float: left; margin:0mm 0mm 0mm 2mm;">';
					$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode1($item, $barcode_config); 
					$_sHtml = $_sHtml .'</div>';

				} 
			
				$count++;
			}
		} else { 
		    $_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
		}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}

function print_barcode_thuoc_3x105($items,$barcode_config)
{
    $CI =& get_instance();
    $_sHtml = '<div style=" width: '.$barcode_config['barcode_page_width'].'mm; margin:auto; ">';

	if (!empty($items)) {
		$count = 0;
	  	foreach($items as $item)
		{ 
			if ($count % 3 == 0 and $count != 0)
			{
				$_sHtml = $_sHtml . '<div class="pagebreak"></div>';	
			}
			$_sHtml = $_sHtml . '<div class="2" style=" width: '.$barcode_config['barcode_width'].'mm; text-align: center;float: left; margin:7px 0 0 0; ">';
			$_sHtml = $_sHtml . $CI->barcode_lib->_display_barcode_thuoc($item, $barcode_config);
			$_sHtml = $_sHtml . '</div>';
		    ++$count; 
		} 
	} else {
		$_sHtml = $_sHtml . 'Hiện tại chưa có sản phẩm nào để in barcode, vui lòng chọn sản phẩm để in.';
	}
    $_sHtml = $_sHtml .'</div>';
    echo $_sHtml;
}

/**
 * Summary of has_grant
 * @param mixed $str cấu trúc {module_id}_{str}
 * @return mixed
 */
function has_grant($str, $module_id='')
{
    $CI =& get_instance();
    //echo $CI->module_id; die();
    if($module_id == '')
    {
        $str = $CI->module_id . "_".$str;
    } else {
        $str = $module_id . "_".$str;
    }
    return $CI->Employee->has_grant($str);
}

function MakeExludeModules($exludeModule,$moduleName)
{
    if(empty($exludeModule))
    {
        return [];
    }
    $_aTheReturn = [];
    foreach($exludeModule as $k=>$v)
    {
        $_aTheReturn[] = $moduleName.'_'.$v;
    }

    return $_aTheReturn;
}

// Hàm callback để kiểm tra phần tử của mảng 1 với mảng 2
function matchWithWildcards($value, $patterns) {
    foreach ($patterns as $pattern) {
        // Sử dụng fnmatch để so sánh với wildcards
        if (fnmatch($pattern, $value)) {
            return true;
        }
    }
    return false;
}
/**
 * Đầu vào là danh sách các sản phẩm lens,
 * Đầu ra là mảng 2 chiều [SPH][CYL]
 */
function transform2Matrix($items)
{
    if(empty($items))
    {
        return [];
    }

    $CI =& get_instance();
    $report_data = $items;
    $map = $CI->config->item('map');

    $re_map = $CI->config->item('mysphs');

    $grid_data = array();
    $myopia = array(); //can
    $hyperopia = array(); //vien
    foreach ($report_data as $item)
    {
        //var_dump($item);
        if(empty($item['name']))
        {
            $name = $item['item_name'];
        } else {
            $name = $item['name'];
        }
        $arr_name = explode(' ',$name);

        if(count($arr_name) > 2) {
            $ct = strtoupper($arr_name[count($arr_name)-1]);
            $ct = str_replace('C','',$ct);

            $st = strtoupper($arr_name[count($arr_name)-2]);
            $st = str_replace('S','',$st);
            $sph = $st;
            $cyl = $ct;
            $cyl = str_replace('-','',$cyl);
            if(strpos($sph,'-')===0) //Độ cận
            {
                $sph = str_replace('-','',$sph);
                if(isset($map[$sph]) && isset($map[$cyl])) {
                    $s = $map[$sph];
                    $c = $map[$cyl];
                    $myopia[$s][$c] = number_format($item['item_quantity']);
                    if ($myopia[$s][$c] <= 0) {
                        $myopia[$s][$c] = '';
                    }
                }else{
                    echo $sph . '|'.$cyl .'-> - ' . $item['item_number'];
                }
                //$myopia[] = $map[$sph];

            }else{
                $sph = str_replace('+','',$sph);
                if(isset($map[$sph]) && isset($map[$cyl])) {
                    $s = $map[$sph];
                    $c = $map[$cyl];

                    $hyperopia[$s][$c] = number_format($item['item_quantity']);
                    if ($hyperopia[$s][$c] <= 0) {
                        $hyperopia[$s][$c] = '';
                    }
                }else{
                    echo $sph . '|'.$cyl.'-> +' . $item['item_number'];
                }
            }
        }

    }
    //var_dump($myopia);die();

    for($i =0;$i < count($re_map);$i++)
    {
        for($j =0;$j<26;$j++)
        {
            if(!isset($myopia[$i][$j]))
            {
                $myopia[$i][$j] = '';
            }else{

            }
            if(!isset($hyperopia[$i][$j]))
            {
                $hyperopia[$i][$j]='';
            }
        }
    }
    $sub_myopia = array();
    $sub_hyperopia = array();
    $sub_group = array();
    $total = 0;
    for($i =1;$i<10;$i++)
    {
        $sub_myopia[$i] = 0;
        for($j =1;$j<count($re_map);$j++)
        {

            if($myopia[$j][$i] !='') {
                $sub_myopia[$i] = $sub_myopia[$i] + $myopia[$j][$i];
            }
        }
    }
    //var_dump($myopia);
    $sub_group[0] =0;
    for($i = 1;$i<26;$i++)
    {
        for($j=1;$j<10;$j++)
        {
            if($myopia[$i][$j] !='') {
                $sub_group[0] = $sub_group[0] + $myopia[$i][$j];
            }
        }
    }

    $sub_group[1] =0;
    for($i = 26;$i<34;$i++)
    {
        for($j=1;$j<10;$j++)
        {
            if($myopia[$i][$j] !='') {
                $sub_group[1] = $sub_group[1] + $myopia[$i][$j];
            }
        }
    }
    $sub_group[2] =0;
    for($i = 34;$i<56;$i++)
    {
        for($j=1;$j<10;$j++)
        {
            if($myopia[$i][$j] !='') {
                $sub_group[2] = $sub_group[2] + $myopia[$i][$j];
            }
        }
    }

    $sub_group[3] =0;
    for($i = 1;$i<26;$i++)
    {
        for($j=1;$j<10;$j++)
        {
            if($hyperopia[$i][$j] !='') {
                $sub_group[3] = $sub_group[3] + $hyperopia[$i][$j];
            }
        }
    }

    $sub_group[4] =0;
    for($i = 1;$i<42;$i++)
    {
        for($j=10;$j<14;$j++)
        {
            if($myopia[$i][$j] !='') {
                $sub_group[4] = $sub_group[4] + $myopia[$i][$j];
            }
        }
    }
    $sub_group[5] =0;
    for($i = 1;$i<42;$i++)
    {
        for($j=14;$j<18;$j++)
        {
            if($myopia[$i][$j] !='') {
                $sub_group[5] = $sub_group[5] + $myopia[$i][$j];
            }
        }
    }

    $sub_group[6] =0; //do nothing

    $sub_group[7] =0;
    for($i = 2;$i<9;$i++)
    {
        for($j=10;$j<18;$j++)
        {
            if($hyperopia[$i][$j] !='') {
                $sub_group[7] = $sub_group[7] + $hyperopia[$i][$j];
            }
        }
    }

    for($i =1;$i<10;$i++)
    {
        $sub_hyperopia[$i] = 0;
        for($j =1;$j<26;$j++)
        {

            if($hyperopia[$j][$i] !='') {
                $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
            }
        }
    }

    for($i =10;$i<18;$i++)
    {
        $sub_hyperopia[$i] = 0;
        for($j =1;$j<9;$j++)
        {

            if($hyperopia[$j][$i] !='') {
                $sub_hyperopia[$i] = $sub_hyperopia[$i] + $hyperopia[$j][$i];
            }
        }
    }
    foreach($myopia as $k=>$v)
    {
        //$item = $v;
        ksort($v);
        $v[0] = $re_map[$k];
        $myopia[$k] = $v;
    }
    ksort($myopia);
    
    foreach($hyperopia as $k=>$v)
    {
        //$item = $v;
        ksort($v);
        $v[0] = $re_map[$k];
        $hyperopia[$k] = $v;
    }
    ksort($hyperopia);

    unset($myopia[0]);

    // Sắp xếp lại mảng
    $myopia = array_values($myopia);

    unset($hyperopia[0]);

    // Sắp xếp lại mảng
    $hyperopia = array_values($hyperopia);

    //array_shift($myopia);
    //array_shift($hyperopia);
    $total = array_sum($sub_group);
    //$data['total'] = $total;
    //$data['map'] = $map;
    //$data['re_map'] = $re_map;
    //remove the first row
    //array_shift($myopia);
    //array_shift($hyperopia);
    //var_dump($myopia);
    
    $data['myopia'] = $myopia;
    $data['hyperopia'] = $hyperopia;

    return $data;
}
/**
 * Kiểm tra mảng 2 chiều;
 * Nếu tất các  phần từ nếu có giá trị empty hoặc "" return true;
 * Loại trừ cột đầu tiên
 */
function is_empty_array($items)
{
    
    if(empty($items))
    {
        return true;
    }
    foreach ($items as $key=>$item)
    {
        foreach($item as $k=>$v)
        {
            if((trim($v) != '') && ($k > 0))
            {
                //echo $v;
                return false;
            } 

        }
    }
    return true;
}

function debug_log( $object = null, $name='' ) {
    
    $config = get_instance()->config;
    if(! $config->item('debugging_mode'))
    {
        return;
    }

    $debug_file = get_debug_log_filename();

    // add timestamp and newline
    $message = '[' . date( 'Y-m-d H:i:s' ) . '] ';

    $trace = debug_backtrace();
    if ( isset( $trace[0]['file'] ) ) {
        $file = basename( $trace[0]['file'] );
        if ( isset( $trace[0]['line'] ) ) {
            $file .= ':' . $trace[0]['line'];
        }
        if($name != '')
        {
            $message .= '[' . $file . '] ; Var '.$name . ': ';
        } else {
            $message .= '[' . $file . '] ';
        }
    }

    $contents = get_contents_from_object( $object );

    // get message onto a single line
    $contents = preg_replace( "/\r|\n/", "", $contents );

    $message .= $contents . "\n";

    // log the message to the debug file instead of the usual error_log location
    //echo $message;
    error_log( $message, 3, $debug_file );
}

/**
	 * Return the filename for the debug log
	 * @return string Filename for the debug log
 */
function get_debug_log_filename() {
    // Get directories.
    $uploads_dir       = APPPATH;
    $simply_static_dir = $uploads_dir . 'logs' . DIRECTORY_SEPARATOR;
    // Set name for debug file.
    return $simply_static_dir . 'debug.txt';
    
}

/**
 * Get contents of an object as a string
 *
 * @param mixed $object Object to get string for
 *
 * @return string         String containing the contents of the object
 */
function get_contents_from_object( $object ) {
    if ( is_string( $object ) ) {
        return $object;
    }

    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}

/**
 * Clear the debug log
 * @return void
 */
function clear_debug_log() {
    $debug_file = get_debug_log_filename();
    if ( file_exists( $debug_file ) ) {
        // Clear file
        file_put_contents( $debug_file, '' );
    }
}

function to_upper($str){
    return mb_strtoupper($str, 'UTF-8');
}

function make_diff_first($array)
{
    
    $_array_first = [];
    $_array_second = [];
    foreach($array as $key=>$value)
    {
        if(($value['in_whs_quantity'] - $value['quantity']) == 0)
        {
            $_array_second[] = $value;
        } else {
            $_array_first[] = $value;
        }
    }
    
    
    return array_merge($_array_first,$_array_second);
}
?>
