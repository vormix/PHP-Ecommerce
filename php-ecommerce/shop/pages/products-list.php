<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  if (isset($_POST['add_to_cart'])) {

    $productId = trim($_POST['id']);

    $cm = new CartManager();
    $cartId = $cm->getCurrentCartId();
    //var_dump($cartId); die;
    $cm->addToCart($productId, $cartId);

    $alertMsg = 'add_to_cart';
    echo "<script>location.href='".ROOT_URL."shop?page=products-list&msg=$alertMsg';</script>";
    exit;
  }
  
  $pm = new ProductManager();
  $products = $pm->GetProducts();
?>

<h1>Lista Prodotti</h1>

<?php if (count($products) > 0) : ?>
<p class="lead">Di seguito la lista dei nostri prodotti in vendita...</p>

<div class="row">

    <?php foreach($products as $product) : ?>
    <div class="product-card card mb-3 col-md-3 col-6" >
      <div class="card-header bg-dark text-light rounded-0">
        <?php echo esc_html($product->name); ?>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <?php echo substr(esc_html($product->description), 0, 50); ?>
          <br>
          <?php if ($product->disc_price) : ?>
          <span class="badge badge-pill badge-warning">Prezzo speciale <?php echo esc_html($product->disc_price); ?> €</span>
          <span class="badge badge-pill badge-warning">Fino a: <?php echo esc_html($product->remaining_time);  ?></span>
          
          <?php endif ?>
          <span class="badge badge-pill badge-info" >Qta:  <?php echo esc_html($product->qta);  ?></span>
          <br>
          <small class="text-muted right"><?php echo esc_html($product->price); ?> €</small>    
                
        </li>
      </ul>
      <div class="footer">
        <div class="product-actions">
          <button class="btn btn-secondary btn-sm btn-block rounded-0" onclick="location.href='<?php echo ROOT_URL . 'shop?page=view-product&id=' . esc_html($product->id); ?>'">Vedi</button>
          <!--<a class="btn btn-outline-primary btn-sm" href="#">Aggiungi al carrello</a>-->
         <!-- <form method="post">-->
            <input type="hidden" name="id" value="<?php echo esc_html($product->id); ?>">
            <input name="add_to_cart" type="submit" class="btn btn-primary btn-sm btn-block rounded-0" value="Aggiungi al carrello">
          <!--</form>-->
        </div>
      </div>
    </div>
    <?php endforeach; ?>

</div>
<?php else : ?>

<?php endif; ?>
<script>
var $document = $(document);
$document.ready(function(){
    $document.find('.product-card input:submit').on('click', e => {
     
      var $target = $(e.target);
      var $productButtons = $target.closest('div.product-actions');
      var productId = $productButtons.find('input[name="id"]').val();

      var postData = {id: productId };
       
      $.post('../api/shop/product-list.php', postData, response => { 
        console.log(response);
        displayMessage(response);
        if (response.result == 'danger') return;
        
        $('.js-totCartItems').text(parseInt($('.js-totCartItems:last').text())+1);
       });
    });
});

</script>