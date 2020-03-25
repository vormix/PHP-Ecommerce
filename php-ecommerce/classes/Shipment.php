<?php
class Shipment {

    public $id;
    public $name;
    public $price;
     
  
    public function __construct($id, $name, $price){
      $this->id = (int)$id;
      $this->name = $name;
      $this->price = (float)$price;
     
    }
  
 
  }
  class ShipmentManager extends DBManager {

    public function __construct(){
        parent::__construct();
        $this->columns = array( 'id', 'name', 'price' );
        $this->tableName = 'shipment';
    }
    public function GetShipments() {
        $shipment = parent::getAll();
        return $shipment;
    }
    public function GetShipment($shipmentId) {
        $shipment = $this->get($shipmentId);
        //var_dump($product); die;
       
        return $shipment;
      }
      public function DeleteShipment($shipmentId) {
        $this->delete($shipmentId);
     
      }
  
    


  }