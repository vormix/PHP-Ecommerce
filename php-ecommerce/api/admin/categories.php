<?php

require_once '../../inc/init.php'; 
global $loggedInUser;

if (!$loggedInUser || $loggedInUser->user_type != 'admin') {
  header('Content-type: application/json', false, 400);
  echo json_encode(['error' => 'Forbidden']);
  exit;
}

$action = $_GET['action'];
switch($action) {

  case 'getSubcategories':
    getSubcategories();
    break;
    
  default:
    errorResponse('invalid action');
    break;
}

function getSubcategories() {

  $parentId = isset($_GET['parentId']) ? (int) $_GET['parentId'] : NULL;
  if (!is_numeric($parentId)){
    errorResponse('invalid parentId');
    die;
  }

  $catMgr = new CategoryManager();
  $category = $catMgr->GetCategoriesAndSubs($parentId);

  header('Content-type: application/json');
  $array=[
      'data' => $category
  ];
  echo json_encode($array);

}

function errorResponse($msg){
  header('Content-type: application/json', false, 400);
  $array = ['error' => 'true', 'message' => $msg];
  echo json_encode($array);
}

?>