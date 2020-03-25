<?php

require_once '../../inc/init.php'; 
global $loggedInUser;

if (!$loggedInUser || $loggedInUser->user_type != 'admin') {
  header('Content-type: application/json', false, 400);
  echo json_encode(['error' => 'Forbidden']);
  exit;
}

$imageMgr = new ProductImageManager();

if (isset($_POST['operation']) && $_POST['operation'] == 'img-details') {

  $id = $_POST['id'];
  if (!is_numeric($id)) {
    die('prevent sql injection');
  }

  $title = $_POST['title'];
  $alt = $_POST['alt'];
  $order = $_POST['order'];

  $existingImage = $imageMgr->get($id);
  $existingImage->title = $title;
  $existingImage->alt = $alt;
  $existingImage->order_number = $order;
  
  // header('Content-type: application/json');  echo json_encode(['existingImage' => $existingImage]); exit;
  
  $imageMgr->update($existingImage, $id);
  header('Content-type: application/json');
  echo json_encode(['result' => 'ok']);
  exit;
}

if (!isset($_POST['productId'])) {
  header('Content-type: application/json', false, 400);
  echo json_encode(['error' => 'productId not provided']);
  exit;
}

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

  $imgFullPath = $imgFolder . '/' . $imageId . '.jpg';
  move_uploaded_file($_FILES[$key]['tmp_name'], $imgFullPath);

  ImageUtilities::wallpaper($imgFullPath);
  ImageUtilities::thumbnail($imgFullPath);

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