<?php
session_start();

require_once 'sdk/config/config.php';
require_once 'sdk/mercadoLivreActions.php';
require_once 'sdk/mercadoLivreAuth.php';
require_once 'sdk/mercadoLivreProductsCategory.php';

$sdk = null;


$sdk = new mercadoLivreAuth();

$sdk->client_id = APP_ID;
$sdk->client_secret = SECRET_KEY;

$url_auth = mercadoLivreAuth::$AUTH_URL['MLB'];
$redirectUrl = $sdk->getAuthUrl(URL_CALLBACK,$url_auth);

if(isset($_GET["error"])) {

  exit($_GET["error_description"]);
}
else {
  
  //verifica se o navegador trouxe a variavel code

  if(isset($_GET['code']) && !empty($_GET['code'])){  
    
    if(empty($sdk->access_token)) $user = $sdk->authorize($_GET['code'], URL_CALLBACK);

    
    if($user['httpCode'] != 200) exit($user['body']->message);

      $sdk->access_token = $user['body']->access_token;
      $sdk->refresh_token = $user['body']->refresh_token;

      $mlProdCategory = new mercadoLivreProductsCategory();

      //lista todasa as categorias
      //print_r($mlProdCategory->getCategory());

      //seleciona uma categoria pelo id
      print_r($mlProdCategory->setCategory('MLA5726'));
    
  }
  //Caso não tenha trasido é feito uma chamada para realizar a validação dos dados e assim gerar o code
  else{

   echo "<script language='javascript'> window.open('".$redirectUrl."', '_self'); </script>"; 
  }
  
}