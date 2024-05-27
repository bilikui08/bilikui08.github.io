<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Core\View;

class SeccionesController extends Controller
{
    private $secciones = ['home', 'quienes-somos', 'objetivos', 'mision', 'sorteo', 'ganadores-sorteo'];

    public function index(Request $request = null, array $params = [])
	{
		$messages = [];
		if ($request !== null) {
			$messages = $request->get('messages');
		}

        $seccion = isset($params[0]['seccion']) ? $params[0]['seccion'] : '';

        if ($seccion == 'index') {
            return $this->redirectToRoute('home');
        }

        if (in_array($seccion, $this->secciones)) {
            $view = $seccion . '.php';
            return $this->renderView("Views/secciones/$view", ['messages' => $messages]);
        }

        return View::renderViewPageNotFound();
	}
}