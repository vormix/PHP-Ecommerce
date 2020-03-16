<?php
require_once '../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

$productId = trim($_POST['id']);
$pm = new ProductManager();
$product=$pm->get($productId);

 if($product->qta > 1) {

    $pm->decreaseQuantity($productId);
    $cm = new CartManager();
    $cartId = $cm->getCurrentCartId(); 
    $cm->addToCart($productId, $cartId);

    $result = [ 'result' => 'success', 'message' => 'Aggiunto al carrello'] ;

 } else {
    $result = [ 'result' => 'danger', 'message' => 'Quantità non disponibile'] ;
 }
 
header('Content-type: application/json');
$array=$result;
echo json_encode($array);