<?php

require_once './inc/init.php'; 

if (! defined('ROOT_URL')) {
  die;
}

if ( 0 < $_FILES['file']['error'] ) {
    echo json_encode([
        'error' => 'qualcosa è andato storto'
    ]);
}
else {
    //echo json_encode (['files' => $_FILES['file'], 'post' => $_POST]); die;
    $productId = $_POST['productId'];
    $imageMgr = new ProductImageManager();
    $image = new ProductImage(0, $productId, 'jpg');
    $imageId = $imageMgr->create($image);

    $imgFolder = 'images/'. $productId;
    if (!file_exists($imgFolder)) {
        mkdir($imgFolder, 0777, true);
    }

    move_uploaded_file($_FILES['file']['tmp_name'], $imgFolder . '/' . $imageId . '.jpg');
    echo json_encode([
        'image' => $imageMgr->get($imageId)
    ]);
}

?>