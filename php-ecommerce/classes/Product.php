<?php

class ProductImage {
  public $id;
  public $product_id;
  public $image_extension;

  public $title;
  public $alt;

  public function __construct($id, $product_id, $image_extension, $title = '', $alt = '', $order_number = 0) {
    $this->id = (int)$id;
    $this->product_id = (int)$product_id;
    $this->image_extension = $image_extension;
    $this->title = $title;
    $this->alt = $alt;
    $this->order_number = (int) $order_number;

  }
}

class ProductImageManager extends DBManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array( 'id', 'product_id', 'image_extension', 'title', 'alt', 'order_number');
    $this->tableName = 'product_images';
  }

  public function GetImagesPath() {
    return ROOT_PATH . '/images/';
  }

  public function getImages($productId) {
    // $images = parent::getAll();
    $imgsArr = $this->db->query("
      SELECT *
      FROM product_images
      WHERE product_id = $productId;
    ");

    $images = [];
    if ($imgsArr) {
      foreach($imgsArr as $img){
        array_push($images, (object) $img);
      }
    }
    return $images;
  }
}

class Product {

  public $id;
  public $name;
  public $mtitle;
  public $price;
  public $description;
  public $category_id;
  public $data_inizio_sconto;
  public $data_fine_sconto;
  public $qta;
  public $sconto;
  public $metadescription;

  public function __construct($id, $name, $price, $description, $category_id, $sconto = 0, $data_inizio_sconto = NULL, $data_fine_sconto = NULL, $qta = 0,$mtitle=NULL, $metadescription=NULL){
    $this->id = (int)$id;
    $this->name = $name;
    $this->price = (float)$price;
    $this->description = $description;
    $this->category_id = (int)$category_id;
    $this->sconto = (int)$sconto;
    $this->mtitle = $mtitle;
    $this->metadescription = $metadescription;
    
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
    return new Product(0, "", 0, "", 0, 0, NULL, NULL, 0, NULL, NULL);
  }

}

class ProductManager extends DBManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array( 'id', 'name', 'price', 'description', 'category_id', 'sconto', 'data_inizio_sconto', 'data_fine_sconto', 'qta', 'mtitle',  'metadescription' );
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
    $product = $this->_getProducts(0, $productId);
    if (!$product) {
      return NULL;
    }
    $product = $product[0];
    //var_dump($product); die;
    $imgMgr = new ProductImageManager();
    $images = $imgMgr->getImages($productId);
    $product->images = $images;
    return $product;
  }

  public function GetProductSubcategories($productId){
    $product = $this->get($productId);
    if (!isset($product->id)) {
      return [];
    }
    $cm = new CategoryManager();
    $subcats = $cm->GetCategoriesAndSubs($product->category_id, $productId);
    return $subcats;
  }

  public function GetProducts($categoryId) {
    return $this->_getProducts($categoryId);
  }

  private function _getProducts($categoryId = 0, $productId = 0, $search = '', $limit = '') {

    $products = $this->_getProductsQuery($categoryId, $productId, $search, $limit);
    
    $urlUtilities = new UrlUtilities('shop');

    foreach($products as $product){  

      $product->disc_price = NULL;
      if ($product->sconto != "0" && $product->data_inizio_sconto <= date('Y-m-d') && $product->data_fine_sconto >= date('Y-m-d')){
        $product->disc_price = $product->price - (($product->price * $product->sconto)/100.0);
      } 

      $product->url = $urlUtilities->product($product->id, $product->name);
    }
    
    $pm = new ProfileManager();
    $userDiscount = $pm->GetUserDiscount();
    if ($userDiscount > 0)  {
      foreach($products as $product){
        $product->price = number_format(($product->price - (($product->price * $userDiscount)/100)), 2, '.', '');
        if ( $product->disc_price != NULL) {
          $product->disc_price = number_format(($product->disc_price - (($product->disc_price * $userDiscount)/100)), 2, '.', '');
        }
      }
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

  public function SearchProducts($search) {

    $products = $this->_getProducts(0, 0, $search, 5);
    if (!$products) {
      return [];
    }

    $imgMgr = new ProductImageManager();
    foreach($products as $product) {
      $images = $imgMgr->getImages($product->id);
      $product->image_id = count($images) > 0 ? $images[0]->id : "0";
    }

    return $products;
  }

  public function getDiscountedPrice($productId){
    $product = $this->get($productId);
    if ($product->sconto == 0) {
      return null;
    }

    $now = date('Y-m-d');
    if ($product->data_inizio_sconto <= $now && $product->data_fine_sconto >= $now) {
      return round($product->price - (($product->sconto * $product->price)/100), 2);
    }
    return null;
  }

  // Private Methods
  private function _deleteImagesFromFileSystem($productId){
    $imgMgr = new ProductImageManager();
    $dirname = $imgMgr->GetImagesPath() . $productId;
    array_map('unlink', glob("$dirname/*.*"));
    if(is_dir($dirname))rmdir($dirname);
  }

  private function _deleteImagesFromDB($productId){
    $this->db->query("DELETE FROM product_images WHERE product_id = $productId");
  }

  private function _getProductsQuery($categoryId = 0, $productId = 0, $search = '', $limit = '') {

    if ($limit != '') {
      $limit = "LIMIT $limit";
    }

    if ($search != '') {
      $search = "
        AND
        (
          p.name like '%$search%'
          OR
          c.name like '%$search%'
        )
      ";
    }

    $query = "
      SELECT 
        p.*
        , c.name as category
      FROM 
        product p
        INNER JOIN category c
          ON p.category_id = c.id
      WHERE  
        ($categoryId = 0 OR p.category_id = $categoryId)
        AND
        ($productId = 0 OR p.id = $productId)
      $search
      $limit;
    ";

    $productsObjArr = [];
    $products = $this->db->query($query);
    if ($products){
      foreach($products as $product){
        array_push($productsObjArr, (object) $product);
      }
    }
    return $productsObjArr;
  } 
 
 }