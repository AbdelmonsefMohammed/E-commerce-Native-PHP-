<?php
include 'connect.php';
// Routes

$tpl = "includes/templates/";
$css = "layout/css/";
$js = "layout/js/";
$lang = "includes/languages/";
$func = "includes/functions/";

//include the important Files

include $func . "functions.php";
include $lang . "english.php";
include $tpl . 'header.php';

//include nav bar except the page with $nonavbar variable
if(!isset($nonavbar)){include $tpl . 'navbar.php';}