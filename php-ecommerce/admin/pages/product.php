<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$mgr = new ProductManager();
//$product = new Product(0, '', 0, '', 0);


global $alertMsg;

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {

  $id = trim($_GET['id']);
  $product = $mgr->GetProductWithImages($id);

  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {

  $name = trim($_POST['name']);
  $category_id = trim($_POST['category_id']);
  $description = trim($_POST['description']);
  $price = trim($_POST['price']);
  $sconto = isset($_POST['sconto']) ? trim($_POST['sconto']): "0";
  $data_inizio_sconto = trim($_POST['data_inizio_sconto']);
  $data_fine_sconto = trim($_POST['data_fine_sconto']);
  $qta = trim($_POST['qta']);

  if ($name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {

    $id = $mgr->create(new Product(0, $name, $price, $description, $category_id, $sconto, $data_inizio_sconto, $data_fine_sconto, $qta));

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
  $sconto = isset($_POST['sconto']) ? trim($_POST['sconto']): "0";
  $qta = trim($_POST['qta']);
  
  if(isset($_POST['data_inizio_sconto']) && $_POST['data_inizio_sconto'] != ""){$data_inizio_sconto= $_POST['data_inizio_sconto'];}else{$data_inizio_sconto= "NULL";}
  if(isset($_POST['data_fine_sconto']) && $_POST['data_fine_sconto'] != ""){$data_fine_sconto= $_POST['data_fine_sconto'];}else{$data_fine_sconto= "NULL";}


  if ($id != '' && $id != '0' && $name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {
    // var_dump($data_fine_sconto); die;
    // if($data_inizio_sconto != null){
    //   $parts = explode('/', $data_inizio_sconto);
    //   $dateStart  = "$parts[2]-$parts[1]-$parts[0]";
    // }
    // if($data_fine_sconto != null){
    //   $parts = explode('/', $data_fine_sconto);
    //   $dateEnd  = "$parts[2]-$parts[1]-$parts[0]";
    // }

    $numUpdated = $mgr->update(new Product($id, $name, $price, $description, $category_id,  $sconto, $data_inizio_sconto, $data_fine_sconto,$qta ), $id);

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
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="description">Prezzo</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">€</span>
          </div>
          <input type="text" class="form-control" name="price" id="price" value="<?php echo esc_html($product->price); ?>" >
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="description">Quantità</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Pz</span>
          </div>
          <input type="number" class="form-control" name="qta" min="1" step="1" value="<?php echo esc_html($product->qta); ?>" >
        </div>
      </div>
    </div>
    </div>
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="description">Sconto</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">%</span>
          </div>
          <input min="0" max="100" type="number" step="1" class="form-control" name="sconto" id="sconto" value="<?php echo esc_html($product->sconto ); ?>" >
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="description">Data Inizio</label>
        <div class="input-group mb-3">
          <input type="date" class="form-control" id="data_inizio_sconto" name="data_inizio_sconto" value="<?php echo esc_html($product->data_inizio_sconto); ?>">
      
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="description">Data Fine</label>
        <div class="input-group mb-3">
        <input type="date" class="form-control" id="data_fine_sconto" name="data_fine_sconto" value="<?php echo esc_html($product->data_fine_sconto); ?>">
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text" id="imgLbl">Immagini</span>
        </div>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="img" aria-describedby="imgLbl" accept=".jpg" multiple>
          <label class="custom-file-label" for="inputGroupFile01">Carica Immagine...</label>
        </div>
      </div>
    </div>
  </div>

  <div class="images-wrapper">
    <?php if ($product->images ) : ?>
    <div class="row product-images">
      <?php foreach ($product->images as $image) : ?>
      <div class="product-image col-md-3 col-sm-4 col-6">
        <span data-id="<?php echo $image->id ?>" title="Elimina" class="delete-img badge badge-danger p-2 rounded-circle">&times;</span>
        <img data-id="<?php echo $image->id ?>" class="img-thumbnail" src="<?php echo ROOT_URL . '/images/' . $product->id . '/' . $image->id . '.' . $image->image_extension ?>" />
      </div>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>

  <input type="hidden" id="id" name="id" value="<?php echo esc_html($product->id); ?>">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary" value="<?php echo esc_html($lblAction); ?> Prodotto">
</form>

<script>
var $document = $(document);
$document.ready(function() {
  $('#img').on('change', uploadFiles );

  $document.on('click', '.delete-img', e => deleteFile(e))
});

function deleteFile(e) {
  if (!confirm("Confermi eliminazione ?")) return;

  var $target = $(e.target);
  var imageId = $target.attr('data-id');
  $.post('../delete.php', {imageId: imageId}, response => {
    $target.closest('.product-image').fadeOut('slow', function(){$(this).remove();});
  });
}

function createImgList() {
  $('<div class="row product-images"></div>').appendTo('.images-wrapper');
}

function uploadFiles() {

  var $img = $('#img');
  var productId = $('#id').val();  
  var form_data = new FormData();                  
  form_data.append('productId', productId);

  $.each($img.prop('files'), function (index, file) {
    form_data.append('file', file);
  });
  //var file_data = $img.prop('files')[0];   


    //alert(form_data);                             
    $.ajax({
        url: '../upload.php', // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(response){
            var image = JSON.parse(response).image;
            var $imgList = $('.product-images');
            if ($imgList.length == 0) {
              createImgList();
            }
            $imgList = $('.product-images');

            var htmlStr = `
            <div class="product-image col-md-3 col-sm-4 col-6">
              <span data-id="${image.id}" title="Elimina" class="delete-img badge badge-danger p-2 rounded-circle">&times;</span>
              <img data-id="${image.id}" class="img-thumbnail" src="<?php echo ROOT_URL ?>/images/${image.product_id}/${image.id}.jpg" />
            </div>
            `;
            $imgList.append(htmlStr);
        },
        error: function (err) {
          alert(err);
        }
     });

}
</script>