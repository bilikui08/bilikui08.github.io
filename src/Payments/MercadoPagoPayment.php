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

class MercadoPagoPayment extends AbstractPayment implements PaymentAdapterInterface
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';
	const FILE_CREDENTIALS = __DIR__ . '/../../config/credentials.json';
	
	private $externalReference;
	private $aportante;
	private $monto;
	private $numeroOrdenApiExterna;
	
	public function __construct($entityManager)
	{
		parent::__construct($entityManager);
		$this->service = new MercadoPagoService(array_merge($this->config, $this->credentials));
	}
	
	public function process(Aportante $aportante, $monto)
	{
		$this->service->initPreference($aportante, $monto);
		
		return $this->service->initPoint();
	}
	
	public function success(Request $request)
	{
		$status = $request->get('status');
		$mensajes = [];
		
		if ($status == 'approved') {
			
			if (!$this->initValidaciones($request, $mensajes)) {
				return $mensajes;
			}
			
			$tipoPago = $this->em->getRepository(TipoPago::class)->find(TipoPago::MERCADO_PAGO);
			$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::APROBADO);
			
			$pago = new Pago();
			
			$pago
				->setAportante($this->aportante)
				->setTipoPago($tipoPago)
				->setEstadoPago($estadoPago)
				->setMonto($this->monto)
				->setNumeroOrdenApiExterna($this->numeroOrdenApiExterna)
				->setRespuestaApiExterna($request->query->all())
				->setHash($this->getHash($this->aportante, $this->monto));
			
			
			if ($this->aportante->getNumeroSorteo() == null) {
				$numeroSorteo = $this->getNumeroSorteo();
			} else {
				$numeroSorteo = $this->aportante->getNumeroSorteo();
			}
			
			$this->aportante
				->addPago($pago)
				->setNumeroSorteo($numeroSorteo);
				
			$this->em->persist($pago);
			$this->em->persist($this->aportante);
			
			$this->em->flush();
			
			$mensaje = "El pago de $ {$pago->getMonto()} se realizó en forma exitosa.<br/>";
			$mensaje .= "Su número para participar en el sorteo es: <strong>$numeroSorteo</strong>"; 
			
			$mensajes = ['success' => $mensaje];

			$subject = 'Se ha efectuado un pago por Mercado Pago de ' . $this->aportante->getNombre() . ' ' . $this->aportante->getApellido();
			$body = "Se registro un pago por medio de <b>Mercado Pago</b> por la suma de {$this->monto} ARS del aportante {$this->aportante->getNombre()} {$this->aportante->getApellido()}, cuyo DNI es {$this->aportante->getDni()}<br/><br/>";
			$body .= "El número de sorteo del aportante es: {$this->aportante->getNumeroSorteo()}";
			Notification::sendMail($subject, $body);

			$subject = 'Gracias por aportar a Nueva Generación';
			$body = 'Hola ' . $this->aportante->__toString() . '<br/><br/>';
			$body .= "Muchas gracias por aportar a Nueva Generación. <br/><br/>";
			$body .= "Su número de sorteo para participar del premio es <strong>{$this->aportante->getNumeroSorteo()}</strong>.<br/><br/>";
			$body .= "--<br/><br/>";
			$body .= "El equipo de Nueva Generación.";

			$to[0]['email'] = $this->aportante->getEmail();
			$to[0]['alias'] = $this->aportante->__toString();

			Notification::sendMail($subject, $body, $to);
			
			return $mensajes;
		}
	}
	
	public function failure(Request $request)
	{
		$mensajes = [];
		if (!$this->initValidaciones($request, $mensajes)) {
			return $mensajes;
		}
		
		$tipoPago = $this->em->getRepository(TipoPago::class)->find(TipoPago::MERCADO_PAGO);
		$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::RECHAZADO);
		
		$pago = new Pago();
		
		$pago
			->setAportante($this->aportante)
			->setTipoPago($tipoPago)
			->setEstadoPago($estadoPago)
			->setMonto($this->monto)
			->setNumeroOrdenApiExterna($this->numeroOrdenApiExterna)
			->setRespuestaApiExterna($request->query->all())
			->setHash($this->getHash($this->aportante, $this->monto));
		
		$this->aportante->addPago($pago);
			
		$this->em->persist($pago);
		$this->em->persist($this->aportante);
		
		$this->em->flush(); 
		
		$mensajes = ['danger' => 'Hubo un error al procesar el pago. [003 - El pago fue rechazo con Mercado Pago o su entidad financiera. Vuelva a intentarlo]'];
		
		return $mensajes;
	}
	
	public function pending(Request $request)
	{
		$mensajes = [];
		if (!$this->initValidaciones($request, $mensajes)) {
			return $mensajes;
		}
		
		$tipoPago = $this->em->getRepository(TipoPago::class)->find(TipoPago::MERCADO_PAGO);
		$estadoPago = $this->em->getRepository(EstadoPago::class)->find(EstadoPago::PENDIENTE);
		
		$pago = new Pago();
		
		$pago
			->setAportante($this->aportante)
			->setTipoPago($tipoPago)
			->setEstadoPago($estadoPago)
			->setMonto($this->monto)
			->setNumeroOrdenApiExterna($this->numeroOrdenApiExterna)
			->setRespuestaApiExterna($request->query->all())
			->setHash($this->getHash($this->aportante, $this->monto));
		
		$this->aportante->addPago($pago);
			
		$this->em->persist($pago);
		$this->em->persist($this->aportante);
		
		$this->em->flush();
		
			
		$mensaje  = 'El pago esta pendiente de cobro, por su entidad financiera o porque no se ha cobrado el cupón de pago.<br/><br/>';
		$mensaje .= 'En el caso, de que haya seleccionado pagar por Pago Fácil o Rapipago, tenemos que esperar a la confirmación de cobro ';
		$mensaje .= 'de parte de Marcado Pago. Una vez que tengamos la confirmación de pago, recién se le asignará un número de sorteo.<br/>';
		$mensaje .= 'Adicionalmente, para agilizar la verificación, por favor envienos su comprobante de pago, a la casilla <strong>aportes@nuevageneracion.com.ar</strong>';
		
		$mensajes = ['warning' => $mensaje];

		$subject = 'Se ha efectuado un pago pendiente por Mercado Pago de ' . $this->aportante->getNombre() . ' ' . $this->aportante->getApellido();
		$body = "Se registro un pago pendiente de cobro, por medio de <b>Mercado Pago</b> por la suma de {$this->monto} ARS del aportante {$this->aportante->getNombre()} {$this->aportante->getApellido()}, cuyo DNI es {$this->aportante->getDni()}<br/><br/>";
		$body .= "Esto se debe, porque el aportante decidió pagar por un cupón de pago (ya sea Pago Fácil o Rapipago) y todavía no lo ha pagado.<br/><br/>";

		$linkConfirmacion = Url::generateUrl('/payment/confirm', ['hash' => $pago->getHash(), 'payment' => 'mepa']);
		$linkCancelacion = Url::generateUrl('/payment/cancel', ['hash' => $pago->getHash(), 'payment' => 'mepa']);

		$body .= "Para confirmar el pago, haga click <a href=\"$linkConfirmacion\" target=\"_blank\">aquí</a><br/><br/>";
		$body .= "Para cancelar el pago, haga click <a href=\"$linkCancelacion\" target=\"_blank\">aquí</a>";
		
		Notification::sendMail($subject, $body);
		
		return $mensajes;
	}
	
	private function initValidaciones(Request $request, &$mensajes = [])
	{
		$this->setExternalReferenceData($request);
		$this->setNumeroOrdenApiExterna($request);
		
		if (!$this->externalReference && isset($this->externalReference['aportanteId']) && isset($this->externalReference['monto'])) {
			$mensajes = ['danger' => 'Hubo un error al procesar el pago. [001 - No existe la referencia externa de la API]'];
			return false;
		}
		
		if (!$this->aportante) {
			$mensajes = ['danger' => 'Hubo un error al procesar el pago. [002 - No existe el aportante]'];
			return false;
		}
		/*
		$pagoExistente = $this->em->getRepository(Pago::class)->findOneByNumeroOrdenApiExterna($this->numeroOrdenApiExterna);
		if ($pagoExistente) {
			$mensajes = ['danger' => 'Hubo un error al procesar el pago. [003 - El pago ya se ha ingresado]'];
			return false;
		}
		*/
		
		return true;
	}
	
	private function setExternalReferenceData(Request $request)
	{
		$this->externalReference = json_decode($request->get('external_reference'), true);
		$aportanteId = isset($this->externalReference['aportanteId']) ? $this->externalReference['aportanteId'] : null;
		
		if ($aportanteId !== null) {
			$this->aportante = $this->em->getRepository(Aportante::class)->find($aportanteId);
			$this->monto = isset($this->externalReference['monto']) ? $this->externalReference['monto'] : 0;
		}
	}
	
	private function setNumeroOrdenApiExterna(Request $request) 
	{
		$this->numeroOrdenApiExterna = $request->get('merchant_order_id');
	}
}