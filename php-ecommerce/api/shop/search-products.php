<?php
require_once '../../inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

$search = esc_html(trim($_GET['search']));
$pm = new ProductManager();
$products=$pm->SearchProducts($search);

$result = [ 'data' => $products ] ;

header('Content-type: application/json');
$array=$result;
echo json_encode($array);