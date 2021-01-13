<?php

class CartManager extends DbManager {

  private $clientId;

  public function __construct(){
    parent::__construct();
    $this->columns = array('id', 'client_id');
    $this->tableName = 'cart';

    $this->_initializeClientIdFromSession();
  }

  // Public Methods

  public function getCartTotal($cartId) {
    $result = $this->db->query("
   SELECT 
      SUM(quantity) as num_products
      , SUM(quantity* price) as total
    FROM cart_item
    INNER JOIN product
      ON cart_item.product_id = product.id
    WHERE cart_id = $cartId
    ");
    return $result[0];
  }

  public function getCartItems($cartId) {
    return $this->db->query("
    SELECT
      product.name AS name
      , product.description AS description
      , product.price AS single_price
      , cart_item.quantity AS quantity
      , product.price * cart_item.quantity AS total_price
      , product.id as id
    FROM
      cart_item 
      INNER JOIN product 
      ON cart_item.product_id = product.id
    WHERE
      cart_item.cart_id = $cartId
    ");
  }

  public function removeFromCart($productId, $cartId) {

    $quantity = 0;
    $result = $this->db->query("SELECT quantity, id FROM cart_item WHERE cart_id = $cartId AND product_id = $productId");
    $cartItemId = $result[0]['id'];
    if (count($result) > 0){
      $quantity = $result[0]['quantity'];
    } 
    $quantity--;

    if ($quantity > 0) {
      $this->db->execute("UPDATE cart_item SET quantity = $quantity WHERE cart_id = $cartId AND product_id = $productId");
    } else {
      $cartItemMgr = new CartItemManager();
      $cartItemMgr->delete($cartItemId);
    }
  }

  public function addToCart($productId, $cartId){
    $quantity = 0;
    $result = $this->db->query("SELECT quantity FROM cart_item WHERE cart_id = $cartId AND product_id = $productId");
    if (count($result) > 0){
      $quantity = $result[0]['quantity'];
    } 
    $quantity++;

    if (count($result) > 0) {
      $this->db->execute("UPDATE cart_item SET quantity = $quantity WHERE cart_id = $cartId AND product_id = $productId");
    } else {
      $cartItemMgr = new CartItemManager();
      $newId = $cartItemMgr->create([
        'cart_id' => $cartId,
        'product_id' => $productId,
        'quantity' => 1
      ]);
    }

  }

  public function getCurrentCartId() {
    $cartId = 0;

    $result = $this->db->query("SELECT * FROM cart WHERE client_id = '$this->clientId'");
    if (count($result) > 0) {
      $cartId = $result[0]['id'];
    } else {
      $cartId = $this->create([
        'client_id' => $this->clientId
      ]);
    }

    return $cartId;
  }


  // Private Methods
  private function _initializeClientIdFromSession(){
    if (isset($_SESSION['client_id'])){
      $this->clientId = $_SESSION['client_id'];
    } else {
      // generare una stringa casuale
      $str = $this->_random_string();
      // settare clientId in sessione con questa stringa
      $_SESSION['client_id'] = $str;
      $this->clientId = $str;
    }
  }

  private function _random_string(){
    return substr(md5(mt_rand()), 0, 20);
  }

}

class CartItemManager extends DbManager {

  public function __construct(){
    parent::__construct();
    $this->columns = array('id', 'cart_id', 'product_id', 'quantity');
    $this->tableName = 'cart_item';
  }
}

?>