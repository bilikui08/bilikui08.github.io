<?php

ini_set("date.timezone", "America/Argentina/Buenos_Aires");

error_reporting(E_ALL);

require_once 'Db.php';

$ahora = new DateTime();

$db = Db::getInstance();

$sql = 'SELECT * FROM sorteo WHERE vigente = 1 ORDER BY id DESC LIMIT 1';

$stmt = $db->prepare($sql);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$sorteo = $stmt->fetch();

if (!$sorteo) {
	// Si no existe un sorteo vigente, lo creo
	
	$sql = "
		INSERT INTO sorteo (fecha, vigente) 
		VALUES (DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 1)
	";
	
	$stmt = $db->prepare($sql);
	$stmt->execute();
	
} else {
	
	$fechaSorteo = new DateTime($sorteo['fecha']);
	if ($ahora > $fechaSorteo) {
		// El sorteo esta vencido, creo uno nuevo
		
		// Primero paso el sorteo vencido, a no vigente
		$sql = "
			UPDATE sorteo
			SET vigente = 0
			WHERE id = {$sorteo['id']}
		";
		
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		// Ahora si, inserto el nuevo sorteo vigente, a sortearse dentro de un mes
		$sql = "
			INSERT INTO sorteo (fecha, vigente) 
			VALUES (DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 1)
		";
		
		$stmt = $db->prepare($sql);
		$stmt->execute();
	}
}

