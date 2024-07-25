<?php
class Environment extends JI_Controller{
  var $is_log = 1;
  public function __construct(){
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/common_code_model","ccm");
  }
  public function index(){
    $nation_code = $this->input->get("nation_code");
    $this->status = 200;
    $this->message = "Success";
    $data = $this->ccm->getByClassified($nation_code,"environment");
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "environment");
  }

  public function maintenance(){
    $nation_code = $this->input->get("nation_code");
    $this->status = 200;
    $this->message = "Success";
    $data = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C2");
    
    if(isset($data->remark)){
      $data = $data->remark;
    }else{
      $data = "on";
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "environment");
  }

  public function singapore_server_status() {
    $nation_code = 62;
    $this->status = 200;
    $this->message = "Success";
    $data = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C23");
    
    if(isset($data->remark)){
      $data = $data->remark;
    }else{
      $data = "on";
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "environment");
  }

}
