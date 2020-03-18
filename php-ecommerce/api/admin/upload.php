<?php

require_once '../../inc/init.php'; 
global $loggedInUser;

if (!$loggedInUser || $loggedInUser->user_type != 'admin') {
  header('Content-type: application/json', false, 400);
  echo json_encode(['error' => 'Forbidden']);
  exit;
}

if (!isset($_POST['productId'])) {
  header('Content-type: application/json', false, 400);
  echo json_encode(['error' => 'productId not provided']);
  exit;
}

$imageMgr = new ProductImageManager();
$productId = $_POST['productId'];
$imgFolder = ROOT_PATH . 'images/'. $productId;
$tmpDir = isset($_POST['tmpDir']) ? $_POST['tmpDir'] : NULL;

if ($productId == "0") {
  $tmpDir = $tmpDir == NULL ? uniqid() : $_POST['tmpDir'];
  $imgFolder = ROOT_PATH . 'images/'. $tmpDir;
}

if (!file_exists($imgFolder)) {
    mkdir($imgFolder, 0777, true);
}

$images = [];
foreach($_FILES as $key => $file) {

  $image = new ProductImage(0, $productId, 'jpg');
  $imageId = $imageMgr->create($image);
  $image->id = $imageId;
  move_uploaded_file($_FILES[$key]['tmp_name'], $imgFolder . '/' . $imageId . '.jpg');
  if ($productId == "0") {
    $image->product_id = $tmpDir;
  }
  array_push($images, $image);
}

header('Content-type: application/json');
$result = [
  'images' => $images,
  'tmpDir' => $tmpDir
];
echo json_encode($result);


?>