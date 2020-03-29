<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$stm = new SpecialTreatmentManager();
$treatmentTypes = $stm->GetTypes();
$st = new SpecialTreatment(0, '', '', '');

global $alertMsg;

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {
  
  $id = trim($_GET['id']);
  if (!is_numeric($id)) {
    echo 'prevent sql injection';
    die;
  }
  $st = $stm->get($id);
  
  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {
  
    $name = trim($_POST['name']);
    $special_treatment_value = trim($_POST['special_treatment_value']);
    $type_code = trim($_POST['type_code']);

    if ($name != '' && $special_treatment_value != '' && $type_code != ''){

      $st = new SpecialTreatment(0, $name, $special_treatment_value, $type_code);
      $id = $stm->create($st);
      if ($id > 0) {
        echo "<script>location.href='".ROOT_URL."admin?page=special-treatment&msg=created';</script>";
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
    $special_treatment_value = trim($_POST['special_treatment_value']);
    $type_code = trim($_POST['type_code']);

    if ($id != '' && $id != '0' && $name != '' && $special_treatment_value != '' && $type_code != '') {

      $st = new SpecialTreatment($id, $name, $special_treatment_value, $type_code);
      $numUpdated = $stm->update($st, $id);
  
      if ($numUpdated >= 0) {
        echo "<script>location.href='".ROOT_URL."admin?page=special-treatments-list&msg=updated';</script>";
        exit;
      } else {
        $alertMsg = 'err';
      }
    } else {
      $alertMsg = 'mandatory_fields';
    }
  
  }


 
?>
  <a href="<?php echo ROOT_URL . 'admin?page=special-treatments-list'; ?>" class="back underline">&laquo; Lista Trattamenti Speciali</a>

<h1><?php echo esc_html($lblAction); ?> Trattamento Speciale</h1>

<form method="post" class="mt-2">

  <div class="form-group">
    <label for="name">Nome</label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($st->name); ?>">
  </div>

  <div class="form-group">
    <label for="category_id">Tipologia Trattamento</label>
    <select name="type_code" id="type_code" type="text" class="form-control" value="<?php echo esc_html($st->type_code); ?>">
      <option value="0"> - Scegli una tipologia - </option>
      <?php if (count($treatmentTypes) > 0) : ?>
        <?php foreach ($treatmentTypes as $type) : ?>
          <option data-type-name="<?php echo $type->special_treatment_name ?>" <?php if ($st->type_code == $type->code ) echo 'selected' ; ?> value="<?php echo esc_html($type->code); ?>"><?php echo esc_html($type->description); ?></option>
        <?php endforeach ; ?>
      <?php endif ; ?>
    </select>
  </div>
 
  <input type="hidden" id="id" name="id" value="<?php echo esc_html($st->id); ?>">
  <input type="hidden" id="value" name="value" value="<?php echo esc_html($st->special_treatment_value); ?>">

  <input id="submit" name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Trattamento Speciale">
</form>

<script>
var $type_code;
$(document).ready(function(){
  $type_code = $('#type_code');
  setLabel();
  $type_code.on('change', setLabel);
});

function setLabel(){
  $selectedOption = $type_code.find(":selected");
  descType = $selectedOption.text();
  codeType = $selectedOption.val();
  label = $selectedOption.attr('data-type-name');
  var value = $('.treatmentValueInput').val() || $('#value').val();
  createTreatmentValueInput(descType, codeType, label, value);
}

function createTreatmentValueInput(descType, codeType, label, value){
  $('.treatmentValueInput').remove();
  if (codeType == "0") return;

  var markup = `
  <div class="treatmentValueInput" class="form-group">
    <label for="special_treatment_value">${descType}</label>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">${label}</span>
      </div>
      <input data-code-type="${codeType}" value="${value}" name="special_treatment_value" id="special_treatment_value" type="text" class="form-control" >
    </div>
  </div>
  `;
  $(markup).insertBefore('#submit');

}
</script>