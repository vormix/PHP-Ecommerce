<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

global $alertMsg;
$mgr = new UserManager();

if (isset($_POST['delete'])) {
  
  $id = trim($_POST['id']);
  $mgr->delete($id);
  $alertMsg = 'deleted';
}

$users = $mgr->getAll();
?>

<a href="<?php echo ROOT_URL . 'admin?page=user'; ?>" class="btn btn-primary mb-3">Aggiungi Utente</a>


<h1>Elenco Utenti</h1>

<?php if (count($users) > 0) : ?>
<table id="table" class="table table-hover">
  <thead>
    <tr>
      <th scope="col" class="big-screen">Nominativo</th>
      <th scope="col">Email</th>
      <th scope="col" class="big-screen">Tipo Utente</th>
      <th scope="col" class="right">Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user) : ?>
    <tr>
      <td class="big-screen"><?php echo esc_html($user->first_name) . ' ' . esc_html($user->last_name); ?></td>
      <td><?php echo esc_html($user->email); ?></td>
      <td class="big-screen"><?php echo esc_html($user->user_type); ?></td>
      <td>
        <a class="btn btn-outline-secondary btn-sm left" href="<?php echo ROOT_URL . 'admin?page=user'; ?>&id=<?php echo esc_html($user->id); ?>">Vedi</a>
        <form method="post" class="right">
          <input type="hidden" name="id" value="<?php echo esc_html($user->id); ?>">
          <input name="delete" onclick="return confirm('Procedere ad eliminare?');" type="submit" class="btn btn-outline-danger btn-sm" value="Elimina">
        </form>
      </td>
    </tr>
    <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <p>Nessun Utente presente...</p>
<?php endif ; ?>

<script>
 $(document).ready(function() {
    $('#table').DataTable({
      bLengthChange: false
    });
} );
</script>
