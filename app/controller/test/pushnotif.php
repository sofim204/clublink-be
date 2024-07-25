<?php
class PushNotif extends JI_Controller
{
    public $is_log = 1;
  
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('front');
        $this->lib('seme_log');
        $this->load('front/b_user_model', 'bum');
    }
    public function index()
    {
    }
    public function kirim()
    {
        // $ios_fcmtokens = array();
        // $android_fcmtokens = array();
        // $user = $this->bum->getYangAdaNotifnya();
        // foreach ($user as $u) {
        //     if (strlen($u->fcm_token)>9) {
        //         if (strtolower($u->device) == 'ios') {
        //             $ios_fcmtokens[] = $u->fcm_token;
        //         } elseif (strtolower($u->device) == 'android') {
        //             $android_fcmtokens[] = $u->fcm_token;
        //         }
        //     }
        // }
        // $ios_count = count($ios_fcmtokens);
        // $android_count = count($android_fcmtokens);
        // $this->seme_log->write("api_mobile", 'test/Pushnotif::kirim -> __pushNotif iOS: '.$ios_count.' android: '.$android_count);
        // echo 'test/Pushnotif::kirim -> __pushNotifiOS: iOS: '.$ios_count.' android: '.$android_count."<br />";
        
        // $payload = new stdClass();
        // $payload->judul = 'Test Blast Daeng Server';
        // $payload->deskripsi = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        // $payload->gambar = 'https://cms-sgmaster.sellon.net/media/pemberitahuan/promotion.png';
        // $payload->jenis = 'promotion';
        // $title = $payload->judul;
        // $message = $payload->deskripsi;
        // $type = $payload->jenis;
        // $image = $payload->gambar;
        // $res = 'NOT TRIGGERED';
        // if ($ios_count>0) {
        //     $res = $this->__pushNotif("ios", $ios_fcmtokens, $title, $message, $type, $image, $payload);
        // }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", 'test/Pushnotif::kirim -> __pushNotifiOS: '.json_encode($res));
        //     echo 'test/Pushnotif::kirim -> __pushNotifiOS: '."<br />";
        //     $this->debug($res);
        //     echo ''."<br />";
        // }
        // $res = 'NOT TRIGGERED';
        // if ($android_count>0) {
        //     $res = $this->__pushNotif("android", $android_fcmtokens, $title, $message, $type, $image, $payload);
        // }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", 'test/Pushnotif::kirim -> __pushNotifAndroid: '.json_encode($res));
        //     echo 'test/Pushnotif::kirim -> __pushNotifAndroid: '."<br />";
        //     $this->debug($res);
        //     echo ''."<br />";
        // }
    }
}
