<?php

global $alertMsg;
$cssClass = 'hidden';
$msgHeading = '';
$msgBody = '';

$alertMsg = $alertMsg == '' ? (isset($_GET['msg']) ? esc_html($_GET['msg']) : '') : $alertMsg;

if ($alertMsg != '') {

  switch($alertMsg) {

    case 'created':
      $cssClass = 'alert-success';
      $msgHeading = 'OK';
      $msgBody = 'Inserimento riuscito';
      break;

    case 'order_sent':
      $cssClass = 'alert-success';
      $msgHeading = 'OK';
      $msgBody = 'Ordine inviato correttamente';
      break;

    case 'registered':
      $cssClass = 'alert-success';
      $msgHeading = 'Registrazione avvenuta';
      $msgBody = 'Ora è possibile effettuare il login';
      break;

    case 'login_for_checkout':
      $cssClass = 'alert-success';
      $msgHeading = 'EFFETTUARE IL LOGIN';
      $msgBody = 'Effettuare il login o registrarsi per poter inviare ordine';
      break;

    case 'add_to_cart':
      $cssClass = 'alert-success';
      $msgHeading = 'OK';
      $msgBody = 'Aggiunto al carrello';
      break;

    case 'order_shipped':
      $cssClass = 'alert-success';
      $msgHeading = 'Ordine Spedito';
      $msgBody = 'Email inviata al cliente';
      break;
      
    case 'updated':
      $cssClass = 'alert-success';
      $msgHeading = 'OK';
      $msgBody = 'Modifica riuscita';
      break;

    case 'deleted':
      $cssClass = 'alert-success';
      $msgHeading = 'OK';
      $msgBody = 'Eliminazione riuscita';
      break;

    case 'err':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Qualcosa è andato storto';
      break;

    case 'address_not_found':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Indirizzo di spedizione non presente. Correggere anagrafica';
      break;


    case 'login_err':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Login Fallito';
      break;

    case 'forbidden':
      $cssClass = 'alert-danger';
      $msgHeading = 'PAGINA RISERVATA';
      $msgBody = 'Non disponi dei privilegi necessari';
      break;

    case 'mandatory_fields':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Compilare i campi obbligatori';
      break;

    case 'not_found':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Elemento non presente';
      break;

    case 'invalid_password':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'la password non è abbastanza robusta';
      break; 

    case 'invalid_email':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'la mail non è valida';
      break; 

    case 'passwords_not_match':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Le password non corrispondono';
      break; 

    case 'user_already_exists':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Mail già presente a sistema';
      break;

    case 'cart_empty':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = 'Il carrello è vuoto';
      break;

    case 'order_empty':
      $cssClass = 'alert-danger';
      $msgHeading = 'ERRORE';
      $msgBody = "L'ordine non contiene alcun elemento";
      break;
  }

}
?>

<div class="alert alert-dismissible <?php echo $cssClass; ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <h4 class="alert-heading"><?php echo $msgHeading; ?></h4>
  <p class="mb-0"><?php echo $msgBody; ?></p>
</div>