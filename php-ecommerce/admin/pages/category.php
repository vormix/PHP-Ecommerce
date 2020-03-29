<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$ctm = new CategoryManager();
$categories = $ctm->GetCategories();
$category = new Category(0, '','', '', null);

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
    $description = trim($_POST['description']);
    $metadescription = trim($_POST['metadescription']);
    $parent_id = trim($_POST['parent_id']);
    
    if ($name != ''){

      $category = new Category(0, $name, $description, $metadescription, $parent_id);
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
      $description=trim($_POST['description']);
      $metadescription=trim($_POST['metadescription']);
      $parent_id = trim($_POST['parent_id']);

      if ($id != '' && $id != '0') {

        $category = new Category($id, $name, $description, $metadescription, $parent_id);
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
  <div class="form-group">
  <div class="custom-file">
          <input type="file" class="custom-file-input" id="img" aria-describedby="imgLbl" accept=".jpg" multiple>
          <label class="custom-file-label" for="inputGroupFile01">Aggiungi Immagine</label>
        </div><hr>
    <label for="description">Descrizione</label>
    <textarea rows="7" name="description" id="description" type="text" class="form-control"><?php echo html_entity_decode($category->description);?></textarea>
    <textarea rows="7" name="metadescription" id="metad" type="text" class="form-control"><?php echo html_entity_decode($category->metadesc);?></textarea>
    </div>

    <div class="form-group">
    <label for="category_id">Categoria Padre</label>
    <select name="parent_id" id="parent_id" type="text" class="form-control" value="<?php echo esc_html($category->parent_id); ?>">
      <option value="0"> - Scegli una categoria padre - </option>
      <?php if (count($categories) > 0) : ?>
        <?php foreach ($categories as $cat) : ?>
          <option <?php if ($category->parent_id == $cat->id ) echo 'selected' ; ?> value="<?php echo esc_html($cat->id); ?>"><?php echo esc_html($cat->name); ?></option>
        <?php endforeach ; ?>
      <?php endif ; ?>
    </select>
  </div>
  <div class="form-group">

  <input type="hidden" id="id" name="id" value="<?php echo esc_html($category->id); ?>">
  <input type="hidden" id="tmpDir" name="tmpDir">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Categoria">
</form>
<script>
var $document = $(document);
$document.ready(function() {

   // $('#img').on('change', uploadFiles );

    $('#description').summernote({
      placeholder: 'Descrizione',
      tabsize: 2,
      height: 100
    });
    $('#metad').summernote({
      placeholder: 'Meta descrizione',
      tabsize: 2,
      height: 50
    });

    $document.on('click', '.delete-img', e => deleteFile(e));
  $document.on('click', '.edit-img', e => openImageDetailsModal(e));
  $document.on('submit', '.imgDetails', e => saveImgDetails(e));

  

  window.addEventListener('beforeunload', removeTempImages, false);
    
});
</script>