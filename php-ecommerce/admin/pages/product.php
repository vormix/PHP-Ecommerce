<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$mgr = new ProductManager();
$product = new Product(0, '', 0, '', 0);


global $alertMsg;

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {

  $id = trim($_GET['id']);
  $product = $mgr->get($id);

  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {

  $name = trim($_POST['name']);
  $category_id = trim($_POST['category_id']);
  $description = trim($_POST['description']);
  $price = trim($_POST['price']);



  if ($name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {

    $id = $mgr->create(new Product(0, $name, $price, $description, $category_id));

    if ($id > 0) {
      echo "<script>location.href='".ROOT_URL."admin?page=products-list&msg=created';</script>";
      exit;
    } else {
      $alertMsg = 'err';
    }
  } else {
    $alertMsg = 'mandatory_fields';
  }
}

// Submit update
if (isset($_POST['update'])) {

  $name = trim($_POST['name']);
  $category_id = trim($_POST['category_id']);
  $description = trim($_POST['description']);
  $price = trim($_POST['price']);
  $id = trim($_POST['id']);

  if ($id != '' && $id != '0' && $name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {

    $numUpdated = $mgr->update(new Product($id, $name, $price, $description, $category_id), $id);

    if ($numUpdated > 0) {
      echo "<script>location.href='".ROOT_URL."admin?page=products-list&msg=updated';</script>";
      exit;
    } else {
      $alertMsg = 'err';
    }
  } else {
    $alertMsg = 'mandatory_fields';
  }
}
?>

<a href="<?php echo ROOT_URL . 'admin?page=products-list'; ?>" class="back underline">&laquo; Lista Prodotti</a>

<h1><?php echo esc_html($lblAction); ?> Prodotto</h1>

<form method="post" class="mt-5">
  <div class="form-group">
    <label for="name">Nome</label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($product->name); ?>">
  </div>
  <div class="form-group">
    <label for="category_id">Categoria</label>
    <select name="category_id" id="category_id" type="text" class="form-control" value="<?php echo esc_html($product->category_id); ?>">
      <option value="0"> - Scegli una categoria - </option>
      <option <?php if ($product->category_id == '1' ) echo 'selected' ; ?> value="1">Categoria 1</option>
      <option <?php if ($product->category_id == '2' ) echo 'selected' ; ?> value="2">Categoria 2</option>
    </select>
  </div>
  <div class="form-group">
    <label for="description">Descrizione</label>
    <textarea rows="7" name="description" id="description" type="text" class="form-control"><?php echo esc_html($product->description); ?></textarea>
  </div>
  <div class="form-group">
    <label for="price">Prezzo</label>
    <div class="form-group">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text">€</span>
        </div>
        <input type="text" class="form-control" name="price" id="price" value="<?php echo esc_html($product->price); ?>" >
      </div>
    </div>
  </div>
  <input type="hidden" name="id" value="<?php echo esc_html($product->id); ?>">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary" value="<?php echo esc_html($lblAction); ?> Prodotto">
</form>

