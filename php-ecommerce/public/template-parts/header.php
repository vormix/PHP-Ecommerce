<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.css">
  <link rel="stylesheet" href="<?php echo ROOT_URL; ?>assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
  <title>PHP E-commerce</title>
</head>
<body>
  
<nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo ROOT_URL; ?>public?page=homepage">PHP E-commerce</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">

      <ul class="navbar-nav mr-auto"> 
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>public?page=about">Chi Siamo</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>public?page=services">Servizi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>shop?page=products-list">Prodotti</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>public?page=contacts">Contatti</a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
        <li  class="nav-item">
          <a class="nav-link" href="<?php echo ROOT_URL; ?>shop?page=cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge badge-success rounded-pill js-totCartItems"></span>
          </a>
        </li>
      </ul>
      
      <?php if (!$loggedInUser) : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Area Riservata
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">        
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>auth?page=login">Login</a>
            </div>
          </li>
        </ul>
      <?php endif; ?>

      <?php if ($loggedInUser) : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $loggedInUser->email ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">        
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>auth?page=logout">Logout</a>
            </div>
          </li>
        </ul>
      <?php endif; ?>

      <?php if ($loggedInUser && $loggedInUser->is_admin) : ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Amministrazione
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">        
              <a class="dropdown-item" href="<?php echo ROOT_URL; ?>admin">Dashboard</a>
            </div>
          </li>
        </ul>
      <?php endif; ?>

    </div>
  </div>
</nav>

