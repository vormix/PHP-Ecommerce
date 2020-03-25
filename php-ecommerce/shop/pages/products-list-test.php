<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $cm = new CartManager();
  $mgr = new ProductManager();
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
<?php  

$x=0;
$row=0;

foreach($products as $product) 
{ 
    if($product->qta <= 1)
    {
        $btn="disabled";
        $qta="Qta: Non Disp.";
    }
    else
    {
        $btn="";
        $qta="Qta: <span class='qta'>".$product->qta . "</span>";
    }
    if($row==4)
    {
        echo '</div><div class="row">';
        $row=0;
    }
    echo '<div class="mb-3 col-md-3 col-6 border">';
    echo '<div class="shop-item">';

    $proimg = $mgr->GetProductWithImages($product->id); 
    if ($proimg->images ) 
    {
        echo '
        <div id="thumbnail'.$x.'" class="product-list mycentered-text carousel slide" data-ride="carousel" data-interval="false" width="50px" height="50px" >
            <div class="carousel-inner">';
   
        $active = 'active';
        foreach ($proimg->images as $image)
        {
            echo '<div class="carousel-item '.$active.'">';
            echo '<img src="'.ROOT_URL . '/images/' . $proimg->id . '/' . $image->id . '.' . $image->image_extension.'" class="product-list"></div>';
            $active='';
        } 
      
        echo'
        </div>
            <a class="carousel-control-prev" href="#thumbnail'.$x.'" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#thumbnail'.$x.'" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>';
        $x++;
    }
    else
    {
        echo '<img src="../images/products/noimage.jpg" class="product-list" />';
    }//END IF

    echo '
    <p>
        <span class="mycentered-text">
            <h5>' . esc_html($product->name) . '</h5>
        </span>
    </p>
    <div class="productcard d-flex justify-content-between bd-highlight mb-3">
        <div class="p-2 bd-highlight bg-success text-white">' . esc_html($product->price) .'  € </div>
        <div class="p-2 bd-highlight bg-secondary text-white" name="qta">Qta 
            <span class="qta">' . $product->qta . '</span>
        </div>
    </div>
    <div class="product-actions mycentered-text">'; 
    if($product->disc_price != NULL)
    { 
        echo '<span class="btn-danger">Scontato a ' .$product->disc_price.' €</span><br>'; 
        if($product->remaining_time != NULL && $product->data_fine_sconto != '2099-12-31') 
        {
            echo ' <b><span class="countdownstop btn-danger">'.$product->remaining_time.'</span><br>';
        }
    }
            
    echo '
    <input type="hidden" name="id" value="'. esc_html($product->id) .'">
    <input name="add_to_cart" type="submit" class="product-submit btn btn-success btn-sm justify-content-center" value="Aggiungi al carrello">
    </div>
    </div>
    </div>';
    $row++; 
} 
?>


<?php else : ?>

<?php endif;?>
</div><!--ultimo row -->
<script>
var $document = $(document);
$document.ready(function(){

    $.each($document.find('.countdown'), (i, item) => {
      countdown(item);
    });

    $document.find('.shop-item input:submit').on('click', e => {

      var $target = $(e.target);
      var $productButtons = $target.closest('div.product-actions');
      var productId = $productButtons.find('input[name="id"]').val();
    
      var $qta = $target.closest('.shop-item').find('.qta').text();
      alert($qta);
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