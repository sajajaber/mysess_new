<?php
 
session_start();
 
require __DIR__ . '/app/core/init.php';
 
$app = new App();
$app->loadController();