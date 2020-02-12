<?php
  // Prevent from direct access
  if (! defined('ROOT_URL')) {
    die;
  }

 function esc($str) {
    global $conn;  
    return mysqli_real_escape_string($conn, htmlspecialchars($str));
  }

  function esc_html($str) {
    return htmlspecialchars($str);
  }

  function shorten($str) {
    return substr($str, 0, 30) . '...';
  }

  function random_string(){
    return substr(md5(mt_rand()), 0, 20);
  }