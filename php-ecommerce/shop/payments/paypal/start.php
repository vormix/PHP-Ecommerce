<?php

require_once '../../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

if (!$loggedInUser) {
  exit;
}

require   ROOT_PATH . '/vendor/autoload.php';

$paypal = new \PayPal\Rest\ApiContext(
  new \PayPal\Auth\OAuthTokenCredential(
    PAYPAL_CLIENT_ID,
    PAYPAL_CLIENT_SECRET
  )
);
