<?php

  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $emailMgr = new EmailManager();
  if (isset($_POST['delete'])) {

    $id = trim($_POST['id']);
    $emailMgr->DeleteEmail($id);
    $alertMsg = 'deleted';
  }
  
  $emails=$emailMgr->GetEmails();

?>
<a href="<?php echo ROOT_URL . 'admin?page=email'; ?>" class="btn btn-primary mb-3">Aggiungi Email</a>

<h1>Elenco Emails</h1>

<?php if (count($emails) > 0) : ?>
<table id="table" class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Oggetto</th>
      <th scope="col" >Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($emails as $email) : ?>
    <tr>
      <td><?php echo esc_html($email->subject); ?></td>
      <td>
        <form method="post" class="inline" >
          <input type="hidden" name="id" value="<?php echo esc_html($email->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo ROOT_URL . 'admin?page=email'; ?>&id=<?php echo esc_html($email->id); ?>">Vedi</a>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessuna Email presente...</p>
<?php endif ; ?>

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css"> -->

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" ></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" ></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" ></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js" ></script>

<script>
 $(document).ready(function() {
    $('#table').DataTable();
} );
</script>