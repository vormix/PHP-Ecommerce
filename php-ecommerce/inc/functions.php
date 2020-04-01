<?php

function esc($str) {
  global $conn;  
  return mysqli_real_escape_string($conn, htmlspecialchars($str));
}