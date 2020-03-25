<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

$ship = new ShipmentManager();
$shipment = new Shipment(0, '','');

global $alertMsg;

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {
  
  $id = trim($_GET['id']);
  $shipment = $ship->GetShipment($id);

  
  $lblAction = 'Modifica';
  $submit = 'update';
}

// Submit add
if (isset($_POST['add'])) {
    $price = trim($_POST['price']);
    $name = trim($_POST['name']);
    if ($name != ''&& $price!=''){

      $shipment = new Shipment(0, $name,$price);
      $id = $ship->create($shipment);
      if ($id > 0) {
        echo "<script>location.href='".ROOT_URL."admin?page=shipment-list&msg=created';</script>";
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
      $price = trim($_POST['price']);
      if ($id != '' && $id != '0') {

        $shipment = new Shipment($id, $name,$price);
        $numUpdated = $ship->update($shipment, $id);
    
        if ($numUpdated >= 0) {
          echo "<script>location.href='".ROOT_URL."admin?page=shipment-list&msg=updated';</script>";
          exit;
        } else {
          $alertMsg = 'err';
        }
      } else {
        $alertMsg = 'mandatory_fields';
      }
  
  }


 
?>
  <a href="<?php echo ROOT_URL . 'admin?page=shipment-list'; ?>" class="back underline">&laquo; Lista Spedizioni</a>

<h1><?php echo esc_html($lblAction); ?> Spedizione</h1>

<form method="post" class="mt-2">
  <div class="form-group">
    <label for="name">Nome</label>
    <input name="name" id="name" type="text" class="form-control" value="<?php echo esc_html($shipment->name); ?>">
  </div>
  <label for="name">Prezzo €</label>
    <input name="price" id="price" type="text" class="form-control" value="<?php echo esc_html($shipment->price); ?>">
  </div>
  <input type="hidden" id="id" name="id" value="<?php echo esc_html($shipment->id); ?>">
  <input type="hidden" id="tmpDir" name="tmpDir">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary mt-3" value="<?php echo esc_html($lblAction); ?> Spedizione">
</form>