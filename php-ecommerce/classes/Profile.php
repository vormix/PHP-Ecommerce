<?php

class Profile {
    public $id;
    public $name;

    public function __construct($id, $name){
      $this->id = (int)$id;
      $this->name = $name;   
    }
  }

  class ProfileManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'name' );
        $this->tableName = 'profile';
    }
    public function GetAllProfiles() {
        $profiles = parent::getAll();
        foreach ($profiles as $profile) {
          $profile->treatments_count = $this->_getProfileTreatments($profile->id);
        }
        return $profiles;
    }

    public function SaveProfileTreatments($profileId, $treatments){

      $this->db->query("
        DELETE FROM profile_treatments
        WHERE profile_id = $profileId;
      ");
      if (!$treatments) return;

      foreach($treatments as $treatmentId){
        $this->db->query("
          INSERT INTO profile_treatments (profile_id, special_treatment_id)
          VALUES ($profileId, $treatmentId);
        ");
      }
    }

    public function GetProfileTreatments($profileId){
      return $this->_getProfileTreatments($profileId);
    }

    public function GetUserDiscount(){

      global $loggedInUser;
      $discPercentage = 0;
      if ($loggedInUser && $loggedInUser->profile_id != NULL) {

        $treatments = $this->GetTreatmentsByType($loggedInUser->profile_id, 'extra-discount');
        if (count($treatments) > 0)  {
          foreach ($treatments as $treatment) {
            if ($treatment->special_treatment_value > $discPercentage){
              $discPercentage = $treatment->special_treatment_value;              
            }
          }
        }
      }
      return $discPercentage;
    }

    public function GetUserDelayedPayments(){

      global $loggedInUser;
      if ($loggedInUser && $loggedInUser->profile_id != NULL) {

        $treatments = $this->GetTreatmentsByType($loggedInUser->profile_id, 'delayed-payment');
        return $treatments;
      }
      return [];
    }

    public function GetTreatmentsByType($profileId, $treatmentType){
      $result = $this->db->query("
        SELECT 
          t.id as id
          , t.name as name
          , tt.special_treatment_name as special_treatment_name
          , t.special_treatment_value as special_treatment_value
        FROM profile p
          INNER JOIN profile_treatments pt
            ON p.id = pt.profile_id
          INNER JOIN special_treatment t
            ON pt.special_treatment_id = t.id
          INNER JOIN special_treatment_type tt
            ON t.type_code = tt.code
        WHERE
          p.id = $profileId
          AND t.type_code = '$treatmentType';
      ");
      if (count($result) == 0) {
        return [];
      }
      $treatments = [];
      foreach($result as $treatment){
        array_push($treatments, (object) $treatment);
      }
      return $treatments;
    }

    // Private methods
    private function _getProfileTreatments($profileId){
      $treatments = $this->db->query("
        SELECT t.*
        FROM 
          profile_treatments pt
          INNER JOIN special_treatment t
            ON pt.special_treatment_id = t.id
        WHERE
          pt.profile_id = $profileId;
      ");

      if (count($treatments) == 0) {
        return [];
      }

      $result = [];
      foreach($treatments as $t){
        array_push($result, (object) $t);
      }
      return $result;
    }
    
  }