<?php

namespace App\Payments;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Aportante;
use App\Entity\EstadoPago;
use App\Entity\Pago;
use App\Entity\Sorteo;
use App\Utils\Notification;


abstract class AbstractPayment 
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';
	const FILE_CREDENTIALS = __DIR__ . '/../../config/credentials.json';
	
	protected $em;
	protected $config;
	protected $credentials;
	
	public function __construct($entityManager)
	{
		$this->em = $entityManager;
		$this->config = json_decode(file_get_contents(self::FILE_CONFIG), true);
		$this->credentials = json_decode(file_get_contents(self::FILE_CREDENTIALS), true);
	}

	public function confirm(Request $request) 
	{
		$mensajes = [];
		$hash = $request->get('hash');
		$pago = $this->getPago($hash);
		
		if ($pago == null) {
			$mensaje  = 'El pago no existe o esta dado de baja.';
			$mensajes = ['danger' => $mensaje];
			return $mensajes;
		}
		
		$aportante = $pago->getAportante();
		
		$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::APROBADO);
		$pago->setEstadoPago($estadoPago);
		
		if ($aportante->getNumeroSorteo() == null) {
			$numeroSorteo = $this->getNumeroSorteo();
		} else {
			$numeroSorteo = $aportante->getNumeroSorteo();
		}
		
		$aportante->setNumeroSorteo($numeroSorteo);
		
		$this->em->persist($pago);
		$this->em->persist($aportante);
		$this->em->flush();
		
		$mensaje  = 'El pago se confirmo con éxito. ';
		$mensaje .= "El número de sorteo del aportante es: <strong>{$aportante->getNumeroSorteo()}</strong>"; 
		
		$mensajes = ['success' => $mensaje];

		$subject = 'Gracias por aportar a Nueva Generación';
		$body = 'Hola ' . $aportante->__toString() . '<br/><br/>';
		$body .= "Se ha confirmado su pago, correctamente, muchas gracias por aportar a Nueva Generación. <br/><br/>";
		$body .= "Su número de sorteo para participar del premio es <strong>{$aportante->getNumeroSorteo()}</strong>.<br/><br/>";
		$body .= "--<br/><br/>";
		$body .= "El equipo de Nueva Generación.";

		$to[0]['email'] = $aportante->getEmail();
		$to[0]['alias'] = $aportante->__toString();

		Notification::sendMail($subject, $body, $to);
		
		return $mensajes;
	}
	
	public function cancel(Request $request) 
	{
		$mensajes = [];
		$hash = $request->get('hash');
		$pago = $this->getPago($hash);
		
		if ($pago == null) {
			$mensaje  = 'El pago no existe o esta dado de baja.';
			$mensajes = ['danger' => $mensaje];
			return $mensajes;
		}
		
		$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::RECHAZADO);
		$pago->setEstadoPago($estadoPago);
		
		$this->em->persist($pago);
		$this->em->flush();
		
		$mensaje  = 'El pago se rechazo con éxito. ';
		
		$mensajes = ['success' => $mensaje];
		
		return $mensajes;
	}
	
	protected function getNumeroSorteo($nuevoNumeroSorteo = null)
	{
		$numeroSorteo = rand(1, 999);
		if ($nuevoNumeroSorteo !== null) {
			$numeroSorteo = $nuevoNumeroSorteo;
		}
			
		$sorteo = $this->em->getRepository(Sorteo::class)->findOneByVigente(1);
			
		$existeNumeroSorteo = $this
			->em
			->getRepository(Aportante::class)
			->getNumeroSorteoUnivoco($numeroSorteo, $sorteo);
		
		if ($existeNumeroSorteo) {
			$nuevoNumeroSorteo = rand(1, 999);
			$this->getNumeroSorteo($nuevoNumeroSorteo, $sorteo);
		}
		
		return $numeroSorteo;
	}
	
	protected function getHash(Aportante $aportante, $monto)
	{
		return hash('sha256', $aportante->getNombre() . $aportante->getApellido() . $aportante->getDni() . $aportante->getEmail() . $monto . $this->config['payment_secret']);
	}

	protected function getPago($hash)
	{
		$pago = $this->em->getRepository(Pago::class)->findOneByHash($hash);
		
		if (!$pago) {
			return null;
		}
		
		return $pago;
	}
}