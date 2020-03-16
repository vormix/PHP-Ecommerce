<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $cm = new CartManager();
  $cartId = $cm->getCurrentCartId();

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
   //var_dump($cartId);die;
  // var_dump($cart_items);
  // var_dump($cart_total);
?>

<div class="col-md-12 order-md-2 mb-4">
  <?php if (count($cart_items) > 0) : ?>
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-primary">Carrello</span>
      <span class="badge badge-secondary badge-pill"><span id="numOfCartItems"><?php echo esc_html($cart_total[0]['num_products']); ?></span> prodotti nel carrello</span>
    </h4>
    <ul class="list-group mb-3">
    <?php foreach ($cart_items as $item) : ?>
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
              <input id="plus" name="plus" class="btn btn-primary btn-sm right" type="submit" value="+" >
            </div>
          <!-- </form> -->
         
        </div>
        <div class="col-lg-2 col-6">
          <strong class="text-primary total">€ <?php echo esc_html($item['total_price']); ?></strong>
        </div>  
      </div>   
    </li>
    <?php endforeach; ?>
        <li class="cart-total list-group-item d-flex justify-content-between p-4">
          <div class="row w-100">  
            <div class="col-lg-4 col-6">
              <span class="text-primary">Totale</span>
            </div>
            <div class="col-lg-6 lg-screen"></div>
            <div class="col-lg-2 col-6">
              <span id="spanGrandTotal" class="text-primary">€ <?php echo esc_html($cart_total[0]['total']); ?></span>
            </div>
          </div>
        </li>
      </ul>
      <hr class="mb-4">
      <?php
      global $loggedInUser;
      ?>
      <?php if ($loggedInUser) : ?>
        <a onclick="return confirm('Confermi invio ordine?');" class="btn btn-primary btn-block" href="<?php echo ROOT_URL . 'shop?page=checkout' ?>">Invia Ordine</a>
      <?php else : ?>
        <a class="btn btn-primary btn-block" href="<?php echo ROOT_URL . 'auth?page=register' ?>">Registrati per effettuare ordine</a>
      <?php endif ; ?>
  <?php else : ?>
    <p class="lead">Nessun elemento nel carrello...</p>
    <a href="<?php echo ROOT_URL . 'shop?page=products-list'; ?>" class="btn btn-primary btn-lg mb-5 mt-3">Vai allo Shopping &raquo;</a>
  <?php endif ; ?>

</div>

<script>
var $document = $(document);
$document.ready(function(){
  $document.find('.order-md-2 input:submit').on('click', e => {
    var $target = $(e.target);
    var $productButtons = $target.closest('div.cart-buttons');
   // e.preventDefault();

    var productId = $productButtons.find('input[name="product_id"]').val();
    var cartId = $productButtons.find('input[name="cart_id"]').val();
    var incrementOrDecrement = $target.is('input[name="plus"]') ? 'plus': 'minus';

    var postData = {
      cart_id: cartId,
      product_id: productId
    };
    postData[incrementOrDecrement]= "QUALCOSA"; 

    // console.log('productId', productId, 'cartId,', cartId);
    // return;

    $.post('../api/shop/cart.php', postData, data => { 
      console.log(data);
      printTotal(data.cartTotal);
      printProductBoxes(data.cart_items, productId);
      printNumOfCartItems(data.cart_items);
      var $quantitySpan = $productButtons.find('.text-muted');
      var previousQuantity = parseInt($quantitySpan.text());
      $quantitySpan.text(incrementOrDecrement == 'plus' ? ++previousQuantity : --previousQuantity);
      if (previousQuantity == 0) {
        $productButtons.closest('.product-box').fadeOut('slow', function() {
            $(this).remove();
        });
      }
    });
  });
});

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
  $('#spanGrandTotal').text('€ '+(cartTotal[0].total != null ? cartTotal[0].total : "0,00"));
}

function printProductBoxes(cart_items, productId){
  $.each(cart_items, (index,product) => {
    if (product.product_id == productId){
      $('.product-box[data-product-id="'+productId+'"]').find('.total').text('€ '+ product.total_price);
    }
  });
}
</script>