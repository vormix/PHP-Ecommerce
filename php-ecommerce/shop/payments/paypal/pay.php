<?php

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

require_once '../../../inc/init.php'; 
require   ROOT_PATH . 'shop/payments/paypal/start.php';

if (! defined('ROOT_URL')) {
  die;
}

if (!$loggedInUser) {
  die;
}

if ( $_GET['success'] != "true") {
  // die; // something goes wrong with payment
  header("Location: " . ROOT_URL . 'shop?page=cart');
  die;
}

if (!isset($_GET['success'], $_GET['paymentId'], $_GET['PayerID'])) {
  die; // something goes wrong with payment
}


$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];

$payment = Payment::get($paymentId, $paypal);

$execute = new PaymentExecution();
$execute->setPayerId($payerId);

try {
  $result = $payment->execute($execute, $paypal);
} catch (Exception $e) {
  die($e);
}

$cartMgr = new CartManager();
$orderMgr = new OrderManager();
$cartId = $cartMgr->getCurrentCartId();

$orderId = $orderMgr->createOrderFromCart($cartId, $loggedInUser->id);

$paymentCode = $result->id;
$paymentStatus = $result->state;
$paymentMethod = 'paypal';

$orderMgr->SavePaymentDetails($orderId, $paymentCode, $paymentStatus, $paymentMethod);

if ($paymentStatus == 'approved') {
  header("Location: " . ROOT_URL . "shop?page=checkout&success=true&orderId=$orderId");
} else {
  header("Location: " . ROOT_URL . "shop?page=checkout&success=false&orderId=$orderId");
}
