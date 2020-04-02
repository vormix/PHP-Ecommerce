<?php

  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
  $stm = new ProfileManager();
  if (isset($_POST['delete'])) {

    $id = trim($_POST['id']);
    if (!is_numeric($id)) {
      echo 'prevent sql injection';
      die;
    }

    $stm->delete($id);
    $alertMsg = 'deleted';
  }
  
  $profiles=$stm->getAllProfiles();

?>
<a href="<?php echo ROOT_URL . 'admin?page=profile'; ?>" class="btn btn-primary mb-3">Aggiungi Profilo</a>

<h1>Elenco Profili</h1>

<?php if (count($profiles) > 0) : ?>
<table id="table" class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">N. Trattamenti</th>
      <th scope="col" >Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($profiles as $profile) : ?>
    <tr>
      <td><?php echo esc_html($profile->name); ?></td>
      <td><?php echo esc_html(count($profile->treatments_count)); ?></td>
      <td>
        <form method="post" class="inline" >
          <input type="hidden" name="id" value="<?php echo esc_html($profile->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=profile'; ?>&id=<?php echo esc_html($profile->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessun Profilo presente...</p>
<?php endif ; ?>

<script>
 $(document).ready(function() {
    $('#table').DataTable({
      bLengthChange: false
    });
} );
</script>
