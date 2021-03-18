<?php

require_once '../inc/init.php'; 

$pm = new ProductManager();
$imageMgr = new ProductImageManager();

$products = $pm->getAll();

foreach($products as $product) {

  $imgFolder = ROOT_PATH . 'images/'. $product->id . '/';
  if (!file_exists($imgFolder)) {
      mkdir($imgFolder, 0777, true);
  }

  $image = new ProductImage(0, $product->id, 'jpg');
  $imageId = $imageMgr->create($image);
  $image->id = $imageId;

  copy('http://placeimg.com/1280/720/any', $imgFolder . "$imageId.jpg");
 
  ImageUtilities::wallpaper($imgFolder . "$imageId.jpg");
  ImageUtilities::thumbnail($imgFolder . "$imageId.jpg");

}