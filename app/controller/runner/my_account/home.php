<?php
class Home extends JI_Controller {
  public function __construct(){
    parent::__construct();

  }
  public function index(){
    echo '<p></p><h3>My Account Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/my_account/my_profile").'">My Profile</a></li>';
    echo '<li><a href="'.base_url("runner/my_account/bank").'">Bank</a></li>';
    echo '<li><a href="'.base_url("runner/my_account/alamat").'">Address</a></li>';
    echo '</ul>';
  }
}
