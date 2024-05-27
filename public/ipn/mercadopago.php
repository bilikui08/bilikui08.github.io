<?php 

class mercadopago
{
    const API_BASE_URL = "https://api.mercadopago.com";
    
    public function __construct($userid,$access_token)
    {
        if (isset($userid)) {
            $this->userid = $userid;
        }
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
    }

    public function query($path, $req=array(), $verb='get',$sandbox='false')
    {

        $access_token = $this->access_token;
        $path = "https://api.mercadopago.com/" . $path . "?access_token=" . $access_token;
        $request = $verb;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));
    if ($verb=='post') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        }

        $response = curl_exec($ch);
        $decode = json_decode($response);

        return $decode;

    }


    /**
    * 
    * Nueva funcionalidad para usar con mercado pago
    *
    */

    private static function build_query($params)
    {
        if (function_exists("http_build_query")) {
            return http_build_query($params, "", "&");
        } else {
            foreach ($params as $name => $value) {
                $elements[] = "{$name}=".urlencode($value);
            }
            return implode("&", $elements);
        }
    }


    public function search_payment($request, $offset = 0, $limit = 0)
    {
        $request["offset"] = $offset;
        $request["limit"]  = $limit;
        $request           = array(
            "uri" => "/v1/payments/search",
            "params" => array_merge($request, array(
                "access_token" => $this->access_token
            ))
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    public function balance()
    {
        $userid = $this->userid;
        $request = array(
            "uri" => "/users/$userid/mercadopago_account/balance",
            "params" => array(
                "access_token" => $this->access_token
            )
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    public function getPayment($paymentId)
    {
        if(!isset($paymentId)){
            return false;
        }

        $request           = array(
            "uri" => "/v1/payments/".$paymentId,
            "params" => array("access_token" => $this->access_token)
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    public function search_movement($request, $offset = 0, $limit = 0)
    {
        $request["offset"] = $offset;
        $request["limit"]  = $limit;
        $request           = array(
            "uri" => "/mercadopago_account/movements/search",
            "params" => array_merge($request, array(
                "access_token" => $this->access_token
            ))
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    public function report_list($request, $offset = 0, $limit = 0)
    {
        $request["offset"] = $offset;
        $request["limit"]  = $limit;
        $request           = array(
            "uri" => "/v1/account/settlement_report/list",
            "params" => array_merge($request, array(
                "access_token" => $this->access_token
            ))
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    public function reportFile($file)
    {
        if(!isset($file)){
            return false;
        }

        $request           = array(
            "uri" => "/v1/account/settlement_report/".$file,
            "params" => array("access_token" => $this->access_token)
        );

        $request["method"] = "GET";
        $collection_result = self::exec($request);
        return $collection_result;
    }

    private static function build_request($request)
    {
        if (!extension_loaded("curl")) {
            throw new MercadoPagoException("cURL extension not found. You need to enable cURL in your php.ini or another configuration you have.");
        }
        if (!isset($request["method"])) {
            throw new MercadoPagoException("No HTTP METHOD specified");
        }
        if (!isset($request["uri"])) {
            throw new MercadoPagoException("No URI specified");
        }
        // Set headers
        $headers              = array("accept: application/json");
        $json_content         = true;
        $form_content         = false;
        $default_content_type = true;
        if (isset($request["headers"]) && is_array($request["headers"])) {
            foreach ($request["headers"] as $h => $v) {
                $h = strtolower($h);
                $v = strtolower($v);
                if ($h == "content-type") {
                    $default_content_type = false;
                    $json_content         = $v == "application/json";
                    $form_content         = $v == "application/x-www-form-urlencoded";
                }
                array_push($headers, $h.": ".$v);
            }
        }
        if ($default_content_type) {
            array_push($headers, "content-type: application/json");
        }
        array_push($headers, "x-product-id: BC32A7VTRPP001U8NHK0");
        // Build $connect
        $connect = curl_init();
        
        curl_setopt($connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connect, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($connect, CURLOPT_CUSTOMREQUEST, $request["method"]);
        curl_setopt($connect, CURLOPT_HTTPHEADER, $headers);
        // Set parameters and url
        if (isset($request["params"]) && is_array($request["params"]) && count($request["params"]) > 0) {
            $request["uri"] .= (strpos($request["uri"], "?") === false) ? "?" : "&";
            $request["uri"] .= self::build_query($request["params"]);
        }
        curl_setopt($connect, CURLOPT_URL, self::API_BASE_URL.$request["uri"]);
        // Set data
        if (isset($request["data"])) {
            if ($json_content) {
                if (gettype($request["data"]) == "string") {
                    json_decode($request["data"], true);
                } else {
                    $request["data"] = json_encode($request["data"]);
                }
                if (function_exists('json_last_error')) {
                    $json_error = json_last_error();
                    if ($json_error != JSON_ERROR_NONE) {
                        throw new MercadoPagoException("JSON Error [{$json_error}] - Data: ".$request["data"]);
                    }
                }
            } else if ($form_content) {
                $request["data"] = self::build_query($request["data"]);
            }
            curl_setopt($connect, CURLOPT_POSTFIELDS, $request["data"]);
        }
        return $connect;
    }

    private static function exec($request)
    {
        $connect       = self::build_request($request);
        $api_result    = curl_exec($connect);
        $api_http_code = curl_getinfo($connect, CURLINFO_HTTP_CODE);
        if ($api_result === FALSE) {
            return false;
        }
        $data = json_decode($api_result, true);
        if(!isset($data)){
            $data = $api_result;
        }

        $response = array(
            "status" => $api_http_code,
            "response" => $data
        );

        if ($response['status'] >= 400) {
            $message = $response['response']['message'];
            if (isset($response['response']['cause'])) {
                if (isset($response['response']['cause']['code']) && isset($response['response']['cause']['description'])) {
                    $message .= " - ".$response['response']['cause']['code'].': '.$response['response']['cause']['description'];
                } else if (is_array($response['response']['cause'])) {
                    foreach ($response['response']['cause'] as $causes) {
                        if (is_array($causes)) {
                            foreach ($causes as $cause) {
                                $message .= " - ".$cause['code'].': '.$cause['description'];
                            }
                        } else {
                            $message .= " - ".$causes['code'].': '.$causes['description'];
                        }
                    }
                }
            }
            
        }
        curl_close($connect);
        return $response;
    }
}

class MercadoPagoException extends Exception
{

    public function __construct($message, $code = 500, Exception $previous = null)
    {
        // Default code 500
        parent::__construct($message, $code, $previous);
    }
}