<?php

ini_set("date.timezone", "America/Argentina/Buenos_Aires");
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

require __DIR__ .  '/vendor/autoload.php';

use App\Core\Boostrap;

// Inicializo mi MVC
$boostrap = new Boostrap();
$boostrap->init();