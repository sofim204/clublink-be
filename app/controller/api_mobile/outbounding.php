<?php
class Outbounding extends JI_Controller
{
  public $is_soft_delete=1;
  public $is_log = 1;
  public $imgQueue;

  //by Donny Dennison 16 augustus 2020 00:25
  //fix check emoji in insert & edit product and discussion
  //credit : https://stackoverflow.com/questions/41580483/detect-emoticons-in-string

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->lib("seme_purifier");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/c_detail_outbound_model", "codm");
    $this->load("api_mobile/c_outbounding_model", "com");

    $this->imgQueue = array();
  }

  public function detail($ieid)
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();

    //by Donny Dennison - 28 august 2020 12:01
    //request from mobile dev to add reponse
    $data['outbounding'] = '';
  	$data['id'] = '';
  	$data['judul'] = '';

  	//by Donny Dennison - 28 august 2020 12:01
    //request from mobile dev to add reponse
    // $data['produk'] = new stdClass();
    // $data['shop'] = new stdClass();
    // $data['outbounding'] = new stdClass();
    $data['produk'] = array();
    $data['shop'] = array();
    $data['outbounding'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
        $pelanggan = new stdClass();
        if($nation_code == 62){ //indonesia
            $pelanggan->language_id = 2;
        }else if($nation_code == 82){ //korea
            $pelanggan->language_id = 3;
        }else if($nation_code == 66){ //thailand
            $pelanggan->language_id = 4;
        }else {
            $pelanggan->language_id = 1;
        }
    }

    $id = (int) $ieid;
    $outbounding = $this->com->getById($nation_code,$id);
    if (!isset($outbounding->id)) {
      $this->status = 595;
      $this->message = 'Invalid outbounding ID';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
      die();
    }

    $data['outbounding'] = $outbounding;
    $data['id'] = $outbounding->id;
    $data['judul'] = $outbounding->judul;

    //by Donny Dennison - 30 September 2020 17:12
    //bug fixing ' or " become emoji
    // $data['teks'] = $outbounding->teks;
    $data['teks'] = html_entity_decode($outbounding->teks,ENT_QUOTES);

    $typeP = "product";
    $produk = $this->codm->getByIdP($nation_code,$id,$typeP);
    $data['product'] = $produk;

    $typeS = "shop";
    $shop = $this->codm->getByIdS($nation_code,$id,$typeS);
    $data['shop'] = $shop;

    $typeO = "other";
    $other = $this->codm->getByIdO($nation_code,$id,$typeO);
    $data['other'] = $other;

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
  }

  public function click_notif() {
    $data = array();
    $outbound_id = $this->input->get('marketing_outbound_id');
    // check if data exists 
    $check = $this->com->getById("62", $outbound_id);

    if(!isset($check->id)) {
      $this->status = 320;
      $this->message = 'You entering wrong id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
      die();
    }

    $this->com->updateTotalData("total_clicked", "+", "1", $outbound_id);
    $this->status = 200;
    $this->message = 'Success';
    
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "outbounding");
  }
}
