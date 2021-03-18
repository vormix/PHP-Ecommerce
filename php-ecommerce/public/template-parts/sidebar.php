<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $catMgr = new CategoryManager();
  $categoriesAndSubs = $catMgr->GetCategoriesAndSubs();
  
?>

<div class="card mb-3" >
  <div class="card-header bg-dark text-light">
    Categorie
  </div>

  <?php if ($categoriesAndSubs): ?>
  <ul class="accordion p-0" id="categoriesMenu">

  <?php foreach ($categoriesAndSubs as $parentCategory): ?>
    <?php
    $parent = $parentCategory['parent'];
    ?>
    <li class="card border-0">
      <div class="card-header rounded-0 m-0" id="headingOne">               
          <a href="<?php echo $parent->url; ?>"><?php echo $parent->name ?></a>
          <span class="right text-success" data-toggle="collapse" data-target="#collapse-<?php echo $parent->id ?>" aria-expanded="true" aria-controls="collapseOne">
            <i class="caret fas fa-caret-down fa-lg"></i>
          </button>
      </div>

      <ul id="collapse-<?php echo $parent->id ?>" class="collapse list-group" aria-labelledby="headingOne" data-parent="#categoriesMenu">
        <?php foreach ($parentCategory['children'] as $child): ?>
          <li class="list-group-item"><a href="<?php echo $child->url; ?>"><?php echo $child->name ?></a></li>
        <?php endforeach; ?>
      </ul>
    </li>
    <?php endforeach; ?>
    
  </ul>
  <?php else : ?>
    <p>Nessuna categoria presente.</p>
  </div>
  <?php endif; ?>


</div>

