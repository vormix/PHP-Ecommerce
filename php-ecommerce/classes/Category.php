<?php
class Category {

    public $id;
    public $name;
     
  
    public function __construct($id, $name){
      $this->id = (int)$id;
      $this->name = $name;
     
    }
  
 
  }
  class CategoryManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'name' );
        $this->tableName = 'category';
    }
    public function GetCategories() {
        $category = parent::getAll();
        return $category;
    }
    public function GetCategory($categorytId) {
        $category = $this->get($categorytId);
        //var_dump($product); die;
       
        return $category;
      }
      public function deleteCategory($categorytId) {
        $this->delete($categorytId);
     
      }
  
    


  }