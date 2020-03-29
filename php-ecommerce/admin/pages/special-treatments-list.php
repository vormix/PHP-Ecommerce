<?php

  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
  $stm = new SpecialTreatmentManager();
  if (isset($_POST['delete'])) {

    $id = trim($_POST['id']);
    if (!is_numeric($id)) {
      echo 'prevent sql injection';
      die;
    }

    $stm->delete($id);
    $alertMsg = 'deleted';
  }
  
  $specialTreatments=$stm->getAllTreatments();

?>
<a href="<?php echo ROOT_URL . 'admin?page=special-treatment'; ?>" class="btn btn-primary mb-3">Aggiungi Trattamento Speciale</a>

<h1>Elenco Trattamenti Speciali</h1>

<?php if (count($specialTreatments) > 0) : ?>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">Tipo</th>
      <th scope="col">Chiave</th>
      <th scope="col">Valore</th>
      <th scope="col" >Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($specialTreatments as $specialTreatment) : ?>
    <tr>
      <td><?php echo esc_html($specialTreatment->name); ?></td>
      <td><?php echo esc_html($specialTreatment->type_desc); ?></td>
      <td><?php echo esc_html($specialTreatment->special_treatment_name); ?></td>
      <td><?php echo esc_html($specialTreatment->special_treatment_value); ?></td>
      <td>
        <form method="post" class="inline" >
          <input type="hidden" name="id" value="<?php echo esc_html($specialTreatment->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=special-treatment'; ?>&id=<?php echo esc_html($specialTreatment->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessun Trattamento speciale presente...</p>
<?php endif ; ?>

