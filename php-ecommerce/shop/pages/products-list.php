<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $cm = new CartManager();
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $cm->ResetExpiredCarts();
  }

  if (isset($_POST['add_to_cart'])) {

    $productId = trim($_POST['id']);

    if (!is_numeric($productId)) {
      die('productId must be numeric...'); // prevent sql injection
    }

    $cartId = $cm->getCurrentCartId();
    //var_dump($cartId); die;
    $cm->addToCart($productId, $cartId);

    $alertMsg = 'add_to_cart';
    echo "<script>location.href='".ROOT_URL."shop?page=products-list&msg=$alertMsg';</script>";
    exit;
  }

  $categoryId = isset($_GET['categoryId']) ? trim($_GET['categoryId']) : 0;
  if (!is_numeric($categoryId)) {
    die('categoryId must be numeric...'); // prevent sql injection
  }
  
  $pm = new ProductManager();
  $products = $pm->GetProducts($categoryId);
  
?>

<h1>Lista Prodotti</h1>

<?php if (count($products) > 0) : ?>
<p class="lead">Di seguito la lista dei nostri prodotti in vendita...</p>

<div class="row">

    <?php foreach($products as $product) : 
      if($product->qta <= 1){
        $btn="disabled";
        $qta="Qta: Non Disp.";

      }else{
        $btn="";
        $qta="Qta: <span class='qta'>".$product->qta . "</span>";
      }?>
    <div class="product-card card mb-3 col-md-3 col-6" >
      <div class="card-header bg-dark text-light rounded-0">
        <?php echo esc_html($product->name); ?>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <?php echo substr(esc_html($product->description), 0, 50); ?>
          <br>
          <?php if ($product->disc_price) : ?>
          <span class="badge-pill badge-warning">Prezzo speciale <?php echo esc_html($product->disc_price); ?> €</span>
          <span data-inizio-sconto="<?php echo esc_html($product->data_inizio_sconto); ?>" data-fine-sconto="<?php echo esc_html($product->data_fine_sconto); ?>" class="countdown badge badge-pill badge-warning"></span>
          
          <?php endif ?>
          <span class="badge badge-pill badge-info" ><?php echo $qta;  ?></span>
         
          <small class="text-muted right"><?php echo esc_html($product->price); ?> €</small>    
                
        </li>
      </ul>
      <div class="footer">
        <div class="product-actions">
          <button class="btn btn-secondary btn-sm btn-block rounded-0" onclick="location.href='<?php echo ROOT_URL . 'shop?page=view-product&id=' . esc_html($product->id); ?>'">Vedi</button>
          <!--<a class="btn btn-outline-primary btn-sm" href="#">Aggiungi al carrello</a>-->
         <!-- <form method="post">-->
            <input type="hidden" name="id" value="<?php echo esc_html($product->id); ?>">
            <input name="add_to_cart" type="submit" class="btn btn-primary btn-sm btn-block rounded-0" value="Aggiungi al carrello" <?php echo $btn;?>>
          <!--</form>-->
        </div>
      </div>
    </div>
    <?php endforeach; ?>

</div>
<?php else : ?>

<?php endif;?>
<script>
var $document = $(document);
$document.ready(function(){

    $.each($document.find('.countdown'), (i, item) => {
      countdown(item);
    });

    $document.find('.product-card input:submit').on('click', e => {
     
      var $target = $(e.target);
      var $productButtons = $target.closest('div.product-actions');
      var productId = $productButtons.find('input[name="id"]').val();
      var $qta = $target.closest('.product-card').find('.qta');
      var qta = parseInt($qta.text());

      var postData = {id: productId };
       
      $.post('../api/shop/product-list.php', postData, response => { 
        console.log(response);
        displayMessage(response);
        if (response.result == 'danger') return;
        
        if (qta <= 2) {
          $qta.text("Non Disp.");
          $target.attr('disabled', 'disabled');
        } else {
          $qta.text(--qta);
        }
        $('.js-totCartItems').text(parseInt($('.js-totCartItems:last').text())+1);
       });
    });
});


</script>