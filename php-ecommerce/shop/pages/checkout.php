<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  if (!$loggedInUser) {
    echo "<script>location.href='".ROOT_URL."auth?page=login&msg=login_for_checkout';</script>";
    exit;
  }
?>

<?php
  global $alertMsg;
  $error = false;

  $cartMgr = new CartManager();
  $orderMgr = new OrderManager();

  $cartId = $cartMgr->getCurrentCartId();

  if ($cartMgr->isEmptyCart($cartId)){
    $alertMsg = 'cart_empty';
    $error = true;
  }
  
  $address = $orderMgr->getUserAddress($loggedInUser->id);
  if(!$error && !$address) {
    
    $alertMsg = 'address_not_found';
    $error = true;
  }

  if(!$error){
    
    $orderId = $orderMgr->createOrderFromCart($cartId, $loggedInUser->id);

    $orderItems = $orderMgr->getOrderItems($orderId);
    $orderTotal = $orderMgr->getOrderTotal($orderId)[0];

    $br = "\r\n";
    $to = $loggedInUser->email;
    $subject = "ORDINE N. " . $orderId;
    $txt = "<h2>Grazie per l'acquisto</h2>" . $br ;

    $headers = "From: ".SITE_NAME . $br ;
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $style = "style='border: 1px solid black; border-collapse: collapse;'";

    $br = "<br>";
    $txt.= $br . "<h3>Riepilogo Ordine:</h3>";

    $mailBody = "<table $style><tr><th $style>Prodotto</th><th $style >Prezzo Unitario</th><th $style >N. Pezzi</th><th $style >Importo</th></tr>";
    foreach($orderItems as $item){
      $mailBody .= "<tr><td $style>".$item['product_name']."</td><td $style>".$item['single_price']."</td><td $style>".$item['quantity']."</td><td $style>".$item['total_price']."</td></tr>";
    }
    $mailBody .= "<tr><td $style colspan='4'>Totale €". $orderTotal['total'] . "</td></tr></table>";
 
    $txt .= $mailBody . $br ;
    $txt.= $br . "<h3>Indirizzo di spedizione:</h3>";

    $shippingAddressStr = "<strong>Indirizzo: </strong>" . $address['street'] . $br;
    $shippingAddressStr .= "<strong>Città: </strong>" . $address['city'] . $br;
    $shippingAddressStr .= "<strong>CAP: </strong>" . $address['cap'] . $br;

    $txt .= $shippingAddressStr . $br;
    $txt .= $br . "Riceverà una mail quando l'ordine sarà spedito.";

    $style="";
    $htmlBody = "<table class='table table-bordered' $style><tr><th $style>Prodotto</th><th $style >Prezzo Unitario</th><th $style >N. Pezzi</th><th $style >Importo</th></tr>";
    foreach($orderItems as $item){
      $htmlBody .= "<tr><td $style>".$item['product_name']."</td><td $style>€ ".$item['single_price']."</td><td $style>".$item['quantity']."</td><td $style>€ ".$item['total_price']."</td></tr>";
    }
    $htmlBody .= "<tr><td $style colspan='4'>Totale €". $orderTotal['total'] . "</td></tr></table>";

    mail($to,$subject,$txt,$headers);
  } else {
    echo "<script>location.href='".ROOT_URL."shop?page=cart&msg=$alertMsg';</script>";
    exit;
  }

?>

<h1>Grazie per aver effettuato l'acquisto</h1>
<p class="lead">Di seguito un riepilogo. Riceverà una mail con i dettagli dell'ordine</p>
<br>

<?php echo $htmlBody; ?>

<a class="back underline" href="<?php echo ROOT_URL; ?>">&laquo; Torna alla Home</a>