<?php

class mercadoLivreActions {

    /**
	 * @version 0.0.1
	 */
    const VERSION  = "0.0.1";

    /**
     * @var $AUTH_URL é uma URL para redirecionar o usuário para login.
     * @var $SITE_ID identifica o pais, é usado em algumas chamadas.
     */
    protected static $OAUTH_URL    = "/oauth/token";
    public static $AUTH_URL = array(
        "MLA" => "https://auth.mercadolibre.com.ar", // Argentina 
        "MLB" => "https://auth.mercadolivre.com.br", // Brasil
        "MCO" => "https://auth.mercadolibre.com.co", // Colombia
        "MCR" => "https://auth.mercadolibre.com.cr", // Costa Rica
        "MEC" => "https://auth.mercadolibre.com.ec", // Ecuador
        "MLC" => "https://auth.mercadolibre.cl", // Chile
        "MLM" => "https://auth.mercadolibre.com.mx", // Mexico
        "MLU" => "https://auth.mercadolibre.com.uy", // Uruguay
        "MLV" => "https://auth.mercadolibre.com.ve", // Venezuela
        "MPA" => "https://auth.mercadolibre.com.pa", // Panama
        "MPE" => "https://auth.mercadolibre.com.pe", // Peru
        "MPT" => "https://auth.mercadolibre.com.pt", // Portugal
        "MRD" => "https://auth.mercadolibre.com.do"  // Dominicana
    );
    public static $SITE_ID = array(
        "AR" =>"MLA", // Argentina 
        "BR" =>"MLB", // Brasil
        "CO" =>"MCO" , // Colombia
        "CR" =>"MCR" , // Costa Rica
        "EC" =>"MEC", // Ecuador
        "CL" =>"MLC", // Chile
        "MX" =>"MLM", // Mexico
        "UY" =>"MLU", // Uruguay
        "VE" =>"MLV", // Venezuela
        "PA" =>"MPA", // Panama
        "PE" =>"MPE", // Peru
        "PT" =>"MPT", // Portugal
        "DO" =>"MRD"  // Dominicana
    );
    /**
     * @var $API_ROOT_URL é uma URL principal para acessar as APIs do MercadoLivre.
     * Configuração para CURL
     */
    public static $CURL_OPTS = array(

        CURLOPT_USERAGENT => "MercadoLivre-PHP-SDK-2.0.0", 
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );
    protected static $API_ROOT_URL = "https://api.mercadolibre.com";
    /**
     * Execute uma requisição GET 
     * 
     * @param string $path
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function get($path, $params = null, $assoc = false) {
        
        return $this->execute($path, null, $params, $assoc);
    }

    /**
     * Execute uma requisição POST 
     * 
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {

        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute uma requisição PUT
     * 
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params = array()) {

        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute uma requisição DELETE
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params) {

        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE"
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }

    /**
     * Execute uma requisição OPTION 
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null) {

        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute todos os pedidos e retorne o corpo json e headers
     * 
     * @param string $path
     * @param array $opts
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = array(), $assoc = false) {

        $uri = $this->make_path($path, $params);
        //if($path == "/sites/".self::$SITE_ID['BR']."/search") exit($uri);

        $ch = curl_init($uri);
        curl_setopt_array($ch, self::$CURL_OPTS);

        if(!empty($opts))
            curl_setopt_array($ch, $opts);

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        return $return;
    }
    /**
     * Verifique e construa um URL real para fazer o pedido
     * 
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array()) {
        
        if (!preg_match("/^\//", $path)) {
            $path = '/' . $path;
        }

        $uri = self::$API_ROOT_URL . $path;
        
        if(!empty($params)) {
            $paramsJoined = array();

            foreach($params as $param => $value) {
               $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }

        return $uri;
    }
    
}