<?php
class SellOn {
  private $order;
  private $detail;
  private $items;
  private $items_rejected;
  private $items_accepted;
  
  public function __construct(){
    $this->order = new stdClass();
    $this->detail = new stdClass();
    $this->items = array();
    $this->items_accepted = array();
    $this->items_rejected = array();
  }
  /*
  * For calculating earning
  * Parameters:
  * 1. Input Order Object
  * 2. Input Order Detail Object
  * 3. Input Order Detail Item array
  * 4. Fee Object
  * Result mixed
  */
  public function calculation($order,$detail,$items,$f){
    $this->items_accepted = array();
    $this->items_rejected = array();
    if(isset($f->admin_pg_jenis) && isset($f->admin_pg) && isset($f->admin_fee) && isset($f->admin_fee_jenis)){
      $sub_total = 0;
      $ongkir = $detail->shipment_cost+$detail->shipment_cost_add;
      foreach($items as $itm){
        if($detail->shipment_status == "delivered" || $detail->shipment_status == "succeed"){
          if($itm->buyer_status == "accepted"){
            $this->items_accepted[] = $itm;
          }elseif($itm->buyer_status == "rejected"){
            $this->items_rejected[] = $itm;
          }
        }else{
          $sub_total = $itm->harga_jual*$itm->qty;
        }
        
        
      }
      $grand_total = $sub_total+$ongkir;
      
      //declare var
      $vat = 7;
      $pg_vat = 0.0;
      $pg_fee = 0.0;
      $pg_fee_percent = 0.0;
      if($f->admin_pg_jenis == 'percentage'){
        $pg_fee_percent = $f->admin_pg;
        $pg_fee = number_format($grand_total * ($f->admin_pg/100),2);
      }else{
        $pg_fee = number_format($f->admin_pg,2);
      }
      if(isset($f->admin_vat)) $vat = (int) $f->admin_vat;
      $pg_vat = $pg_fee * ($vat/100);
      
      $profit = 0.0;
      $profit_percent = 0.0;
      if($f->admin_fee_jenis == 'percentage'){
        $profit_percent = $f->admin_fee;
        $profit = number_format($grand_total * ($f->admin_fee/100),2);
      }else{
        $profit = number_format($f->admin_fee,2);
      }
      $profit = $profit-$pg_vat; //vat included
      
      //selling fee calculation
      $selling_fee = $profit+$pg_vat+$pg_fee;

      //declare var
      $cancel_fee = 0;

      //declare admin fee
      $earning_total = $sub_total - $selling_fee;
      
      //margin shipping
      $shipment_service = strtolower(trim($op->shipment_service));

      //by Donny Dennison - 15 september 2020 17:45
      //change name, image, etc from gogovan to gogox
      // if($shipment_service == "gogovan"){
      if($shipment_service == "gogox"){
        
        $earning_total += $ongkir;
      }
      
      //percentage calculation
      $selling_fee_percent = $pg_fee_percent+$profit_percent;
      $earning_percent = 100 - $selling_fee_percent;
      
      //rejected by seller
      if($order->payment_status == "paid" && $detail->seller_status == "rejected"){
        $cancel_fee = $pg_fee + $pg_vat;
        $refund_amount = $grand_total;
        $earning_total = 0;
        $selling_fee = 0;
        $profit = 0;
      }
      
      //assign
      $order->sub_total = $sub_total;
      $order->grand_total = $grand_total;
      $order->pg_fee = $pg_fee;
      $order->pg_vat = $pg_vat;
      $order->profit_amount = $profit;
      $order->cancel_fee = $cancel_fee;
      $order->selling_fee = $selling_fee;
      $order->selling_fee_percent = $selling_fee_percent;
      $order->earning_total = $earning_total;
      $order->earning_percent = $earning_percent;
      $order->refund_amount = $refund_amount;
    }
    
    $this->order = $order;
    $this->detail = $detail;
    $this->items = $items;
  }
  public function getOrder(){
    return $this->order;
  }
  public function getDetail(){
    return $this->detail;
  }
  public function getItems(){
    return $this->items;
  }
  public function getItemsAccepted(){
    return $this->items_accepted;
  }
  public function getItemsRejected(){
    return $this->items_rejected;
  }
}