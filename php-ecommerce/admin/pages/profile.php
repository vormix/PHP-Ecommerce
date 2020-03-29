<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$id = 0;
$pm = new ProfileManager();
$profile = new Profile(0, '');
$profileTreatments = [];

$stm = new SpecialTreatmentManager();
$treatments = $stm->getAllTreatments();

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
  $profile = $pm->get($id);
  $profileTreatments = $pm->GetProfileTreatments($id);
  
  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {
  
    $name = trim($_POST['name']);
    $special_treatment_value = trim($_POST['special_treatment_value']);
    $type_code = trim($_POST['type_code']);

    if ($name != ''){

      $profile = new Profile(0, $name);
      $id = $pm->create($profile);
      if ($id < 0) {
        $alertMsg = 'err';
      }
    } else {
      $alertMsg = 'mandatory_fields';
    }
}
  
if (isset($_POST['update'])) {

    $name = trim($_POST['name']);
    if ($id != '' && $id != '0' && $name != '') {

      $profile = new Profile($id, $name);
      $numUpdated = $pm->update($profile, $id);
  
      if ($numUpdated < 0) {
        $alertMsg = 'err';
      }
    } else {
      $alertMsg = 'mandatory_fields';
    }  
  }

if (isset($_POST['add']) || isset($_POST['update'])) {
  $treatmentsToAdd = [];
  $i = 1;
  while (isset($_POST["treatment-$i"])) {
    $treatmentId = (int) $_POST["treatment-$i"];
    array_push($treatmentsToAdd, $treatmentId);
    $i++;
  };
  $pm->SaveProfileTreatments($id, $treatmentsToAdd);
  echo "<script>location.href='".ROOT_URL."admin?page=profiles-list&msg=updated';</script>";
}

 
?>
  <a href="<?php echo ROOT_URL . 'admin?page=profiles-list'; ?>" class="back underline">&laquo; Lista Profili</a>

<h1><?php echo esc_html($lblAction); ?> Profilo</h1>

<form method="post" class="mt-2">

  <div class="form-group">
    <label for="name">Nome</label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($profile->name); ?>">
  </div>

  <div class="form-group">
    <label for="name">Trattamenti</label>
    <div class="row">
      <div class="col-md-10">
        <select name="treatments" id="treatments" type="text" class="form-control form-control-sm" >
          <option value="0"> - Scegli un trattamento speciale - </option>
            <?php if (count($treatments) > 0) : ?>
              <?php foreach ($treatments as $treatment) : ?>
                <option value="<?php echo esc_html($treatment->id); ?>"><?php echo esc_html($treatment->name); ?></option>
              <?php endforeach ; ?>
            <?php endif ; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button id="addTreatment" name="addTreatment" type="button" class="right btn btn-sm btn-secondary">Aggiungi</button>
      </div>
    </div>
  </div>

  <?php $i = 1; ?>
  <div class="form-group">
    <table id="tableTreatments" class="table table-sm">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Trattamento</th>
          <th scope="col">Azioni</th>
        </tr>
      </thead>
      <tbody>
      <?php if (count($profileTreatments) > 0) : ?>
        <?php foreach ($profileTreatments as $treatment) : ?>
          <tr data-id="<?php echo esc_html($treatment->id) ?>">
            <input type="hidden" name="treatment-<?php echo $i ?>" value="<?php echo esc_html($treatment->id) ?>">
            <th scope="row"><?php echo $i ?></th>
            <td><?php echo esc_html($treatment->name) ?></td>
            <td>
              <button data-id="<?php echo esc_html($treatment->id) ?>" class="deleteTreatment btn btn-outline-danger btn-sm" type="button">Rimuovi</button>
            </td>
          </tr>
          <?php $i++; ?>
        <?php endforeach ; ?>
      <?php else: ?>
        <tr id="noRecords"><td colspan="3">Nessun trattamento...</td></tr>
      <?php endif ; ?>
      </tbody>
    </table>
  </div>

  <input id="submit" name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Profilo">
</form>

<script>
var $document = $(document);
var $treatments = $('#treatments');
var $tableTreatments = $('#tableTreatments');
var $addTreatment = $('#addTreatment');

$document.ready(function(){
  $addTreatment.on('click', addTreatment);
  $document.on('click', '.deleteTreatment', deleteTreatment);
});

function addTreatment(){
  var $option = $treatments.find(':selected');
  if ($option.val() == "0") return;

  var treatmentId = $option.val();
  var treatmentName = $option.text();

  $tableTreatments.find('#noRecords').remove();

  var $rows = $tableTreatments.find('tbody tr');
  var existsTreatment = false;
  $.each($rows, (i, row) => {
    if ($(row).attr('data-id') == treatmentId) {
      existsTreatment = true;
    }
  });
  if (existsTreatment) return;
  
  var index = $rows.length + 1;
  var markup = `
    <tr data-id="${treatmentId}">
      <input type="hidden" name="treatment-${index}" value="${treatmentId}">
      <th scope="row">${index}</th>
      <td>${treatmentName}</td>
      <td>
        <button data-id="${treatmentId}" class="deleteTreatment btn btn-outline-danger btn-sm" type="button">Rimuovi</button>
      </td>
    </tr>
  `;
  $tableTreatments.find('tbody').append(markup);
}

function deleteTreatment(e){
  bootbox.confirm('Confermi rimozione?', function(result){
    if (!result) return;

    var $target = $(e.target);
    $target.closest('tr').remove();
    resetIndexes();
  });  
}

function resetIndexes() {
  var $rows = $tableTreatments.find('tbody tr');
  $.each($rows, (i, row) => {
    $(row).find('input:hidden').attr('name', 'treatment-'+(i+1)); 
  });
}
</script>