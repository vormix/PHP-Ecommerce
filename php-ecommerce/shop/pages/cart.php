<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();
  
  $sm = new ShipmentManager();
  $shipments = $sm->GetShipments();

  if (isset($_POST['minus'])) {
    $cart_id = esc($_POST['cart_id']);
    $product_id = esc($_POST['product_id']);
    $cm->removeFromCart($product_id, $cart_id);
  }

  if (isset($_POST['plus'])) {
    $cart_id = esc($_POST['cart_id']);
    $product_id = esc($_POST['product_id']);
    $cm->addToCart($product_id, $cart_id);
  }

  $cart_total = $cm->getCartTotal($cartId);
  $cart_items = $cm->getCartItems($cartId);


?>

<div class="col-md-12 order-md-2 mb-4">
  <?php if (count($cart_items) > 0) : ?>
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-primary">Carrello</span>
      <span class="badge badge-secondary badge-pill"><span id="numOfCartItems"><?php echo esc_html($cart_total[0]['num_products']); ?></span> prodotti nel carrello</span>
    </h4>
    <ul class="list-group mb-3">
    <?php foreach ($cart_items as $item) : ?>
    <?php
    $disabled = $item['available_quantity'] <= 1 ? "disabled='disabled'" : '';
    ?>
    <li data-product-id="<?php echo esc_html($item['product_id']); ?>" class="list-group-item product-box d-flex justify-content-between lh-condensed p-4">
      <div class="row w-100">      
        <div class="col-lg-4 col-6">
          <h6 class="my-0 text-primary"><?php echo esc_html($item['product_name']); ?></h6>
          <small class="text-muted"><?php echo shorten(esc_html($item['product_description'])); ?></small>
        </div>
        <div class="col-lg-2 col-6">
          <strong class="text-muted">€ <?php echo esc_html($item['single_price']); ?></strong>
        </div>  
        <div class="col-lg-4 col-6">
          <!-- <form method="post"> -->
            <div class="cart-buttons btn-group btn-group-toggle">
              <input name="minus" class="btn btn-primary btn-sm left" type="submit" value="-">
              <input type="hidden" name="cart_id" value="<?php echo esc_html($item['cart_id']); ?>">
              <input type="hidden" name="product_id" value="<?php echo esc_html($item['product_id']); ?>">
              <span class="text-muted"><?php echo esc_html($item['quantity']); ?></span>
              <input <?php echo $disabled ?> id="plus" name="plus" class="btn btn-primary btn-sm right" type="submit" value="+" >
            </div>
          <!-- </form> -->
         
        </div>
        <div class="col-lg-2 col-6">
          <strong class="text-primary total">€ <?php echo esc_html($item['total_price']); ?></strong>
        </div>  
      </div>   
    </li>
    <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between p-4">
          <div class="row w-100">  
            <div class="col-lg-4 col-6">
              <span class="text-primary">Costi di Spedizione</span>
            </div>
            <div class="col-lg-6 lg-screen"></div>
            <div class="col-lg-2 col-6">
            <strong>€ <span id="spanShipmentPrice" class="text-primary"><?php echo (number_format((float) $cart_total[0]['shipment_price'], 2, '.', ''));  ?></span></strong>
            </div>
          </div>
        </li>
        <li class="cart-total list-group-item d-flex justify-content-between p-4">
          <div class="row w-100">  
            <div class="col-lg-4 col-6">
              <span class="text-primary">Totale</span>
            </div>
            <div class="col-lg-6 lg-screen"></div>
            <div class="col-lg-2 col-6">
              <input type="hidden" id="total" value="<?php echo esc_html($cart_total[0]['total']); ?>">
             € <span id="spanGrandTotal" class="text-primary"><?php echo (number_format((float) ($cart_total[0]['shipment_price'] + $cart_total[0]['total']), 2, '.', '')); ?></span>
            </div>
          </div>
        </li>
      </ul>
      <hr class="mb-4">

      <div class="form-group">
        <label for="shipmentMethods">Metodo di Spedizione</label>
        <select name="shipmentMethods" id="shipmentMethods" type="text" class="form-control" value="0">
          <option value="0"> - Scegli una modalità di spedizione - </option>
          <?php if (count($shipments) > 0) : ?>
            <?php foreach ($shipments as $shipment) : ?>
              <option <?php if ($cart_total[0]['shipment_id'] == $shipment->id ) echo 'selected' ; ?>  data-price="<?php echo esc_html($shipment->price); ?>" value="<?php echo esc_html($shipment->id); ?>"><?php echo esc_html($shipment->name); ?> - (€ <?php echo esc_html($shipment->price); ?>)</option>
            <?php endforeach ; ?>
          <?php endif ; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="paymentMethods">Metodo di Pagamento</label>
        <select name="paymentMethods" id="paymentMethods" type="text" class="form-control" value="0">
          <option value="0"> - Scegli una modalità di pagamento - </option>
          <option value="card">Carta di Credito</option>
          <option value="paypal">PayPal</option>
        </select>
      </div>

      <?php
      global $loggedInUser;
      ?>
      <?php if ($loggedInUser) : ?>
        <a  id="paypalPay" onclick="return confirm('Confermi invio ordine?');" class="btn btn-primary btn-block" href="<?php echo ROOT_URL . 'shop/payments/paypal/checkout.php' ?>">Paga con PayPal</a>
        <div id="cardPay">
          <!-- Stripe -->
          <div class="sr-root">
            <div class="sr-main">
              <form id="stripe-payment-form" class="sr-payment-form">
                <div class="sr-combo-inputs-row form-control mb-3">
                  <div class="sr-input sr-card-element" id="card-element"></div>
                </div>
                <div class="sr-field-error" id="card-errors" role="alert"></div>
                <button class="stripe-button btn btn-primary btn-block">
                  <div class="spinner hidden"></div>
                  <span class="button-text">Paga con Carta di Credito</span><span class="order-amount"></span>
                </button>
              </form>
              <div class="sr-result hidden">
                <p>Pagamento Completato<br /></p>
                <pre>
                  <code></code>
                </pre>
              </div>
            </div>
          </div>
          <!-- Stripe -->
        </div>
      <?php else : ?>
        <a class="btn btn-primary btn-block" href="<?php echo ROOT_URL . 'auth?page=register' ?>">Registrati per effettuare ordine</a>
      <?php endif ; ?>
  <?php else : ?>
    <p class="lead">Nessun elemento nel carrello...</p>
    <a href="<?php echo ROOT_URL . 'shop?page=products-list'; ?>" class="btn btn-primary btn-lg mb-5 mt-3">Vai allo Shopping &raquo;</a>
  <?php endif ; ?>

