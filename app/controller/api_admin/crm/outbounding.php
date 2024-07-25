<?php
class Outbounding extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        $this->lib("seme_purifier");
        $this->load("api_admin/common_code_model", 'ccm');
        $this->load("api_admin/c_outbounding_model", 'com');
        $this->load("api_admin/c_detail_outbound_model", 'cdom');
        $this->load("api_admin/e_chat_model", 'ecm');
        $this->load("api_admin/d_pemberitahuan_model", 'dpem');
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $d['sess']->admin->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");


        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "cdate";
                break;
            case 3:
                $sortCol = "judul";
                break;
            case 4:
                $sortCol = "teks";
                break;
            case 5:
                $sortCol = "total_clicked";
                break;
            case 6:
                $sortCol = "is_active";
                break;
            default:
                $sortCol = "no";
        }

        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        $keyword = $sSearch;

        //advanced filter
        $is_active = $this->input->post("is_active");
        /*var_dump($is_active); die();*/
        if ($is_active == "1") {
        } elseif ($is_active == "0") {
        } else {
            $is_active = "";
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->com->countAll($nation_code, $keyword, $is_active);
        $ddata = $this->com->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_active);
        $return = array();
        foreach ($ddata as $key => $dts) {
            $return[$key]['no'] = $dts->no;
            $return[$key]['id'] = $dts->id;
            $return[$key]['cdate'] = date("d M Y H:i:s", strtotime($dts->cdate));

            //by Donny Dennison - 30 September 2020 17:12
            //bug fixing ' or " become emoji
            // $return[$key]['judul'] = strip_tags(html_entity_decode($dts->judul));
            // $return[$key]['teks'] = strip_tags(html_entity_decode($dts->teks));
            $return[$key]['judul'] = strip_tags(html_entity_decode($this->__convertToEmoji($dts->judul),ENT_QUOTES));
            $return[$key]['teks'] = strip_tags(html_entity_decode($this->__convertToEmoji($dts->teks),ENT_QUOTES));
            $return[$key]['total_clicked'] = $dts->total_clicked;
            
            $status = (int) $dts->active;
            if ($status==0) {
                $dts->active = "Inactive";
            }
            else
            {
                $dts->active = "Active";
            }
            $return[$key]['is_active'] = $dts->active;
            // End Edit
        }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($return, $dcount);
    }

    public function tambah()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $di = $_POST;
         // by Muhammad Sofi 23 February 2022 12:21 | change to readable html / special char tags
        foreach ($di as $key=>&$val) {
            if (is_string($val)) {
                if ($key == 'judul' || $key == 'teks') {
                    $val = $this->seme_purifier->richtext($val);
                    // $val = $this->__f($val);
                } else {
                    $val = $this->__f($val);
                }
            }
        }      

        // removes html
        if (isset($di['judul'])) {
            $di['judul'] = strip_tags($di['judul']);
        }
        if (isset($di['teks'])) {
            $di['teks'] = strip_tags($di['teks']);
        }
                
        if (!isset($di['judul'])) {
            $di['judul'] = "";
        }
        if (strlen($di['judul'])>0) {
            $this->com->trans_start();
            $di['cdate'] = 'NOW()';
            $di['nation_code'] = $nation_code;

            // input ke dalam c_outbounding
            $sum['nation_code'] = $di['nation_code'];
            $sum['cdate'] = $di['cdate'];
            $sum['judul'] = $di['judul'];
            $sum['teks'] = $di['teks'];
            $sum['is_active'] = $di['is_active'];
            $sum['is_notif'] = 0;
            $res = $this->com->set($sum);
            $outbounding_id = $this->com->getLastId($nation_code, $di['judul']);

            if(!empty($di['product']))
            {
                foreach ($di['product'] as $key => $product) {
                    if(!empty($product)){
                        $detail['nation_code'] = $nation_code;
                        $detail['c_outbound_id'] = $outbounding_id;
                        $detail['type'] = "product";
                        $detail['name'] = $product;
                        $detail['url'] = $di['urlp'][$key];
                        $detail['cdate'] = 'NOW()';
                        $db_product = $this->cdom->set1($detail);
                    }
                }
            }

            if(!empty($di['shop']))
            {
                foreach ($di['shop'] as $key2 => $shop) {
                    if(!empty($shop)){
                        $detail2['nation_code'] = $nation_code;
                        $detail2['c_outbound_id'] = $outbounding_id;
                        $detail2['type'] = "shop";
                        $detail2['name'] = $shop;
                        $detail2['url'] = $di['urls'][$key2];
                        $detail2['cdate'] = 'NOW()';
                        $db_shop = $this->cdom->set2($detail2);
                    }
                }
            }

            if(!empty($di['other']))
            {  
                foreach ($di['other'] as $key1 => $other) {
                    if(!empty($other)){
                        $detail3['nation_code'] = $nation_code;
                        $detail3['c_outbound_id'] = $outbounding_id;
                        $detail3['type'] = "other";
                        $detail3['name'] = $other;
                        $detail3['url'] = $di['urlo'][$key1];
                        $detail3['cdate'] = 'NOW()';
                        $db_other = $this->cdom->set3($detail3);
                    }
                }
            }
            if ($res) {
                $this->com->trans_commit();
                $this->status = 200;
                $this->message = 'Success';

                // BY muhammad Sofi 18 February 2022 10:45 - Remark Code(only save data to c_outbounding and c_detail_outbounding)
                //check is_active promotion and still active
                //&& (strtotime($di['edate']) > strtotime("now"))

                // if (!empty($di['is_active']&&$di['is_active']==1)) {
                //     //push notif array per device
                //     $ios = array();
                //     $android = array();

                //     //create push notif
                //     $classified = 'setting_notification_user';
                //     $code = 'U1';
                //     $users = $this->ccm->getUsersByNationCode($nation_code, $classified, $code);
                //     //$users = $this->bum->getYangAdaNotifnya();
                //     if (count($users)>0) {
                //         foreach ($users as $user) {
                //             if (strtolower($user->device) == 'ios') {
                //                 $ios[] = $user->fcm_token;
                //             } else {
                //                 $android[] = $user->fcm_token;
                //             }

                //             //notification list for buyer
                //             $dpe = array();
                //             $dpe['nation_code'] = $nation_code;
                //             $dpe['b_user_id'] = $user->id;
                //             $dpe['id'] = $this->dpem->getLastId($nation_code, $user->id);
                //             $dpe['type'] = "outbounding";

                //             //by Donny Dennison - 30 September 2020 17:12
                //             //bug fixing ' or " become emoji
                //             // $dpe['judul'] = strip_tags(html_entity_decode($di['judul']));
                //             // $dpe['teks'] = strip_tags(html_entity_decode($di['teks']));
                //             $dpe['judul'] = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //             $dpe['teks'] = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //             $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
                //             $dpe['cdate'] = "NOW()";
                //             $extras = new stdClass();
                //             $extras->id = (int)$outbounding_id;

                //             //by Donny Dennison - 30 September 2020 17:12
                //             //bug fixing ' or " become emoji
                //             // $extras->judul = strip_tags(html_entity_decode($di['judul']));
                //             // $extras->teks = strip_tags(html_entity_decode($di['teks']));
                //             $extras->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //             $extras->teks = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));
                //             $dpe['extras'] = json_encode($extras);
                //             $this->dpem->set($dpe);
                //             $this->com->trans_commit();
                //         }
                //     } //end foreach

                //     $total = count($ios);
                //     if ($this->is_log) {
                //         $this->seme_log->write("api_admin", "API_Admin/Marketing::baru __pushNotifiOSCount: $total");
                //     }
                //     if (is_array($ios) && $total>0) {
                //         //push notif to ios
                //         $device = "ios"; //jenis device
                //         $tokens = $ios; //device token

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $title = strip_tags(html_entity_decode($di['judul']));
                //         // $message = strip_tags(html_entity_decode($di['teks']));
                //         $title = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //         $message = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //         $image = 'media/pemberitahuan/promotion.png';
                //         $type = 'outbounding';
                //         $payload = new stdClass();
                //         $payload->id = $outbounding_id;

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $payload->judul = strip_tags(html_entity_decode($di['judul']));
                //         $payload->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));

                //         $payload->teks = '';
                //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_admin", 'API_Admin/Marketing::baru __pushNotifiOS: '.json_encode($res));
                //         }
                //     }

                //     //push notif to android
                //     $total = count($android);
                //     if ($this->is_log) {
                //         $this->seme_log->write("api_admin", "API_Admin/Marketing::baru __pushNotifAndroidCount: $total");
                //     }
                //     if (is_array($android) && $total>0) {
                //         $device = "android"; //jenis device
                //         $tokens = $android; //device token

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $title = strip_tags(html_entity_decode($di['judul']));
                //         // $message = strip_tags(html_entity_decode($di['teks']));
                //         $title = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //         $message = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //         $type = 'outbounding';
                //         $image = 'media/pemberitahuan/promotion.png';
                //         $payload = new stdClass();
                //         $payload->id = $outbounding_id;

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $payload->judul = strip_tags(html_entity_decode($di['judul']));
                //         $payload->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));

                //         $payload->teks = '';
                //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_admin", 'API_Admin/Marketing::baru __pushNotifAndroid: '.json_encode($res));
                //         }
                //     }
                // } else {
                //     $this->seme_log->write("api_admin", 'API_Admin/Marketing::baru() -> Marketing not active or expired');
                // }
                // BY muhammad Sofi 18 February 2022 10:45 - Remark Code(only save data to c_outbounding and c_detail_outbounding)
            } else {
                $this->status = 200;
                $this->message = 'Success';
            }
        } else {
            $this->status = 109;
            $this->message = 'Title is required, please check again';
        }

        $this->__json_out($data);
    }
    
    public function detail($id)
    {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $data = $this->com->getById($nation_code, $id);
        if (!isset($data->id)) {
            $this->status = 455;
            $this->message = 'Data Marketing not found or Invalid ID';
            $this->__json_out($data);
        }

        if(isset($data->judul)){
			$data->judul = html_entity_decode($this->__convertToEmoji($data->judul), ENT_QUOTES);
		}

        if(isset($data->teks)){
			$data->teks = html_entity_decode($this->__convertToEmoji($data->teks), ENT_QUOTES);
		}

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
    }

    public function link_detail($ieid)
    {
        $id = (int) $ieid; 
        /*var_dump($id); die();*/

        /*var_dump($id); die();*/
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        if ($id<=0) {
            $this->status = 591;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");
        $sortCol = "cdate";

        $tbl_as = $this->cdom->getTableAlias();

        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        
        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "$tbl_as.id";
                break;
            case 2:
                $sortCol = "$tbl_as.type";
                break;
            case 3:
                $sortCol = "$tbl_as.name";
                break;
            case 4:
                $sortCol = "$tbl_as.url";
                break;
            case 5:
                $sortCol = "$tbl_as.cdate";
                break;
            default:
            $sortCol = "no";
        }
        
        $keyword = $sSearch;

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cdom->getAll($nation_code, $id, $page, $pagesize, $sortCol, $sortDir, $keyword);
        $dcount = $this->cdom->countAll($nation_code, $id, $keyword);

        foreach ($data as &$dt) {
            if (isset($dt->cdate)) {
                $dt->cdate = date("d M Y", strtotime($dt->cdate));
            }
        }
        $return = array();
        foreach ($data as $key => $dts) {
            $return[$key]['no'] = $dts->no;
            $return[$key]['id'] = $dts->id;
            $return[$key]['type'] = $dts->type;
            $return[$key]['name'] = $this->__convertToEmoji($dts->name);
            $return[$key]['url'] = $dts->url;
            $return[$key]['cdate'] = $dt->cdate;
            $return[$key]['active'] = $dt->active;
            /*$return[$key]['action'] = $dt->action;*/
            // End Edit
        }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($return, $dcount);
    }

    public function edit($ieid)
    {
        $d = $this->__init();
        /*var_dump($id); die();*/
        $data = array();
        if(!$this->admin_login){
            $this->status = 401;
            $this->message = 'Authorization required';
            header("HTTP/1.0 400 Harus Authorization required");
            $this->__json_out($data);
            die();
        }
        $id = (int) $ieid;
        $du = $_POST;

        if($du['is_active'] == 0)
        {
            $du['is_notif'] = 0;
        }

        $nation_code = $d['sess']->admin->nation_code;
        $res = $this->com->update($nation_code,$id,$du); 

        if($res){
            $this->status = 200;
            $this->message = 'Success';
    
            // BY muhammad Sofi 18 February 2022 10:45 - Remark Code(only save data to c_outbounding and c_detail_outbounding)
            //check is_active promotion and still active
            // if ($du['is_active'] == 1 && $du['is_notif'] == 0) {
            //     /*echo "UHUY CIHUY"; die();*/
            //     $ios = array();
            //     $android = array();

            //     //create push notif
            //     $classified = 'setting_notification_user';
            //     $code = 'U1';
            //     $users = $this->ccm->getUsersByNationCode($nation_code, $classified, $code);

            //     if (count($users)>0) {
            //     foreach ($users as $user) {
            //         if (strtolower($user->device) == 'ios') {
            //             $ios[] = $user->fcm_token;
            //         } else {
            //             $android[] = $user->fcm_token;
            //         }
            //         $dpe = array();
            //         $dpe['nation_code'] = $nation_code;
            //         $dpe['b_user_id'] = $user->id;
            //         $dpe['id'] = $this->dpem->getLastId($nation_code, $user->id);
            //         $dpe['type'] = "outbounding";

            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $dpe['judul'] = strip_tags(html_entity_decode($du['judul']));
            //         // $dpe['teks'] = strip_tags(html_entity_decode($du['teks']));
            //         $dpe['judul'] = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));
            //         $dpe['teks'] = strip_tags(html_entity_decode($du['teks'],ENT_QUOTES));

            //         $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
            //         $dpe['cdate'] = "NOW()";
            //         $extras = new stdClass();
            //         $extras->id = (int)$id;

            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $extras->judul = strip_tags(html_entity_decode($du['judul']));
            //         // $extras->teks = strip_tags(html_entity_decode($du['teks']));
            //         $extras->judul = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));
            //         $extras->teks = strip_tags(html_entity_decode($du['teks'],ENT_QUOTES));

            //         $dpe['extras'] = json_encode($extras);
            //         $this->dpem->set($dpe);
            //         $this->com->trans_commit();
            //         }
            //     }

            //     $total = count($ios);
            //     if ($this->is_log) {
            //         $this->seme_log->write("api_admin", "API_Admin/Marketing::baru __pushNotifiOSCount: $total");
            //     }
            //     if (is_array($ios) && $total>0) {
            //         //push notif to ios
            //         $device = "ios"; //jenis device
            //         $tokens = $ios; //device token

            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $title = strip_tags(html_entity_decode($du['judul']));
            //         // $message = strip_tags(html_entity_decode($du['teks']));
            //         $title = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));
            //         $message = strip_tags(html_entity_decode($du['teks'],ENT_QUOTES));

            //         $type = 'outbounding';
            //         $image = 'media/pemberitahuan/promotion.png';
            //         $payload = new stdClass();
            //         $payload->id = $id;

            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $payload->judul = strip_tags(html_entity_decode($du['judul']));
            //         $payload->judul = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));

            //         $payload->teks = '';
            //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            //         if ($this->is_log) {
            //             $this->seme_log->write("api_admin", 'API_Admin/Marketing::baru __pushNotifiOS: '.json_encode($res));
            //         }
            //     }
            //     $total = count($android);
            //     if ($this->is_log) {
            //         $this->seme_log->write("api_admin", "API_Admin/Marketing::baru __pushNotifAndroidCount: $total");
            //     }
            //     if (is_array($android) && $total>0) {
            //         $device = "android"; //jenis device
            //         $tokens = $android; //device token

            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $title = strip_tags(html_entity_decode($du['judul']));
            //         // $message = strip_tags(html_entity_decode($du['teks']));
            //         $title = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));
            //         $message = strip_tags(html_entity_decode($du['teks'],ENT_QUOTES));

            //         $type = 'outbounding';
            //         $image = 'media/pemberitahuan/promotion.png';
            //         $payload = new stdClass();
            //         $payload->id = $id;
                    
            //         //by Donny Dennison - 30 September 2020 17:12
            //         //bug fixing ' or " become emoji
            //         // $payload->judul = strip_tags(html_entity_decode($du['judul']));
            //         $payload->judul = strip_tags(html_entity_decode($du['judul'],ENT_QUOTES));

            //         $payload->teks = '';
            //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            //         if ($this->is_log) {
            //             $this->seme_log->write("api_admin", 'API_Admin/Marketing::baru __pushNotifAndroid: '.json_encode($res));
            //         }
            //     }
            // }else {
            //     $this->status = 200;
            //     $this->message = 'Success';
            //     /*echo "Langsung ke sini nihh"; die();*/
            // }
        }else{
            $this->status = 901;
            $this->message = 'Cannot edit data to database';
        }
        $this->__json_out($data);
    }

    public function show_edit($id)
    {
        $id = (int) $id; 
        /*var_dump($id); die();*/
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        if ($id<=0) {
            $this->status = 591;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cdom->getById($nation_code,$id);
        $this->__json_out($data);
    }

    public function editDetail($ieid)
    {
        $d = $this->__init();
        /*var_dump($id); die();*/
        $data = array();
        if(!$this->admin_login){
            $this->status = 401;
            $this->message = 'Authorization required';
            header("HTTP/1.0 400 Harus Authorization required");
            $this->__json_out($data);
            die();
        }
            $id = (int) $ieid;
            /*var_dump($id); die();*/
            $du = $_POST;

            /*var_dump($du); die();*/
            $nation_code = $d['sess']->admin->nation_code;
            $res = $this->cdom->update($nation_code,$id,$du);

            if($res){
                $this->status = 200;
                $this->message = 'Success';
            }else{
                $this->status = 901;
                $this->message = 'Cannot edit data to database';
            }
        $this->__json_out($data);
    }
            
    public function hapus($ieid)
    {
        $id = (int) $ieid;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        $nation_code = $d['sess']->admin->nation_code;
        

        $res = $this->com->del($nation_code, $id);
        $ress = $this->cdom->del($nation_code, $id);
        if ($res && $ress) 
        {
            $this->status = 200;
            $this->message = 'Success';
        }else
        {
            $this->status = 902;
            $this->message = 'Failed while deleting data from database';
        }
        $this->__json_out($data);
    }

    public function hapusDetail($ieid)
    {
        $id = (int) $ieid;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        $nation_code = $d['sess']->admin->nation_code;
        
        $res = $this->cdom->delDetail($nation_code, $id);
        if ($res) 
        {
            $this->status = 200;
            $this->message = 'Success';
        }else
        {
            $this->status = 902;
            $this->message = 'Failed while deleting data from database';
        }
        $this->__json_out($data);
    }
}
