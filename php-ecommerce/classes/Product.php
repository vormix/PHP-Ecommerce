<?php

class Product {

  public $id;
  public $name;
  public $price;
  public $description;
  public $category_id;

  public function __construct($id, $name, $price, $description, $category_id){
    $this->id = (int)$id;
    $this->name = $name;
    $this->price = (float)$price;
    $this->description = $description;
    $this->category_id = (int)$category_id;
  }
}

class ProductManager extends DBManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array( 'id', 'name', 'price', 'description', 'category_id' );
    $this->tableName = 'Product';
  }
}