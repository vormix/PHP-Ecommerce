
<?php

$errMsg = '';

if ($loggedInUser) {
  echo '<script>location.href="'.ROOT_URL.'public"</script>';
  exit;
}

if (isset($_POST['login'])) {

  $email = htmlspecialchars(trim($_POST['email']));
  $password = htmlspecialchars(trim($_POST['password']));
  
  $userMgr = new UserManager();
  $result = $userMgr->login($email, $password);

  if ($result) {
    echo '<script>location.href="'.ROOT_URL.'public"</script>';
    exit;
  } else {
    $errMsg = 'Login Fallito...';
  }
}

?>

<h2>Login</h2>

<form method="post">

  <div class="form-group">
    <label for="email">Email</label>
    <input name="email" id="email" type="text" class="form-control">
  </div>

   <div class="form-group">
    <label for="password">Password</label>
    <input name="password" id="password" type="password" class="form-control">
  </div>

  <div class="text-danger">
    <?php echo $errMsg ?>
  </div>

  <button class="btn btn-primary" type="submit" name="login">Login</button>
</form>

Non hai un account ? <a href="<?php echo ROOT_URL ?>auth?page=register">Registrati &raquo;</a>