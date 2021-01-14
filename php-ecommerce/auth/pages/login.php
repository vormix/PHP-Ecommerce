
<?php

if ($loggedInUser) {
  echo '<script>location.href="'.ROOT_URL.'public"</script>';
  exit;
}

if (isset($_POST['login'])) {
  // logica di login ...

  $user = (object) [
    'id' => 1,
    'email' => 'utente@mail.it',
    'is_admin' => true
  ];

  $_SESSION['user'] = $user;

}

?>

<form method="post">
  <button type="submit" name="login">Login</button>
</form>