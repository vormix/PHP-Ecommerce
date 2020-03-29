<?php
class SpecialTreatment {

    public $id;
    public $name;
    public $special_treatment_value;
    public $type_code;

    public function __construct($id, $name, $special_treatment_value, $type_code){
      $this->id = (int)$id;
      $this->name = $name;
      $this->special_treatment_value = $special_treatment_value;
      $this->type_code = $type_code;     
    }
  
 
  }
  class SpecialTreatmentManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'name', 'special_treatment_value', 'type_code' );
        $this->tableName = 'special_treatment';
    }

    public function GetTypes(){
      $types = $this->db->query("
        SELECT 
          code AS code
          , description AS description
          , special_treatment_name as special_treatment_name
        FROM 
          special_treatment_type;
      ");
      if (!$types){
        return [];
      }
      $typesObjArr = [];
      foreach ($types as $type) {
        array_push($typesObjArr, (object) $type);
      }
      return $typesObjArr;
    }

    public function getAllTreatments(){
      $treatments = $this->db->query("
        SELECT 
          st.id as id
          , st.name as name
          , sttype.code as type_code
          , sttype.description as type_desc
          , sttype.special_treatment_name as special_treatment_name
          , st.special_treatment_value as special_treatment_value
        FROM 
          special_treatment st
          INNER JOIN special_treatment_type sttype
            ON st.type_code = sttype.code;
      ");
      if (!$treatments){
        return [];
      }
      $treatmentsObjArr = [];
      foreach ($treatments as $treatment) {
        array_push($treatmentsObjArr, (object) $treatment);
      }
      return $treatmentsObjArr;
    }    
    
  }