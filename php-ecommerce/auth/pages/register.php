<?php

$nome = '';
$cognome = '';
$email = '';
$password = '';
$confirm_password = '';
$street = '';
$city = '';
$cap = '';

if (isset($_POST['register'])) {

  global $alertMsg;
  $errors = false;
  //var_dump($_POST); die;

  $nome = esc($_POST['nome']);
  $cognome = esc($_POST['cognome']);
  $email = esc($_POST['email']);
  $password = esc($_POST['password']);
  $confirm_password = esc($_POST['confirm_password']);

  $street = esc($_POST['street']);
  $city = esc($_POST['city']);
  $cap = esc($_POST['cap']);

  if ($nome != '' AND $cognome != '' AND $email != '' AND $password != '' AND $confirm_password != '' AND $street != '' AND $city != '' AND $cap != '' ) {

    $userMgr = new UserManager();

    if(!$errors AND !$userMgr->isValidEmail($email)) {
      $alertMsg = 'invalid_email';
      $errors = true;
    }

    if(!$errors AND !$userMgr->isValidPassword($password)) {
      $alertMsg = 'invalid_password';
      $errors = true;
    }

    if(!$errors AND !$userMgr->passwordsMatch($password, $confirm_password)){
      $alertMsg = 'passwords_not_match';
      $errors = true;
    }

    if(!$errors AND $userMgr->userExists($email)){
      $alertMsg = 'user_already_exists';
      $errors = true;
    }

    if (!$errors ) {
      $userId = $userMgr->register($nome, $cognome, $email, $password);
      if ($userId > 0){
        $userMgr->createAddress($userId, $street, $city, $cap);
        echo "<script>location.href='".ROOT_URL."auth?page=login&msg=registered';</script>";
        exit;
      } else {
        $alertMsg = 'err';
      }     
    }
  } else {
    $alertMsg = 'mandatory_fields';
  }
}
?>

<a class="underline " href="<?php echo ROOT_URL; ?>auth?page=login">Già Possiedi un account? Accedi</a>

<h1>Registrazione</h1>

<form method="post" class="mb-4">
  <h5 class="mb-3 mt-3">Informazioni personali</h5>
  <div class="form-group">
    <label for="nome">Nome</label>
    <input name="nome" id="nome" type="text" class="form-control" value="<?php echo esc_html($nome); ?>">
  </div>
  <div class="form-group">
    <label for="cognome">Cognome</label>
    <input name="cognome" id="cognome" type="text" class="form-control" value="<?php echo esc_html($cognome); ?>">
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input name="email" id="email" type="text" class="form-control" value="<?php echo esc_html($email); ?>">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input name="password" id="password" type="password" class="form-control" value="<?php echo esc_html($password); ?>">
  </div>
  <div class="form-group">
    <label for="confirm_password">Conferma Password</label>
    <input name="confirm_password" id="confirm_password" type="password" class="form-control" value="<?php echo esc_html($confirm_password); ?>">
  </div>

  <hr class=mb-4>

  <h5  class="mb-3 mt-3">Indirizzo di spedizione</h5>
  <div class="mb-3">
    <label for="street">Via</label>
    <input name="street" type="text" class="form-control"  id="street" value="<?php echo esc_html($street); ?>" >
  </div>
  <div class="row">
    <div class="col-md-8 mb-3">
      <label for="city">Città</label>
      <input name="city" type="text" class="form-control"  id="city" value="<?php echo esc_html($city); ?>" >
    </div>
    <div class="col-md-4 mb-3">
      <label for="cap">CAP</label>
      <input name="cap" type="text" class="form-control"  id="cap" value="<?php echo esc_html($cap); ?>"  >
    </div>
  </div>

  <input class="btn btn-primary right mt-3" type="submit" value="registrati" name="register">
  
</form>

