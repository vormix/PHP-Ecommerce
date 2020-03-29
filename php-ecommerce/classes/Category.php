<?php
class Category {

    public $id;
    public $name;
    public $description;
    public $metadesc;
    public $parent_id;     
  
    public function __construct($id, $name, $description, $metadesc = NULL, $parent_id = NULL){ 
      $this->id = (int)$id;
      $this->name = $name;
      $this->description = $description;
      $this->metadesc = $metadesc;
      $this->parent_id = $parent_id;
    }
  
 
  }
  class CategoryManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'name','description', 'metadesc', 'parent_id');
        $this->tableName = 'category';
    }

    public function GetCategoriesAndSubs() {
      return $this->_getCategoriesAndSubs();
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
  
    private function _getCategoriesAndSubs(){
      $categoriesArr = $this->db->query("
        SELECT 
          parent_cat.id as parent_id
          , parent_cat.name as parent_name
          , child_cat.id as child_id
          , child_cat.name as child_name
        FROM 
          category parent_cat
          LEFT JOIN category child_cat
            ON parent_cat.id = child_cat.parent_id
        WHERE 
          parent_cat.parent_id IS NULL;
      ");

      if (!$categoriesArr){
        return [];
      }

      $categories = [
        [
          'parent' => (object) [
            'id' => $categoriesArr[0]['parent_id'],
            'name' => $categoriesArr[0]['parent_name']
          ],
          'children' => [
            (object) [
              'id' => $categoriesArr[0]['child_id'],
              'name' => $categoriesArr[0]['child_name'],
            ]
          ] 
        ]
      ];      
      $i = 0;
      foreach($categoriesArr as $cat){  
        if ($i == 0) {
          $i++; 
          continue;
        }

        $cat = (object) $cat;
        if ($cat->parent_id != $categories[count($categories) - 1]['parent']->id) 
        {
          $categoryGroup = [
            'parent' => (object) [
              'id' => $categoriesArr[$i]['parent_id'],
              'name' => $categoriesArr[$i]['parent_name']
            ],
            'children' => [
              (object) [
                'id' => $categoriesArr[$i]['child_id'],
                'name' => $categoriesArr[$i]['child_name'],
              ]
            ] 
          ];
          array_push($categories, $categoryGroup);
        } 
        else 
        {
          array_push(
            $categories[count($categories) - 1]['children'], 
            (object) [
              'id' => $categoriesArr[$i]['child_id'],
              'name' => $categoriesArr[$i]['child_name'],
            ]
          );
        }

        $i++;
      }
      return $categories;

    }


  }