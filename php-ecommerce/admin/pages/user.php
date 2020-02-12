<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

global $alertMsg;
$mgr = new UserManager();
$user = new User(0, '', '', '', '');

$lblAction = 'Aggiungi';
$submit = 'add';

// Querystring param id
if (isset($_GET['id'])) {

  $id = trim($_GET['id']);
  $user = $mgr->get($id);

  $lblAction = 'Modifica';
  $submit    = 'update';
}

// Submit add
if (isset($_POST['add'])) {

  $first_name = trim($_POST['first_name']);
  $last_name  = trim($_POST['last_name']);
  $email      = trim($_POST['email']);
  $user_type  = trim($_POST['user_type']);

  if ($first_name != '' && $last_name != '' && $email != '' && $user_type != '') {

    $id = $mgr->createUser(new User(0, $first_name, $last_name, $email, $user_type), null);
      
    if ($id > 0) {
      echo "<script>location.href='".ROOT_URL."admin?page=users-list&msg=created';</script>";
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

  $first_name   = trim($_POST['first_name']);
  $last_name    = trim($_POST['last_name']);
  $email        = trim($_POST['email']);
  $user_type    = trim($_POST['user_type']);
  $id           = trim($_POST['id']);

  if ($id != '' && $id != '0' && $first_name != '' && $last_name != '' && $email != '' && $user_type != '') {

    $numUpdated = $mgr->update(new User($id, $first_name, $last_name, $email, $user_type), $id);

    if ($numUpdated > 0) {
      echo "<script>location.href='".ROOT_URL."admin?page=users-list&msg=updated';</script>";
      exit;
    } else {
      $alertMsg = 'err';
    }
  } else {
    $alertMsg = 'mandatory_fields';
  }
}
?>

<a href="<?php echo ROOT_URL . 'admin?page=users-list'; ?>" class="back underline">&laquo; Lista Utenti</a>

<h1><?php echo esc_html($lblAction); ?> Utente</h1>

<form method="post" class="mt-5">
  <div class="form-group">
    <label for="first_name">Nome</label>
    <input name="first_name" id="first_name" type="text" class="form-control" value="<?php echo esc_html($user->first_name); ?>">
  </div>
  <div class="form-group">
    <label for="last_name">Cognome</label>
    <input name="last_name" id="last_name" type="text" class="form-control" value="<?php echo esc_html($user->last_name); ?>">
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input name="email" id="email" type="text" class="form-control" value="<?php echo esc_html($user->email); ?>">
  </div>
  <div class="form-group">
    <label for="user_type">Tipo Utente</label>
    <select name="user_type" id="user_type" type="text" class="form-control" value="<?php echo esc_html($user->user_type); ?>">
      <option value=""> - Seleziona - </option>
      <option <?php if ($user->user_type == 'admin' ) echo 'selected' ; ?> value="admin">Amministratore</option>
      <option <?php if ($user->user_type == 'regular' ) echo 'selected' ; ?> value="regular">Regolare</option>
    </select>
  </div>
  <input type="hidden" name="id" value="<?php echo esc_html($user->id); ?>">
  <input name="<?php echo esc_html($submit); ?>" type="submit" class="btn btn-primary" value="<?php echo esc_html($lblAction); ?> Utente">
</form>
