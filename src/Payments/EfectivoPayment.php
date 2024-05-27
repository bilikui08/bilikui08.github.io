<?php

namespace App\Payments;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\MercadoPagoService;
use App\Entity\Sorteo;
use App\Entity\Aportante;
use App\Entity\Pago;
use App\Entity\TipoPago;
use App\Entity\EstadoPago;
use App\Utils\Url;
use App\Utils\Notification;

class EfectivoPayment extends AbstractPayment implements PaymentAdapterInterface
{
	public function process(Aportante $aportante, $monto)
	{
		$tipoPago = $this->em->getRepository(TipoPago::class)->find(TipoPago::EFECTIVO);
		$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::PENDIENTE);
		
		$pago = new Pago();
		
		$pago
			->setAportante($aportante)
			->setTipoPago($tipoPago)
			->setEstadoPago($estadoPago)
			->setMonto($monto)
			->setHash($this->getHash($aportante, $monto));
			
		$aportante->addPago($pago);
		
		$this->em->persist($pago);
		$this->em->persist($aportante);

		$this->em->flush(); 
		
		try {
			
			$subject = 'Pago pendiente en efectivo de ' . $aportante->getNombre() . ' ' . $aportante->getApellido();
			$linkConfirmacion = Url::generateUrl('/payment/confirm', ['hash' => $pago->getHash(), 'payment' => 'efectivo']);
			$linkCancelacion = Url::generateUrl('/payment/cancel', ['hash' => $pago->getHash(), 'payment' => 'efectivo']);
			
			$body = "Se encuentra un pago pendiente de cobro en <b>EFECTIVO</b> por la suma de $monto ARS del aportante {$aportante->getNombre()} {$aportante->getApellido()}, cuyo DNI es {$aportante->getDni()}<br/><br/>";
			$body .= "Para confirmar el pago, haga click <a href='$linkConfirmacion' target='_blank'>aquí</a><br/><br/>";
			$body .= "Para cancelar el pago, haga click <a href='$linkCancelacion' target='_blank'>aquí</a>";
			
			Notification::sendMail($subject, $body);
			
		} catch (\Exception $e) {
			$mail = Notification::getMail();
			throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
		}
		
		return Url::generateUrl('/payment/pending', ['payment' => 'efectivo']);
	}
	
	public function success(Request $request)
	{
		
	}
	
	public function failure(Request $request)
	{
		
	}
	
	public function pending(Request $request)
	{	
		$mensaje  = 'El pago esta pendiente de cobro, hasta que no se pague el efectivo a alguno de los referentes de la asociación.<br/><br/>';
		$mensaje .= 'Cuando se complete el pago, se le asignará un número de sorteo.';
		
		$mensajes = ['warning' => $mensaje];
		
		return $mensajes;
	}
}