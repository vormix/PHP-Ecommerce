<?php

  class User {

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $user_type;

    public function __construct($id, $first_name, $last_name, $email, $user_type) {
      $this->id = (int)$id;
      $this->first_name = $first_name;
      $this->last_name = $last_name;
      $this->email = $email;
      $this->user_type = $user_type;
    }

    public static function generatePassword(){
      return "password";
    }
  }
  
  class UserManager extends DBManager {
    
    public function __construct(){
      parent::__construct();
      $this->tableName = 'user';
      $this->columns = array('id', 'email', 'first_name', 'last_name', 'user_type');
    }

    public function guidExists($guid) {
      $result = $this->db->query("
        SELECT id AS userId
        FROM user
        WHERE reset_link = '$guid';
      ");
      if ($result){
        return $result[0]['userId'];
      }
      return false;
    }

    public function invalidateGuid($guid){
      $this->db->query("
        UPDATE user
        SET reset_link = NULL
        WHERE reset_link = '$guid';
      ");
    }

    public function createResetLink($userId){
      $guid = Utilities::guidv4();
      $this->db->query("
        UPDATE user
        SET reset_link = '$guid'
        WHERE id = '$userId';
      ");
      return ROOT_URL . "auth?page=reset-password&guid=$guid";
    }
    
    public function register($first_name, $last_name, $email, $password){
      $user = new User(0, $first_name, $last_name, $email, 'regular');
      $userId = $this->_createUser($user, $password);
      return $userId;
    }    

    public function login($email, $password) {

      $user = $this->_getUserByEmail($email);
      if (!$user){
        return false;
      }
      $existingHashFromDb = $this->_getPassword($user['id']);
      $isPasswordCorrect = password_verify($password, $existingHashFromDb);

      if ($isPasswordCorrect) {
        return new User($user['id'], $user['first_name'], $user['last_name'], $user['email'], $user['user_type']);
      } else {
        return false;
      }
    }
    
    public function isValidPassword($pwd){
      return strlen($pwd) > 6;
    }
    
    public function isValidEmail($email){
      return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public function passwordsMatch($pwd1, $pwd2){
      return $pwd1 == $pwd2;
    }
    
    public function userExists($email){
      $result = $this->db->query("SELECT count(id) as count FROM user WHERE email = '$email'");
      return $result[0]['count'] > 0;
    }

    public function updatePassword($userId, $password){
      $pwd = $password ? $password : User::generatePassword();
      $pwd = password_hash($pwd, PASSWORD_DEFAULT);
      $query = "UPDATE $this->tableName SET password = '$pwd' where id = $userId";
      //var_dump($query); die;
      $this->db->query($query);
    }

    public function createAddress($userId, $street, $city, $cap){
      $query = "SELECT count(1) as has_address FROM address WHERE user_id = $userId"; 
      //var_dump($query); die;
      $result = $this->db->query($query);
      
      if ($result[0]['has_address'] > 0) {
        $this->db->query("UPDATE address SET street = '$street', city = '$city', cap = '$cap' WHERE user_id = $userId");
      } else {
        $this->db->query("INSERT INTO address (user_id, street, city, cap) VALUES ($userId, '$street', '$city', '$cap' )");
      }
    }
    
    public function getAddress($userId){
      $result = $this->db->query("SELECT street, city, cap  FROM address WHERE user_id = $userId");
      if(count($result) > 0){
        return $result[0];
      }
    }

    public function getUserByEmail($email){
      return $this->_getUserByEmail($email);
    }

    public function createUser($user, $password){
      return $this->_createUser($user, $password);
    }

    /*
      Private Methods
    */

    private function _createUser($user, $password){
      $id = parent::create($user);
      $this->updatePassword($id, $password);
      return $id;
    }

    private function _getPassword($userId) {
      $result =  $this->db->query("SELECT password FROM user WHERE id = $userId;");
      if ($result){
        return $result[0]['password'];
      }
      return null;
    }

    private function _getUserByEmail($email){
      $email = esc($email);      
      $query = "SELECT id, email, first_name, last_name, user_type FROM " . $this->tableName . " WHERE email = '$email';";
      $user = $this->db->query($query);
      if (count($user) == 0) {
        return null;
      }
      return $user[0];
    }



  }