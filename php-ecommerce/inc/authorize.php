<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

  global $loggedInUser;
  session_start();
  

  if (isset($_SESSION['user'])) {
    $loggedInUser = unserialize($_SESSION['user']);
  }
