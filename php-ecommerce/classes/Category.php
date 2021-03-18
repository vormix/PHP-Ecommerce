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

    public function GetCategoriesAndSubs($parentId = 0, $productId = 0) {
      return $this->_getCategoriesAndSubs($parentId, $productId);
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

    public function SaveSubcategories($subcategoryIds, $productId){
      $this->db->query("
        DELETE FROM product_categories
        WHERE product_id = $productId;
      ");

      foreach($subcategoryIds as $subcatId){
        $this->db->query("
          INSERT INTO product_categories (product_id, subcategory_id)
          VALUES ($productId, $subcatId);
        ");
      }
    }
  
    private function _getCategoriesAndSubs($parentId = 0, $productId = 0){

      $filter = "";

      if ($parentId != 0) {
        $filter .= " AND parent_cat.id = $parentId";
      }

      // if ($productId != 0) {
      //   $filter .= " AND prod_subcats.product_id = $productId";
      // }

      $categoriesArr = $this->db->query("
        SELECT 
          parent_cat.id as parent_id
          , parent_cat.name as parent_name
          , child_cat.id as child_id
          , child_cat.name as child_name
          , IFNULL(prod_subcats.product_id , 0) as product_id
        FROM 
          category parent_cat
          LEFT JOIN category child_cat
            ON parent_cat.id = child_cat.parent_id
          LEFT JOIN product_categories prod_subcats
            ON child_cat.id = prod_subcats.subcategory_id
            AND ($productId = 0 OR prod_subcats.product_id = $productId)
        WHERE 
          parent_cat.parent_id IS NULL
          $filter
        ORDER BY
        parent_cat.name;
      ");

      if (!$categoriesArr){
        return ( $parentId != 0 || $productId != 0) ?  null : [];
      }

      $urlUtilities = new UrlUtilities('shop');

      $categories = [
        [
          'parent' => (object) [
            'id' => $categoriesArr[0]['parent_id'],
            'name' => $categoriesArr[0]['parent_name'],
            'url' => $urlUtilities->category($categoriesArr[0]['parent_id'], $categoriesArr[0]['parent_name'])
          ],
          'children' => [
            (object) [
              'id' => $categoriesArr[0]['child_id'],
              'name' => $categoriesArr[0]['child_name'],
              'is_selected' => $categoriesArr[0]['product_id'] > 0,
              'url' => $urlUtilities->category($categoriesArr[0]['child_id'], $categoriesArr[0]['child_name'])
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
              'name' => $categoriesArr[$i]['parent_name'],
              'url' => $urlUtilities->category($categoriesArr[$i]['parent_id'], $categoriesArr[$i]['parent_name'])
            ],
            'children' => [
              (object) [
                'id' => $categoriesArr[$i]['child_id'],
                'name' => $categoriesArr[$i]['child_name'],
                'is_selected' => $categoriesArr[$i]['product_id'] > 0,
                'url' => $urlUtilities->category($categoriesArr[$i]['child_id'], $categoriesArr[$i]['child_name'])
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
              'is_selected' => $categoriesArr[$i]['product_id'] > 0,
              'url' => $urlUtilities->category($categoriesArr[$i]['child_id'], $categoriesArr[$i]['child_name'])
            ]
          );
        }

        $i++;
      }
      return ($parentId != 0 || $productId != 0) ? $categories[0] : $categories;

    }



  }