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

<style>
  .cards tbody tr {
    float: left;
    width: 20rem;
    margin: 0.5rem;
    border: 0.0625rem solid rgba(0,0,0,.125);
    border-radius: .25rem;
    box-shadow: 0.25rem 0.25rem 0.5rem rgba(0,0,0,0.25);
  }
  .cards tbody td {
      display: block;
  }
  .table tbody label {
      display: none;
  }
  .cards tbody label {
    display: inline;
    position: relative;
    font-size: 85%;
    top: -0.5rem;
    float: left;
    color: #808080;
    min-width: 4rem;
    margin-left: 0;
    margin-right: 1rem;
    text-align: left;
  }
  tr.selected label {
      color: #404040;
  }

  .table .fa {
      font-size: 2.5rem;
      text-align: center;
  }
  .cards .fa {
      font-size: 7.5rem;
  }
</style>

<h1>Lista Prodotti</h1>

<?php if (count($products) > 0) : ?>
<p class="lead">Di seguito la lista dei nostri prodotti in vendita...</p>

<table id="products-table" class="table">
  <thead>
    <tr>
      <th>Nome</th>
      <th>Dettagli</th>
      <th>Azioni</th>
    </tr>
  </thead>
  <tbody>
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
    <tr class="product-card card mb-3 col-md-3 col-6" >
      <td>
        <div class="card-header bg-dark text-light rounded-0">
          <?php echo esc_html($product->name); ?>
        </div>
      </td>
      <td>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <?php echo substr(esc_html($product->description), 0, 50); ?>
            <br>
            <?php if ($product->disc_price) : ?>
              <span class="badge-pill badge-warning">Prezzo speciale <?php echo esc_html(number_format((float)$product->disc_price, 2, '.', '')); ?> €</span>
              <span data-inizio-sconto="<?php echo esc_html($product->data_inizio_sconto); ?>" data-fine-sconto="<?php echo esc_html($product->data_fine_sconto); ?>" class="countdown badge badge-pill badge-warning"></span>          
            <?php endif ?>
            <span class="badge badge-pill badge-info" ><?php echo $qta;  ?></span>
          
            <small class="text-muted right"><?php echo esc_html($product->price); ?> €</small>    
                  
          </li>
        </ul>
      </td>
      <td>
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
    </tr>
    <?php endforeach; ?>
    <tbody>
</table>
<?php else : ?>
  <p>Nessun prodotto disponibile...</p>
<?php endif;?>

<script>
var $document = $(document);
var $productsTable = $('#products-table');

$document.ready(function(){

    $productsTable.DataTable({
      bLengthChange: false,
      pageLength: 20
    });
    $productsTable.addClass('cards');
    $productsTable.find('thead').hide();

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
