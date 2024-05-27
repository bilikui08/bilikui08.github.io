<?php

namespace App\Payments;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Aportante;

interface PaymentAdapterInterface
{
	public function __construct($entityManager);
	
	public function process(Aportante $aportante, $monto);
	
	public function success(Request $request);
	
	public function failure(Request $request);
	
	public function pending(Request $request);
	
	public function confirm(Request $request);
	
	public function cancel(Request $request);
}