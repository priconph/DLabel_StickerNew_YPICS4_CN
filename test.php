<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$filename = $_SERVER['REMOTE_ADDR'];
echo './adjust_space/'.$filename.'.php';
echo '<br>';

require_once('./adjust_space/'.$filename.'.php');
echo 'top space: '.$top_space;