<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once '../sites-config.php';
require_once '../includes/functions.php';

// getting data sent via json POST to this file
$data = json_decode(file_get_contents('php://input'), true);



$targetFile = '../history/_summary-latest.csv';

// delete old file
if (file_exists($targetFile)) { 
    unlink($targetFile);
}

// create file and open if not already in place
$fh = fopen($targetFile, 'w');

// create table header in CSV
fputcsv($fh,array('System','Sys Ver','PHP Ver','Sat Ver', 'Date', 'Time'));

// - fallbacks
$sys_ver = "n/a";
$php_ver = "n/a";
$sat_ver = "n/a";
$sys = "n/a";

foreach ($data as $item){
    
    $site_name = $item['site_name'];
    
    // getting configuration for this site
    $config = $sites[$site_name];

    // - overwrite fallbacks if there is data
    if(isset($item['sys_ver']) AND $item['sys_ver']!=''){
        $sys_ver = $item['sys_ver'];
    };
    if(isset($item['php_ver']) AND $item['php_ver']!=''){
        $php_ver = $item['php_ver'];
    };
    if(isset($item['sat_ver']) AND $item['sat_ver']!=''){
        $sat_ver = $item['sat_ver'];
    };
    if(isset($item['sys']) AND $item['sys']!=''){
        $sys = $item['sys'];
    } else {
        $sys = $config['sys'];
    };

    // write data to CSV;
    fputcsv($fh,array($sys ,$sys_ver, $php_ver, $sat_ver, date('d.m.Y'), date('H:i:s')));

}

// close file handle
fclose($fh);



var_dump($data);