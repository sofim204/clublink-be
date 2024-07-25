<?php
class Home extends JI_Controller {
  public function __construct(){
    parent::__construct();

  }
  public function index(){
    echo '<p></p><h3>Runner / Unit Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/homepage/").'">Homepage</a></li>';
    echo '<li><a href="'.base_url("runner/pelanggan/").'">Pelanggan</a></li>';
    echo '<li><a href="'.base_url("runner/my_account").'">My Account</a></li>';
    echo '<li><a href="'.base_url("runner/pelanggan/").'">Pelanggan (User)</a></li>';
    echo '<li><a href="'.base_url("runner/bulksale/").'">Bulksale / Buy It All</a></li>';
    echo '<li><a href="'.base_url("runner/cart/").'">Cart</a></li>';
    echo '<li><a href="'.base_url("runner/checkout/").'">Checkout</a></li>';
    echo '<li><a href="'.base_url("runner/buyer/").'">Account: Buyer</a></li>';
    echo '<li><a href="'.base_url("runner/seller/").'">Account: Seller</a></li>';
    echo '<li><a href="'.base_url("runner/complain/").'">Complain</a></li>';
    echo '<li><a href="'.base_url("runner/produk/").'">Produk</a></li>';
    echo '<li><a href="'.base_url("runner/free_product/").'">Free Product</a></li>';
    echo '<li><a href="'.base_url("runner/order/").'">Order Flow Test</a></li>';
    echo '<li><a href="'.base_url("runner/chat/").'">Chat</a></li>';
    echo '</ul>';
  }
}
