<?php

global $loggedInUser;

if ($loggedInUser) {
  echo "<script>location.href='".ROOT_URL."user'</script>";
  exit;
}

$password = isset($_POST["password"]) ? esc($_POST["password"]) : "";
$confirm_password = isset($_POST["confirm_password"]) ? esc($_POST["confirm_password"]) : "";
$guid = isset($_POST["guid"]) ? esc($_POST["guid"]) : esc($_GET["guid"]);

if (isset($_POST['resetPwd'])) 
{


  if ($password == $confirm_password) {

    $userMgr = new UserManager();
    $userId = $userMgr->guidExists($guid);
    $userMgr->updatePassword($userId, $password);
    $userMgr->invalidateGuid($guid);
    echo "<script>location.href='".ROOT_URL."auth?page=login&msg=password_updated'</script>";
    exit;
  }
  global $alertMsg;
  $alertMsg = "passwords_not_match";

}
else
{
  if (!isset($_GET['guid'])) {
    echo 'richiesta non valida';
    exit;
  }
  
  $guid = esc($_GET['guid']);
  $userMgr = new UserManager();
  $guidExists = $userMgr->guidExists($guid);
  if (!$guidExists){
    echo 'richiesta non valida';
    exit;
  }

}

?>

<h1>Reset Password</h1>
<p>Imposta una nuova password per il tuo account </p>

<form method="post" class="mb-4">
  <div class="form-group">
      <label for="password">Password</label>
      <input name="password" id="password" type="password" class="form-control" value="<?php echo esc_html($password); ?>">
    </div>
    <div class="form-group">
      <label for="confirm_password">Conferma Password</label>
      <input name="confirm_password" id="confirm_password" type="password" class="form-control" value="<?php echo esc_html($confirm_password); ?>">
    </div>
    <input type="hidden" name="guid" value="<?php echo $guid ?>">
  <input class="btn btn-primary right mt-3" type="submit" value="Reset Password" name="resetPwd">
</form>




