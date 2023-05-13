<?php 
    $config['cacheable']    = true; //boolean, the default is true
    $config['cachedir']     = 'tmp/cache'; //string, the default is application/cache/
    $config['errorlog']     = 'tmp/log'; //string, the default is application/logs/
    $config['imagedir']     = 'tmp/qrcode_images/'; //direktori penyimpanan qr code
    $config['ciqrcodelib'] = 'application/third_party/qrcode';
    $config['quality']      = true; //boolean, the default is true
    $config['size']         = '1024'; //interger, the default is 1024
    $config['black']        = array(224,255,255); // array, default is array(255,255,255)
    $config['white']        = array(70,130,180); // array, default is array(0,0,0)