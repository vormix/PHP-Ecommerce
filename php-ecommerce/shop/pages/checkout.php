<?php

if (! defined('ROOT_URL')) {
  include_once '../../inc/init.php';
}

// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

global $loggedInUser;
if (!$loggedInUser) {
  echo "<script>location.href='".ROOT_URL."auth?page=login&msg=login_for_checkout';</script>";
  exit;
}

$cartMgr = new CartManager();
$orderMgr = new OrderManager();

// Profilo pagamento Ritardato
if (isset($_POST['pay'])) {

  $pm = new ProfileManager();
  $delayedPayments = $pm->GetUserDelayedPayments();

  if (count($delayedPayments) == 0) {
    echo "<script>location.href='".ROOT_URL."public';</script>";
    exit;
  }

  $hasPayment = false;
  $paymentMethodId = esc($_POST['paymentMethod']); 
  foreach($delayedPayments as $p){
    if ($p->id == $paymentMethodId) {
      $hasPayment = true;
      break;
    }
  }

  if (!$hasPayment) {
    echo "<script>location.href='".ROOT_URL."public';</script>";
    exit;
  }

  // Qui sono sicuro che l'utente ha la facoltà del pagamento ritardato scelto, quindi creo l'ordine
  
  $cartId = $cartMgr->getCurrentCartId();
  if ($cartMgr->isEmptyCart($cartId)){
    die('cart is empty');
  }

  $orderId = $orderMgr->createOrderFromCart($cartId, $loggedInUser->id);
  $paymentCode = NULL;
  $paymentStatus = NULL;
  $status = 'delayed';
  $paymentMethod = $paymentMethodId;
  $orderMgr->SavePaymentDetails($orderId, $paymentCode, $paymentStatus, $paymentMethod, $status);

  echo "<script>location.href='".ROOT_URL."shop?page=checkout&orderId=".$orderId."&success=true';</script>";
  exit;
}

global $alertMsg;
$error = $_GET['success'] != "true";

$orderId =  (int) $_GET['orderId'];
$order = $orderMgr->get($orderId);
if (!$order || $loggedInUser->id != $order->user_id) {
  echo "<script>location.href='".ROOT_URL."public';</script>";
  exit;
}

if ($order->is_email_sent){
  echo '<h1>Ordine già elaborato.</h1>';
  echo '<p>Puoi visualizzare i dettagli oppure tornare alla home...</p>';
  echo '<a class="back underline" href="'.ROOT_URL.'shop?page=view-order&id='.$orderId .'">Visualizza &raquo;</a><br>';
  echo '<a class="back underline" href="'.ROOT_URL.'">&laquo; Torna alla Home</a>';
  exit;
}

$address = $orderMgr->getUserAddress($loggedInUser->id);
$orderItems = $orderMgr->getOrderItems($orderId);
$orderTotal = $orderMgr->getOrderTotal($orderId)[0];

$br = "\r\n";
$to = $loggedInUser->email;
$subject = "ORDINE N. " . $orderId;
$txt = "" . $br ;

$headers = "From: ".SITE_NAME . $br ;
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$style = "style='border: 1px solid black; border-collapse: collapse;'";

if ($error == false) {
  $txt = "<h2>Grazie per l'acquisto</h2>" . $br ;
} else {
  $txt = "<h2>Si è verificato un errore durante il pagamento.</h2>" . $br ;
  $txt .= "<p>L'ordine è stato annullato.</p>" . $br ;
}

$br = "<br>";
$txt.= $br . "<h3>Riepilogo Ordine:</h3>";

$mailBody = "<table $style><tr><th $style>Prodotto</th><th $style >Prezzo Unitario</th><th $style >N. Pezzi</th><th $style >Importo</th></tr>";
foreach($orderItems as $item)
{
  $mailBody .= "<tr><td $style>".$item['product_name']."</td><td $style>".$item['single_price']."</td><td $style>".$item['quantity']."</td><td $style>".$item['total_price']."</td></tr>";
}
$mailBody .= "<tr><td $style colspan='4'>Spedizione: ". $orderTotal['shipment_name'] ." (". $orderTotal['shipment_price'] . " €)</td></tr>";
$mailBody .= "<tr><td $style colspan='4'>Totale €". (number_format((float)  ($orderTotal['total'] + $orderTotal['shipment_price']), 2, '.', '')) . "</td></tr>";
$mailBody .= "</table>";

$txt .= $mailBody . $br ;

if ($error == false) {
  $txt.= $br . "<h3>Indirizzo di spedizione:</h3>";

  $shippingAddressStr = "<strong>Indirizzo: </strong>" . $address['street'] . $br;
  $shippingAddressStr .= "<strong>Città: </strong>" . $address['city'] . $br;
  $shippingAddressStr .= "<strong>CAP: </strong>" . $address['cap'] . $br;
  $txt .= $shippingAddressStr . $br;
  $txt .= $br . "Riceverà una mail quando l'ordine sarà spedito.";
}

mail($to,$subject,$txt,$headers);
$order->is_email_sent = 1;
$orderMgr->update($order, $orderId);

$style="";
$htmlBody = "<table class='table table-bordered' $style><tr><th $style>Prodotto</th><th $style >Prezzo Unitario</th><th $style >N. Pezzi</th><th $style >Importo</th></tr>";
foreach($orderItems as $item)
{
  $htmlBody .= "<tr><td $style>".$item['product_name']."</td><td $style>€ ".$item['single_price']."</td><td $style>".$item['quantity']."</td><td $style>€ ".$item['total_price']."</td></tr>";
}
$htmlBody .= "<tr><td $style colspan='4'>Spedizione: ". $orderTotal['shipment_name'] ." (". $orderTotal['shipment_price'] . " €)</td></tr>";
$htmlBody .= "<tr><td $style colspan='4'>Totale €". (number_format((float)  ($orderTotal['total'] + $orderTotal['shipment_price']), 2, '.', ''))  . "</td></tr>";
$htmlBody .= "</table>";



?>

<?php if ($error == false) : ?>
<h1>Grazie per aver effettuato l'acquisto</h1>
<p class="lead">Di seguito un riepilogo. Riceverà una mail con i dettagli dell'ordine</p>
<a class="back underline" href="<?php echo ROOT_URL . "shop?page=view-order&id=$orderId" ?>">Visualizza Ordine &raquo;</a><br>

<br>
<?php  else : ?>
<h1>Si è verificato un errore durante il pagamento.</h1>
<p class="lead">Riceverà una mail con dettagli del mancato pagamento.</p>
<br>
<?php endif ?>

<?php echo $htmlBody; ?>

<a class="back underline" href="<?php echo ROOT_URL; ?>">&laquo; Torna alla Home</a>