</div>

<!-- Stripe Payment -->
 <!-- <link rel="stylesheet" href="<?php echo ROOT_URL ?>shop/payments/stripe/css/normalize.css" /> -->
<!--<link rel="stylesheet" href="<?php echo ROOT_URL ?>shop/payments/stripe/css/global.css" /> -->
<script src="https://js.stripe.com/v3/"></script>
<script src="<?php echo ROOT_URL ?>shop/payments/stripe/js/script.js" ></script>
<!-- / tripe Payment -->

<script>
var $document = $(document);

var $paymentMethods;
var $paypal;
var $card;

$document.ready(function(){

  $paymentMethods = $('#paymentMethods');
  $paypal = $('#paypalPay');
  $card = $('#cardPay');

  $paypal.hide();
  $card.hide();

  $document.find('#paymentMethods').on('change', enablePaymentButton);
  $document.find('#shipmentMethods').on('change', updateShipmentPrice);
  
  $document.find('.order-md-2 input:submit').on('click', e => {
    var $target = $(e.target);
    var $productButtons = $target.closest('div.cart-buttons');
   // e.preventDefault();

    var productId = $productButtons.find('input[name="product_id"]').val();
    var cartId = $productButtons.find('input[name="cart_id"]').val();
    var incrementOrDecrement = $target.is('input[name="plus"]') ? 'plus': 'minus';

    var postData = {
      action: 'incrementOrDecrement',
      cart_id: cartId,
      product_id: productId
    };
    postData[incrementOrDecrement]= "QUALCOSA"; 

    $.post('../api/shop/cart.php', postData, data => { 
      console.log(data);
      printTotal(data.cartTotal);
      printProductBox(data.cart_items, productId, $productButtons, incrementOrDecrement);
      printNumOfCartItems(data.cart_items);
      
    });
  });
});

function updateShipmentPrice(e) {
  var price = 0;
  var shipmentMethod = $(e.target).val();
  var options = e.target.options;
  $.each(options, (i, option) => {
    var $opt = $(option);
    if ($opt.val() == shipmentMethod) {
      price = $opt.attr('data-price');
    }
  });
  setShipmentPrice(price);

  var postData = {
      action: 'setShipmentMethod',
      shipmentMethod: shipmentMethod
    };
  $.post('../api/shop/cart.php', postData, response => {
    console.log(response);
  });
}

function setShipmentPrice(price){
  $('#spanShipmentPrice').text(price.replace('.', ','));

  price = parseFloat(price.replace('.', ','));
  var total = parseFloat($('#total').val().replace(',', '.'));

  total = total + price;
  total =(Math.round(total * 100) / 100).toFixed(2);
  $('#spanGrandTotal').text(total.toString().replace('.', ','));

}

function enablePaymentButton(e) {

  switch($paymentMethods.val()) {
    case 'paypal':
      $paypal.show();
      $card.hide();
      break;
    case 'card':
      $card.show();
      $paypal.hide();
      break;
    default:
      $card.hide();
      $paypal.hide();
  }
}

function printNumOfCartItems(cart_items) {
  var $span = $('#numOfCartItems');
  var $cartBadge = $('.js-totCartItems');
  var totItems = 0;
  $.each(cart_items, (index,product) => {
    totItems += parseInt(product.quantity);
  });
  $span.text(totItems);
  $cartBadge.text(totItems);
}

function printTotal(cartTotal){
  var total = parseFloat(cartTotal[0].total);
  total =(Math.round(total * 100) / 100).toFixed(2);
  $('#total').val(total);

  total = parseFloat(cartTotal[0].total) + parseFloat(cartTotal[0].shipment_price);
  total =(Math.round(total * 100) / 100).toFixed(2);
  $('#spanGrandTotal').text((total));
}

function printProductBox(cart_items, productId, $productButtons, incrementOrDecrement){
  var clickedProduct = null;
  $.each(cart_items, (index,product) => {
    if (product.product_id == productId){
      clickedProduct = product;
      $('.product-box[data-product-id="'+productId+'"]').find('.total').text('€ '+ clickedProduct.total_price);
    }
  });

  var $plusBtn = $productButtons.find('input[name="plus"]');
  if (parseInt(incrementOrDecrement == 'plus' && clickedProduct.available_quantity) <= 1) {
    $plusBtn.attr('disabled', 'disabled');
  }

  if (incrementOrDecrement == 'minus') {
    $plusBtn.removeAttr('disabled');
  }

  var $quantitySpan = $productButtons.find('.text-muted');
  var previousQuantity = parseInt($quantitySpan.text());
  $quantitySpan.text(incrementOrDecrement == 'plus' ? ++previousQuantity : --previousQuantity);
  if (previousQuantity == 0) {
    $productButtons.closest('.product-box').fadeOut('slow', function() {
        $(this).remove();
    });
  }
}
</script>