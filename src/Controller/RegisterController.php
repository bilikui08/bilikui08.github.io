<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Payments\MercadoPagoPayment;
use App\Payments\EfectivoPayment;
use App\Payments\TransferenciaPayment;
use App\Entity\Aportante;
use App\Entity\Sorteo;
use App\Entity\Pago;
use App\Entity\TipoPago;
use App\Entity\EstadoPago;

class RegisterController extends Controller
{
	public function register(Request $request)
	{
		$nombre = $request->get('firstName');
		$apellido = $request->get('lastName');
		$dni = $request->get('dni');
		$email = $request->get('email');
		$numeroTelefono = $request->get('numero_telefono');
		$monto = $request->get('monto');
		$terminosCondiciones = $request->get('terminos-condiciones');
		$payment = $request->get('payment'); 
		$token = $request->get('g-recaptcha-response');
		
		$jsonResponse = [];
		
		if ($nombre == null || empty($nombre)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El nombre es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($apellido == null  || empty($apellido)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El apellido es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($dni == null  || empty($dni)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El dni es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($email == null || empty($email)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El email es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($monto == null  || empty($monto)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El monto es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($monto < 200) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El monto tiene que ser mayor a $200.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($terminosCondiciones == null  || !isset($terminosCondiciones)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'Aceptar los terminos y condiciones es obligatorio.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if ($token == null || empty($token)) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'Debes completar el reCAPTCHA.';
			
			return new JsonResponse($jsonResponse);
		}
		
		if (!$this->verificarToken($token, $this->get('google_repatcha_v2_clave_secreta'))) {
			$jsonResponse['status'] = 'nok';
			$jsonResponse['message'] = 'El reCAPTCHA es inválido.';
			
			return new JsonResponse($jsonResponse);
		}
		
		$numeroTelefono = empty($numeroTelefono) ? null : $numeroTelefono;
		
		try {
			
			// Busco el sorteo vigente
			$sorteo = $this->em->getRepository(Sorteo::class)->findOneByVigente(1);
			
			if (!$sorteo) {
				throw new \Exception("No hay un sorteo vigente. Por favor comuniquese con alguno de los referentes de la asociación");
			}
		
			// Creo/Actualizo el aportante
			$aportante = $this->getAportante($dni);
			
			if ($aportante->getId() == null) {
				// Se da de alta los datos, solamente cuando no existe
				$aportante
					->setNombre($nombre)
					->setApellido($apellido)
					->setDni((int)$dni)
					->setEmail($email)
					->setNumeroTelefono($numeroTelefono);

				$sorteo->addAportante($aportante);
			}
					
			$this->em->persist($aportante);
			$this->em->persist($sorteo);
			$this->em->flush(); 
			
			$adapter = new PaymentAdapterController();
			
			if ($payment == 'mepa') {
				$adapter->adaptee(new MercadoPagoPayment($this->em));
			} 
			
			if ($payment == 'efectivo') {
				$adapter->adaptee(new EfectivoPayment($this->em));
			}
			
			if ($payment == 'transferencia') {
				$adapter->adaptee(new TransferenciaPayment($this->em));
			}
			
			$href = $adapter->process($aportante, $monto);
				
			$jsonResponse['status'] = 'ok';
			$jsonResponse['payment'] = $payment;
			$jsonResponse['href'] = $href;
			
			return new JsonResponse($jsonResponse);
			
		} catch (\Exception $e) {
			
			$jsonResponse['status'] = 'nok';
			$jsonResponse['error'] = $e->getMessage();
			
			return new JsonResponse($jsonResponse);
		}
	}
	
	private function getAportante($dni) : Aportante
	{
		$aportante = $this->em->getRepository(Aportante::class)->findOneByDni($dni);
		if ($aportante) {
			return $aportante;
		}
		
		return new Aportante();
	}
	
	private function verificarToken($token, $claveSecreta)
	{
		$url = "https://www.google.com/recaptcha/api/siteverify";
		
		$datos = [
			"secret" => $claveSecreta,
			"response" => $token,
		];
		
		$opciones = array(
			"http" => array(
				"header" => "Content-type: application/x-www-form-urlencoded\r\n",
				"method" => "POST",
				"content" => http_build_query($datos), # Agregar el contenido definido antes
			),
		);
		
		$contexto = stream_context_create($opciones);
		$resultado = file_get_contents($url, false, $contexto);
		
		if ($resultado === false) {
			return false;
		}

		$resultado = json_decode($resultado);
		$pruebaPasada = $resultado->success;
		return $pruebaPasada;
	}
}