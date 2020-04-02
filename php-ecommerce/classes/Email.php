<?php
class Email {

    public $id;
    public $subject;
    public $message;     
  
    public function __construct($id, $subject, $message){ 
      $this->id = (int)$id;
      $this->subject = $subject;
      $this->message = $message;
    }
  
 
  }
  class EmailManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'subject','message');
        $this->tableName = 'email';
    }

    public function getEmail($id){
      $email = $this->get($id);
      $email->recipients = $this->_getEmailRecipients($id);
      return $email;
    }

    public function GetEmails(){
      return $this->getAll();
    }

    public function getCustomers(){

      $customersArr = $this->db->query("
        SELECT
          u.id AS id  
          , CONCAT(u.first_name, ' ', u.last_name) AS name
          , u.email AS email
        FROM user u;
      ");

      $customers = [];

      if ($customersArr){
        foreach($customersArr as $customer){
          array_push($customers, (object) $customer);
        }
      }
      return $customers;
    }

    public function saveRecipients($id, $recipients) {

      $to = "'" . str_replace(';', "','", $recipients) . "'";

      $this->db->query("
        DELETE FROM email_recipients
        WHERE email_id = $id;
      ");

      $this->db->query("
        INSERT INTO email_recipients
        (email_id, recipient_id)
        SELECT
          $id
          , u.id
        FROM user u
        WHERE email IN ($to);
      ");
    }

    private function _getEmailRecipients($emailId){

      $recipientsArr = $this->db->query("
        SELECT
          u.id AS id  
          , CONCAT(u.first_name, ' ', u.last_name) AS name
          , u.email AS email
        FROM 
          user u
          INNER JOIN email_recipients r
            ON u.id = r.recipient_id
        WHERE
          r.email_id = $emailId;
      ");

      $recipients = [];

      if ($recipientsArr){
        foreach($recipientsArr as $recipient){
          array_push($recipients, (object) $recipient);
        }
      }
      return $recipients;
    }
    
  }