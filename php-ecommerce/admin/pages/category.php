<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
    die;
  }

  $ctm = new CategoryManager();

  global $alertMsg;

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {
  
  $id = trim($_GET['id']);
  $category = $ctm->GetCategory($id);

  
  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {
  
    $name = trim($_POST['name']);
    if ($name != ''){

      $category = new Category(0, $name);
      $id = $ctm->create($category);
      if ($id > 0) {
        echo "<script>location.href='".ROOT_URL."admin?page=category-list&msg=created';</script>";
        exit;
      } else {
        $alertMsg = 'err';
      }
    } else {
      $alertMsg = 'mandatory_fields';
    }
}
  if (isset($_POST['update'])) {

      $name = trim($_POST['name']);
      if ($id != '' && $id != '0') {

        $category = new Category($id, $name);
        $numUpdated = $ctm->update($category, $id);
    
        if ($numUpdated >= 0) {
          echo "<script>location.href='".ROOT_URL."admin?page=category-list&msg=updated';</script>";
          exit;
        } else {
          $alertMsg = 'err';
        }
      } else {
        $alertMsg = 'mandatory_fields';
      }
  
  }


 
?>
  <a href="<?php echo ROOT_URL . 'admin?page=category-list'; ?>" class="back underline">&laquo; Lista Categorie</a>

<h1><?php echo esc_html($lblAction); ?> Categoria</h1>

<form method="post" class="mt-2">
  <div class="form-group">
    <label for="name">Nome</label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($category->name); ?>">
  </div>
 
  <input type="hidden" id="id" name="id" value="<?php echo esc_html($category->id); ?>">
  <input type="hidden" id="tmpDir" name="tmpDir">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Categoria">
</form>
