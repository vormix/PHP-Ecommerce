<?php

class ProductImage {
  public $id;
  public $product_id;
  public $image_extension;
  public function __construct($id, $product_id, $image_extension) {
    $this->id = (int)$id;
    $this->product_id = (int)$product_id;
    $this->image_extension = $image_extension;
  }
}

class ProductImageManager extends DBManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array( 'id', 'product_id', 'image_extension');
    $this->tableName = 'product_images';
  }

  public function GetImagesPath() {
    return ROOT_PATH . '/images/';
  }

  public function getImages($productId) {
    $images = parent::getAll();
    
    foreach($images as $imgKey => $imgVal){
      
      if ($imgVal->product_id != $productId) {
        unset($images[$imgKey]);
      }
    }
    return $images;
  }
}

class Product {

  public $id;
  public $name;
  public $price;
  public $description;
  public $category_id;
  public $data_inizio_sconto;
  public $data_fine_sconto;
  public $qta;

  public function __construct($id, $name, $price, $description, $category_id, $sconto = 0, $data_inizio_sconto = NULL, $data_fine_sconto = NULL, $qta = 0){
    $this->id = (int)$id;
    $this->name = $name;
    $this->price = (float)$price;
    $this->description = $description;
    $this->category_id = (int)$category_id;
    $this->sconto = (int)$sconto;
    if($this->sconto>0){
      $this->data_inizio_sconto = $data_inizio_sconto == NULL ? '1900-01-01' : $data_inizio_sconto;
      $this->data_fine_sconto = $data_fine_sconto == NULL ? '2099-01-01' : $data_fine_sconto;
    }else{
      $this->data_inizio_sconto = $data_inizio_sconto == NULL ? '1900-01-01' : $data_inizio_sconto;
      $this->data_fine_sconto = $data_fine_sconto == NULL ? '1900-01-01' : $data_fine_sconto;
      
    }
    $this->qta = (int) $qta;

  }

  public static function CreateEmpty() {
    return new Product(0, "", 0, "", 0, 0, NULL, NULL, 0);
  }

}

class ProductManager extends DBManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array( 'id', 'name', 'price', 'description', 'category_id', 'sconto', 'data_inizio_sconto', 'data_fine_sconto', 'qta' );
    $this->tableName = 'product';
  }

  public function decreaseQuantity($productId) {
    $product = $this->get($productId);
    $product->qta = ((int)$product->qta) - 1;
    $this->update($product, $productId);
  }
  public function increaseQuantity($productId) {
    $product = $this->get($productId);
    $product->qta = ((int)$product->qta) + 1;
    $this->update($product, $productId);
  }

  public function MoveTempImages($tmpDir, $productId) {
    $imgMgr = new ProductImageManager();
    $imgPath = $imgMgr->GetImagesPath();
    rename("$imgPath/$tmpDir", "$imgPath/$productId");

    $files = scandir("$imgPath/$productId");

    foreach($files as $file) {
      if (strpos($file, '.jpg') != false) {
        $imgId = str_replace(".jpg", "", $file);
        $query="UPDATE product_images SET product_id = '$productId' WHERE id = '$imgId'"; 
        $this->db->exec($query);
      }
    }
  }

  public function GetProductWithImages($productId) {
    $product = $this->get($productId);
    //var_dump($product); die;
    $imgMgr = new ProductImageManager();
    $images = $imgMgr->getImages($productId);
    $product->images = $images;
    return $product;
  }

  public function GetProducts() {
    $products = parent::getAll();
    
   // echo $r . $secs . ' Sec';die;
    foreach($products as $product){
      
      $product->disc_price = NULL;
      if ($product->sconto != "0" && $product->data_inizio_sconto <= date('Y-m-d') && $product->data_fine_sconto >= date('Y-m-d')){
        $product->disc_price = $product->price - (($product->price * $product->sconto)/100.0);
        $secs ='';
        $days ='';
        $hours='';
        $minutes='';
        $secs =strtotime($product->data_fine_sconto) - strtotime(date("Y-m-d H:s:i")) ;
        $r = '';
        if ($secs >= 86400) {
          $days = floor($secs/86400);
          $secs = $secs%86400;
          $r .= $days . ' gg';
          if ($secs > 0) $r .= '- ';
        }
        if ($secs >= 3600) {
          $hours = floor($secs/3600);
          $secs = $secs%3600;
          $r .= $hours . ' hh';
          if ($secs > 0) $r .= '- ';
        }
        if ($secs>=60) {
          $minutes = floor($secs/60);
          $secs = $secs%60;
          $r .= $minutes . ' mm';
          if ($secs > 0) $r .= '- ';
        }
        
        $product->remaining_time=$r . $secs . ' Sec';
      } 
    
    //var_dump($products);
  }
    return $products;
  }

  public function DeleteProduct($productId) {
    $this->delete($productId);
    $this->_deleteImagesFromFileSystem($productId);
    $this->_deleteImagesFromDB($productId);
  }

  public function DeleteTempImages($tmpDir){
    $this->_deleteImagesFromFileSystem($tmpDir);
  }

  public function AddQuantity($productId, $quantity){
    $query = "
      UPDATE product
      SET qta = qta + $quantity
      WHERE id = $productId;
    ";
    
    $this->db->query($query);
  }

  // Private Methods
  private function _deleteImagesFromFileSystem($productId){
    $imgMgr = new ProductImageManager();
    $dirname = $imgMgr->GetImagesPath() . $productId;
    array_map('unlink', glob("$dirname/*.*"));
    rmdir($dirname);
  }

  private function _deleteImagesFromDB($productId){
    $this->db->query("DELETE FROM product_images WHERE product_id = $productId");
  }
 
 }