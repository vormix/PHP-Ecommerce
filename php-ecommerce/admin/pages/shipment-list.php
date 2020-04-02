<?php

  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
  $ship = new ShipmentManager();
  if (isset($_POST['delete'])) {

    $id = trim($_POST['id']);
    $ship->DeleteShipment($id);
    $alertMsg = 'deleted';
  }
  
  $shipments=$ship->GetShipments();

?>
<a href="<?php echo ROOT_URL . 'admin?page=shipment'; ?>" class="btn btn-primary mb-3">Aggiungi Spedizione</a>

<h1>Elenco Spedizioni</h1>

<?php if (count($shipments) > 0) : ?>
<table id="table" class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col" >Prezzo</th>
      <th scope="col" >Azioni</th>

    </tr>
  </thead>
  <tbody>
    <?php foreach ($shipments as $shipment) : ?>
    <tr>
      <td><?php echo esc_html($shipment->name); ?></td>
      <td><?php if($shipment->price=="0.00"){echo "Gratuita";}else{echo esc_html($shipment->price)." €";} ?> </td>
      <td>
        <form method="post" class="inline" >
          <input type="hidden" name="id" value="<?php echo esc_html($shipment->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=shipment'; ?>&id=<?php echo esc_html($shipment->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessuna Spedizione presente...</p>
<?php endif ; ?>

<script>
 $(document).ready(function() {
    $('#table').DataTable({
      bLengthChange: false
    });
} );
</script>

