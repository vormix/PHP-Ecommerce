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
      //return substr(md5(mt_rand()), 0, 20);
      return 'test123';
    }
  }

  class UserManager extends DBManager {

    public function __construct(){
      parent::__construct();
      $this->tableName = 'User';
      $this->columns = array('id', 'email', 'first_name', 'last_name', 'user_type');
    }

    // public function create($user){
    //   $id = parent::create($user);

    //   $pwd = $password ? $password : User::generatePassword();
    //   $this->db->query("UPDATE $this->tableName SET password = '$pwd' where id = $id");
    //   return $id;
    // }

    public function createUser($user, $password){
      $id = parent::create($user);
      $this->updatePassword($id, $password);
      return $id;
    }

    public function updatePassword($userId, $password){
      $pwd = $password ? $password : User::generatePassword();
      $query = "UPDATE $this->tableName SET password = '$pwd' where id = $userId";
      //var_dump($query); die;
      $this->db->query($query);
    }

    public function isValidPassword($pwd){
      return strlen($pwd) > 6;
    }

    public function passwordsMatch($pwd1, $pwd2){
      return $pwd1 == $pwd2;
    }

    public function login($email, $password) {

      $email = esc($email);
      $password = esc($password);
      
      $query = "SELECT id, email, first_name, last_name, user_type FROM " . $this->tableName . " WHERE email = '$email' AND password = '$password'";
      $user = $this->db->query($query);
      //var_dump( $query); die;
      if (count($user) > 0) {
        $user = $user[0];
        return new User($user['id'], $user['first_name'], $user['last_name'], $user['email'], $user['user_type']);
      } else {
        return false;
      }
    }

    public function register($first_name, $last_name, $email, $password){
      $user = new User(0, $first_name, $last_name, $email, 'regular');
      $userId = $this->createUser($user, $password);
      return $userId;
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

    public function userExists($email){
      $result = $this->db->query("SELECT count(id) as count FROM user WHERE email = '$email'");
      return $result[0]['count'] > 0;
    }

    public function isValidEmail($email){
      return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
  }