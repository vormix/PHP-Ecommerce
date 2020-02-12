<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  $errors = false;
  $password = '';
  $confirm_password = '';

  $userMgr = new UserManager();

  if (isset($_POST['change_password'])){

    $password = esc($_POST['password']);
    $confirm_password = esc($_POST['confirm_password']);

    if (!$userMgr->passwordsMatch($password, $confirm_password)){
      $errors = true;
      $alertMsg = 'passwords_not_match';
    } 
    
    if(!$errors AND !$userMgr->isValidPassword($password)){
      $errors = true;
      $alertMsg = 'invalid_password';
    } 
    
    if(!$errors){
      $userMgr->updatePassword($loggedInUser->id, $password);
      $alertMsg = 'updated';
      $password = '';
      $confirm_password = '';
    }
  }

  if (isset($_POST['change_address'])){
    $street = esc($_POST['street']);
    $city = esc($_POST['city']);
    $cap = esc($_POST['cap']);

    if ($street == '' OR $city == '' OR $cap == ''){
      $errors = true;
      $alertMsg = 'mandatory_fields';
    } else {
      $userMgr->createAddress($loggedInUser->id, $street, $city, $cap);
      $alertMsg = 'updated';
    }
  }

  $address = $userMgr->getAddress($loggedInUser->id);
?>

<h1>Il tuo Profilo</h1>
<p>Puoi gestire i tuoi dati personali...</p>

<hr class=mb-4>

<h5  class="mb-3 mt-3">Indirizzo di spedizione</h5>

<form method="post">
  <div class="mb-3">
    <label for="street">Via</label>
    <input name="street" type="text" class="form-control"  id="street" value="<?php echo esc_html($address['street']); ?>" >
  </div>
  <div class="row">
    <div class="col-md-8 mb-3">
      <label for="city">Città</label>
      <input name="city" type="text" class="form-control"  id="city" value="<?php echo esc_html($address['city']); ?>" >
    </div>
    <div class="col-md-4 mb-3">
      <label for="cap">CAP</label>
      <input name="cap" type="text" class="form-control"  id="cap" value="<?php echo esc_html($address['cap']); ?>"  >
    </div>
  </div>
  <input name="change_address" type="submit" class="btn btn-primary" value="Cambia Indirizzo">
</form>

<hr class="mb-4">

<h5  class="mb-3 mt-3">Cambio Password</h5>
<form method="post">
  <div class="form-group">
    <label for="password">Password</label>
    <input name="password" id="password" type="password" class="form-control" value="<?php echo esc_html($password); ?>">
  </div>
  <div class="form-group">
    <label for="confirm_password">Conferma Password</label>
    <input name="confirm_password" id="confirm_password" type="password" class="form-control" value="<?php echo esc_html($confirm_password); ?>">
  </div>
  <input name="change_password" type="submit" class="btn btn-primary" value="Cambia Password">
</form>