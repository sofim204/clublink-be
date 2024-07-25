<?php
class Outbounding extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/common_code_model", 'ccm');
        $this->load("api_admin/c_outbounding_model", 'com');
        $this->load("api_admin/c_detail_outbound_model", 'cdom');
        $this->load("api_admin/e_chat_model", 'ecm');
        $this->load("api_admin/d_pemberitahuan_model", 'dpem');
    }

    public function index()
    {
        //open transaction
        // $this->cpm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Outbounding::index Start");

        $outboundingList = $this->com->getByIsNotif(0);
        $this->seme_log->write("api_cron", "API_Cron/Outbounding::total outbounding: ".count($outboundingList));
        if($outboundingList){
			
			foreach ($outboundingList as $list) {

	          	$du = array();
	          	$du['is_notif'] = 1;
        		$this->com->update($list->nation_code, $list->id, $du);
        		// $this->cpm->trans_commit();

	        }
	        unset($list);

          	foreach ($outboundingList as $list) {

        		//push notif array
                $tokens = array();

                //create push notif
                $classified = 'setting_notification_user';
                $code = 'U1';
                $users = $this->ccm->getUsersByNationCodeAndSettingValueTrue($list->nation_code, $classified, $code);
                if (count($users)>0) {
                    foreach ($users as $user) {
                        $tokens[] = $user->fcm_token;

                        // //notification list for buyer
                        // $dpe = array();
                        // $dpe['nation_code'] = $list->nation_code;
                        // $dpe['b_user_id'] = $user->id;
                        // $dpe['id'] = $this->dpem->getLastId($list->nation_code, $user->id);
                        // $dpe['type'] = "outbounding";
                        // if($user->language_id == 2) {
                        //     $dpe['judul'] = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                        //     $dpe['teks'] = strip_tags(html_entity_decode($list->teks,ENT_QUOTES));
                        // } else {
                        //     $dpe['judul'] = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                        //     $dpe['teks'] = strip_tags(html_entity_decode($list->teks,ENT_QUOTES));
                        // }

                        // $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
                        // $dpe['cdate'] = "NOW()";
                        // $extras = new stdClass();
                        // $extras->id = (int)$list->id;
                        // if($user->language_id == 2) { 
                        //     $extras->judul = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                        //     $extras->teks = strip_tags(html_entity_decode($list->teks,ENT_QUOTES));
                        // } else {
                        //     $extras->judul = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                        //     $extras->teks = strip_tags(html_entity_decode($list->teks,ENT_QUOTES));
                        // }

                        // $dpe['extras'] = json_encode($extras);
                        // $this->dpem->set($dpe);
                    }
                    unset($users, $user);
                } //end foreach

                $total = count($tokens);
                $this->seme_log->write("api_cron", "API_Cron/Outbounding::baru __pushNotifCount: $total");
                if(is_array($tokens) && $total>0) {
                    $device = "not use anymore"; //jenis device
                    $tokens = $tokens; //device token

                    $title = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                    $message = strip_tags(html_entity_decode($list->teks,ENT_QUOTES));
                    $image = 'media/pemberitahuan/promotion.png';
                    $type = 'outbounding';
                    $payload = new stdClass();
                    $payload->id = $list->id;
                    $payload->judul = strip_tags(html_entity_decode($list->judul,ENT_QUOTES));
                    $payload->teks = '';
                    $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                }
                unset($tokens, $total);

                //change log filename
                // $this->seme_log->changeFilename("cron.log");

	        }

        }

        //end transacation
        // $this->cpm->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Outbounding::index Stop");

        die();
    }

}
