<?php

require_once '../../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

if (!$loggedInUser) {
  exit;
}

require_once 'shared.php';

echo json_encode(['publishableKey' => STRIPE_PUBLISHABLE_KEY]);