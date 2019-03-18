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
fputcsv($fh,array('Site','System','Sys Ver','PHP Ver','Sat Ver', 'Date', 'Time'));

// - fallbacks
$sys_ver = "n/a";
$php_ver = "n/a";
$sat_ver = "n/a";
$sys = "n/a";
$date = "n/a";
$time = "n/a";

foreach ($data as $item){
    
    $site_name = $item['site_name'];
    
    // getting configuration for this site
    $config = $sites[$site_name];

    // overwrite fallbacks if there is data
    if(isset($item['sys_ver']) AND $item['sys_ver']!=''){
        $sys_ver = $item['sys_ver'];
    };

    // - PHP version
    if(isset($item['php_ver']) AND $item['php_ver']!=''){
        $php_ver = $item['php_ver'];
    };

    // - satellite version
    if(isset($item['sat_ver']) AND $item['sat_ver']!=''){
        $sat_ver = $item['sat_ver'];
    };

    // - system
    if(isset($item['sys']) AND $item['sys']!=''){
        $sys = $item['sys'];
    } else {
        $sys = $config['sys'];
    };

    // - date
    if(isset($item['date']) AND $item['date']!=''){
        $date = $item['date'];
    } else {
        $date = date('d.m.Y');
    };

    // - time
    if(isset($item['time']) AND $item['time']!=''){
        $time = $item['time'];
    } else {
        $time = date('H:i:s');
    };

    // write data to CSV;
    fputcsv($fh,array($site_name, $sys ,$sys_ver, $php_ver, $sat_ver, $date, $time));

}

// close file handle
fclose($fh);