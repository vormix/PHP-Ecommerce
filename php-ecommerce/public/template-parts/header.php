<?php
// Prevent from direct access
if (! defined('ROOT_URL')) {
  die;
}

global $loggedInUser;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo SITE_NAME; ?></title>
  <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.css">
  <link rel="stylesheet" href="<?php echo ROOT_URL; ?>assets/css/style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

  <script src="https://bootswatch.com/_vendor/jquery/dist/jquery.min.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="<?php echo ROOT_URL; ?>index.php"><?php echo SITE_NAME; ?></a>

      <a class="cart-smartphone nav-link btn text-light" href="<?php echo ROOT_URL; ?>shop?page=cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge badge-primary badge-pill js-totCartItems"></span>
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo ROOT_URL; ?>?page=about">Chi Siamo</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo ROOT_URL; ?>?page=services">Servizi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo ROOT_URL; ?>shop?page=products-list">Prodotti</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo ROOT_URL; ?>?page=contacts">Contatti</a>
          </li>
        </ul>

        <ul class="cart-desktop navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo ROOT_URL; ?>shop?page=cart">
              <i class="fas fa-shopping-cart"></i>
              <span class="badge badge-primary badge-pill js-totCartItems"></span>
            </a>
          </li>
        </ul>

        <?php if ($loggedInUser && $loggedInUser->user_type == 'admin') : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Amministrazione</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=dashboard">Cruscotto</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=orders-list">Gestione Ordini</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=product">Aggiungi Prodotto</a>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=products-list">Lista Prodotti</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=user">Aggiungi Utente</a>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin?page=users-list">Lista Utenti</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>auth?page=logout">Logout</a>
            </div>
          </li>
        </ul>
        <?php endif; ?>

        <?php if ($loggedInUser) : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $loggedInUser->email; ?></a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>user?page=dashboard">Cruscotto</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>user?page=profile">Profilo</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>shop?page=my-orders">I miei Ordini</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>auth?page=logout">Logout</a>
            </div>
          </li>
        </ul>
        <?php endif; ?>

        <?php if (!$loggedInUser) : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Area Riservata</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>auth/index.php?page=login">Login / Registrazione</a>
            </div>
          </li>
        </ul>
        <?php endif; ?>

      </div>
    </div>
  </nav>