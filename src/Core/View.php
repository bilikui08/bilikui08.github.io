<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Response;

class View 
{
    private $response;
	private $data;

    public function __construct(Response $response = null)
    {
        $this->response = $response;
    }

    public function render()
    {
        ob_start();
        echo $this->response->getContent();
        $view = ob_get_contents();
        ob_end_clean();
        echo $view;
    }
	
	public function renderViewData()
	{
		ob_start();
        echo $this->getData();
        $view = ob_get_contents();
        ob_end_clean();
        echo $view;
	}
	
	public function setData($data)
	{
		$this->data = $data;
		
		return $this;
	}
	
	public function getData()
	{
		return $this->data;
	}

    public static function renderViewPageNotFound()
    {
        $data = [
            'httpCode' => 404,
            'message' => 'Page not found.'
        ];

        echo json_encode($data);
    }

    public static function renderViewError($message = '')
    {
        $data = [
            'httpCode' => 500,
            'message' => $message != '' ? $message : 'Servidor error.'
        ];
        echo json_encode($data);
    }

    
}