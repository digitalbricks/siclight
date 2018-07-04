<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once '../sites-config.php';
require_once '../includes/functions.php';

// getting data sent via json POST to this file
$data = json_decode(file_get_contents('php://input'), true);

var_dump($data);