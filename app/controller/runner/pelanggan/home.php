<?php
class Home extends JI_Controller {
  public function __construct(){
    parent::__construct();

  }
  public function index(){
    echo '<p></p><h3>User Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/pelanggan/daftar").'">Daftar</a></li>';
    echo '<li><a href="'.base_url("runner/pelanggan/login").'">Login</a></li>';
    echo '<li><a href="'.base_url("runner/pelanggan/address").'">Address</a></li>';
    echo '<li><a href="'.base_url("runner/pelanggan/notification_list").'">Notification List</a></li>';
    echo '</ul>';
  }
}
