<?php

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;

require_once '../../../inc/init.php'; 
require   ROOT_PATH . 'shop/payments/paypal/start.php';

if (! defined('ROOT_URL')) {
  die;
}

if (!$loggedInUser) {
  exit;
}

$cartMgr = new CartManager();

$cartId = $cartMgr->getCurrentCartId();
if ($cartMgr->isEmptyCart($cartId)){
  die('cart is empty');
}

$cartItems = $cartMgr->getCartItems($cartId);
$cartTotal = $cartMgr->getCartTotal($cartId)[0];

// Paypal info
$shipping = $cartTotal['shipment_price'];

$items = [];
foreach($cartItems as $item) {
  $product = $item['product_name'];
  $price = $item['single_price']; 
  $quantity = $item['quantity'];

  // Uno per ogni elemento del carrello
  $item = new Item();
  $item->setName($product)
    ->setCurrency('EUR')
    ->setQuantity($quantity)
    ->setPrice($price);

  array_push($items, $item);
}

$totPrice = $cartTotal['total'];
$itemList = new ItemList();
$itemList->setItems($items);

$total = $totPrice + $shipping;

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$details = new Details();
$details->setShipping($shipping)
  ->setSubtotal($totPrice);

$amount = new Amount();
$amount->setCurrency('EUR')   
  ->setTotal($total)
  ->setDetails($details);

$transaction = new Transaction();
$transaction->setAmount($amount)
  ->setItemList($itemList)
  ->setDescription('Pagamento Ordine su ' . SITE_NAME) // descrizione qui
  ->setInvoiceNumber(uniqid()); // salvare il num. di fattura

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl(ROOT_URL . 'shop/payments/paypal/pay.php?success=true')
  ->setCancelUrl(ROOT_URL . 'shop/payments/paypal/pay.php?success=false');

$payment = new Payment();
$payment->setIntent('sale')
  ->setPayer($payer)
  ->setRedirectUrls($redirectUrls)
  ->setTransactions([$transaction]);

//die;

try {
  $result = $payment->create($paypal);
} catch (Exception $e) {
  die($e);
}

//var_dump($result); die;
$approvalUrl = $payment->getApprovalLink();
header("Location: {$approvalUrl}");










