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
<div class="row">
    <div class="mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h5>Black Coat</h5>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"> 235  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta 4</div>
            </div>
            <div class="mycentered-text"> 
            <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
            </div>
        </div>
    </div>
    <div class="mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h3>Black Coat</h3>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"> 235  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta 4</div>
            </div>
            <div class="mycentered-text"> 
            <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
            </div>
        </div>
    </div>
    <div class="mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h3>Black Coat</h3>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"> 235  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta 4</div>
            </div>
             
            <div class="mycentered-text"> 
            <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
            </div>
             
        </div>
    </div>
    <div class="product-card mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h3>Black Coat</h3>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"> 235  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta 4</div>
            </div>
            <div class="mycentered-text"> 
            <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
            </div>
        </div>
    </div>
</div>
<?php if (count($products) > 0) : ?>
<p class="lead">Di seguito la lista dei nostri prodotti in vendita...</p>

<div class="row">
<div class="product-card mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h3>Black Coat</h3>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"> 235  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta 4</div>
            </div>
            <div class="mycentered-text"> 
            <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
            </div>
        </div>
    </div>
    </div>
    <?php foreach($products as $product) : 
      if($product->qta <= 1){
        $btn="disabled";
        $qta="Qta: Non Disp.";

      }else{
        $btn="";
        $qta="Qta: <span class='qta'>".$product->qta . "</span>";
      }?>
    <div class="product-card mb-3 col-md-3 col-6 border">
        <div class="shop-item">
            <img src="../images/6/1.jpg" class="img-fluid" alt="">
            <h3><?php echo esc_html($product->name); ?></h3>
            <div class="d-flex justify-content-between bd-highlight mb-3">
              <div class="p-2 bd-highlight bg-success text-white"><?php echo esc_html($product->price); ?>  € </div>
              <div class="p-2 bd-highlight bg-secondary text-white">Qta ><?php echo $qta;  ?></div>
            </div>
            <div class="mycentered-text"> 
                <a class="btn btn-success btn-sm justify-content-center" href="#" role="button">Aggiungi al carrello</a>
                <br>
                <span>Prezzo Scontato</span>
                <br>
                <span>Prezzo Scontato</span>
                <button class="btn btn-secondary btn-sm btn-block rounded-0" onclick="location.href='<?php echo ROOT_URL . 'shop?page=view-product&id=' . esc_html($product->id); ?>'">Vedi</button>
          <!--<a class="btn btn-outline-primary btn-sm" href="#">Aggiungi al carrello</a>-->
         <!-- <form method="post">-->
            <input type="hidden" name="id" value="<?php echo esc_html($product->id); ?>">
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