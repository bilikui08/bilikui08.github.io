<?php

namespace App\Services;

use MercadoPago\SDK;
use MercadoPago\RestClient;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payer;
use App\Entity\Aportante;

class MercadoPagoService extends SDK
{
	private $preference;
	private $env;
	private $params;
	
	public function __construct(array $parameters = [])
	{
		$this->params = $parameters;
		$this->env = $parameters['enviroment'];
	}
	
	public function auth()
	{
		self::$_restClient = new RestClient();
		if ($this->env == 'dev') {	
			self::$_restClient->setHttpParam('use_ssl', ' ');
		}
		
		SDK::setAccessToken($this->params['mepa_access_token']);
	}
	
	public function initPreference(Aportante $aportante, $amount)
	{
		try {
			
			$this->auth();
			
			// Creo el pagador
			$payer = new Payer();
			$payer->id = $aportante->getId();
			$payer->name = $aportante->getNombre();
			$payer->surname = $aportante->getApellido();
			$payer->email = $aportante->getEmail();

			// Crea el item
			$item = new Item();
			$item->title = 'Aporte a Nueva Generación';
			$item->quantity = 1;
			$item->unit_price = (float) $amount;
			
			$externalReference = [
				'aportanteId' => $aportante->getId(),
				'monto' => $amount, 
				'payment' => 'mepa'
			];
			
			// Creo la preferencia
			$this->preference = new Preference();
			$this->preference->payer = $payer;
			$this->preference->items = [$item];
			$this->preference->external_reference = json_encode($externalReference);
			$this->preference->back_urls = [
				'success' => $this->params['payment_success_page'],
				'failure' => $this->params['payment_failure_page'],
				'pending' => $this->params['payment_pending_page'],
			];
			$this->preference->auto_return = 'approved';

			$this->preference->save();
			
		} catch(\Exception $e) {
			throw new \Exception("Error en la API de Mercado Pago - [{$e->getMessage()}]");
		}
	}
	
	public function getPreference()
	{
		return $this->preference;
	}
	
	public function initPoint()
	{
		return ($this->env == 'dev') ? $this->preference->sandbox_init_point : $this->preference->init_point;
	}
}