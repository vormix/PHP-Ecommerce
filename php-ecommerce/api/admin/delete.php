<?php

require_once '../../inc/init.php'; 

if (!$loggedInUser || $loggedInUser->user_type != 'admin') {
  header('HTTP/1.0 403 Forbidden');
  echo 'Forbidden';
  exit;
}

$imageId = $_POST['imageId'];
$imageMgr = new ProductImageManager();
$img = $imageMgr->get($imageId);
// cancellare immagine
$imageMgr->delete($imageId);

$file = ROOT_PATH . '/images/' . $img->product_id . '/' . $img->id . '.jpg';
unlink($file);

echo json_encode(['result' => 'success']);

?>