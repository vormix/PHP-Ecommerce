<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $catMgr = new CategoryManager();
  $categories = $catMgr->GetCategories();
  
?>

<div class="card mb-3" >
  <div class="card-header bg-dark text-light">
    Categorie
  </div>
  <?php if ($categories): ?>
  <ul class="list-group list-group-flush">
    <?php foreach ($categories as $category): ?>
      <li class="list-group-item">
      <a href="<?php echo ROOT_URL; ?>shop?page=products-list&categoryId=<?php echo $category->id ?>"><?php echo $category->name ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php else : ?>
    <p>Nessuna categoria presente.</p>
  </div>
  <?php endif; ?>
</div>

