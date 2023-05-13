<?php defined('BASEPATH') OR exit('No direct script access allowed');
$config['cyls'] = array(
                            '-',
                            '0.00',
                            '0.25',
                            '0.50',
                            '0.75',
                            '1.00',
                            '1.25',
                            '1.50',
                            '1.75',
                            '2.00',
                            '2.25',
                            '2.50',
                            '2.75',
                            '3.00',
                            '3.25',
                            '3.50',
                            '3.75',
                            '4.00',
                            '4.25',
                            '4.50',
                            '4.75',
                            '5.00',
                            '5.25',
                            '5.50',
                            '5.75',
                            '6.00',

                        );
$config['mysphs'] = array(
                            '-',
                            '0.00',
                            '0.25',
                            '0.50',
                            '0.75',
                            '1.00',
                            '1.25',
                            '1.50',
                            '1.75',
                            '2.00',
                            '2.25',
                            '2.50',
                            '2.75',
                            '3.00',
                            '3.25',
                            '3.50',
                            '3.75',
                            '4.00',
                            '4.25',
                            '4.50',
                            '4.75',
                            '5.00',
                            '5.25',
                            '5.50',
                            '5.75',
                            '6.00',
                            '6.25',
                            '6.50',
                            '6.75',
                            '7.00',
                            '7.25',
                            '7.50',
                            '7.75',
                            '8.00',
                            '8.25',
                            '8.50',
                            '8.75',
                            '9.00',
                            '9.25',
                            '9.50',
                            '9.75',
                            '10.00',
                            '10.25',
                            '10.50',
                            '10.75',
                            '11.00',
                            '11.25',
                            '11.50',
                            '11.75',
                            '12.00',
                            '12.25',
                            '12.50',
                            '12.75',
                            '13.00',
                            '13.25',
                            '13.50',
                            '13.75',
                            '14.00',
                            '14.25',
                            '14.50',
                            '14.75',
                            '15.00',
                        );
$config['hysphs'] = array(
                            '-',
                            '0.00',
                            '0.25',
                            '0.50',
                            '0.75',
                            '1.00',
                            '1.25',
                            '1.50',
                            '1.75',
                            '2.00',
                            '2.25',
                            '2.50',
                            '2.75',
                            '3.00',
                            '3.25',
                            '3.50',
                            '3.75',
                            '4.00',
                            '4.25',
                            '4.50',
                            '4.75',
                            '5.00',
                            '5.25',
                            '5.50',
                            '5.75',
                            '6.00',
                            '6.25',
                            '6.50',
                            '6.75',
                            '7.00',
                            '7.25',
                            '7.50',
                            '7.75',
                            '8.00',
                        );

$config['exclude_module'] = array(
    'secure_controller',
    'no_access',
    'login',
    'testex',
    'home',
    'persons',
    'cron',
    'verify'
);
/*
** config qrcode; = 0 don't active qrcode; 
** = 1: Active module qrcode;
*/
$config['qrcode'] = 1;
$config['barcode'] = 1;

/*
Báo cáo Khác;
*/
/*
$config['filter'] = array('GONG 1T','GONG 2T','GONG 3T','GONG 4T','GONG 5T','GONG 5T+','G07','G08','G09','G10','G11','G12','G13','G14','G15',
        'G01',
        'G02',
        'G03',
        'G04',
        'G05',
        'G06'
        );

$config['filter_sun_glasses'] = array('MAT 1T','MAT 2T','MAT 3T','MAT 4T','MAT 5T','MAT 5T+','MAT 6T','M01','M02','M03','M04','M05','M06','M07','M08','M09','M10','M11','M12','M13','VẬT TƯ','M.HOYA','G.CHEMI',
      'Ngâm-Nhỏ',
      'Lens Seed 1M Trong',
        'Lens Seed 1M Pure',
        'Lens Seed 1D Rich',
        'Lens Seed 1D Base',
        'Lens Seed 1D Pure',
        'CLALEN 1D Latin',
        'CLALEN 1D Alica Brown',
        'CLALEN 1D Soul Brown',
        'CLALEN 1D Suzy Gray',
        'Lens Biomedics 1D',
        'Lens Biomedics55 3M'
    );

$config['filter_contact_lens'] = array(
        'Ngâm-Nhỏ',
        'Lens Seed 1M Trong',
        'Lens Seed 1M Pure',
        'Lens Seed 1D Rich',
        'Lens Seed 1D Base',
        'Lens Seed 1D Pure',
        'CLALEN 1D Latin',
        'CLALEN 1D Alica Brown',
        'CLALEN 1D Soul Brown',
        'CLALEN 1D Suzy Gray',
        'Lens Biomedics 1D',
        'Lens Biomedics55 3M');
        */

//TRạng thái PO

$config['caPOStatus'] = array(
    0=>'Mới tạo', // Có thể sửa
    1=>'Yêu cầu sửa lại', // Có thể sửa
    2=>'Đang chờ phê duyệt',
    3=>'Đã phê duyệt',
    4=>'Đã nhập hàng'
);

$config['default_city'] = 'Hà Nội';

        
