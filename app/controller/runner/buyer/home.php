<?php
class Home extends JI_Controller {
  public function __construct(){
    parent::__construct();

  }
  public function index(){
    echo '<p></p><h3>Buyer Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/buyer/orderlist").'">Order List</a></li>';
    echo '<li><a href="'.base_url("runner/buyer/confirmed").'">Confirm Delivery</a></li>';
    echo '<li><a href="'.base_url("runner/buyer/rejected").'">Reject Delivered</a></li>';
    echo '</ul>';
  }
}
