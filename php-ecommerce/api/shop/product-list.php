<?php
require_once '../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}
  $productId = trim($_POST['id']);

  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();
  //var_dump($cartId); die;
  $cm->addToCart($productId, $cartId);
  // $cart_items = $cm->getCartItems($cartId);

 
header('Content-type: application/json');
$array=[
    'productId' => $productId,
    // 'cart_items' => $cart_items
];
echo json_encode($array);