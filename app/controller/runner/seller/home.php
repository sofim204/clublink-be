<?php
class Home extends JI_Controller {
  public function __construct(){
    parent::__construct();

  }
  public function index(){
    echo '<p></p><h3>Seller Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/seller/orderlist").'">Order List</a></li>';
    echo '<li><a href="'.base_url("runner/seller/product").'">Active Product</a></li>';
    echo '<li><a href="'.base_url("runner/seller/wait").'">New Order -> Delivery</a></li>';
    echo '<li><a href="'.base_url("runner/seller/process").'">Process -> Delivery</a></li>';
    echo '</ul>';
  }
}
