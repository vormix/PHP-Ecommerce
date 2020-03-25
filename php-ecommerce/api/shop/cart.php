<?php
require_once '../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

$action = $_POST['action'];
switch($action) {

  case 'setShipmentMethod':
    setShipmentMethod();
    break;

  case 'incrementOrDecrement':
    incrementOrDecrement();
    break;
    
  default:
    break;
}


function setShipmentMethod() {

  $shipmentMethod = esc($_POST['shipmentMethod']);
  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();
  $cm->setShipmentMethod($cartId, $shipmentMethod);
}

function incrementOrDecrement() {

  // Instanzio Cart Manager
  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();
  $cart_id = esc($_POST['cart_id']);
  $product_id = esc($_POST['product_id']);
  $pm = new ProductManager();
  $product=$pm->get($product_id);
      
  if (isset($_POST['minus'])){
    $cm->removeFromCart($product_id, $cart_id);
  }
  else if (isset($_POST['plus']) && $product->qta > 1){
    $cm->addToCart($product_id, $cart_id);
  }

  $cart_total = $cm->getCartTotal($cartId);
  $cart_items = $cm->getCartItems($cartId);

  header('Content-type: application/json');
  $array=[
      'cartTotal' => $cart_total,
      'cart_items' => $cart_items
  ];
  echo json_encode($array);

}