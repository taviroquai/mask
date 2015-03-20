<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load dependencies
require_once '../vendor/autoload.php';

// Tell what we will use
use Taviroquai\Mask\Mask;

// Set configuration
Mask::$templateCachePath = './cache';
Mask::$templatePath = './templates';

// Load demo
require_once 'DemoView.php';

// Run demo
$view = new LayoutView();
echo $view->mask('layout');