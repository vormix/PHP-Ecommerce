<?php

require_once '../../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}
global $loggedInUser;
if (!$loggedInUser) {
  exit;
}

require_once 'shared.php';

$cartMgr = new CartManager();

$cartId = $cartMgr->getCurrentCartId();
if ($cartMgr->isEmptyCart($cartId)){
  die('cart is empty');
}

$cartTotal = $cartMgr->getCartTotal($cartId)[0]['total'];
$shipmentPrice = $cartMgr->getCartTotal($cartId)[0]['shipment_price'];

if (!$cartTotal || $cartTotal <= 0) {
  die('amount is zero');
}

$amount = round((float) ($cartTotal + $shipmentPrice), 2) * 100;

try {
  $intent = \Stripe\PaymentIntent::create([
    'amount' => $amount,
    'currency' => 'EUR',
    'payment_method' => $body->paymentMethodId,
    'error_on_requires_action' => true,
    'confirm' => true,
  ]);

  // The payment is complete and the money has been moved
  // You can add any post-payment code here (e.g. shipping, fulfillment, etc)
  $orderId = paymentOk ($intent, $cartId, $loggedInUser->id);
  echo json_encode(['success' => true, 'clientSecret' => $intent->client_secret, 'orderId' => $orderId]);

} catch (\Stripe\Error\Card $e) {
  if ($e->getCode() == 'authentication_required') {
    global $loggedUserId;
    $orderId = paymentKo($e, $cartId, $loggedInUser->id);
    echo json_encode(['success' => false, 'orderId' => $orderId]);
    // echo json_encode([
    //   'error' => 'This card requires authentication in order to proceeded. Please use a different card'
    // ]);  
  } else {
    $orderId = paymentKo($e, $cartId, $loggedInUser->id);
    echo json_encode(['success' => false, 'orderId' => $orderId]);
  }
} 
catch (\Stripe\Exception\CardException $e) {
  global $loggedUserId;
  $orderId = paymentKo($e, $cartId, $loggedInUser->id);
  echo json_encode(['success' => false, 'orderId' => $orderId]);
} 
catch( Exception $e) {
  global $loggedUserId;
  $orderId = paymentKo($e, $cartId, $loggedInUser->id);
  echo json_encode(['success' => false, 'orderId' => $orderId]);
}

function paymentOk ($intent, $cartId,  $userId) {

  $orderMgr = new OrderManager();
  $orderId = $orderMgr->createOrderFromCart($cartId, $userId);
  $paymentCode = $intent->client_secret;
  $paymentStatus = 'approved';
  $paymentMethod = 'card';
  $orderMgr->SavePaymentDetails($orderId, $paymentCode, $paymentStatus, $paymentMethod);
  // header("Location: " . ROOT_URL . "shop?page=checkout&success=true&orderId=$orderId");
  return $orderId;
}

function paymentKo($e, $cartId, $userId) {
  $orderMgr = new OrderManager();

  $orderId = $orderMgr->createOrderFromCart($cartId, $userId);
  $paymentCode = '';
  $paymentStatus = 'rejected';
  $paymentMethod = 'card';
  $orderMgr->SavePaymentDetails($orderId, $paymentCode, $paymentStatus, $paymentMethod);
  // header("Location: " . ROOT_URL . "shop?page=checkout&success=false&orderId=$orderId");
  return $orderId;
}