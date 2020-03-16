<?php

require_once './inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
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