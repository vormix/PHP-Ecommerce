<?php
  $querystring = $_SERVER['QUERY_STRING'] != '' ? '?' . $_SERVER['QUERY_STRING'] : '';
  header('Location: public' . $querystring);
  exit; 
 