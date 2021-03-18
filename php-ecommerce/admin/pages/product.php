<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$id = 0;
$catMgr = new CategoryManager();
$categories = $catMgr->GetCategoriesAndSubs();

$mgr = new ProductManager();
$product = Product::CreateEmpty();
$product->images = [];

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
  $mtitle = trim($_POST['mtitle']);
  $category_id = trim($_POST['category_id']);
  $description = esc(trim($_POST['description']));
  $metadescription = esc(trim($_POST['metadescription']));
  $price = trim($_POST['price']);
  $sconto = isset($_POST['sconto']) ? trim($_POST['sconto']): "0";
  $data_inizio_sconto = trim($_POST['data_inizio_sconto']);
  $data_fine_sconto = trim($_POST['data_fine_sconto']);
  $qta = trim($_POST['qta']);
  $tmpDir = isset($_POST['tmpDir']) ? $_POST['tmpDir'] : NULL;

  if ($name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {

    $product = new Product(0, $name, $price, $description, $category_id, $sconto, $data_inizio_sconto, $data_fine_sconto, $qta,$mtitle,$metadescription);
    //var_dump($product);die;
    $id = $mgr->create($product);

    if ($id > 0) {
      if ($tmpDir) {
        $mgr->MoveTempImages($tmpDir, $id);        
      }
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
  $mtitle = trim($_POST['mtitle']);
  $metadescription = esc(trim($_POST['metadescription']));

  if(isset($_POST['data_inizio_sconto']) && $_POST['data_inizio_sconto'] != ""){$data_inizio_sconto= $_POST['data_inizio_sconto'];}else{$data_inizio_sconto= "NULL";}
  if(isset($_POST['data_fine_sconto']) && $_POST['data_fine_sconto'] != ""){$data_fine_sconto= $_POST['data_fine_sconto'];}else{$data_fine_sconto= "NULL";}


  if ($id != '' && $id != '0' && $name != '' && $category_id != '' && $category_id != '0' && $description != '' && $price != '') {

    $product = new Product($id, $name, $price, $description, $category_id,  $sconto, $data_inizio_sconto, $data_fine_sconto,$qta,$mtitle,$metadescription);
    $numUpdated = $mgr->update($product, $id);

    if ($numUpdated < 0) {
      $alertMsg = 'err';
    }
  } else {
    $alertMsg = 'mandatory_fields';
  }
}

if (isset($_POST['add']) || isset($_POST['update'])){

  $subcategoriesIds = [];
  $i = 1;
  while(isset($_POST['subcat-'.$i])) {
    if ($_POST['subcat-'.$i] > 0) {
      array_push($subcategoriesIds, (int) $_POST['subcat-'.$i]);      
    }
    $i++;
  }

  $catMgr->SaveSubcategories($subcategoriesIds, $id);

  echo "<script>location.href='".ROOT_URL."admin?page=products-list&msg=updated';</script>";
}

$productSubcats = $mgr->GetProductSubcategories($id);
?>

<a href="<?php echo ROOT_URL . 'admin?page=products-list'; ?>" class="back underline">&laquo; Lista Prodotti</a>

<h1><?php echo esc_html($lblAction); ?> Prodotto</h1>
<?php 
   $mtitle=isset($product->mtitle) ? $product->mtitle : NULL;
   $metadescript= isset($product->metadescription) ? $product->metadescription : NULL;  
   $descript= isset($product->description) ? $product->description : NULL;
?> 
<form method="post" class="mt-2">
  <div class="form-group">
    <label for="name"><strong>Titolo</strong></label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($product->name); ?>">

  </div>
  <div class="form-group">
    <label for="category_id"><strong>Categoria</strong></label>
    <select name="category_id" id="category_id" type="text" class="form-control" value="<?php echo esc_html($product->category_id); ?>">
      <option value="0"> - Scegli una categoria - </option>
      <?php if (count($categories) > 0) : ?>
        <?php foreach ($categories as $category) : ?>
          <?php
          $parentCategory = $category['parent'];
          ?>
          <option <?php if ($product->category_id == $parentCategory->id ) echo 'selected' ; ?> value="<?php echo esc_html($parentCategory->id); ?>"><?php echo esc_html($parentCategory->name); ?></option>
        <?php endforeach ; ?>
      <?php endif ; ?>
    </select>

    <?php
    $selectedIds = "";
    ?>
    <div id="subcatWrapper">

    <?php if ($productSubcats): ?>
      <div class="row pt-3">
        <div class="col-12">
          <label><strong>Sottocategorie</strong></label>
        </div>
        <?php
        $i = 1;
        $children = $productSubcats['children'];        
        ?>
        <?php foreach ($children as $subcat): ?>
        <div class="col-md-3 col-sm-4 col-6">
          <div class="form-check">
            <input <?php echo $subcat->is_selected  ? 'checked' : '' ?> class="form-check-input" type="checkbox" value="<?php echo $subcat->id; ?>" name="subcat-<?php echo $i; ?>" id="subcat-<?php echo $i; ?>">
            <label class="form-check-label" for="subcat-<?php echo $i; ?>">
              <?php echo $subcat->name; ?>
            </label>
          </div>
        </div>
        <?php
        $selectedIds .= ($subcat->is_selected) ? $subcat->id . ',' : '';
        $i++;
        ?>
        <?php endforeach; ?>        
      </div>
    <?php endif; ?>

    </div>
    <input type="hidden" id="selectedSubcatIds" value="<?php echo $selectedIds ?>">

  </div>

  <div class="form-group">
    <label class="mt-3" for="mtitle"><strong>Meta Titolo</strong></label>
    <textarea rows="2" name="mtitle" id="mtitle" type="text" class="form-control"><?php echo html_entity_decode($mtitle);?></textarea>

    <label class="mt-3" for="description"><strong>Descrizione</strong></label>
    <textarea rows="7" name="description" id="description" type="text" class="form-control"><?php echo html_entity_decode($descript);?></textarea>

    <label class="mt-3" for="metadescription"><strong>Meta Descrizione</strong></label>
    <textarea rows="7" name="metadescription" id="metad" type="text" class="form-control"><?php echo html_entity_decode($metadescript);?></textarea>
  </div> 
    
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label for="description"><strong>Prezzo</strong></label>
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
        <label for="description"><strong>Quantità</strong></label>
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
        <label for="description"><strong>Sconto</strong></label>
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
        <label for="description"><strong>Data Inizio</strong></label>
        <div class="input-group mb-3">
          <input type="date" class="form-control" id="data_inizio_sconto" name="data_inizio_sconto" value="<?php echo esc_html($product->data_inizio_sconto); ?>">
      
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="description"><strong>Data Fine</strong></label>
        <div class="input-group mb-3">
        <input type="date" class="form-control" id="data_fine_sconto" name="data_fine_sconto" value="<?php echo esc_html($product->data_fine_sconto); ?>">
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="imgLbl">Immagini</span>
        </div>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="img" aria-describedby="imgLbl" accept=".jpg" multiple>
          <label class="custom-file-label" for="inputGroupFile01">Aggiungi Immagini...</label>
        </div>
      </div>
    </div>

    <div class="mt-3 col-12 images-wrapper">
      <?php if ($product->images ) : ?>
      <div class="row product-images">
        <?php foreach ($product->images as $image) : ?>
        <div class="product-image col-md-3 col-sm-4 col-6">
          <span data-id="<?php echo $image->id ?>" title="Modifica" class="edit-img badge badge-info p-2 rounded-circle"><i class="fas fa-edit"></i></span>
          <span data-id="<?php echo $image->id ?>" title="Elimina" class="delete-img badge badge-danger p-2 rounded-circle">&times;</span>
          <img title="<?php echo $image->title ?>" data-order="<?php echo $image->order_number ?>" alt="<?php echo $image->alt ?>" data-id="<?php echo $image->id ?>" class="img-thumbnail" src="<?php echo ROOT_URL . '/images/' . $product->id . '/' . $image->id . '_thumbnail.' . $image->image_extension ?>" />
        </div>
        <?php endforeach ?>
      </div>
      <?php endif ?>
    </div>

  </div>

  <input type="hidden" id="id" name="id" value="<?php echo esc_html($product->id); ?>">
  <input type="hidden" id="tmpDir" name="tmpDir">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Prodotto">
</form>

<script>
var $document = $(document);
var $subcatWrapper = $('#subcatWrapper');
var $selectedSubcatIds = $('#selectedSubcatIds');
var selectedIds = $selectedSubcatIds.val().split(',');

$document.ready(function() {

  $('#img').on('change', uploadFiles );
  $('#category_id').on('change', getSubcategories);
  $document.on('click', 'input:checkbox', updateSelectedIds);
  $('form').on('submit', postUnchecked);

  $('#description').summernote({
    placeholder: 'Descrizione',
    tabsize: 2,
    height: 100
  });
  $('#metad').summernote({
    placeholder: 'Meta descrizione',
    tabsize: 2,
    height: 100
  });
  $('#mtitle').summernote({
    placeholder: 'Meta Titolo ',
    tabsize: 2,
    height: 100
  });

  $document.on('click', '.delete-img', e => deleteFile(e));
  $document.on('click', '.edit-img', e => openImageDetailsModal(e));
  $document.on('submit', '.imgDetails', e => saveImgDetails(e));

  

  window.addEventListener('beforeunload', removeTempImages, false);
});

function postUnchecked(){
  var checkboxes = $('input:checkbox');
  $.each(checkboxes, (i, cb) => {
    if (!$(cb).is(':checked')){
      var name = $(cb).attr('name');
      $(`<input type="hidden" name="${name}" value="0">`).insertAfter($(cb));
    }
  });
}

function updateSelectedIds(e) {
  var $target = $(e.target);
  var subcatId = $target.val();
  if ($target.is(':checked')){
    selectedIds.push(subcatId);
  } else {
    selectedIds = selectedIds.filter(currentId => currentId != subcatId);
  }
}

function getSubcategories(e){
  var selectedId = $(e.target).val();
  $.getJSON(rootUrl + 'api/admin/categories.php?action=getSubcategories&parentId=' + selectedId, response => createSubcategoriesList(response.data));
}

function createSubcategoriesList(data){
  
  $subcatWrapper.html('');

  if (!data || data.error) return;

  var markup = `
    <div class="row pt-3">
      <div class="col-12">
        <label><strong>Sottocategorie di "${data.parent.name}"</strong></label>
      </div>`;

  $.each(data.children, (i, child) => {
    var checked = '';
    if (selectedIds.includes(child.id)) {
      checked = 'checked';
    }
    markup += `<div class="col-md-3 col-sm-4 col-6">
          <div class="form-check">
            <input ${checked} class="form-check-input" type="checkbox" value="${child.id}" name="subcat-${(i+1)}" id="subcat-${(i+1)}">
            <label class="form-check-label" for="subcat-${(i+1)}">
              ${child.name}
            </label>
          </div>
        </div>`;
  });

  markup +=  '</div>';
  $subcatWrapper.html(markup);
}

function removeTempImages(){
  var tmpDir = $('#tmpDir').val();
    if (tmpDir != null && tmpDir != "") {
      $.post('../api/admin/delete.php', {action: 'removeTempImages', tmpDir: tmpDir});
    }
}

function saveImgDetails(e) {

  e.preventDefault();
  var $target = $(e.target);

  var id = $target.attr('data-id');
  var title = $target.find('#imgtitle').val();
  var alt = $target.find('#imgalt').val();
  var order = $target.find('#imgorder').val();

  $('.product-image img[data-id="'+id+'"]')
    .attr('title', title)
    .attr('alt', alt)
    .attr('data-order', order);

  var postData = {
    operation: 'img-details',
    id: id,
    title: title,
    alt: alt,
    order:order
  };
  
  $.post('../api/admin/upload.php', postData, response => {
    console.log(response);
  });

}

function openImageDetailsModal(e){
  var $target = $(e.target);
  var $img = $target.closest('.edit-img').siblings('img').first();

  var imageId = $img.attr('data-id');

  var imageTitle = $img.attr('title');
  imageTitle = imageTitle == null ? '' : imageTitle;

  var imageAlt = $img.attr('alt');
  imageAlt = imageAlt == null ? '' : imageAlt;

  var imageOrder = $img.attr('data-order');
  imageOrder = imageOrder == null ? '' : imageOrder;


  bootbox.confirm(`
    <form data-id="${imageId}" class='imgDetails' action=''>
      <label>Title:</label> 
      <input  class="form-control" type='text' name='imgtitle' id="imgtitle" value="${imageTitle}" /><br/>
      <label>Alt:</label> 
      <input  class="form-control" type='text'  name='imgalt' id="imgalt" value="${imageAlt}"  />
      <label>Order:</label> 
      <input  class="form-control" type='number' min="0"  name='imgorder' id="imgorder" value="${imageOrder}"  />
    </form>`, 
    function(result) {
      if(result)
          $('.imgDetails').submit();
    }
  );
}

function deleteFile(e) {
  if (!confirm("Confermi eliminazione ?")) return;

  var $target = $(e.target);
  var imageId = $target.attr('data-id');
  $.post('../api/admin/delete.php', {imageId: imageId}, response => {
    $target.closest('.product-image').fadeOut('slow', function(){$(this).remove();});
  });
}

function createImgList() {
  $('<div class="row product-images"></div>').appendTo('.images-wrapper');
}

function uploadFiles() {

  var $img = $('#img');
  var productId = $('#id').val(); 
  var tmpDir = $('#tmpDir').val();
  
  var form_data = new FormData();  

  form_data.append('productId', productId);
  form_data.append('tmpDir', tmpDir);

  $.each($img.prop('files'), function (index, file) {
    form_data.append('file-' + index, file);
  });
                           
  $.ajax({
    url: '../api/admin/upload.php', 
    dataType: 'text',  
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,                         
    type: 'post',
    success: function(response){
      response = JSON.parse(response);

      var images = response.images;
      tmpDir = response.tmpDir;
      $('#tmpDir').val(tmpDir);

      var $imgList = $('.product-images');
      if ($imgList.length == 0) {
        createImgList();
      }
      $imgList = $('.product-images');

      var htmlStr = '';
      $.each(images, (i, image) => {
        htmlStr += `
        <div class="product-image col-md-3 col-sm-4 col-6">
          <span data-id="${image.id}" title="Modifica" class="edit-img badge badge-info p-2 rounded-circle"><i class="fas fa-edit"></i></span>
          <span data-id="${image.id}" title="Elimina" class="delete-img badge badge-danger p-2 rounded-circle">&times;</span>
          <img title="${image.title}" data-order="${image.order}" alt="${image.alt}"  data-id="${image.id}" class="img-thumbnail" src="<?php echo ROOT_URL ?>/images/${image.product_id}/${image.id}_thumbnail.jpg" />
        </div>
        `;
      });
      $imgList.append(htmlStr);
    },
    error: function (err) {
      alert(err);
    }
  });

}
</script>