<?php

// error_reporting(E_ALL ^ E_NOTICE);
// ini_set('display_errors', 'On');

define('FILE_CREDENTIALS', dirname(__FILE__) . '/../../config/credentials.json');

require_once(dirname(__FILE__) . '/../crons/Db.php');
require_once 'mercadopago.php';

$json_event = file_get_contents('php://input', true);

var_dump($json_event); exit;

$fecha = new DateTime();
$fecha = $fecha->getTimestamp();
file_put_contents('mercadopago.json', $json_event);
$event = json_decode($json_event);

$credentials = json_decode(file_get_contents(FILE_CREDENTIALS), true);

$mp = new mercadopago(null, $credentials['mepa_access_token']);

if ($event->type == 'payment') {
    
    $payment_info = $mp->query('/v1/payments/'.$event->data->id,null,'get');
    
    if ($payment_info->status == "approved") {

       
    }
}