<?php
  echo 'hello from "' . basename(__FILE__) .'"';

  $productMgr = new ProductManager();
  $products = $productMgr->getAll();
?>

<div class="row">

<?php if($products) : ?>
  <?php foreach($products as $product) : ?>

  <div class="card" style="width: 18rem;">
    <div class="card-body">
      <h5 class="card-title"><?php echo $product->name ?></h5>
      <h6 class="card-subtitle mb-2 text-muted"><?php echo $product->price ?> €</h6>
      <p class="card-text"><?php echo $product->description ?></p>
      <a href="<?php echo ROOT_URL . 'shop?page=view-product&id=' . $product->id ?>" class="card-link">Vedi &raquo;</a>      
    </div>
  </div>

  <?php endforeach; ?>
<?php else : ?>
  <p>Nessun prodotto disponibile...</p>
<?php endif; ?>

</div>