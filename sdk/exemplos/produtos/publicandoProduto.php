<?php
session_start();

require_once 'sdk/config/config.php';
require_once 'sdk/mercadoLivreActions.php';
require_once 'sdk/mercadoLivreAuth.php';
require_once 'sdk/mercadoLivreProducts.php';
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

      $mlProd= new mercadoLivreProducts();


      $attributes = '[{"id":"COLOR", "value_id":"52019"},{"id":"COLOR", "value_id":"283160"}]';
      $sale_terms = '[{"id": "WARRANTY_TYPE", "value_id": "2230279" },{"id": "WARRANTY_TIME", "value_name": "90 dias"}]';
      $pictures = '[{"source":"https://www.albaruscalcados.com.br/loja/uploads/produtos/thumb/af71c22020e58b24c906cabeb3201663.JPG"},{"source":"https://www.albaruscalcados.com.br/loja/uploads/produtos/thumb/af71c22020e58b24c906cabeb3201663.JPG"}]';

      $mlProd->token = $sdk->access_token;
      $mlProd->title = "Bota Free Way Preta";
      $mlProd->category_id = "MLB112871";
      $mlProd->price =27490;
      $mlProd->currency_id = "BRL";
      $mlProd->available_quantity = 1;
      $mlProd->buying_mode = "buy_it_now";
      $mlProd->listing_type_id = "bronze";
      $mlProd->condition = "new";
      $mlProd->seller_custom_field = 6046;
      $mlProd->description = "Bota fabricada em  Semi-Cromo, com solado Tractor e detalhes na frente que aumentam a proteção e o conforto. ";
      $mlProd->video_id = "";
      $mlProd->attributes = json_decode($attributes);
      $mlProd->sale_terms = json_decode($sale_terms);
      $mlProd->pictures = json_decode($pictures);

      print_r($mlProd->publishProduct());
    
  }
  //Caso não tenha trasido é feito uma chamada para realizar a validação dos dados e assim gerar o code
  else{

   echo "<script language='javascript'> window.open('".$redirectUrl."', '_self'); </script>"; 
  }
  
}