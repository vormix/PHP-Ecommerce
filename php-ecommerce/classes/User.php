<?php

class UserManager extends DBManager {

  public function __construct() {
    parent::__construct();
    $this->tableName = 'user';
    $this->columns = ['id', 'email', 'password', 'user_type_id'];
  }

  // Public Methods

  public function passwordsMatch($password, $confirm_password) {
    return $password == $confirm_password;
  }

  public function register($email, $password) {
    $result = $this->db->query("SELECT * FROM user WHERE email = '$email'");
    if (count($result) > 0) {
      return false;
    }
    $userId = $this->create([
      'email' => $email,
      'password' => md5($password),
      'user_type_id' => 2
    ]);
    return $userId;
  }

  public function login($email, $password) {
    $result = $this->db->query("
      SELECT *
      FROM user
      WHERE email = '$email'
      AND password = MD5('$password');
    ");

    if (count($result) > 0 ) {
      $user = (object) $result[0];

      $this->_setUser($user);
      return true;
    }

    return false;
  }

  // Private Methods
  private function _setUser($user) {    
    $userToStore = (object) [
      'id' => $user->id,
      'email' => $user->email,
      'is_admin' => $user->user_type_id == 1
    ];
    $_SESSION['user'] = $userToStore;
  }
}