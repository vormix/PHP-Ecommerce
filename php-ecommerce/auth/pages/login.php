<?php

$email = '';
$password = '';

if (isset($_POST['login'])) {

  $email = $_POST['email'];
  $password = $_POST['password'];

  $userMgr = new UserManager();

  $userObj = $userMgr->login($email, $password);

  if ($userObj) {
    $_SESSION['user'] = serialize($userObj);
    if (isset($_SESSION['client_id'])) {
      $cartMgr = new CartManager();
      //var_dump($_SESSION); die;
      $cartMgr->mergeCarts();
    }
    echo "<script>location.href='".ROOT_URL."user?page=dashboard';</script>";
    exit;
  } else {
    global $alertMsg;
    $alertMsg = 'login_err';
  }
}
?>

<h1>Login</h1>

<form method="post" class="mb-4">
  <div class="form-group">
    <label for="email">Email</label>
    <input name="email" id="email" type="text" class="form-control" value="<?php echo $email; ?>">
  </div>
  <div class="form-group">
    <label for="name">Password</label>
    <input name="password" id="password" type="password" class="form-control" value="<?php echo $password; ?>">
  </div>
  <input class="btn btn-primary right" type="submit" value="login" name="login">
  <a class="underline" href="<?php echo ROOT_URL; ?>auth?page=register">Non hai un account? Registrati</a>
</form>

