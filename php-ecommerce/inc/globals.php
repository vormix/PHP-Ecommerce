<?php

  $alertMsg = '';
  $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  $loggedInUser = null;
  $client_ip = $_SERVER['REMOTE_ADDR'];
