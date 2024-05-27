<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Aportante;
use App\Payments\MercadoPagoPayment;
use App\Payments\EfectivoPayment;
use App\Payments\TransferenciaPayment;
use App\Payments\PaymentAdapterInterface;

class PaymentAdapterController extends Controller
{
	private $paymentMethod;
	
	public function adaptee(PaymentAdapterInterface $paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}
	
	public function process(Aportante $aportante, $monto) 
	{
		return $this->paymentMethod->process($aportante, $monto);
	}
	
	public function success(Request $request)
	{
		if (isset($this->parameters['site_id']) && $this->parameters['site_id'] == 'MLA') {
			$this->adaptee(new MercadoPagoPayment($this->em));
		}
		
		$mensajes = $this->paymentMethod->success($request);
		if (!empty($mensajes)) {
			return $this->redirectToRoute('sorteo', ['messages' => $mensajes]);
		}
		
		return $this->redirectToRoute('sorteo');
	}
	
	public function failure(Request $request)
	{	
		if (isset($this->parameters['site_id']) && $this->parameters['site_id'] == 'MLA') {
			$this->adaptee(new MercadoPagoPayment($this->em));
		}
		
		$mensajes = $this->paymentMethod->failure($request);
		if (!empty($mensajes)) {
			return $this->redirectToRoute('sorteo', ['messages' => $mensajes]);
		}
		
		return $this->redirectToRoute('sorteo');
	}
	
	public function pending(Request $request)
	{
		if (isset($this->parameters['site_id']) && $this->parameters['site_id'] == 'MLA') {
			$this->adaptee(new MercadoPagoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'efectivo') {
			$this->adaptee(new EfectivoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'transferencia') {
			$this->adaptee(new TransferenciaPayment($this->em));
		}
		
		$mensajes = $this->paymentMethod->pending($request);
		if (!empty($mensajes)) {
			return $this->redirectToRoute('sorteo', ['messages' => $mensajes]);
		}
		
		return $this->redirectToRoute('sorteo');
	}
	
	public function confirm(Request $request)
	{
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'mepa') {
			$this->adaptee(new MercadoPagoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'efectivo') {
			$this->adaptee(new EfectivoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'transferencia') {
			$this->adaptee(new TransferenciaPayment($this->em));
		}
		
		$mensajes = $this->paymentMethod->confirm($request);
		if (!empty($mensajes)) {
			return $this->redirectToRoute('sorteo', ['messages' => $mensajes]);
		}
		
		return $this->redirectToRoute('sorteo');
	}
	
	public function cancel(Request $request)
	{
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'mepa') {
			$this->adaptee(new MercadoPagoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'efectivo') {
			$this->adaptee(new EfectivoPayment($this->em));
		}
		
		if (isset($this->parameters['payment']) && $this->parameters['payment'] == 'transferencia') {
			$this->adaptee(new TransferenciaPayment($this->em));
		}
		
		$mensajes = $this->paymentMethod->cancel($request);
		if (!empty($mensajes)) {
			return $this->redirectToRoute('sorteo', ['messages' => $mensajes]);
		}
		
		return $this->redirectToRoute('sorteo');
	}
}