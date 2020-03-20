<?php

// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

if (!$loggedInUser) {
  echo "<script>location.href='".ROOT_URL."auth?page=login&msg=login_for_checkout';</script>";
  exit;
}

global $alertMsg;
$error = $_GET['success'] != "true";

$cartMgr = new CartManager();
$orderMgr = new OrderManager();

$orderId = $_GET['orderId'];


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
$mailBody .= "<tr><td $style colspan='4'>Totale €". $orderTotal['total'] . "</td></tr></table>";

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

$style="";
$htmlBody = "<table class='table table-bordered' $style><tr><th $style>Prodotto</th><th $style >Prezzo Unitario</th><th $style >N. Pezzi</th><th $style >Importo</th></tr>";
foreach($orderItems as $item)
{
  $htmlBody .= "<tr><td $style>".$item['product_name']."</td><td $style>€ ".$item['single_price']."</td><td $style>".$item['quantity']."</td><td $style>€ ".$item['total_price']."</td></tr>";
}
$htmlBody .= "<tr><td $style colspan='4'>Totale €". $orderTotal['total'] . "</td></tr></table>";

?>

<?php if ($error == false) : ?>
<h1>Grazie per aver effettuato l'acquisto</h1>
<p class="lead">Di seguito un riepilogo. Riceverà una mail con i dettagli dell'ordine</p>
<br>
<?php  else : ?>
<h1>Si è verificato un errore durante il pagamento.</h1>
<p class="lead">Riceverà una mail con dettagli del mancato pagamento.</p>
<br>
<?php endif ?>

<?php echo $htmlBody; ?>

<a class="back underline" href="<?php echo ROOT_URL; ?>">&laquo; Torna alla Home</a>