<?php
//account password
class Activate extends JI_Controller {
  public function __construct(){
    parent::__construct();
    $this->setTheme('user');
    $this->load('front/b_user_model','bu');

    //by Donny Dennison - 12 september 2022 14:59
    //kode referral
    $this->load("api_mobile/b_user_model", 'bu2');
    $this->load("api_mobile/b_user_alamat_model", 'bua');
    $this->load("api_mobile/common_code_model", 'ccm');
    // $this->load("api_mobile/g_leaderboard_point_total_model", 'glptm');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

  }

  public function index($kode=""){
    $data = $this->__init();
    if(strlen($kode)>8){
      $user = $this->bu->getByApiRegToken($kode);
      if(isset($user->id)){
        if(empty($user->is_confirmed) && strlen($user->api_reg_token) > 0){
          $du = array();
          $du['is_confirmed'] = 1;

          //by Donny Dennison - 10 december 2020 15:01
          //new registration system for apple id
          $du['is_reset_password'] = 1;
          
          $res = $this->bu->edit($user->nation_code, $user->id, $du);

          // //START by Donny Dennison - 12 september 2022 14:59
          // //kode referral
          // $recruiterData = $this->bu2->getById($user->nation_code, $user->b_user_id_recruiter);

          // //RECRUITED
          // if($user->b_user_id_recruiter != 0){

          //   //get point
          //   $pointGet = $this->ccm->getByClassifiedAndCode($user->nation_code, "leaderboard_point", "EY");
          //   if (!isset($pointGet->remark)) {
          //     $pointGet = new stdClass();
          //     $pointGet->remark = 10;
          //   }

          //   $pelangganAddress = $this->bua->getByUserIdDefault($user->nation_code, $user->id);

          //   $leaderBoardHistoryId = $this->glphm->getLastId($user->nation_code, $user->id, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi);
          //   $di = array();
          //   $di['nation_code'] = $user->nation_code;
          //   $di['id'] = $leaderBoardHistoryId;
          //   $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
          //   $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
          //   $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
          //   $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
          //   $di['b_user_id'] = $user->id;
          //   $di['point'] = $pointGet->remark;
          //   $di['custom_id'] = $user->b_user_id_recruiter;
          //   $di['custom_type'] = 'referral';
          //   $di['custom_type_sub'] = 'link';
          //   $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          //   $this->glphm->set($di);

          //   // $this->glptm->updateTotal($user->nation_code, $user->id, 'total_point', '+', $di['point']);

          //   // $this->glrm->updateTotal($user->nation_code, $user->id, 'total_point', '+', $di['point']);

          // }

          // //RECRUITER
          // if($user->b_user_id_recruiter != 0 && $recruiterData->is_active == 1){

          //   //get point
          //   $pointGet = $this->ccm->getByClassifiedAndCode($user->nation_code, "leaderboard_point", "EZ");
          //   if (!isset($pointGet->remark)) {
          //     $pointGet = new stdClass();
          //     $pointGet->remark = 10;
          //   }

          //   $pelangganAddress = $this->bua->getByUserIdDefault($user->nation_code, $user->b_user_id_recruiter);

          //   $leaderBoardHistoryId = $this->glphm->getLastId($user->nation_code, $user->b_user_id_recruiter, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi);
          //   $di = array();
          //   $di['nation_code'] = $user->nation_code;
          //   $di['id'] = $leaderBoardHistoryId;
          //   $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
          //   $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
          //   $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
          //   $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
          //   $di['b_user_id'] = $user->b_user_id_recruiter;
          //   $di['point'] = $pointGet->remark;
          //   $di['custom_id'] = $user->id;
          //   $di['custom_type'] = 'referral';
          //   $di['custom_type_sub'] = 'link';
          //   $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          //   $this->glphm->set($di);

          //   // $this->glptm->updateTotal($user->nation_code, $user->b_user_id_recruiter, 'total_point', '+', $di['point']);

          //   // $this->glrm->updateTotal($user->nation_code, $user->b_user_id_recruiter, 'total_point', '+', $di['point']);
            
          //   $this->bu->updateTotal($user->nation_code, $user->b_user_id_recruiter, "total_recruited", "+", "1");

          // }
          // //END by Donny Dennison - 12 september 2022 14:59
          // //kode referral

          //send email
          $this->lib('seme_email');
          $replacer = array();
          $replacer['site_name'] = $this->app_name;
          $replacer['fnama'] = $user->fnama;

          //building email properties
          $this->seme_email->replyto($this->site_name,$this->site_replyto);
          $this->seme_email->from($this->site_email,$this->site_name);
          $this->seme_email->subject('Your account is now Active!');
          $this->seme_email->to($user->email,$user->fnama);
          $this->seme_email->template('account_active');
          $this->seme_email->replacer($replacer);
          $this->seme_email->send();

          $data = $this->__init();
          $data['email_debug'] = $this->seme_email->getLog();
          $this->setTitle('Account Activation '.$this->site_suffix);
          $this->putThemeContent('account/activate',$data);
          $this->putJsReady('account/activate_bottom',$data);
          $this->loadLayout('col-1',$data);
          $this->render();
        }else{
          header("HTTP/1.0 404 Not Found");
          echo '<h1>507</h1><p>Account already activated</p>';
          die();
        }
      }else{
        header("HTTP/1.0 404 Not Found");
        echo '<h1>506</h1><p>Invalid Activation Link</p>';
        die();
      }
    }else{
      header("HTTP/1.0 404 Not Found");
      echo '<h1>505</h1><p>Invalid Activation Link</p>';
      die();
    }
  }


  public function test_email_active(){
    $email = $this->input->request("email");
    if(strlen($email)<=4) $email = 'daeng@somein.co.id';
    $user = $this->bu->getByEmail($email);
    if(!isset($user->id)){
      die("unregistered email");
    }
    $nama = $user->fnama;

    //generate acativation link
    $link = $this->__activateGenerateLink($user->id);

    //load email libary
    $this->lib('seme_email');
    $replacer = array();
    $replacer['site_name'] = $this->app_name;
    $replacer['fnama'] = $nama;

    //building email properties
    $this->seme_email->replyto($this->site_name,$this->site_replyto);
    $this->seme_email->from($this->site_email,$this->site_name);
    $this->seme_email->subject('Please confirm your '.$this->site_name.' registration');
    $this->seme_email->to($email,$nama);
    $this->seme_email->template('account_register');
    $this->seme_email->replacer($replacer);
    $this->seme_email->send();
    $this->debug($this->seme_email->getLog());
  }
}
