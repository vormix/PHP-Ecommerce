<?php

$email = '';
$password = '';
global $alertMsg;

if (isset($_POST['resetPwd'])) {

  $email = esc($_POST['email']);
  $userMgr = new UserManager();
  $userObj = $userMgr->getUserByEmail($email);

  if (!$userObj) {
    echo "<script>location.href='".ROOT_URL."auth?page=forgot-password&msg=email_not_exists';</script>";
    exit;
  }

  echo "<script>location.href='".ROOT_URL."auth?page=reset-password-request&email=$email';</script>";
  exit;
}
?>

<h3>Recupero Password</h3>
<p class="text-muted">Inserire l'indirizzo email del tuo account. </p>
<!-- <p class="text-muted">Ti invieremo una mail con un link per reimpostare la password.</p> -->

<form method="post" class="mb-4">
  <div class="form-group">
    <label for="email">Email</label>
    <input name="email" id="email" type="text" class="form-control" value="<?php echo $email; ?>">
  </div>
  <input class="btn btn-primary right" type="submit" value="Reimposta Password" name="resetPwd">
</form>

