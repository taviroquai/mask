<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load dependencies
require_once '../vendor/autoload.php';

// Load demo
require_once 'LayoutView.php';

// Run demo
$view = new LayoutView();
echo $view->mask('layout');