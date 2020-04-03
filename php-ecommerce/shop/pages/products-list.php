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

<div id="spinner" class="d-flex justify-content-center">
  <div class="spinner-border" role="status">
    <span class="sr-only">Caricamento...</span>
  </div>
</div>

<table id="products-table" class="table mt-5" style="display:none;">
  <thead>
    <tr>
      <th>Nome</th>
      <th class="visibility-hidden">Dettagli</th>
      <th class="visibility-hidden">Azioni</th>
      <th class="sort-toggle">Prezzo</th>
      <th class="sort-toggle">Con Sconto</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $i = 1;
    ?>
    <?php foreach($products as $product) : ?>
      <?php 
      if($product->qta <= 1){
        $btn="disabled";
        $qta="Qta: Non Disp.";

      }else{
        $btn="";
        $qta="Qta: <span class='qta'>".$product->qta . "</span>";
      }
      ?>
    <tr class="product-card card mb-3 col-xl-4 col-6" >
      <td>
        <div class="card-header bg-dark text-light rounded-0">
          <?php echo esc_html($product->name); ?>
        </div>
      </td>
      <td>
        <ul class="list-group list-group-flush">
          <li class="list-group-item p-0">
          <div id="carousel-<?php echo $i ?>" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                  <img src="<?php echo ROOT_URL ?>/images/285/190_thumbnail.jpg" class="d-block w-100">
              </div>
              <div class="carousel-item">
                <img src="<?php echo ROOT_URL ?>/images/285/191_thumbnail.jpg" class="d-block w-100">
              </div>
              <div class="carousel-item">
                <img src="<?php echo ROOT_URL ?>/images/285/1191_thumbnail.jpg" class="d-block w-100">
              </div>
            </div>
            <a class="carousel-control-prev" href="#carousel-<?php echo $i ?>" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel-<?php echo $i ?>" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>

          </li>
          <li class="list-group-item">
            <?php echo substr(esc_html($product->description), 0, 50); ?>
            <br>
            <?php if ($product->disc_price) : ?>
              <span class="badge badge-pill badge-warning">Prezzo speciale <?php echo esc_html(number_format((float)$product->disc_price, 2, '.', '')); ?> €</span>
              <br>
              <span data-inizio-sconto="<?php echo esc_html($product->data_inizio_sconto); ?>" data-fine-sconto="<?php echo esc_html($product->data_fine_sconto); ?>" class="countdown badge badge-pill badge-warning"></span>          
              <br>
            <?php endif ?>
            <span class="badge badge-pill badge-info" ><?php echo $qta;  ?></span>    
                               
          </li>
        </ul>
      </td>
      <td>
        <small class="text-muted right"><?php echo esc_html($product->price); ?> €</small> 
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
      </td>
      <td class="visibility-hidden"><?php echo esc_html($product->price); ?></td>
      <td class="visibility-hidden"><?php echo esc_html(isset($product->disc_price) ? '1' : '0'); ?></td>

    </tr>
    <?php
    $i++;
    ?>
    <?php endforeach; ?>
    <tbody>
</table>
<?php else : ?>
  <p>Nessun prodotto disponibile...</p>
<?php endif;?>

<script>
var $document = $(document);
var $productsTable = $('#products-table');
var $spinner = $('#spinner');

$document.ready(function(){

    var dt = $productsTable.DataTable({
      bLengthChange: false,
      pageLength: 18,
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Cerca un prodotto...",
        sLengthMenu: "Visualizzati __"
      },
      initComplete: function(settings, json) {
        $productsTable.show();
        $spinner.remove();
        $productsTable.find('th.visibility-hidden').hide();
      }
    });
    $productsTable.addClass('cards');

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
