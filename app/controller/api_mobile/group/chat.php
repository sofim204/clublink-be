<?php
class Chat extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_curl");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/group/i_group_model", "igm");
        $this->load("api_mobile/group/i_chat_room_model", 'icrm');
        $this->load("api_mobile/group/i_chat_participant_model", "icpm");
        $this->load("api_mobile/group/i_chat_model", 'icm');
        $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
        $this->load("api_mobile/group/i_chat_attachment_model", 'icam');
        $this->load("api_mobile/group/i_chat_read_model", 'icreadm');
        $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
    }

    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                  substr($charid,  0,  8).$hyphen.
                  substr($charid,  8,  4).$hyphen.
                  substr($charid, 12,  4).$hyphen.
                  substr($charid, 16,  4).$hyphen.
                  substr($charid, 20, 12).
                  $rbrace;
        return $guidv4;
    }

    private function __moveImagex($nation_code, $url, $targetdir, $produk_id="0", $ke="")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';
        // $produk_id = (int) $produk_id;

        // $targetdir = $this->media_community;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $tahun = date("Y");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $bulan = date("m");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);
        if (file_exists($file_path) && is_file($file_path)) {
          
            $file_path_thumb = parse_url($url, PHP_URL_PATH);
            $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
            $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
            $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

            $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
            $filethumb = $filename."-thumb.".$extension;
            $filename = $filename.".".$extension;

            rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
            rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
      
            $sc->status = 200;
            $sc->message = 'Success';
            $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
            $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
            $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
            $sc->file_size_thumb = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
    
        } else {
            $sc->status = 997;
            $sc->message = 'Failed';
        }

        // $this->seme_log->write("api_mobile", 'API_Mobile/Community::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
        return $sc;
    }

    private function __moveVideox($nation_code, $url, $targetdir, $produk_id="0", $ke="")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';
        // $produk_id = (int) $produk_id;

        // $targetdir = $this->media_community;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $tahun = date("Y");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $bulan = date("m");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);
        if (file_exists($file_path) && is_file($file_path)) {
          
            $file_path_thumb = parse_url($url, PHP_URL_PATH);
            $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);

            $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
            $filename = $filename.".".$extension;

            rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      
            $sc->status = 200;
            $sc->message = 'Success';
            $sc->url = str_replace("//", "/", $targetdir.'/'.$filename);
            $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    
        } else {
            $sc->status = 997;
            $sc->message = 'Failed';
        }
        return $sc;
    }

    private function __moveFilex($nation_code, $url, $targetdir, $produk_id="0", $ke="")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';

        // $targetdir = $this->media_community;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $tahun = date("Y");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $bulan = date("m");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);
        if (file_exists($file_path) && is_file($file_path)) {

        $file_path_thumb = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);

        $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
        $filename = $filename.".".$extension;

        rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

        $sc->status = 200;
        $sc->message = 'Success';
        $sc->url = str_replace("//", "/", $targetdir.'/'.$filename);
        $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    
        } else {
            $sc->status = 997;
            $sc->message = 'Failed';
        }
        return $sc;
    }

    private function __sortCol($sort_col, $tbl_as)
    {
        switch ($sort_col) {
          case 'cdate':
          $sort_col = "$tbl_as.cdate";
          break;
          case 'last_chat_cdate':
          $sort_col = "$tbl_as.last_chat_cdate";
          break;
          default:
          $sort_col = "$tbl_as.last_chat_cdate";
        }
        return $sort_col;
    }

    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "asc") {
          $sort_dir = "ASC";
        } else {
          $sort_dir = "DESC";
        }
        return $sort_dir;
    }

    private function __page($page)
    {
        if (!is_int($page)) {
          $page = (int) $page;
        }
        if ($page<=0) {
          $page = 1;
        }
        return $page;
    }

    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
          $page_size = 10;
        }
        return $page_size;
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_room_total'] = 0;
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // $sort_col = $this->input->post("sort_col");
        // $sort_dir = $this->input->post("sort_dir");
        $sort_col = "last_chat_cdate";
        $sort_dir = "desc";
        $page = $this->input->post("page");
        $page_size = $this->input->post("page_size");
        $i_group_id = $this->input->post("i_group_id");
        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        if($i_group_id == "0"){
            $i_group_id = "";
        }

        $tbl_as = $this->icrm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        // $data['chat_room_total'] = $this->icrm->countRoomChatByUserID($nation_code, $pelanggan->id, $i_group_id);
        $data['chat_room'] = $this->icrm->getRoomChatByUserID($nation_code, $pelanggan->id, $page, $page_size, $sort_col, $sort_dir, $i_group_id);
        foreach ($data['chat_room'] as &$cr) {
            $cr->image = $this->cdn_url($cr->image);
            $cr->b_user_ids = json_decode($cr->b_user_ids);
            $cr->images = array();
            $cr->custom_name_3 = "";
            $cr->id_lawan_bicara = "0";
            if($cr->type == "private"){
                if(count($cr->b_user_ids) > 1){
                    if($cr->b_user_ids[1] == $pelanggan->id){
                        $cr->id_lawan_bicara = $cr->b_user_ids[0];
                    }else{
                        $cr->id_lawan_bicara = $cr->b_user_ids[1];
                    }
                    $lawan_bicara_data = $this->bu->getById($nation_code, $cr->id_lawan_bicara);
                    $cr->custom_name_1 = $lawan_bicara_data->band_fnama;
                    if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                        $cr->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                    }else{
                        $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                    }
                }else{
                    if($pelanggan->language_id == 2){
                        $cr->custom_name_1 = "Tidak ada member";
                    }else{
                        $cr->custom_name_1 = "No member";
                    }
                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
                $cr->custom_name_3 = $cr->band_group_name;
            }else if($cr->type == "group"){
                if($cr->is_edited == "0"){
                    if($cr->is_main_group_chat_room == "1"){
                        $cr->images[] = $this->cdn_url($cr->band_group_image);
                    }else{
                        $memberList = $cr->b_user_ids;
                        if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                          unset($memberList[$key]);
                        }
                        $memberList = array_values($memberList);
                        $fourUserids = array_chunk($memberList, 4);
                        if($fourUserids){
                            $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                            foreach($arrayUserData AS $users){
                                $cr->custom_name_1 .= $users->band_fnama.", ";
                                if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                                }else{
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                                } 
                            }
                        }
                        $cr->custom_name_1 = substr($cr->custom_name_1, 0, -2);
                        $cr->custom_name_3 = $cr->band_group_name;
                    }
                }else{
                    $cr->images[] = str_replace("//", "/", $this->cdn_url($cr->image));
                    $cr->custom_name_3 = $cr->band_group_name;
                }
            }
            $cr->custom_name_1 = html_entity_decode($cr->custom_name_1,ENT_QUOTES);
            $cr->custom_name_3 = html_entity_decode($cr->custom_name_3,ENT_QUOTES);
            if(strtotime($cr->last_delete_chat) > strtotime($cr->last_chat_cdate)){
                $cr->custom_name_2 = "";
                if($cr->is_main_group_chat_room == "1"){
                    $cr->custom_name_2 = "Default club chat room with all Club members";
                    $cr->last_chat_message = "Default club chat room with all Club members";
                }
            }else{
                $cr->custom_name_2 = $cr->last_chat_message != "" ? $cr->last_chat_b_user_fnama.": ".$cr->last_chat_message : "";
            }
            $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by, null, $pelanggan->language_id);
            $cr->last_chat_cdate = $this->customTimezone($cr->cdate_for_order_by, $timezone);
        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function new_room_chat()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_room_total'] = "0";
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        // $sort_col = $this->input->post("sort_col");
        // $sort_dir = $this->input->post("sort_dir");
        $sort_col = "last_chat_cdate";
        $sort_dir = "desc";
        // $page = $this->input->post("page");
        // $page_size = $this->input->post("page_size");
        $i_group_id = $this->input->post("i_group_id");
        $datetime_last_call = $this->input->post("datetime_last_call");
        if (empty($datetime_last_call)) {
            $this->status = 402;
            $this->message = 'datetime_last_call is required';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }
        $datetime_last_call_server = $this->customTimezoneFrom($datetime_last_call, $timezone);

        if($i_group_id == "0"){
            $i_group_id = "";
        }

        $tbl_as = $this->icrm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);

        // $data['chat_room_total'] = $this->icrm->countRoomChatByUserID($nation_code, $pelanggan->id, $i_group_id, $datetime_last_call_server);
        $data['chat_room'] = $this->icrm->getRoomChatByUserID($nation_code, $pelanggan->id, 0, 0, $sort_col, $sort_dir, $i_group_id, $datetime_last_call_server);
        foreach ($data['chat_room'] as &$cr) {
            $cr->b_user_ids = json_decode($cr->b_user_ids);
            $cr->images = array();
            $cr->custom_name_3 = "";
            $cr->id_lawan_bicara = "0";
            if($cr->type == "private"){
                if(count($cr->b_user_ids) > 1){
                    if($cr->b_user_ids[1] == $pelanggan->id){
                        $cr->id_lawan_bicara = $cr->b_user_ids[0];
                    }else{
                        $cr->id_lawan_bicara = $cr->b_user_ids[1];
                    }
                    $lawan_bicara_data = $this->bu->getById($nation_code, $cr->id_lawan_bicara);
                    $cr->custom_name_1 = $lawan_bicara_data->band_fnama;
                    if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                        $cr->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                    }else{
                        $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                    }
                }else{
                    if($pelanggan->language_id == 2){
                        $cr->custom_name_1 = "Tidak ada member";
                    }else{
                        $cr->custom_name_1 = "No member";
                    }
                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
                $cr->custom_name_3 = $cr->band_group_name;
            }else if($cr->type == "group"){
                if($cr->is_edited == "0"){
                    if($cr->is_main_group_chat_room == "1"){
                        $cr->images[] = $this->cdn_url($cr->band_group_image);
                    }else{
                        $memberList = $cr->b_user_ids;
                        if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                          unset($memberList[$key]);
                        }
                        $memberList = array_values($memberList);
                        $fourUserids = array_chunk($memberList, 4);
                        if($fourUserids){
                            $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                            foreach($arrayUserData AS $users){
                                $cr->custom_name_1 .= $users->band_fnama.", ";
                                if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                                }else{
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                                } 
                            }
                        }
                        $cr->custom_name_1 = substr($cr->custom_name_1, 0, -2);
                        $cr->custom_name_3 = $cr->band_group_name;
                    }
                }else{
                    $cr->images[] = str_replace("//", "/", $this->cdn_url($cr->image));
                    $cr->custom_name_3 = $cr->band_group_name;
                }
            }
            $cr->custom_name_1 = html_entity_decode($cr->custom_name_1,ENT_QUOTES);
            $cr->custom_name_3 = html_entity_decode($cr->custom_name_3,ENT_QUOTES);
            if(strtotime($cr->last_delete_chat) > strtotime($cr->last_chat_cdate)){
                $cr->custom_name_2 = "";
                if($cr->is_main_group_chat_room == "1"){
                    $cr->custom_name_2 = "Default club chat room with all Club members";
                    $cr->last_chat_message = "Default club chat room with all Club members";
                }
            }else{
                $cr->custom_name_2 = $cr->last_chat_message != "" ? $cr->last_chat_b_user_fnama.": ".$cr->last_chat_message : "";
            }
            $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by, null, $pelanggan->language_id);
            $cr->last_chat_cdate = $this->customTimezone($cr->cdate_for_order_by, $timezone);
        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function detail()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_id'] = '';
        $data['chat_room'] = new stdClass();
        // $data['participant_list'] = array();
        // $data['chat_total'] = "0";
        $data['chats'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $data['chat_room_id'] = $this->input->post('chat_room_id');
        $chat_room_id = $this->input->post('chat_room_id');
        $data['chat_room'] = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data["chat_room"]->b_user_ids = json_decode($data["chat_room"]->b_user_ids);
        if(!in_array($pelanggan->id, $data["chat_room"]->b_user_ids)){
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $data["chat_room"]->images = array();
        $data["chat_room"]->custom_name_3 = "";
        $data["chat_room"]->id_lawan_bicara = "0";
        if($data["chat_room"]->type == "private"){
            if(count($data["chat_room"]->b_user_ids) > 1){
                if($data["chat_room"]->b_user_ids[1] == $pelanggan->id){
                    $data["chat_room"]->id_lawan_bicara = $data["chat_room"]->b_user_ids[0];
                }else{
                    $data["chat_room"]->id_lawan_bicara = $data["chat_room"]->b_user_ids[1];
                }
                $lawan_bicara_data = $this->bu->getById($nation_code, $data["chat_room"]->id_lawan_bicara);
                $data["chat_room"]->custom_name_1 = $lawan_bicara_data->band_fnama;
                if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                    $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                }else{
                    $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
            }else{
                if($pelanggan->language_id == 2){
                    $data["chat_room"]->custom_name_1 = "Tidak ada member";
                }else{
                    $data["chat_room"]->custom_name_1 = "No member";
                }
                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
            }
            $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
        }else if($data["chat_room"]->type == "group"){
            if($data["chat_room"]->is_edited == "0"){
                if($data["chat_room"]->is_main_group_chat_room == "1"){
                    $data["chat_room"]->images[] = $this->cdn_url($data["chat_room"]->band_group_image);
                }else{
                    $memberList = $data["chat_room"]->b_user_ids;
                    if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                      unset($memberList[$key]);
                    }
                    $memberList = array_values($memberList);
                    $fourUserids = array_chunk($memberList, 4);
                    if($fourUserids){
                        $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                        foreach($arrayUserData AS $users){
                            $data["chat_room"]->custom_name_1 .= $users->band_fnama.", ";
                            if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                            }else{
                                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                            } 
                        }
                    }
                    $data["chat_room"]->custom_name_1 = substr($data["chat_room"]->custom_name_1, 0, -2);
                    $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
                }
            }else{
                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($data["chat_room"]->image));
                $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
            }
        }
        $data["chat_room"]->custom_name_1 = html_entity_decode($data["chat_room"]->custom_name_1,ENT_QUOTES);
        $data["chat_room"]->custom_name_3 = html_entity_decode($data["chat_room"]->custom_name_3,ENT_QUOTES);
        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $data["chat_room"]->id, $pelanggan->id);
        if(strtotime($oneParticipantData->last_delete_chat) > strtotime($data["chat_room"]->last_chat_cdate)){
            $data["chat_room"]->custom_name_2 = "";
            if($data["chat_room"]->is_main_group_chat_room == "1"){
                $data["chat_room"]->custom_name_2 = "Default club chat room with all Club members";
                $data["chat_room"]->last_chat_message = "Default club chat room with all Club members";
            }
            $data["chat_room"]->cdate_text = $this->humanTiming($data["chat_room"]->cdate, null, $pelanggan->language_id);
            $data["chat_room"]->last_chat_cdate = $this->customTimezone($data["chat_room"]->cdate, $timezone);
        }else{
            $data["chat_room"]->custom_name_2 = $data["chat_room"]->last_chat_message != "" ? $data["chat_room"]->last_chat_b_user_fnama.": ".$data["chat_room"]->last_chat_message : "";
            $data["chat_room"]->cdate_text = $this->humanTiming($data["chat_room"]->last_chat_cdate, null, $pelanggan->language_id);
            $data["chat_room"]->last_chat_cdate = $this->customTimezone($data["chat_room"]->last_chat_cdate, $timezone);
        }

        // $sort_col = $this->input->post("sort_col");
        $sort_dir = $this->input->post("sort_dir");
        $page = $this->input->post("page");
        $page_size = $this->input->post("page_size");

        // $tbl_as = $this->icm->getTblAs();
        // $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_col = "ic.cdate";
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        // $data['participant_list'] = $this->igparticipantm->getParticipantByRoomChatId($nation_code,$chat_room_id);
        // $data['participant_total'] = count($data['participant_list']);

        // $last_delete_chat = '';
        // $id_lawan_bicara = 0;
        // foreach($data['participant_list'] as &$participantList){

        //     if($participantList->b_user_id == $pelanggan->id){
        //         $last_delete_chat = $participantList->last_delete_chat;
        //     }else{
        //         $id_lawan_bicara = $participantList->b_user_id;
        //     }

        //     if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
        //         $participantList->b_user_image = $this->cdn_url($participantList->b_user_image);
        //     } else {
        //         $participantList->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }

        //     if($data['chat_room']->chat_type == 'admin'){
        //         $data['chat_room']->custom_name_2 = 'SellOn Support';

        //         $data['chat_room']->custom_image = $participantList->b_user_image;
        //     }else if($participantList->b_user_id != $pelanggan->id && $data['chat_room']->chat_type != 'community'){
        //         $data['chat_room']->custom_name_2 = $participantList->b_user_fnama;

        //         $data['chat_room']->custom_image = $participantList->b_user_image;
        //     }else if($data['chat_room']->chat_type == 'community'){
        //         $data['chat_room']->custom_name_2 = $data['chat_room']->b_user_nama_starter;

        //         $data['chat_room']->custom_image = $data['chat_room']->b_user_image_starter;
        //     }

        // }
        // unset($participantList);

        // $data['chat_total'] = $this->icm->countAllByChatRoomId($nation_code, $chat_room_id, $oneParticipantData->last_delete_chat);
        $data['chats'] = $this->icm->getAllByChatRoomId($nation_code, $chat_room_id, $oneParticipantData->last_delete_chat, $page, $page_size, $sort_col, $sort_dir, $pelanggan->language_id);

        $chat_ids = array();
        foreach($data['chats'] AS $chat){
            $chat_ids[] = $chat->id;
        }
        unset($chat);

        //get attachment 
        $att = array();
        if($chat_ids){
            $attachments = $this->icam->getDetailByChatRoomID($nation_code, $chat_room_id, $chat_ids);
            foreach ($attachments as $atc) {
                $key = $nation_code.'-'.$chat_room_id.'-'.$atc->i_chat_id;

                $temp = new stdClass();
                if($atc->jenis == 'image'){
                    if (empty($atc->url)) {
                      $atc->url = 'media/community_default.png';
                    }
                    if (empty($atc->url_thumb)) {
                      $atc->url_thumb = 'media/community_default.png';
                    }
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->url_thumb = $this->cdn_url($atc->url_thumb);
                }else if($atc->jenis == 'video'){
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->url_thumb = $this->cdn_url($atc->url_thumb);
                }else if($atc->jenis == 'location'){
                    $temp->location_nama = $atc->location_nama;
                    $temp->location_address = $atc->location_address;
                    $temp->location_place_id = $atc->location_place_id;
                    $temp->location_latitude = $atc->location_latitude;
                    $temp->location_longitude = $atc->location_longitude;
                }else if($atc->jenis == 'file'){
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->file_name = $atc->file_name;
                    $temp->file_size = $atc->file_size;
                }

                //put to array key
                if (!isset($att[$key])) {
                    $att[$key] = array();
                }
                $att[$key][] = $temp;
            }
            unset($atc, $temp);
            unset($attachments);
        }
        unset($chat_ids);

        //chat iteration
        foreach ($data['chats'] as &$chat) {
            $chat->message = html_entity_decode($chat->message,ENT_QUOTES);
            if (isset($chat->b_user_band_image)) {
                if(file_exists(SENEROOT.$chat->b_user_band_image) && $chat->b_user_band_image != 'media/user/default.png'){
                    $chat->b_user_band_image = $this->cdn_url($chat->b_user_band_image);
                } else {
                    $chat->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $chat->cdate_text = $this->humanTiming($chat->cdate, null, $pelanggan->language_id);

            if($pelanggan->language_id == 2) {
                $chat->cdate_text_2 = $this->__dateIndonesia($chat->cdate);
            }else{
                $chat->cdate_text_2 = $this->__dateEnglish($chat->cdate);
            }

            $chat->cdate = $this->customTimezone($chat->cdate, $timezone);
            $chat->attachments = array();
            $key = $nation_code.'-'.$chat_room_id.'-'.$chat->id;
            if (isset($att[$key])) {
                $chat->attachments = $att[$key];
            }

            $chat->is_read_lawan_bicara = $this->icreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $chat->id, $pelanggan->id);
        }
        unset($att);

        $this->icpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);
        $this->icreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        //render
        $this->status = 200;
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function new_chat()
    {
        //initial
        //default result
        $data = array();
        $data['chat_room_id'] = '';
        $data['chat_room'] = new stdClass();
        // $data['participant_list'] = array();
        // $data['chat_total'] = "0";
        $data['chats'] = array();
        $data['chat_id_havent_read_lawan_bicara'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $data['chat_room_id'] = $this->input->post('chat_room_id');
        $chat_room_id = $this->input->post('chat_room_id');
        $data['chat_room'] = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data["chat_room"]->b_user_ids = json_decode($data["chat_room"]->b_user_ids);
        if(!in_array($pelanggan->id, $data["chat_room"]->b_user_ids)){
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $data["chat_room"]->images = array();
        $data["chat_room"]->custom_name_3 = "";
        $data["chat_room"]->id_lawan_bicara = "0";
        if($data["chat_room"]->type == "private"){
            if(count($data["chat_room"]->b_user_ids) > 1){
                if($data["chat_room"]->b_user_ids[1] == $pelanggan->id){
                    $data["chat_room"]->id_lawan_bicara = $data["chat_room"]->b_user_ids[0];
                }else{
                    $data["chat_room"]->id_lawan_bicara = $data["chat_room"]->b_user_ids[1];
                }
                $lawan_bicara_data = $this->bu->getById($nation_code, $data["chat_room"]->id_lawan_bicara);
                $data["chat_room"]->custom_name_1 = $lawan_bicara_data->band_fnama;
                if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                    $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                }else{
                    $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
            }else{
                if($pelanggan->language_id == 2){
                    $data["chat_room"]->custom_name_1 = "Tidak ada member";
                }else{
                    $data["chat_room"]->custom_name_1 = "No member";
                }
                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
            }
            $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
        }else if($data["chat_room"]->type == "group"){
            if($data["chat_room"]->is_edited == "0"){
                if($data["chat_room"]->is_main_group_chat_room == "1"){
                    $data["chat_room"]->images[] = $this->cdn_url($data["chat_room"]->band_group_image);
                }else{
                    $memberList = $data["chat_room"]->b_user_ids;
                    if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                      unset($memberList[$key]);
                    }
                    $memberList = array_values($memberList);
                    $fourUserids = array_chunk($memberList, 4);
                    if($fourUserids){
                        $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                        foreach($arrayUserData AS $users){
                            $data["chat_room"]->custom_name_1 .= $users->band_fnama.", ";
                            if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                            }else{
                                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                            } 
                        }
                    }
                    $data["chat_room"]->custom_name_1 = substr($data["chat_room"]->custom_name_1, 0, -2);
                    $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
                }
            }else{
                $data["chat_room"]->images[] = str_replace("//", "/", $this->cdn_url($data["chat_room"]->image));
                $data["chat_room"]->custom_name_3 = $data["chat_room"]->band_group_name;
            }
        }
        $data["chat_room"]->custom_name_1 = html_entity_decode($data["chat_room"]->custom_name_1,ENT_QUOTES);
        $data["chat_room"]->custom_name_3 = html_entity_decode($data["chat_room"]->custom_name_3,ENT_QUOTES);
        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $data["chat_room"]->id, $pelanggan->id);
        if(strtotime($oneParticipantData->last_delete_chat) > strtotime($data["chat_room"]->last_chat_cdate)){
            $data["chat_room"]->custom_name_2 = "";
            if($data["chat_room"]->is_main_group_chat_room == "1"){
                $data["chat_room"]->custom_name_2 = "Default club chat room with all Club members";
                $data["chat_room"]->last_chat_message = "Default club chat room with all Club members";
            }
            $data["chat_room"]->cdate_text = $this->humanTiming($data["chat_room"]->cdate, null, $pelanggan->language_id);
            $data["chat_room"]->last_chat_cdate = $this->customTimezone($data["chat_room"]->cdate, $timezone);
        }else{
            $data["chat_room"]->custom_name_2 = $data["chat_room"]->last_chat_message != "" ? $data["chat_room"]->last_chat_b_user_fnama.": ".$data["chat_room"]->last_chat_message : "";
            $data["chat_room"]->cdate_text = $this->humanTiming($data["chat_room"]->last_chat_cdate, null, $pelanggan->language_id);
            $data["chat_room"]->last_chat_cdate = $this->customTimezone($data["chat_room"]->last_chat_cdate, $timezone);
        }

        // if($pelanggan->id != $data['chat_room']->b_user_id_starter){
        //     if($data['chat_room']->chat_type == 'community'){
        //         $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $data['chat_room']->c_community_id);
        //         $blockDataAccount = $this->cbm->getById($nation_code, 0, $data['chat_room']->b_user_id_starter, "account", $pelanggan->id);
        //         $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $data['chat_room']->b_user_id_starter);
        //         if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        //             $this->status = 1005;
        //             $this->message = "You can no longer chat as you're blocked";
        //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
        //             die();
        //         }
        //     }
        // }

        // if($data['chat_room']->chat_type != 'community' && $data['chat_room']->chat_type != 'admin'){
        //     $blockDataAccount = $this->cbm->getById($nation_code, 0, $id_lawan_bicara, "account", $pelanggan->id);
        //     $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $id_lawan_bicara);
        //     if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        //         if($data['chat_room']->chat_type == 'offer'){
        //             $this->status = 1005;
        //             $this->message = "An offer is not allowed as you're blocked";
        //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_offer");
        //             die();
        //         }
        //         $this->status = 1005;
        //         $this->message = "You can no longer chat as you're blocked";
        //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
        //         die();
        //     }
        // }

        // $data['chat_total'] = $this->icreadm->countAllByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id);
        $data['chats'] = $this->icm->getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id, $pelanggan->language_id);

        $chat_ids = array();
        foreach($data['chats'] AS $chat){
            $chat_ids[] = $chat->id;
        }
        unset($chat);

        //get attachment 
        $att = array();
        if($chat_ids){
            $attachments = $this->icam->getDetailByChatRoomID($nation_code, $chat_room_id, $chat_ids);
            foreach ($attachments as $atc) {
                $key = $nation_code.'-'.$chat_room_id.'-'.$atc->i_chat_id;

                $temp = new stdClass();
                if($atc->jenis == 'image'){
                    if (empty($atc->url)) {
                      $atc->url = 'media/community_default.png';
                    }
                    if (empty($atc->url_thumb)) {
                      $atc->url_thumb = 'media/community_default.png';
                    }
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->url_thumb = $this->cdn_url($atc->url_thumb);
                }else if($atc->jenis == 'video'){
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->url_thumb = $this->cdn_url($atc->url_thumb);
                }else if($atc->jenis == 'location'){
                    $temp->location_nama = $atc->location_nama;
                    $temp->location_address = $atc->location_address;
                    $temp->location_place_id = $atc->location_place_id;
                    $temp->location_latitude = $atc->location_latitude;
                    $temp->location_longitude = $atc->location_longitude;
                }else if($atc->jenis == 'file'){
                    $temp->url = $this->cdn_url($atc->url);
                    $temp->file_name = $atc->file_name;
                    $temp->file_size = $atc->file_size;
                }

                //put to array key
                if (!isset($att[$key])) {
                    $att[$key] = array();
                }
                $att[$key][] = $temp;
            }
            unset($atc); //free some memory
            unset($attachments); //free some memory
        }

        //chat iteration
        foreach ($data['chats'] as &$chat) {
            $chat->message = html_entity_decode($chat->message,ENT_QUOTES);
            if (isset($chat->b_user_band_image)) {
                if(file_exists(SENEROOT.$chat->b_user_band_image) && $chat->b_user_band_image != 'media/user/default.png'){
                    $chat->b_user_band_image = $this->cdn_url($chat->b_user_band_image);
                } else {
                    $chat->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $chat->cdate_text = $this->humanTiming($chat->cdate, null, $pelanggan->language_id);

            if($pelanggan->language_id == 2) {
                $chat->cdate_text_2 = $this->__dateIndonesia($chat->cdate);
            }else{
                $chat->cdate_text_2 = $this->__dateEnglish($chat->cdate);
            }

            $chat->cdate = $this->customTimezone($chat->cdate, $timezone);
            $chat->attachments = array();
            $key = $nation_code.'-'.$chat_room_id.'-'.$chat->id;
            if (isset($att[$key])) {
                $chat->attachments = $att[$key];
            }

            $chat->is_read_lawan_bicara = $this->icreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $chat->id, $pelanggan->id);
        }

        $this->icpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);
        $this->icreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id, $chat_ids);
        $data['chat_id_havent_read_lawan_bicara'] = $this->icreadm->GetUnReadByLawanBicara($nation_code, $chat_room_id, $pelanggan->id);

        //render
        $this->status = 200;
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    // public function checkparticipantstatus()
    // {
    //     //default result
    //     $data = array();
    //     $data['chat_room_id'] = $this->input->get('chat_room_id');
    //     $data['chat_room'] = new stdClass();
    //     $data['participant_list'] = array();
    //     $data['is_leave'] = '0';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //cek chat_room_id
    //     $chat_room_id = $this->input->get('chat_room_id');
    //     $data['chat_room_id'] = $chat_room_id;
    //     $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
    //     if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }
        
    //     $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

    //     //check already in group chat or not
    //     if($data['chat_room']->c_community_id > '0' && $data['chat_room']->chat_type == 'community'){

    //         $alreadyInGroupChat = 0;
    //         foreach($data['participant_list'] as $participantList){
                
    //             if($participantList->b_user_id == $pelanggan->id){
    //                 $alreadyInGroupChat = 1;
    //                 break;
    //             }

    //         }
    //         unset($participantList);

    //         if($alreadyInGroupChat == 0){
    //             $data['is_leave'] = '1';
    //         }

    //     }
        
    //     unset($data['chat_room_id'], $data['chat_room'], $data['participant_list']);

    //     //render
    //     $this->status = 200;
    //     // $this->message = 'You can no longer chat as you exited this chat group';
    //     $this->message = 'Success';
    //     //render
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    public function buat()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_id'] = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $group_id = $this->input->post('group_id');
        if (!$group_id) {
            $group_id = 0;
        }
        $groupData = $this->igm->getById($nation_code, $group_id);
        if(!isset($groupData->id)){
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();  
        }

        $memberList = $this->input->post('memberList');
        if (!is_array($memberList)) {
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $this->icrm->trans_start();

        if(count($memberList) == 1){
            $checkRoomChat = $this->icrm->getRoomChatIDByParticipantId($nation_code, $group_id, $pelanggan->id, $memberList[0], "private");
            if (isset($checkRoomChat->id)) {
                $data['chat_room_id'] = $checkRoomChat->id;
            }else{
                //insert room chat
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_group_id'] = $group_id;
                $di['b_user_id_creator'] = $pelanggan->id;
                $di['b_user_ids'] = json_encode(array($pelanggan->id, $memberList[0]));
                $di['total_people_chat_room'] = 2;
                $di['type'] = "private";
                $di['custom_name_1'] = '';
                $di['custom_name_2'] = '';
                $di['cdate'] = 'NOW()';
                $endDoWhile = 0;
                do{
                    $chat_room_id = $this->GUIDv4();
                    $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
                    if($checkId == 0){
                        $endDoWhile = 1;
                    }
                }while($endDoWhile == 0);
                $di['id'] = $chat_room_id;
                $this->icrm->set($di);

                //insert chat participant 1
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $pelanggan->id;
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_read'] = "1";
                $di['is_creator'] = "1";
                $this->icpm->set($di);

                //insert chat participant 2
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $memberList[0];
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_read'] = "1";
                $di['is_first_time_join'] = "1";
                $this->icpm->set($di);

                $data['chat_room_id'] = $chat_room_id;
            }
        }

        if(count($memberList) > 1){
            $memberList = array_merge(array($pelanggan->id), $memberList);

            //insert room chat
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['i_group_id'] = $group_id;
            $di['b_user_id_creator'] = $pelanggan->id;
            $di['b_user_ids'] = json_encode($memberList);
            $di['total_people_chat_room'] = count($memberList);
            $di['type'] = "group";
            $di['custom_name_1'] = '';
            $di['custom_name_2'] = '';
            $di['cdate'] = 'NOW()';
            $endDoWhile = 0;
            do{
                $chat_room_id = $this->GUIDv4();
                $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $di['id'] = $chat_room_id;
            $this->icrm->set($di);
            $data['chat_room_id'] = $chat_room_id;

            $nameList = "";
            foreach($memberList AS $member){
                //insert chat participant
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $member;
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_read'] = "1";
                $oneParticipantData = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $member);
                if(!isset($oneParticipantData->b_user_id)){
                    $this->icrm->trans_rollback();
                    $this->icrm->trans_end();
                    $this->status = 1109;
                    $this->message = 'There is user not the member of this club';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
                    die();
                }
                if(isset($oneParticipantData->b_user_id)){
                    if($oneParticipantData->is_owner == "1"){
                        $di['is_owner'] = "1";
                    }
                    if($oneParticipantData->is_co_admin == "1"){
                        $di['is_co_admin'] = "1";
                    }
                }
                if($member == $pelanggan->id){
                    $di['is_creator'] = "1";
                }else{
                    $di['is_first_time_join'] = "1";
                }
                $this->icpm->set($di);

                if($member != $pelanggan->id){
                    $nameList .= $oneParticipantData->b_user_band_fnama.", ";
                }
            }

            $nameList = substr($nameList, 0, -2);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['i_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = "0";
            $di['type'] = 'announcement';
            $di['message'] = $pelanggan->band_fnama." has invited ".$nameList.".";
            $di['message_indonesia'] = $pelanggan->band_fnama." telah mengundang ".$nameList.".";
            $di['cdate'] = "NOW()";
            $endDoWhile = 0;
            do{
                $chat_id = $this->GUIDv4();
                $checkId = $this->icm->checkId($nation_code, $chat_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $di['id'] = $chat_id;
            $this->icm->set($di);
        }

        $this->icrm->trans_commit();
        $this->icrm->trans_end();
        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    // public function getchatroomid()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['chat_room_id'] = "0";

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     $group_id = $this->input->post('group_id');
    //     if (!$group_id) {
    //         $group_id = 0;
    //     }
    //     $groupData = $this->igm->getById($nation_code, $group_id);
    //     if(!isset($groupData->id)){
    //         $this->status = 300;
    //         $this->message = 'Missing one or more parameters';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();  
    //     }

    //     // $type = $this->input->post('type');
    //     // if (empty($type)) {
    //     //     $type = "private";
    //     // }

    //     $b_user_id_to = $this->input->post('b_user_id_to');
    //     if (!$b_user_id_to) {
    //         $b_user_id_to = 0;
    //     }

    //     if($type == "private" && $b_user_id_to != "0"){
    //         $checkRoomChat = $this->icrm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $b_user_id_to, $type);
    //         if (isset($checkRoomChat->id)) {
    //             $data['chat_room_id'] = $checkRoomChat->id;
    //         }else{
    //             //insert room chat
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['b_user_ids'] = json_encode(array($pelanggan->id, $b_user_id_to));
    //             $di['total_people_chat_room'] = 2;
    //             $di['type'] = $type;
    //             $di['custom_name_1'] = '';
    //             $di['custom_name_2'] = '';
    //             $di['cdate'] = 'NOW()';
    //             $endDoWhile = 0;
    //             do{
    //                 $chat_room_id = $this->GUIDv4();
    //                 $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
    //                 if($checkId == 0){
    //                     $endDoWhile = 1;
    //                 }
    //             }while($endDoWhile == 0);
    //             $di['id'] = $chat_room_id;
    //             $this->icrm->set($di);

    //             //insert chat participant 1
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['i_chat_room_id'] = $chat_room_id;
    //             $di['b_user_id'] = $pelanggan->id;
    //             $di['cdate'] = 'NOW()';
    //             $di['last_delete_chat'] = date('Y-m-d H:i:s');
    //             $di['is_read'] = 1;
    //             $this->icpm->set($di);

    //             //insert chat participant 2
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['i_chat_room_id'] = $chat_room_id;
    //             $di['b_user_id'] = $b_user_id_to;
    //             $di['cdate'] = 'NOW()';
    //             $di['last_delete_chat'] = date('Y-m-d H:i:s');
    //             $di['is_read'] = 1;
    //             $this->icpm->set($di);

    //             $data['chat_room_id'] = $chat_room_id;
    //         }
    //     }

    //     $this->status = 200;
    //     $this->message = 'Success';

    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    // }

    public function member_list()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['total'] = "0";
        $data['list'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $type = trim($this->input->post('type'));
        if(!in_array($type, array("chat_member", "not chat_member"))){
            $type = "chat_member";
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = "0";
        }
        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        // $sort_col = $this->input->post("sort_col");
        $sort_col = "band_fnama";
        // $sort_dir = $this->input->post("sort_dir");
        $sort_dir = "ASC";
        $page = $this->input->post("page");
        $page_size = $this->input->post("page_size");
        $keyword = trim($this->input->post("keyword"));
        // $tbl_as = $this->igparticipantm->getTblAs();
        // $sort_col = $this->__sortCol($sort_col, $tbl_as);
        // $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //keyword
        if (mb_strlen($keyword)>1) {
          //$keyword = utf8_encode(trim($keyword));
          $enc = mb_detect_encoding($keyword, 'UTF-8');
          if ($enc == 'UTF-8') {
          } else {
            $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
          }
        } else {
          $keyword="";
        }
        $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
        $keyword = substr($keyword, 0, 32);

        if($type == "chat_member"){
            $data['total'] = $this->icpm->countAllByChatroomid($nation_code, $keyword, $chatRoom->id);
            $data['list'] = $this->icpm->getAllByChatroomid($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $chatRoom->id);
        }else if($type == "not chat_member"){
            $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
            $data['total'] = $this->igparticipantm->countByGroupIdUseridsNotin($nation_code, $keyword, $chatRoom->i_group_id, $chatRoom->b_user_ids);
            $data['list'] = $this->igparticipantm->getByGroupIdUseridsNotin($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $chatRoom->i_group_id, $chatRoom->b_user_ids);
        }

        foreach($data['list'] AS &$list){
            if (isset($list->b_user_band_image)) {
                if(file_exists(SENEROOT.$list->b_user_band_image) && $list->b_user_band_image != 'media/user/default.png'){
                    $list->b_user_band_image = $this->cdn_url($list->b_user_band_image);
                } else {
                    $list->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            if($list->is_owner == "1" && $list->is_co_admin == "0") {
                $list->status = "Owner";
            } else if($list->is_owner == "0" && $list->is_co_admin == "1") {
                $list->status = "Admin";
            } else if($list->is_owner == "0" && $list->is_co_admin == "0") {
                $list->status = "Member";
            }
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function add_member()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_id'] = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = "0";
        }
        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $memberList = $this->input->post('memberList');
        if (!is_array($memberList)) {
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        foreach($memberList AS $member){
            if(in_array($member, $chatRoom->b_user_ids)){
                $this->status = 1108;
                $this->message = 'There is user already in chat';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
                die();
            }

            $checkStillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $chatRoom->i_group_id, $member);
            if(!isset($checkStillParticipant->b_user_id)){
                $this->status = 1109;
                $this->message = 'There is user not the member of this club';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
                die();
            }
        }

        $this->icrm->trans_start();

        if(count($memberList) == 1 && count($chatRoom->b_user_ids) == 1 && $chatRoom->type == "private"){
            $checkRoomChat = $this->icrm->getRoomChatIDByParticipantId($nation_code, $chatRoom->i_group_id, $pelanggan->id, $memberList[0], "private");
            if (isset($checkRoomChat->id)) {
                $data['chat_room_id'] = $checkRoomChat->id;
            }else{
                // //insert room chat
                // $di = array();
                // $di['nation_code'] = $nation_code;
                // $di['i_group_id'] = $chatRoom->i_group_id;
                // $di['b_user_id_creator'] = $pelanggan->id;
                // $di['b_user_ids'] = json_encode(array($pelanggan->id, $memberList[0]));
                // $di['total_people_chat_room'] = 2;
                // $di['type'] = "private";
                // $di['custom_name_1'] = '';
                // $di['custom_name_2'] = '';
                // $di['cdate'] = 'NOW()';
                // $endDoWhile = 0;
                // do{
                //     $chat_room_id = $this->GUIDv4();
                //     $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
                //     if($checkId == 0){
                //         $endDoWhile = 1;
                //     }
                // }while($endDoWhile == 0);
                // $di['id'] = $chat_room_id;
                // $this->icrm->set($di);

                $data['chat_room_id'] = $chat_room_id;

                // //insert chat participant 1
                // $di = array();
                // $di['nation_code'] = $nation_code;
                // $di['i_chat_room_id'] = $chat_room_id;
                // $di['b_user_id'] = $pelanggan->id;
                // $di['cdate'] = 'NOW()';
                // $di['last_delete_chat'] = date('Y-m-d H:i:s');
                // $di['is_read'] = "1";
                // $di['is_creator'] = "1";
                // $this->icpm->set($di);

                //insert chat participant 2
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $memberList[0];
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_first_time_join'] = "1";
                $di['is_read'] = "1";
                $this->icpm->set($di);

                $chatRoom->b_user_ids[] = $memberList[0];
                $du = array();
                $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
                $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
                $this->icrm->update($nation_code, $chat_room_id, $du);

                $oneParticipantData = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $chatRoom->i_group_id, $memberList[0]);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = "0";
                $di['type'] = 'announcement';
                $di['message'] = $pelanggan->band_fnama." has invited ".$oneParticipantData->b_user_band_fnama.".";
                $di['message_indonesia'] = $pelanggan->band_fnama." telah mengundang ".$oneParticipantData->b_user_band_fnama.".";
                $di['cdate'] = "NOW()";
                $endDoWhile = 0;
                do{
                    $chat_id = $this->GUIDv4();
                    $checkId = $this->icm->checkId($nation_code, $chat_id);
                    if($checkId == 0){
                        $endDoWhile = 1;
                    }
                }while($endDoWhile == 0);
                $di['id'] = $chat_id;
                $this->icm->set($di);
            }
        }else if(count($memberList) >= 1 && count($chatRoom->b_user_ids) >= 1 && $chatRoom->type == "private"){
            $memberList = array_merge($chatRoom->b_user_ids, $memberList);

            //insert room chat
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['i_group_id'] = $chatRoom->i_group_id;
            $di['b_user_id_creator'] = $pelanggan->id;
            $di['b_user_ids'] = json_encode($memberList);
            $di['total_people_chat_room'] = count($memberList);
            $di['type'] = "group";
            $di['custom_name_1'] = '';
            $di['custom_name_2'] = '';
            $di['cdate'] = 'NOW()';
            $endDoWhile = 0;
            do{
                $chat_room_id = $this->GUIDv4();
                $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $di['id'] = $chat_room_id;
            $this->icrm->set($di);
            $data['chat_room_id'] = $chat_room_id;

            $nameList = "";
            foreach($memberList AS $member){
                //insert chat participant
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $member;
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_read'] = "1";
                $oneParticipantData = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $chatRoom->i_group_id, $member);
                if(!isset($oneParticipantData->b_user_id)){
                    $this->icrm->trans_rollback();
                    $this->icrm->trans_end();
                    $this->status = 1109;
                    $this->message = 'There is user not the member of this club';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
                    die();
                }
                if($oneParticipantData->is_owner == "1"){
                    $di['is_owner'] = "1";
                }
                if($oneParticipantData->is_co_admin == "1"){
                    $di['is_co_admin'] = "1";
                }

                if($member == $pelanggan->id){
                    $di['is_creator'] = "1";
                }else{
                    $di['is_first_time_join'] = "1";
                }
                $this->icpm->set($di);

                if($member != $pelanggan->id){
                    $nameList .= $oneParticipantData->b_user_band_fnama.", ";
                }
            }

            $nameList = substr($nameList, 0, -2);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['i_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = "0";
            $di['type'] = 'announcement';
            $di['message'] = $pelanggan->band_fnama." has invited ".$nameList.".";
            $di['message_indonesia'] = $pelanggan->band_fnama." telah mengundang ".$nameList.".";
            $di['cdate'] = "NOW()";
            $endDoWhile = 0;
            do{
                $chat_id = $this->GUIDv4();
                $checkId = $this->icm->checkId($nation_code, $chat_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $di['id'] = $chat_id;
            $this->icm->set($di);
        }

        if($chatRoom->type == "group"){
            $data['chat_room_id'] = $chat_room_id;

            $nameList = "";
            foreach($memberList AS $member){
                //insert chat participant
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['i_chat_room_id'] = $chat_room_id;
                $di['b_user_id'] = $member;
                $di['cdate'] = 'NOW()';
                $di['last_delete_chat'] = date('Y-m-d H:i:s');
                $di['is_read'] = "1";
                $oneParticipantData = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $chatRoom->i_group_id, $member);
                if(!isset($oneParticipantData->b_user_id)){
                    $this->icrm->trans_rollback();
                    $this->icrm->trans_end();
                    $this->status = 1109;
                    $this->message = 'There is user not the member of this club';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
                    die();
                }
                if($oneParticipantData->is_owner == "1"){
                    $di['is_owner'] = "1";
                }
                if($oneParticipantData->is_co_admin == "1"){
                    $di['is_co_admin'] = "1";
                }

                if($member == $chatRoom->b_user_id_creator){
                    $di['is_creator'] = "1";
                }else{
                    $di['is_first_time_join'] = "1";
                }
                $this->icpm->set($di);

                if($member != $pelanggan->id){
                    $nameList .= $oneParticipantData->b_user_band_fnama.", ";
                }
            }

            $chatRoom->b_user_ids = array_merge($chatRoom->b_user_ids, $memberList);
            $du = array();
            $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
            $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
            $this->icrm->update($nation_code, $chat_room_id, $du);

            $nameList = substr($nameList, 0, -2);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['i_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = "0";
            $di['type'] = 'announcement';
            $di['message'] = $pelanggan->band_fnama." has invited ".$nameList.".";
            $di['message_indonesia'] = $pelanggan->band_fnama." telah mengundang ".$nameList.".";
            $di['cdate'] = "NOW()";
            $endDoWhile = 0;
            do{
                $chat_id = $this->GUIDv4();
                $checkId = $this->icm->checkId($nation_code, $chat_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $di['id'] = $chat_id;
            $this->icm->set($di);
        }

        $this->icrm->trans_commit();
        $this->icrm->trans_end();

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function leave(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_unread'] = "0";
        // $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = "0";
        }
        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $this->icrm->trans_start();

        $this->icpm->del($nation_code, $chatRoom->id, $pelanggan->id);

        if (($key = array_search($pelanggan->id, $chatRoom->b_user_ids)) !== false) {
          unset($chatRoom->b_user_ids[$key]);
        }
        $chatRoom->b_user_ids = array_values($chatRoom->b_user_ids);
        $du = array();
        $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
        $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
        if($du['total_people_chat_room'] == 0){
            $du['is_active'] = 0;
        }
        $this->icrm->update($nation_code, $chatRoom->id, $du);

        // $type = 'chat';
        // $replacer = array();
        // $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = "0";
        $di['type'] = 'announcement';
        $di['message'] = $pelanggan->band_fnama." has left the chat.";
        $di['message_indonesia'] = $pelanggan->band_fnama." telah meninggalkan chat.";
        $di['cdate'] = "NOW()";
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);

        $this->icrm->trans_commit();
        $this->icrm->trans_end();

        // $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
        
        // //set unread in table e_chat_read
        // $insertArray = array();
        // foreach($participant_list AS $participant){
            
        //     $du = array();
        //     $du['nation_code'] = $nation_code;
        //     $du['b_user_id'] = $participant->b_user_id;
        //     $du['e_chat_room_id'] = $roomChat->id;
        //     $du['e_chat_id'] = $chat_id;
        //     if($participant->b_user_id == $pelanggan->id){
        //         $du['is_read'] = 1;
        //     }else{
        //         $du['is_read'] = 0;
        //     }
        //     $du['cdate'] = "NOW()";
        //     $insertArray[] = $du;

        // }
        // unset($participant_list, $participant);

        // $chunkInsertArray = array_chunk($insertArray,50);
        // foreach($chunkInsertArray AS $chunk){
        //     //insert multi
        //     $this->ecreadm->setMass($chunk);
        // }
        // unset($insertArray, $chunkInsertArray, $chunk);

        // //send push notif
        // $ownerCommunity = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
        // $sender = $this->bu->getById($nation_code, $pelanggan->id);
        // $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
        // $ios = array();
        // $android = array();

        // foreach($participant_list as $participant){

        //     if($participant->b_user_id != $pelanggan->id){
        //         $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

        //         $classified = 'setting_notification_user';
        //         $code = 'U4';

        //         $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

        //         if (!isset($receiverSettingNotif->setting_value)) {
        //             $receiverSettingNotif->setting_value = 0;
        //         }

        //         //push notif
        //         if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
        //             if (strtolower($receiver->device) == 'ios') {
        //               $ios[] = $receiver->fcm_token;
        //             } else {
        //               $android[] = $receiver->fcm_token;
        //             }

        //         }
        //     }

        // }

        // $type = 'chat';
        // $anotid = 3;
        // $replacer = array();
        // $replacer['user_nama'] = html_entity_decode($sender->fnama,ENT_QUOTES);
        // if($sender->language_id == 2) {
        //     $title = 'Obrolan Baru';
        //     $message = "$sender->fnama telah meninggalkan obrolan grup";
        // } else {
        //     $title = 'New Chat';
        //     $message = "$sender->fnama has left the group chat";
        // }
        
        // $image = 'media/pemberitahuan/chat.png';

        // if (array_unique($ios)) {
        //     $device = "ios"; //jenis device
        //     $tokens = $ios; //device token
        //     $payload = new stdClass();
        //     $payload->chat_room_id = (string) $chat_room_id;
        //     $payload->user_id = $sender->id;
        //     $payload->user_fnama = $sender->fnama;

        //     // by Muhammad Sofi - 27 October 2021 10:12
        //     // if user img & banner not exist or empty, change to default image
        //     // $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
        //         $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     } else {
        //         $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }
        //     $payload->chat_type = $roomChat->chat_type;
        //     $payload->custom_name_1 = $roomChat->custom_name_1;
        //     $payload->custom_name_2 = $roomChat->custom_name_2;
        //     // $payload->custom_image = $roomChat->custom_image;


        //     $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
        //     if (isset($nw->message)) {
        //         $message = $this->__nRep($nw->message, $replacer);
        //     }
        //     if (isset($nw->image)) {
        //         $image = $nw->image;
        //     }
        //     $image = $this->cdn_url($image);
        //     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        // }

        // if (array_unique($android)) {
        //     $device = "android"; //jenis device
        //     $tokens = $android; //device token
        //     $payload = new stdClass();
        //     $payload->chat_room_id = (string) $chat_room_id;
        //     $payload->user_id = $sender->id;
        //     $payload->user_fnama = $sender->fnama;
            
        //     // by Muhammad Sofi - 27 October 2021 10:12
        //     // if user img & banner not exist or empty, change to default image
        //     // $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
        //         $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     } else {
        //         $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }
        //     $payload->chat_type = $roomChat->chat_type;
        //     $payload->custom_name_1 = $roomChat->custom_name_1;
        //     $payload->custom_name_2 = $roomChat->custom_name_2;
        //     // $payload->custom_image = $roomChat->custom_image;

        //     $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
        //     if (isset($nw->message)) {
        //         $message = $this->__nRep($nw->message, $replacer);
        //     }
        //     if (isset($nw->image)) {
        //         $image = $nw->image;
        //     }
        //     $image = $this->cdn_url($image);
        //     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        // }

        // $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
        // $res = $this->seme_curl->get($url);
        
        // $body = json_decode($res->body);
        // $chat_room = $body->data;

        $this->status = 200;
        $this->message = 'Success';

        // $data['chat_room'] = $chat_room;
        // unset($chat_room);
        // //get unread count
        // $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function kick(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_unread'] = "0";
        // $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = "0";
        }
        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $chatRoom->id, $pelanggan->id);
        if(!isset($oneParticipantData->last_delete_chat)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($oneParticipantData->is_owner == "0" && $oneParticipantData->is_creator == "0"){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $b_user_id = trim($this->input->post('b_user_id'));
        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $chatRoom->id, $b_user_id);
        if(!isset($oneParticipantData->last_delete_chat)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($pelanggan->id == $b_user_id){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $this->icrm->trans_start();

        $this->icpm->del($nation_code, $chatRoom->id, $b_user_id);

        if (($key = array_search($b_user_id, $chatRoom->b_user_ids)) !== false) {
          unset($chatRoom->b_user_ids[$key]);
        }
        $chatRoom->b_user_ids = array_values($chatRoom->b_user_ids);
        $du = array();
        $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
        $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
        if($du['total_people_chat_room'] == 0){
            $du['is_active'] = 0;
        }
        $this->icrm->update($nation_code, $chatRoom->id, $du);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = "0";
        $di['type'] = 'announcement';
        $di['message'] = $pelanggan->band_fnama." removed ".$oneParticipantData->b_user_band_fnama." the chat room.";
        $di['message_indonesia'] = $pelanggan->band_fnama." telah mengeluarkan ".$oneParticipantData->b_user_band_fnama." dari chat room.";
        $di['cdate'] = "NOW()";
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);

        $this->icrm->trans_commit();
        $this->icrm->trans_end();

        // $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
        
        // //set unread in table e_chat_read
        // $insertArray = array();
        // foreach($participant_list AS $participant){
            
        //     $du = array();
        //     $du['nation_code'] = $nation_code;
        //     $du['b_user_id'] = $participant->b_user_id;
        //     $du['e_chat_room_id'] = $roomChat->id;
        //     $du['e_chat_id'] = $chat_id;
        //     if($participant->b_user_id == $pelanggan->id){
        //         $du['is_read'] = 1;
        //     }else{
        //         $du['is_read'] = 0;
        //     }
        //     $du['cdate'] = "NOW()";
        //     $insertArray[] = $du;

        // }
        // unset($participant_list, $participant);

        // $chunkInsertArray = array_chunk($insertArray,50);
        // foreach($chunkInsertArray AS $chunk){
        //     //insert multi
        //     $this->ecreadm->setMass($chunk);
        // }
        // unset($insertArray, $chunkInsertArray, $chunk);

        // //send push notif
        // $ownerCommunity = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
        // $sender = $this->bu->getById($nation_code, $pelanggan->id);
        // $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
        // $ios = array();
        // $android = array();

        // foreach($participant_list as $participant){

        //     if($participant->b_user_id != $pelanggan->id){
        //         $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

        //         $classified = 'setting_notification_user';
        //         $code = 'U4';

        //         $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

        //         if (!isset($receiverSettingNotif->setting_value)) {
        //             $receiverSettingNotif->setting_value = 0;
        //         }

        //         //push notif
        //         if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
        //             if (strtolower($receiver->device) == 'ios') {
        //               $ios[] = $receiver->fcm_token;
        //             } else {
        //               $android[] = $receiver->fcm_token;
        //             }

        //         }
        //     }

        // }

        // $type = 'chat';
        // $anotid = 3;
        // $replacer = array();
        // $replacer['user_nama'] = html_entity_decode($sender->fnama,ENT_QUOTES);
        // if($sender->language_id == 2) {
        //     $title = 'Obrolan Baru';
        //     $message = "$sender->fnama telah meninggalkan obrolan grup";
        // } else {
        //     $title = 'New Chat';
        //     $message = "$sender->fnama has left the group chat";
        // }
        
        // $image = 'media/pemberitahuan/chat.png';

        // if (array_unique($ios)) {
        //     $device = "ios"; //jenis device
        //     $tokens = $ios; //device token
        //     $payload = new stdClass();
        //     $payload->chat_room_id = (string) $chat_room_id;
        //     $payload->user_id = $sender->id;
        //     $payload->user_fnama = $sender->fnama;

        //     // by Muhammad Sofi - 27 October 2021 10:12
        //     // if user img & banner not exist or empty, change to default image
        //     // $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
        //         $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     } else {
        //         $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }
        //     $payload->chat_type = $roomChat->chat_type;
        //     $payload->custom_name_1 = $roomChat->custom_name_1;
        //     $payload->custom_name_2 = $roomChat->custom_name_2;
        //     // $payload->custom_image = $roomChat->custom_image;


        //     $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
        //     if (isset($nw->message)) {
        //         $message = $this->__nRep($nw->message, $replacer);
        //     }
        //     if (isset($nw->image)) {
        //         $image = $nw->image;
        //     }
        //     $image = $this->cdn_url($image);
        //     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        // }

        // if (array_unique($android)) {
        //     $device = "android"; //jenis device
        //     $tokens = $android; //device token
        //     $payload = new stdClass();
        //     $payload->chat_room_id = (string) $chat_room_id;
        //     $payload->user_id = $sender->id;
        //     $payload->user_fnama = $sender->fnama;
            
        //     // by Muhammad Sofi - 27 October 2021 10:12
        //     // if user img & banner not exist or empty, change to default image
        //     // $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
        //         $payload->user_image = $this->cdn_url($ownerCommunity->image);
        //     } else {
        //         $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }
        //     $payload->chat_type = $roomChat->chat_type;
        //     $payload->custom_name_1 = $roomChat->custom_name_1;
        //     $payload->custom_name_2 = $roomChat->custom_name_2;
        //     // $payload->custom_image = $roomChat->custom_image;

        //     $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
        //     if (isset($nw->message)) {
        //         $message = $this->__nRep($nw->message, $replacer);
        //     }
        //     if (isset($nw->image)) {
        //         $image = $nw->image;
        //     }
        //     $image = $this->cdn_url($image);
        //     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        // }

        // $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
        // $res = $this->seme_curl->get($url);
        
        // $body = json_decode($res->body);
        // $chat_room = $body->data;

        $this->status = 200;
        $this->message = 'Success';

        // $data['chat_room'] = $chat_room;
        // unset($chat_room);
        // //get unread count
        // $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function send()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat'] = new stdClass();
        $data['chat_room_id'] = "0";
        $data['chat_id'] = "0";
        $data['file_attachment'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = 0;
        }

        $location_json = $this->input->post("location_json");

        // $b_user_id_to = $this->input->post('b_user_id_to');
        // if ($b_user_id_to <= '0') {
        //     $this->status = 300;
        //     $this->message = 'Missing one or more parameters';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        //     die();
        // }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        if($chat_room_id){
            $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
            if (!isset($chatRoom->id)) {
                $this->status = 7280;
                $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            }
        }else{
            // if($type == 'private'){
            //     //check already have room or not
            //     $checkRoomChat = $this->icrm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $b_user_id_to, $type);
            //     if (isset($checkRoomChat->id)) {
            //         $chat_room_id = $checkRoomChat->id;
            //     }else{
            //         //insert room chat
            //         $di = array();
            //         $di['nation_code'] = $nation_code;
            //         $di['b_user_ids'] = json_encode(array($pelanggan->id, $b_user_id_to));
            //         $di['total_people_chat_room'] = 2;
            //         $di['type'] = $type;
            //         $di['custom_name_1'] = '';
            //         $di['custom_name_2'] = '';
            //         $di['cdate'] = 'NOW()';
            //         $endDoWhile = 0;
            //         do{
            //             $chat_room_id = $this->GUIDv4();
            //             $checkId = $this->icrm->checkId($nation_code, $chat_room_id);
            //             if($checkId == 0){
            //                 $endDoWhile = 1;
            //             }
            //         }while($endDoWhile == 0);
            //         $di['id'] = $chat_room_id;
            //         $this->icrm->set($di);

            //         //insert chat participant 1
            //         $di = array();
            //         $di['nation_code'] = $nation_code;
            //         $di['i_chat_room_id'] = $chat_room_id;
            //         $di['b_user_id'] = $pelanggan->id;
            //         $di['cdate'] = 'NOW()';
            //         $di['last_delete_chat'] = date('Y-m-d H:i:s');
            //         $di['is_read'] = 1;
            //         $this->icpm->set($di);

            //         //insert chat participant 2
            //         $di = array();
            //         $di['nation_code'] = $nation_code;
            //         $di['i_chat_room_id'] = $chat_room_id;
            //         $di['b_user_id'] = $b_user_id_to;
            //         $di['cdate'] = 'NOW()';
            //         $di['last_delete_chat'] = date('Y-m-d H:i:s');
            //         $di['is_read'] = 1;
            //         $this->icpm->set($di);
            //     }
            //     $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
            // }else{
                $this->status = 7280;
                $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            // }
        }

        // $roomChatparticipant = $this->igparticipantm->getParticipantByRoomChatId($nation_code, $chat_room_id);

        // $checkStillParticipant = 0;
        // foreach($roomChatparticipant as $participant){

        //     if($participant->b_user_id == $pelanggan->id){
        //         $checkStillParticipant = 1;
        //         break;
        //     }

        // }
        // unset($participant);

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        // $chatRoom->id_lawan_bicara = "0";
        // if($chatRoom->type == "private"){
        //     if($chatRoom->b_user_ids[1] == $pelanggan->id){
        //         $chatRoom->id_lawan_bicara = $chatRoom->b_user_ids[0];
        //     }else{
        //         $chatRoom->id_lawan_bicara = $chatRoom->b_user_ids[1];
        //     }
        // }
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 8104;
            $this->message = 'You can no longer chat as you exited this chat group';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        // if($pelanggan->id != $chatRoom->b_user_id_starter){
        //     if($chatRoom->type == 'community'){
        //         $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $chatRoom->c_community_id);
        //         $blockDataAccount = $this->cbm->getById($nation_code, 0, $roomChat->b_user_id_starter, "account", $pelanggan->id);
        //         $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $chatRoom->b_user_id_starter);
        //         if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        //             $this->status = 1005;
        //             $this->message = "You can no longer chat as you're blocked";
        //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
        //             die();
        //         }
        //     }
        // }

        // if($chatRoom->type != 'community' && $chatRoom->type != 'admin'){

        //     $id_lawan_bicara = 0;
        //     foreach($roomChatparticipant as $participant){

        //         if($participant->b_user_id != $chatRoom->b_user_id_starter){
        //             $id_lawan_bicara = $participant->b_user_id;
        //             break;
        //         }

        //     }
        //     unset($participant);

        //     $blockDataAccount = $this->cbm->getById($nation_code, 0, $roomChat->b_user_id_starter, "account", $id_lawan_bicara);
        //     $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $id_lawan_bicara, "account", $chatRoom->b_user_id_starter);

        //     if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

        //         if($chatRoom->type == 'offer'){
        //             $this->status = 1005;
        //             $this->message = "An offer is not allowed as you're blocked";
        //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_offer");
        //             die();
        //         }

        //         $this->status = 1005;
        //         $this->message = "You can no longer chat as you're blocked";
        //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
        //         die();

        //     }

        //     if($pelanggan->id == $chatRoom->b_user_id_starter){
        //         $b_user_id_to = $id_lawan_bicara;
        //     }else{
        //         $b_user_id_to = $chatRoom->b_user_id_starter;
        //     }

        // }

        $message = trim(((empty($this->input->post('message')))? "" : $this->input->post('message')));
        if (!strlen($message) > 0) {
            if ($this->input->post('foto') == null && !is_array($location_json) && $this->input->post('video') == null && $this->input->post('file') == null) {
                $this->status = 8105;
                $this->message = 'Message is empty';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            }
        }
        $message = str_replace('’',"'",$message);
        $message = nl2br($message);
        $message = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $message);
        $message = str_replace("\\n", "<br />", $message);

        //start transaction
        $this->icm->trans_start();

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = $pelanggan->id;
        $di['message'] = $message;
        $di['message_indonesia'] = $message;
        $di['cdate'] = "NOW()";
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $res = $this->icm->set($di);
        if (!$res) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 1107;
            $this->message = 'Error, please try again later';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        $this->status = 200;
        $this->message = 'Success';

        $checkFileExist = 1;
        $checkFileTemporaryOrNot = 1;
        if($this->input->post('foto') != null){
            $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
            if (strpos($file_path, 'temporary') !== false) {
                if (!file_exists(SENEROOT.$file_path)) {
                    $checkFileExist = 0;
                }
            }else{
                $checkFileTemporaryOrNot = 0;
            }
        }

        if ($checkFileExist == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 995;
            $this->message = 'Failed upload, temporary already gone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if ($checkFileTemporaryOrNot == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 996;
            $this->message = 'Failed upload, upload is not temporary';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($this->input->post('foto') != null){
            $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
            $endDoWhile = 0;
            do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->icam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                $endDoWhile = 1;
                }
            }while($endDoWhile == 0);

            $sc = $this->__moveImagex($nation_code, $file_path, $this->media_group_chat_image, $chat_id, $attachmentId);
            if (isset($sc->status)) {
                if ($sc->status==200) {
                    $dix = array();
                    $dix['nation_code'] = $nation_code;
                    $dix['id'] = $attachmentId;
                    $dix['i_chat_room_id'] = $chatRoom->id;
                    $dix['i_chat_id'] = $chat_id;
                    $dix['jenis'] = 'image';
                    $dix['url'] = $sc->image;
                    $dix['url_thumb'] = $sc->thumb;
                    $dix['file_size'] = $sc->file_size;
                    $dix['file_size_thumb'] = $sc->file_size_thumb;
                    $this->icam->set($dix);
                }
            }
        }

        if(is_array($location_json)){
            if(count($location_json) > 0){
                foreach ($location_json as $key => $upload) {
                    if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
                        if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
                            $endDoWhile = 0;
                            do{
                                $attachmentId = $this->GUIDv4();
                                $checkId = $this->icam->checkId($nation_code, $attachmentId);
                                if($checkId == 0){
                                    $endDoWhile = 1;
                                }
                            }while($endDoWhile == 0);

                            $dix = array();
                            $dix['nation_code'] = $nation_code;
                            $dix['id'] = $attachmentId;
                            $dix['i_chat_room_id'] = $chatRoom->id;
                            $dix['i_chat_id'] = $chat_id;
                            $dix['jenis'] = 'location';
                            $dix['location_nama'] = $upload['location_nama'];
                            $dix['location_address'] = $upload['location_address'];
                            $dix['location_place_id'] = $upload['location_place_id'];
                            $dix['location_latitude'] = $upload['location_latitude'];
                            $dix['location_longitude'] = $upload['location_longitude'];
                            $this->icam->set($dix);
                        }
                    }
                }
            }
        }

        $checkFileExist = 1;
        $checkFileTemporaryOrNot = 1;
        if($this->input->post('video') != null){
            $file_path = parse_url($this->input->post('video'), PHP_URL_PATH);
            if (strpos($file_path, 'temporary') !== false) {
                if (!file_exists(SENEROOT.$file_path)) {
                    $checkFileExist = 0;
                }
            }else{
                $checkFileTemporaryOrNot = 0;
            }
        }

        if ($checkFileExist == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 995;
            $this->message = 'Failed upload, temporary already gone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if ($checkFileTemporaryOrNot == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 996;
            $this->message = 'Failed upload, upload is not temporary';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($this->input->post('video_thumb') != null){
            $file_path = parse_url($this->input->post('video_thumb'), PHP_URL_PATH);
            if (strpos($file_path, 'temporary') !== false) {
                if (!file_exists(SENEROOT.$file_path)) {
                    $checkFileExist = 0;
                }
            }else{
                $checkFileTemporaryOrNot = 0;
            }
        }

        if ($checkFileExist == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 995;
            $this->message = 'Failed upload, temporary already gone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if ($checkFileTemporaryOrNot == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 996;
            $this->message = 'Failed upload, upload is not temporary';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($this->input->post('video') != null){
            $file_path = parse_url($this->input->post('video'), PHP_URL_PATH);
            $file_path_thumb = parse_url($this->input->post('video_thumb'), PHP_URL_PATH);
            $endDoWhile = 0;
            do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->icam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);

            $moveVideo = $this->__moveVideox($nation_code, $file_path, $this->media_group_chat_video, $chat_id, $attachmentId);
            $sc = $this->__moveImagex($nation_code, $file_path_thumb, $this->media_group_chat_video, $chat_id, $attachmentId);
            if (isset($moveVideo->status) && isset($sc->status)) {
                if ($moveVideo->status==200 && $sc->status==200) {
                    $dix = array();
                    $dix['nation_code'] = $nation_code;
                    $dix['id'] = $attachmentId;
                    $dix['i_chat_room_id'] = $chatRoom->id;
                    $dix['i_chat_id'] = $chat_id;
                    $dix['jenis'] = 'video';
                    $dix['url'] = $moveVideo->url;
                    $dix['url_thumb'] = $sc->thumb;
                    $dix['file_size'] = $moveVideo->file_size;
                    $dix['file_size_thumb'] = $sc->file_size_thumb;
                    $this->icam->set($dix);
                }
            }
        }

        $checkFileExist = 1;
        $checkFileTemporaryOrNot = 1;
        if($this->input->post('file') != null){
            $file_path = parse_url($this->input->post('file'), PHP_URL_PATH);
            if (strpos($file_path, 'temporary') !== false) {
                if (!file_exists(SENEROOT.$file_path)) {
                    $checkFileExist = 0;
                }
            }else{
                $checkFileTemporaryOrNot = 0;
            }
        }

        if ($checkFileExist == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 995;
            $this->message = 'Failed upload, temporary already gone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if ($checkFileTemporaryOrNot == 0) {
            $this->icm->trans_rollback();
            $this->icm->trans_end();
            $this->status = 996;
            $this->message = 'Failed upload, upload is not temporary';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($this->input->post('file') != null){
            $file_path = parse_url($this->input->post('file'), PHP_URL_PATH);
            $endDoWhile = 0;
            do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->icam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);

            $scFile = $this->__moveFilex($nation_code, $file_path, $this->media_group_chat_file, $chat_id, $attachmentId);
            if (isset($scFile->status)) {
                if ($scFile->status==200) {
                    $dix = array();
                    $dix['nation_code'] = $nation_code;
                    $dix['id'] = $attachmentId;
                    $dix['i_chat_room_id'] = $chatRoom->id;
                    $dix['i_chat_id'] = $chat_id;
                    $dix['jenis'] = 'file';
                    $dix['url'] = $scFile->url;
                    $dix['file_name'] = substr(pathinfo($file_path, PATHINFO_BASENAME), 0, strpos(pathinfo($file_path, PATHINFO_BASENAME),"-")).".".pathinfo($file_path, PATHINFO_EXTENSION);
                    $dix['file_size'] = $scFile->file_size;
                    $this->icam->set($dix);
                }
            }
        }

        $insertArray = array();
        foreach($chatRoom->b_user_ids AS $b_user_id){
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['i_chat_room_id'] = $chatRoom->id;
            $du['i_chat_id'] = $chat_id;
            $du['b_user_id'] = $b_user_id;
            if($b_user_id == $pelanggan->id){
                $du['is_read'] = 1;
            }else{
                $du['is_read'] = 0;
            }
            $du['cdate'] = "NOW()";
            $insertArray[] = $du;
        }
        unset($b_user_id);

        $chunkInsertArray = array_chunk($insertArray,50);
        foreach($chunkInsertArray AS $chunk){
            //insert multi
            $this->icreadm->setMass($chunk);

        }
        unset($insertArray, $chunkInsertArray, $chunk);

        $du = array();
        $du['last_chat_b_user_fnama'] = $pelanggan->band_fnama;
        $du['last_chat_message'] = $message;
        if (!strlen($message) > 0) {
            if ($this->input->post('foto') != null) {
                $du['last_chat_message'] = "Photo";
            }
            if (is_array($location_json)) {
                $du['last_chat_message'] = "Location";
            }
            if ($this->input->post('video') != null) {
                $du['last_chat_message'] = "Video";
            }
            if ($this->input->post('file') != null) {
                $du['last_chat_message'] = "File";
            }
        }
        $du['last_chat_cdate'] = $di['cdate'];
        // $du['is_read_admin'] = 0;
        $this->icrm->update($nation_code, $chatRoom->id, $du);

        $du = array();
        $du['is_read'] = 0;
        $this->icpm->updateUnread($nation_code, $chatRoom->id, $pelanggan->id, $du);

        $du = array();
        $du['is_first_time_join'] = "0";
        $this->icpm->update($nation_code, $chatRoom->id, "0", $du);

        $this->icm->trans_commit();
        $this->icm->trans_end();

        // if ($type == "private") {
        //     $sender = $this->bu->getById($nation_code, $pelanggan->id);
        //     $receiver = $this->bu->getById($nation_code, $b_user_id_to);

        //     $classified = 'setting_notification_user';
        //     $code = 'U4';
        //     $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_to, $classified, $code);
        //     if (!isset($receiverSettingNotif->setting_value)) {
        //         $receiverSettingNotif->setting_value = 0;
        //     }

        //     //push notif
        //     if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
        //         if (strlen($receiver->fcm_token)>50) {
        //             $device = $receiver->device; //jenis device
        //             $tokens = array($receiver->fcm_token); //device token
        //             if($receiver->language_id == 2) {
        //                 $title = 'Obrolan Baru';
        //                 $message = "Anda memiliki pesan obrolan dari $sender->band_fnama";
        //             } else {
        //                 $title = 'New Chat';
        //                 $message = "You have chat messages from $sender->band_fnama";
        //             }
        //             $type = 'group_chat';
        //             $image = 'media/pemberitahuan/chat.png';
        //             $payload = new stdClass();
        //             $payload->chat_room_id = (string) $chatRoom->id;
        //             $image = $this->cdn_url($image);
        //             $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //         }
        //     }
        // }else if($type == 'group'){
            $sender = $this->bu->getById($nation_code, $pelanggan->id);
            foreach($chatRoom->b_user_ids AS $b_user_id){
                if($b_user_id != $pelanggan->id){
                    $receiver = $this->bu->getById($nation_code, $b_user_id);
                    if ($receiver->is_band_push_notif == "1" && $receiver->is_active == 1) {
                        $type = 'band_group_chat';
                        if($receiver->language_id == 2) {
                            $title = 'Obrolan Baru';
                            $message = "Anda memiliki pesan obrolan dari $sender->band_fnama";
                        } else {
                            $title = 'New Chat';
                            $message = "You have chat messages from $sender->band_fnama";
                        }

                        $image = 'media/pemberitahuan/chat.png';
                        $device = $receiver->device;
                        $tokens = array($receiver->fcm_token);
                        $payload = new stdClass();
                        $payload->i_chat_room_id = $chatRoom->id;
                        $image = $this->cdn_url($image);
                        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    }
                }
            }
        // }

        $data['chat_room_id'] = $chatRoom->id;
        $data['chat_id'] = $chat_id;
        if(isset($scFile->status)){
            if($scFile->status==200){
                $data['file_attachment']->url = $this->cdn_url($scFile->url);
                $data['file_attachment']->file_name = $dix['file_name'];
                $data['file_attachment']->file_size = $dix['file_size'];
            }
        }
        // $data['chat'] = $this->icm->getChatByChatIdChatRoomId($nation_code, $chat_id, $chatRoom->id, $pelanggan->language_id);

        // //get attachment 
        // $data['chat']->attachments = $this->icam->getDetailByChatRoomIDChatID($nation_code, $chatRoom->id, $data['chat']->chat_id);
        // if ($data['chat']->attachments) {
        //     foreach($data['chat']->attachments AS &$at){
        //         if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    
        //             $at->produk_thumb = $this->cdn_url($at->produk_thumb);
        //             // $at->produk_nama = $this->__dconv($at->produk_nama);
        //             $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);
        //         }else if($at->jenis == 'order'){
        //             $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
        //             $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);
        //             if (isset($produk->c_produk_id)) {
        //                 $at->order_invoice_code = $produk->invoice_code;
        //                 $at->order_thumb = $this->cdn_url($item->thumb);
        //                 $at->order_user_id_seller = $produk->b_user_id_seller;
        //                 $at->status_text = $this->__statusText($produk, $produk);
        //             }
        //         }else{
        //             $at->url = $this->cdn_url($at->url);
        //         }
        //     }
        // }

        // $data['chat']->cdate_text = $this->humanTiming($data['chat']->cdate, null, $pelanggan->language_id);
        // $data['chat']->cdate = $this->customTimezone($data['chat']->cdate, $timezone);
        // $data['chat']->message = html_entity_decode($data['chat']->message,ENT_QUOTES);
        // if (isset($data['chat']->b_user_image)) {
        //     if(file_exists(SENEROOT.$data['chat']->b_user_image) && $data['chat']->b_user_image != 'media/user/default.png'){
        //         $data['chat']->b_user_image = $this->cdn_url($data['chat']->b_user_image);
        //     } else {
        //         $data['chat']->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
        //     }
        // }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function chat_room_info()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room'] = new stdClass();
        // $data['chat_id'] = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = 0;
        }

        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chatRoom->images = array();
        $chatRoom->custom_name_3 = "";
        $chatRoom->id_lawan_bicara = "0";
        if($chatRoom->type == "private"){
            if(count($chatRoom->b_user_ids) > 1){
                if($chatRoom->b_user_ids[1] == $pelanggan->id){
                    $chatRoom->id_lawan_bicara = $chatRoom->b_user_ids[0];
                }else{
                    $chatRoom->id_lawan_bicara = $chatRoom->b_user_ids[1];
                }
                $lawan_bicara_data = $this->bu->getById($nation_code, $chatRoom->id_lawan_bicara);
                $chatRoom->custom_name_1 = $lawan_bicara_data->band_fnama;
                if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                    $chatRoom->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                }else{
                    $chatRoom->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
            }else{
                if($pelanggan->language_id == 2){
                    $chatRoom->custom_name_1 = "Tidak ada member";
                }else{
                    $chatRoom->custom_name_1 = "No member";
                }
                $chatRoom->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
            }
            $chatRoom->custom_name_3 = $chatRoom->band_group_name;
        }else if($chatRoom->type == "group"){
            if($chatRoom->is_edited == "0"){
                if($chatRoom->is_main_group_chat_room == "1"){
                    $chatRoom->images[] = $this->cdn_url($chatRoom->band_group_image);
                }else{
                    $memberList = $chatRoom->b_user_ids;
                    if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                      unset($memberList[$key]);
                    }
                    $memberList = array_values($memberList);
                    $fourUserids = array_chunk($memberList, 4);
                    if($fourUserids){
                        $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                        foreach($arrayUserData AS $users){
                            $chatRoom->custom_name_1 .= $users->band_fnama.", ";
                            if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                $chatRoom->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                            }else{
                                $chatRoom->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                            } 
                        }
                    }
                    $chatRoom->custom_name_1 = substr($chatRoom->custom_name_1, 0, -2);
                    $chatRoom->custom_name_3 = $chatRoom->band_group_name;
                }
            }else{
                $chatRoom->images[] = str_replace("//", "/", $this->cdn_url($chatRoom->image));
                $chatRoom->custom_name_3 = $chatRoom->band_group_name;
            }
        }
        $chatRoom->custom_name_1 = html_entity_decode($chatRoom->custom_name_1,ENT_QUOTES);
        $chatRoom->custom_name_3 = html_entity_decode($chatRoom->custom_name_3,ENT_QUOTES);
        $chatRoom->description = html_entity_decode($chatRoom->description,ENT_QUOTES);
        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $chatRoom->id, $pelanggan->id);
        if(strtotime($oneParticipantData->last_delete_chat) > strtotime($chatRoom->last_chat_cdate)){
            $chatRoom->custom_name_2 = "";
            if($chatRoom->is_main_group_chat_room == "1"){
                $chatRoom->custom_name_2 = "Default club chat room with all Club members";
                $chatRoom->last_chat_message = "Default club chat room with all Club members";
            }
            $chatRoom->cdate_text = $this->humanTiming($chatRoom->cdate, null, $pelanggan->language_id);
            $chatRoom->last_chat_cdate = $this->customTimezone($chatRoom->cdate, $timezone);
        }else{
            $chatRoom->custom_name_2 = $chatRoom->last_chat_message != "" ? $chatRoom->last_chat_b_user_fnama.": ".$chatRoom->last_chat_message : "";
            $chatRoom->cdate_text = $this->humanTiming($chatRoom->last_chat_cdate, null, $pelanggan->language_id);
            $chatRoom->last_chat_cdate = $this->customTimezone($chatRoom->last_chat_cdate, $timezone);
        }

        $chatRoom->is_creator = "0";
        if($chatRoom->b_user_id_creator == $pelanggan->id){
            $chatRoom->is_creator = "1";
        }

        $data['chat_room'] = $chatRoom;

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function chat_room_info_edit()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_room'] = new stdClass();
        // $data['chat_room_id'] = "0";
        // $data['chat_id'] = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // $timezone = $this->input->post("timezone");
        // if($this->isValidTimezoneId($timezone) === false){
        //   $timezone = $this->default_timezone;
        // }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = 0;
        }

        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 1110;
            $this->message = 'You are forbidden to edit';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        if($chatRoom->b_user_id_creator != $pelanggan->id){
            $this->status = 1110;
            $this->message = 'You are forbidden to edit';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        if($chatRoom->type != "group"){
            $this->status = 1110;
            $this->message = 'You are forbidden to edit';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        $chat_room_name = $this->input->post('chat_room_name');
        if (strlen($chat_room_name)<1){
            $this->status = 300;
            $this->message = 'Missing one or more parameters';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chat_room_description = $this->input->post('chat_room_description');

        $checkFileExist = 1;
        $checkFileTemporaryOrNot = 1;
        if($this->input->post('foto') != null){
            $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
            if (strpos($file_path, 'temporary') !== false) {
                if (!file_exists(SENEROOT.$file_path)) {
                    $checkFileExist = 0;
                }
            }else{
                $checkFileTemporaryOrNot = 0;
            }
        }

        if ($checkFileExist == 0) {
            $this->status = 995;
            $this->message = 'Failed upload, temporary already gone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $this->icrm->trans_start();

        $du = array();
        if($this->input->post('foto') != null && $checkFileTemporaryOrNot == 1){
            $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
            $sc = $this->__moveImagex($nation_code, $file_path, $this->media_group_chat_image, $chatRoom->id, "0");
            if (isset($sc->status)) {
                if ($sc->status==200) {
                    $du['image'] = $sc->image;
                    // $du['image'] = $sc->thumb;
                }
            }
        }
        $du['custom_name_1'] = $chat_room_name;
        $du['description'] = $chat_room_description;
        $du['is_edited'] = "1";
        $res = $this->icrm->update($nation_code, $chatRoom->id, $du);
        if (!$res) {
            $this->icrm->trans_rollback();
            $this->icrm->trans_end();
            $this->status = 1107;
            $this->message = 'Error, please try again later';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = "0";
        $di['type'] = 'announcement';
        $di['message'] = $pelanggan->band_fnama." changed this chat room name to [".$chat_room_name."].";
        $di['message_indonesia'] = $pelanggan->band_fnama." telah mengubah nama chat room ini menjadi [".$chat_room_name."].";
        $di['cdate'] = "NOW()";
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = "0";
        $di['type'] = 'announcement';
        $di['message'] = $pelanggan->band_fnama." changed the chat room photo.";
        $di['message_indonesia'] = $pelanggan->band_fnama." telah mengubah foto chat room.";
        $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));;
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);

        $this->icrm->trans_commit();
        $this->icrm->trans_end();

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function clear_chat_history()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat'] = new stdClass();
        // $data['chat_room_id'] = "0";
        // $data['chat_id'] = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // $timezone = $this->input->post("timezone");
        // if($this->isValidTimezoneId($timezone) === false){
        //   $timezone = $this->default_timezone;
        // }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = 0;
        }

        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //start transaction
        $this->icpm->trans_start();

        $du = array();
        $du['last_delete_chat'] = "NOW()";
        $du['is_read'] = 1;
        $res = $this->icpm->update($nation_code, $chatRoom->id, $pelanggan->id, $du);
        if (!$res) {
            $this->icpm->trans_rollback();
            $this->icpm->trans_end();
            $this->status = 1107;
            $this->message = 'Error, please try again later';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
        }

        $du = array();
        $du['is_read'] = "1";
        $this->icreadm->update($nation_code, $chatRoom->id, $pelanggan->id, $du);

        $this->icpm->trans_commit();
        $this->icpm->trans_end();

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function hapus_chat_room(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['custom_message'] = "";
        // $data['chat_unread'] = "0";
        // $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = "0";
        }
        $chatRoom = $this->icrm->getChatRoomByID($nation_code, $chat_room_id);
        if (!isset($chatRoom->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        if ($chatRoom->is_main_group_chat_room == "1") {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
        if(!in_array($pelanggan->id, $chatRoom->b_user_ids)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $oneParticipantData = $this->icpm->getByChatroomidParticipantid($nation_code, $chatRoom->id, $pelanggan->id);
        if(!isset($oneParticipantData->last_delete_chat)){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        if($oneParticipantData->is_owner == "0" && $oneParticipantData->is_creator == "0"){
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $this->icrm->trans_start();

        $this->icpm->del($nation_code, $chatRoom->id, "0");

        $du = array();
        $du['b_user_ids'] = json_encode(array());
        $du['total_people_chat_room'] = 0;
        $du['is_active'] = 0;
        $this->icrm->update($nation_code, $chatRoom->id, $du);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = "0";
        $di['type'] = 'announcement';
        $di['message'] = $pelanggan->band_fnama." deleted the chat room.";
        $di['message_indonesia'] = $pelanggan->band_fnama." telah menghapus chat room.";
        $di['cdate'] = "NOW()";
        $endDoWhile = 0;
        do{
            $chat_id = $this->GUIDv4();
            $checkId = $this->icm->checkId($nation_code, $chat_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);

        $du = array();
        $du['is_read'] = "1";
        $this->icreadm->update($nation_code, $chatRoom->id, "0", $du);

        $this->icrm->trans_commit();
        $this->icrm->trans_end();

        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    // public function read($chat_room_id="", $chat_type= "private")
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     $chat_room_id = $chat_room_id;
    //     if ($chat_room_id<='0') {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     //render to json
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    // }

    // public function readall()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     $this->ecpm->setAsReadAll($nation_code, $pelanggan->id);

    //     $this->ecreadm->setAsReadAll($nation_code, $pelanggan->id);

    //     //render to json
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    // }

    // public function count(){
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = 0;

    //     //default output
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if(empty($nation_code)){
    //       $this->status = 101;
    //       $this->message = 'Missing or invalid nation_code';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //       die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if(!$c){
    //       $this->status = 400;
    //       $this->message = 'Missing or invalid API key';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //       die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if(!isset($pelanggan->id)){
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //       die();
    //     }

    //     $this->status = 200;
    //     $this->message = "Success";

    //     $data = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    // }

    public function searchchatroom()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat_room_total'] = 0;
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $timezone = $this->input->post("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $page = $this->input->post("page");
        $page_size = $this->input->post("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        $i_group_id = trim($this->input->post("i_group_id"));
        if($i_group_id == "0"){
            $i_group_id = "";
        }

        $i_chat_room_id = trim($this->input->post("i_chat_room_id"));
        if($i_chat_room_id == "0"){
            $i_chat_room_id = "";
        }

        $keyword = trim($this->input->post("keyword"));
        $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
        $keyword = substr($keyword, 0, 32);

        $data['chat_room'] = $this->icm->searchChatFromRoomChatList($nation_code, $pelanggan->id, $i_group_id, $i_chat_room_id, $page, $page_size, $keyword);
        foreach ($data['chat_room'] as &$cr) {
            $cr->b_user_ids = json_decode($cr->b_user_ids);
            $cr->images = array();
            $cr->custom_name_3 = "";
            $cr->id_lawan_bicara = "0";
            if($cr->type == "private"){
                if(count($cr->b_user_ids) > 1){
                    if($cr->b_user_ids[1] == $pelanggan->id){
                        $cr->id_lawan_bicara = $cr->b_user_ids[0];
                    }else{
                        $cr->id_lawan_bicara = $cr->b_user_ids[1];
                    }
                    $lawan_bicara_data = $this->bu->getById($nation_code, $cr->id_lawan_bicara);
                    $cr->custom_name_1 = $lawan_bicara_data->band_fnama;
                    if(file_exists(SENEROOT.$lawan_bicara_data->band_image) && $lawan_bicara_data->band_image != 'media/user/default.png'){
                        $cr->images[] = str_replace("//", "/", $this->cdn_url($lawan_bicara_data->band_image));
                    }else{
                        $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                    }
                }else{
                    if($pelanggan->language_id == 2){
                        $cr->custom_name_1 = "Tidak ada member";
                    }else{
                        $cr->custom_name_1 = "No member";
                    }
                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                }
                $cr->custom_name_3 = $cr->band_group_name;
            }else if($cr->type == "group"){
                if($cr->is_edited == "0"){
                    if($cr->is_main_group_chat_room == "1"){
                        $cr->images[] = $this->cdn_url($cr->band_group_image);
                    }else{
                        $memberList = $cr->b_user_ids;
                        if (($key = array_search($pelanggan->id, $memberList)) !== false) {
                          unset($memberList[$key]);
                        }
                        $memberList = array_values($memberList);
                        $fourUserids = array_chunk($memberList, 4);
                        if($fourUserids){
                            $arrayUserData = $this->bu->getByIds($nation_code, $fourUserids[0]);
                            foreach($arrayUserData AS $users){
                                $cr->custom_name_1 .= $users->band_fnama.", ";
                                if(file_exists(SENEROOT.$users->band_image) && $users->band_image != 'media/user/default.png'){
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url($users->band_image));
                                }else{
                                    $cr->images[] = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
                                } 
                            }
                        }
                        $cr->custom_name_1 = substr($cr->custom_name_1, 0, -2);
                        $cr->custom_name_3 = $cr->band_group_name;
                    }
                }else{
                    $cr->images[] = str_replace("//", "/", $this->cdn_url($cr->image));
                    $cr->custom_name_3 = $cr->band_group_name;
                }
            }
            $cr->custom_name_1 = html_entity_decode($cr->custom_name_1,ENT_QUOTES);
            $cr->custom_name_3 = html_entity_decode($cr->custom_name_3,ENT_QUOTES);
            if(strtotime($cr->last_delete_chat) > strtotime($cr->last_chat_cdate)){
                $cr->custom_name_2 = "";
                if($cr->is_main_group_chat_room == "1"){
                    $cr->custom_name_2 = "Default club chat room with all Club members";
                    $cr->last_chat_message = "Default club chat room with all Club members";
                }
            }else{
                $cr->custom_name_2 = $cr->last_chat_message != "" ? $cr->last_chat_b_user_fnama.": ".$cr->last_chat_message : "";
            }
            $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by, null, $pelanggan->language_id);
            $cr->last_chat_cdate = $this->customTimezone($cr->cdate_for_order_by, $timezone);
        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

  public function unreadcountchatandnotification(){
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['notification_count'] = 0;
    $data['chat_count'] = 0;

    //default output
    $this->status = 200;
    $this->message = 'Success';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
        $this->status = 101;
        $this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if(!$c){
        $this->status = 400;
        $this->message = 'Missing or invalid API key';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if(!isset($pelanggan->id)){
        $this->status = 401;
        $this->message = 'Missing or invalid API session';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
    }

    $data['notification_count'] = $this->ignotifm->countUnRead($nation_code, $pelanggan->id);
    $data['chat_count'] = $this->icpm->countUnread($nation_code, $pelanggan->id);

    //render as json
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}