<?php
require_once '../../inc/init.php'; 

if (! defined('ROOT_URL')) {
    die;
  }

  // Instanzio Cart Manager
  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();
  $cart_id = esc($_POST['cart_id']);
  $product_id = esc($_POST['product_id']);
  $pm = new ProductManager();
  $product=$pm->get($product_id);
  if($product->qta > 1) {  
    
      if (isset($_POST['minus'])){
        $cm->removeFromCart($product_id, $cart_id);
        $pm->increaseQuantity($product_id);
      }
      if (isset($_POST['plus'])){
        $cm->addToCart($product_id, $cart_id);
        $pm->decreaseQuantity($product_id);
      }

  }
  $cart_total = $cm->getCartTotal($cartId);
  $cart_items = $cm->getCartItems($cartId);



header('Content-type: application/json');
$array=[
    'cartTotal' => $cart_total,
    'cart_items' => $cart_items
];
echo json_encode($array);