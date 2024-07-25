<?php
require_once(SENECORE."runner_controller.php");
class Order extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $url = '';
  var $url_aft = '';
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
  }
  public function index(){
    echo '<p></p>';
    echo '<h3>Order Runner</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/shipment_check/qxpress/").'">QXpress</a></li>';
    echo '</ul>';
  }
  public function qxpress(){
    
  }
}
