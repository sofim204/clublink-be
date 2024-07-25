<?php
class Profile extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib('seme_email');
        // $this->load("api_mobile/a_apikey_model", 'aakm');
        // $this->load("api_mobile/b_lokasi_model", 'bl');
        // $this->load("api_mobile/b_kodepos_model", 'bkp');
        $this->load("api_mobile/b_user_follow_model", 'buf');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        // $this->load("api_mobile/b_user_bankacc_model", 'bubam');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/c_produk_model", 'cpm');
        // $this->load("api_mobile/d_order_model", 'order');
        // $this->load("api_mobile/d_order_alamat_model", 'doam');
        // $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        // $this->load("api_mobile/f_version_mobile_model", 'fvmm');

        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
        
        $this->load("api_mobile/c_community_model", "ccomm");
        $this->load("api_mobile/c_community_attachment_model", "ccam");

        $this->load("api_mobile/b_user_wish_product_model", "buwp");

        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/d_pemberitahuan_model", "dpem");

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        $this->load("api_mobile/c_block_model", "cbm");

        //by Donny Dennison - 01 November 2022 14:42
        //report user feature
        $this->load("api_mobile/b_user_report_model", 'burm');

        $this->load("api_mobile/c_community_like_model", "cclm");

    }

    private function __sortCol($sort_col, $tbl_as, $tbl2_as)
    {
        switch ($sort_col) {
          case 'nama':
          $sort_col = "$tbl2_as.fnama";
          break;

          default:
          $sort_col = "$tbl2_as.fnama";
        }
        return $sort_col;
    }

    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "desc") {
          $sort_dir = "DESC";
        } else {
          $sort_dir = "ASC";
        }
        return $sort_dir;
    }

    private function __page($page)
    {
        if (!is_int($page)) {
            $page = (int) $page;
        }
        if (empty($page)) {
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

    /**
     * get User profile
     */
    public function index()
    {
        //default result
        $data = array();
        $data['profile'] = new stdClass();
        $data['address'] = new stdClass();
        $data['total_community_post'] = 0;
        $data['total_follower'] = 0;
        $data['total_following'] = 0;
        $data['is_follow'] = '0';
        $data['can_input_referral'] = '0';
        $data['profile']->wallet_access = $this->ccm->getByClassifiedAndCode('62', "app_config", "C26")->remark;

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByTokenIgnoreIsActive($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //building response
        $data['profile'] = $pelanggan;
        $data['profile']->wallet_access = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C26")->remark;
        
        if(file_exists(SENEROOT.$data['profile']->image) && $data['profile']->image != 'media/user/default.png'){
            $data['profile']->image = str_replace("//", "/", $this->cdn_url($data['profile']->image));
        }else{
            $data['profile']->image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        if(file_exists(SENEROOT.$data['profile']->image_banner)){
            $data['profile']->image_banner = str_replace("//", "/", $this->cdn_url($data['profile']->image_banner));
        }else{
            $data['profile']->image_banner = str_replace("//", "/", $this->cdn_url('media/user/default.png'));
        }

        if(file_exists(SENEROOT.$data['profile']->band_image) && $data['profile']->band_image != 'media/user/default.png'){
            $data['profile']->band_image = str_replace("//", "/", $this->cdn_url($data['profile']->band_image));
        }else{
            $data['profile']->band_image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        // unset($data['profile']->fb_id);
        // unset($data['profile']->apple_id);
        // unset($data['profile']->google_id);
        unset($data['profile']->password);
        unset($data['profile']->latitude);
        unset($data['profile']->longitude);
        unset($data['profile']->kelamin);
        unset($data['profile']->bdate);
        // unset($data['profile']->cdate);
        unset($data['profile']->adate);
        unset($data['profile']->edate);
        // unset($data['profile']->telp);
        unset($data['profile']->intro_teks);
        unset($data['profile']->api_social_id);
        unset($data['profile']->fcm_token);
        unset($data['profile']->device);
        unset($data['profile']->is_agree);
        unset($data['profile']->is_confirmed);
        // unset($data['profile']->is_active);
        // unset($data['profile']->telp_is_verif);
        unset($data['profile']->api_mobile_edate);
        unset($data['profile']->is_reset_password);
        unset($data['profile']->api_web_token);
        unset($data['profile']->api_mobile_token);
        unset($data['profile']->api_reg_token);
        unset($data['profile']->user_wallet_code);

        $data['address'] = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        $data['profile']->total_product = $this->cpm->countAll($nation_code, "", "",$pelanggan->id, "", "", array(), array(), array(), "All", $data['address'], "ProtectionAndMeetUpAndAutomotive", 0, '', array(), array(), array(), 1);

        $data['total_community_post'] = $this->ccomm->countAllByUserId($nation_code, $pelanggan->id);

        $data['total_follower'] = $this->buf->countAllByUserId($nation_code, 'follower', $pelanggan->id);

        $data['total_following'] = $this->buf->countAllByUserId($nation_code, 'following', $pelanggan->id);

        $data['is_follow'] = '0';

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
            $data['profile']->can_input_referral = '1';
        }else{
            $data['profile']->can_input_referral = '0';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        $data['profile']->bKodeRecuiter = "";
        $data['profile']->bNamaRecuiter = "";
        if($pelanggan->b_user_id_recruiter != '0'){

            $recommenderData = $this->bu->getById($nation_code, $pelanggan->b_user_id_recruiter);
            if(isset($recommenderData->kode_referral)){
                $data['profile']->bKodeRecuiter = $recommenderData->kode_referral;
                $data['profile']->bNamaRecuiter = $recommenderData->fnama;
            }

        }

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function friend()
    {
        //default result
        $data = array();
        $data['profile'] = new stdClass();
        $data['address'] = new stdClass();
        $data['total_community_post'] = '0';
        $data['total_follower'] = '0';
        $data['total_following'] = '0';
        $data['is_follow'] = '0';
        $data['is_blocked'] = '0';
        $data['block_id'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

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

        $b_user_id = $this->input->get('b_user_id');
        $userData = $this->bu->getById($nation_code, $b_user_id);
        if (!isset($userData->id)) {
            $this->status = 1001;
            $this->message = 'Missing or invalid b_user_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //building response
        $data['profile'] = $userData;

        if(file_exists(SENEROOT.$data['profile']->image) && $data['profile']->image != 'media/user/default.png'){
            $data['profile']->image = str_replace("//", "/", $this->cdn_url($data['profile']->image));
        }else{
            $data['profile']->image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        if(file_exists(SENEROOT.$data['profile']->image_banner)){
            $data['profile']->image_banner = str_replace("//", "/", $this->cdn_url($data['profile']->image_banner));
        }else{
            $data['profile']->image_banner = str_replace("//", "/", $this->cdn_url('media/user/default.png'));
        }

        if(file_exists(SENEROOT.$data['profile']->band_image) && $data['profile']->band_image != 'media/user/default.png'){
            $data['profile']->band_image = str_replace("//", "/", $this->cdn_url($data['profile']->band_image));
        }else{
            $data['profile']->band_image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        unset($data['profile']->fb_id);
        unset($data['profile']->apple_id);
        unset($data['profile']->google_id);
        unset($data['profile']->password);
        unset($data['profile']->latitude);
        unset($data['profile']->longitude);
        unset($data['profile']->kelamin);
        unset($data['profile']->bdate);
        unset($data['profile']->cdate);
        unset($data['profile']->adate);
        unset($data['profile']->edate);
        unset($data['profile']->telp);
        unset($data['profile']->intro_teks);
        unset($data['profile']->api_social_id);
        unset($data['profile']->fcm_token);
        unset($data['profile']->device);
        unset($data['profile']->is_agree);
        unset($data['profile']->is_confirmed);
        // unset($data['profile']->is_active);
        unset($data['profile']->telp_is_verif);
        unset($data['profile']->api_mobile_edate);
        unset($data['profile']->is_reset_password);
        unset($data['profile']->api_web_token);
        unset($data['profile']->api_mobile_token);
        unset($data['profile']->api_reg_token);
        unset($data['profile']->user_wallet_code);
        unset($data['profile']->user_wallet_code_new);

        $data['address'] = $this->bua->getByUserIdDefault($nation_code, $b_user_id);

        $data['total_community_post'] = $this->ccomm->countAllByUserId($nation_code, $b_user_id);

        $data['total_follower'] = $this->buf->countAllByUserId($nation_code, 'follower', $b_user_id);

        $data['total_following'] = $this->buf->countAllByUserId($nation_code, 'following', $b_user_id);

        if(isset($pelanggan->id)){
            $data['is_follow'] = $this->buf->checkFollow($nation_code, $pelanggan->id, $b_user_id);
        }

        if(isset($pelanggan->id)){

            $dataBlocked = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $b_user_id);

            if(isset($dataBlocked->block_id)){

                $data['is_blocked'] = "1";

                $data['block_id'] = $dataBlocked->block_id;

            }

        }

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function userlist()
    {

        //default result
        $data = array();
        $data['user_total'] = "0";
        $data['users'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

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

        $sort_col = $this->input->post("sort_col");
        $sort_dir = $this->input->post("sort_dir");
        $page = $this->input->post("page");
        $page_size = $this->input->post("page_size");
        $keyword = trim($this->input->post("keyword"));

        //sanitize input
        $tbl_as = $this->buf->getTblAs();
        $tbl2_as = $this->buf->getTbl2As();

        $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //keyword
        $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
        $keyword = substr($keyword, 0, 32);

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if (isset($pelanggan->id)) {

            $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
            $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);

        }else{

            $blockDataAccount = array();
            $blockDataAccountReverse = array();

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        // $data['user_total'] = $this->buf->countAll($nation_code, $pelanggan, $keyword);
        // $data['users'] = $this->buf->getAll($nation_code, $pelanggan, $keyword, $page, $page_size, $sort_col, $sort_dir);
        $data['user_total'] = $this->buf->countAll($nation_code, $pelanggan, $keyword, $blockDataAccount, $blockDataAccountReverse);
        $data['users'] = $this->buf->getAll($nation_code, $pelanggan, $keyword, $page, $page_size, $sort_col, $sort_dir, $blockDataAccount, $blockDataAccountReverse);

        //building response
        foreach($data['users'] AS &$fl){

            $fl->image = str_replace("//", "/", $fl->image);

            if(file_exists(SENEROOT.$fl->image) && $fl->image != 'media/user/default.png'){
              $fl->image = $this->cdn_url($fl->image);
            } else {
              $fl->image = $this->cdn_url('media/user/default-profile-picture.png');
            }

        }

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function followlist()
    {

        //default result
        $data = array();
        $data['followlist'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

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

        $type = $this->input->get('type');
        if ($type != 'following' && $type != 'follower') {
          $type = 'following';
        }

        $b_user_id = $this->input->get('b_user_id');
        $userData = $this->bu->getById($nation_code, $b_user_id);
        if (!isset($userData->id)) {
            $this->status = 1001;
            $this->message = 'Missing or invalid b_user_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if (isset($pelanggan->id)) {

            $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
            $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);

        }else{

            $blockDataAccount = array();
            $blockDataAccountReverse = array();

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        // $data['followlist'] = $this->buf->getListByTypeUserId($nation_code, $type, $b_user_id, $pelanggan);
        $data['followlist'] = $this->buf->getListByTypeUserId($nation_code, $type, $b_user_id, $pelanggan, $blockDataAccount, $blockDataAccountReverse);

        //building response
        foreach($data['followlist'] AS &$fl){

            $fl->image = str_replace("//", "/", $fl->image);

            if(file_exists(SENEROOT.$fl->image) && $fl->image != 'media/user/default.png'){
              $fl->image = $this->cdn_url($fl->image);
            } else {
              $fl->image = $this->cdn_url('media/user/default-profile-picture.png');
            }

        }

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function baru()
    {
        //default result
        $data = array();
        $data['followlist'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        $b_user_id_follow = $this->input->post('b_user_id_follow');
        if ($b_user_id_follow == "0") {
            $this->status = 1004;
            $this->message = 'b_user_id_follow is empty';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        $userData = $this->bu->getById($nation_code, $b_user_id_follow);
        if (!isset($userData->id)) {
            $this->status = 1002;
            $this->message = 'Missing or invalid b_user_id_follow';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        if($pelanggan->id == $b_user_id_follow){
            $this->status = 1003;
            $this->message = 'You cannot follow yourself';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die(); 
        }

        //start transaction
        $this->buf->trans_start();

        $checkAlreadyFollow = $this->buf->checkFollow($nation_code, $pelanggan->id, $b_user_id_follow);

        if($checkAlreadyFollow == '1'){

            //collect input
            $di = array();
            $di['is_active'] = 0;

            //insert into database
            $res = $this->buf->update($nation_code, $pelanggan->id, $b_user_id_follow, $di);
            if ($res) {
                $this->buf->trans_commit();
                $this->status = 200;
                // $this->message = 'Success unfollow';
                $this->message = 'Success';
            } else {
                $this->buf->trans_rollback();
                $this->status = 1771;
                // $this->message = 'Failed unfollow';
                $this->message = 'Failed';
            }

        }else{

            //get last id
            $last_id = $this->buf->getLastId($nation_code, $pelanggan->id);

            //collect input
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $last_id;
            $di['b_user_id'] = $pelanggan->id;
            $di['b_user_id_follow'] = $b_user_id_follow;
            $di['cdate'] = 'NOW()';

            //insert into database
            $res = $this->buf->set($di);
            if ($res) {
                $this->buf->trans_commit();
                $this->status = 200;
                // $this->message = 'Success follow';
                $this->message = 'Success';

                //get missing data
                $sender = $this->bu->getById($nation_code, $pelanggan->id);
                $receiver = $this->bu->getById($nation_code, $b_user_id_follow);

                // $type = 'follower';
                // $anotid = 1;
                // $replacer = array();
                // $replacer['pelanggan_fnama'] = $sender->fnama;
                $classified = 'setting_notification_user';
                $code = 'U6';

                $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_follow, $classified, $code);

                if (!isset($receiverSettingNotif->setting_value)) {
                    $receiverSettingNotif->setting_value = 0;
                }

                //push notif
                if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
                    if (strlen($receiver->fcm_token)>50) {
                        $device = $receiver->device; //jenis device
                        $tokens = array($receiver->fcm_token); //device token
                        if($receiver->language_id == 2) {
                            $title = 'Pengikut Baru';
                            $message =  $sender->fnama." mengikuti Anda sekarang. Jadilah teman yang baik.";
                        } else {
                            $title = 'New Followers';
                            $message =  $sender->fnama." follow you now. Be a good friend.";
                        }
                        
                        $type = 'follower';
                        $image = 'media/pemberitahuan/follower.png';
                        $payload = new stdClass();
                        $payload->b_user_id_follow = (string) $b_user_id_follow;
                        $payload->user_id = $sender->id;
                        $payload->user_fnama = $sender->fnama;

                        // by Muhammad Sofi - 27 October 2021 10:12
                        // if user img & banner not exist or empty, change to default image
                        // $payload->user_image = $this->cdn_url($sender->image);
                        if(file_exists(SENEROOT.$sender->image) && $sender->image != 'media/user/default.png'){
                            $payload->user_image = $this->cdn_url($sender->image);
                        } else {
                            $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        // $payload->custom_image = $roomChat->custom_image;

                        // $nw = $this->anot->get($nation_code, "push", $type, $anotid);
                        // if (isset($nw->title)) {
                        //     $title = $nw->title;
                        // }
                        // if (isset($nw->message)) {
                        //     $message = $this->__nRep($nw->message, $replacer);
                        // }
                        // if (isset($nw->image)) {
                        //     $image = $nw->image;
                        // }
                        $image = $this->cdn_url($image);
                        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    }

                }

                //collect array notification list
                $extras = new stdClass();
                $extras->b_user_id_follow = (string) $b_user_id_follow;
                $extras->user_id = $sender->id;
                $extras->user_fnama = $sender->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->user_image = $this->cdn_url($sender->image);
                if(file_exists(SENEROOT.$sender->image) && $sender->image != 'media/user/default.png'){
                    $extras->user_image = $this->cdn_url($sender->image);
                } else {
                    $extras->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $b_user_id_follow;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id_follow);
                $dpe['judul'] = "Pengikut Baru";
                $dpe['teks'] = $sender->fnama." mengikuti Anda sekarang. Jadilah teman yang baik.";
                $dpe['type'] = "follower";
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/follower.png';
                $dpe['extras'] = json_encode($extras);
                // $nw = $this->anot->get($nation_code, "list", $type, $anotid);
                // if (isset($nw->title)) {
                //   $di2['judul'] = $nw->title;
                // }
                // if (isset($nw->message)) {
                //   $di2['teks'] = $this->__nRep($nw->message, $replacer);
                // }
                // if (isset($nw->image)) {
                //   $di2['gambar'] = $nw->image;
                // }
                // $di2['gambar'] = $this->cdn_url($di2['gambar']);
                $this->dpem->set($dpe);
                $this->buf->trans_commit();

            } else {
                $this->buf->trans_rollback();
                $this->status = 1771;
                // $this->message = 'Failed follow';
                $this->message = 'Failed';
            }

        }

        $this->buf->trans_end();

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function buyinghistory_ongoing()
    {
        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data
        $dcount = $this->dodm->countBuyingHistoryOnGoing($nation_code, $pelanggan->id);

        $ddata = $this->dodm->getBuyingHistoryOnGoing($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            // if (isset($pd->harga_jual)) {
            //     $pd->harga_jual = strval($pd->harga_jual);
            // }
            // if (isset($pd->sub_total)) {
            //     $pd->sub_total = strval($pd->sub_total);
            // }
            // if (isset($pd->ongkir_total)) {
            //     $pd->ongkir_total = strval($pd->ongkir_total);
            // }
            // if (isset($pd->grand_total)) {
            //     $pd->grand_total = strval($pd->grand_total);
            // }
            // $pd->d_order_sub_total = $pd->sub_total;
            // $pd->d_order_grand_total = $pd->grand_total;
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if (!empty($iidc)) {
                $pd->grand_total = strval($pd->sub_total);
                $pd->d_order_grand_total = $pd->grand_total;
            } else {
                $pd->grand_total = strval($pd->sub_total+($pd->shipment_cost + $pd->shipment_cost_add));
                $pd->d_order_grand_total = $pd->grand_total;
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }
    
    public function buyinghistory_finished()
    {
        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data
        $dcount = $this->dodim->countBuyingHistoryFinished($nation_code, $pelanggan->id);

        $ddata = $this->dodim->getBuyingHistoryFinished($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            $pd->d_order_sub_total = $pd->sub_total;
            $pd->d_order_grand_total = $pd->grand_total;
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function sellinghistory_ongoing()
    {
        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data
        $dcount = $this->dodm->countSellingHistoryOnGoing($nation_code, $pelanggan->id);

        $ddata = $this->dodm->getSellingHistoryOnGoing($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            // if (isset($pd->harga_jual)) {
            //     $pd->harga_jual = strval($pd->harga_jual);
            // }
            // if (isset($pd->sub_total)) {
            //     $pd->sub_total = strval($pd->sub_total);
            // }
            // if (isset($pd->ongkir_total)) {
            //     $pd->ongkir_total = strval($pd->ongkir_total);
            // }
            // if (isset($pd->grand_total)) {
            //     $pd->grand_total = strval($pd->grand_total);
            // }
            // $pd->d_order_sub_total = $pd->sub_total;
            // $pd->d_order_grand_total = $pd->grand_total;
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if (!empty($iidc)) {
                $pd->grand_total = strval($pd->sub_total);
                $pd->d_order_grand_total = $pd->grand_total;
            } else {
                $pd->grand_total = strval($pd->sub_total+($pd->shipment_cost + $pd->shipment_cost_add));
                $pd->d_order_grand_total = $pd->grand_total;
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }
    
    public function sellinghistory_finished()
    {
        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data
        $dcount = $this->dodim->countSellingHistoryFinished($nation_code, $pelanggan->id);

        $ddata = $this->dodim->getSellingHistoryFinished($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            $pd->d_order_sub_total = $pd->sub_total;
            $pd->d_order_grand_total = $pd->grand_total;
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function mywish_product()
    {

        //default result
        $data = array();
        $data['product_total'] = 0;
        $data['products'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //populate input get
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");

        //sanitize input
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);
      
        $data['product_total'] = $this->buwp->countAll($nation_code, $pelanggan->id);
      
        $data['products'] = $this->buwp->getAll($nation_code, $page, $page_size, $pelanggan->id);

        //manipulating data
        foreach ($data['products'] as &$pd) {

            $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

            if (isset($pd->thumb)) {
                if (empty($pd->thumb)) {
                  $pd->thumb = 'media/produk/default.png';
                }
                $pd->thumb = str_replace("//", "/", $pd->thumb);
                $pd->thumb = $this->cdn_url($pd->thumb);
            }
            if (isset($pd->foto)) {
                if (empty($pd->foto)) {
                  $pd->foto = 'media/produk/default.png';
                }
                $pd->foto = str_replace("//", "/", $pd->foto);
                $pd->foto = $this->cdn_url($pd->foto);
            }

        }

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function mywish_product_delete()
    {

        //default result
        $data = array();
        $data['product'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //populate input get
        $c_produk_id = $this->input->get("c_produk_id");

        $this->buwp->delete($nation_code, $c_produk_id, $pelanggan->id);

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    public function mywish_post()
    {

        //default result
        $data = array();
        $data['community_total'] = 0;
        $data['communitys'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        //populate input get
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $timezone = $this->input->get("timezone");

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //sanitize input
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);
        
        if (isset($pelanggan->id)) {

          $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
          $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
          
        }else{

          $pelangganAddress1 = array();
          $pelangganAddress2 = array();

        }

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if (isset($pelanggan->id)) {

            $blockDataCommunity = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "community");
            $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
            $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);

        }else{

            $blockDataCommunity = array();
            $blockDataAccount = array();
            $blockDataAccountReverse = array();

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        // $data['community_total'] = $this->ccomm->countAllMyWishPost($nation_code, $pelangganAddress1, $pelanggan->id);
        // $data['community_total'] = $this->ccomm->countAllMyWishPost($nation_code, $pelangganAddress1, $pelanggan->id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $data['communitys'] = $this->ccomm->getAllMyWishPost($nation_code, $page, $page_size, $pelangganAddress2, $pelanggan->id);
        // $data['communitys'] = $this->ccomm->getAllMyWishPost($nation_code, $page, $page_size, $pelangganAddress2, $pelanggan->id, $pelanggan->language_id);
        $data['communitys'] = $this->ccomm->getAllMyWishPost($nation_code, $page, $page_size, $pelangganAddress2, $pelanggan->id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $pelanggan->language_id);
        foreach ($data['communitys'] as &$pd) {
            $pd->can_chat_and_like = "0";

            // if(isset($pelanggan->id) && isset($pelangganAddress2->alamat2)){
            if(isset($pelanggan->id)){
                // if($pd->postal_district == $pelangganAddress2->postal_district){
                    $pd->can_chat_and_like = "1";
                // }
            }

            $pd->is_owner_post = "0";
            $pd->is_liked = '0';
            $pd->is_disliked = '0';
            if(isset($pelanggan->id)){
                if($pd->b_user_id_starter == $pelanggan->id){
                    $pd->is_owner_post = "1";
                }

                $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $pd->id, $pelanggan->id);
                if(isset($checkLike->id)){
                  $pd->is_liked = '1';
                }

                $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $pd->id, $pelanggan->id);
                if(isset($checkDislike->id)){
                  $pd->is_disliked = '1';
                }
            }

            // $pd->cdate_text = $this->humanTiming($pd->cdate);
            $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);

            $pd->cdate = $this->customTimezone($pd->cdate, $timezone);

            //convert to utf friendly
            if (isset($pd->title)) {
                // $pd->title = $this->__dconv($pd->title);
            }
            $pd->title = html_entity_decode($pd->title,ENT_QUOTES);
            $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

            if (isset($pd->b_user_image_starter)) {
                if (empty($pd->b_user_image_starter)) {
                    $pd->b_user_image_starter = 'media/produk/default.png';
                }
            
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
                if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
                    $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
                } else {
                    $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            if($pd->top_like_image_1 > 0){
              $pd->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
            }

            $pd->images = array();
            $pd->locations = array();
            $pd->videos = array();

            $attachments = $this->ccam->getByCommunityId($nation_code, $pd->id);
            foreach ($attachments as $atc) {
                if($atc->jenis == 'image'){
                    if (empty($atc->url)) {
                        $atc->url = 'media/produk/default.png';
                    }
                    if (empty($atc->url_thumb)) {
                        $atc->url_thumb = 'media/produk/default.png';
                    }

                    $atc->url = $this->cdn_url($atc->url);
                    $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                    $pd->images[] = $atc;
                }else if($atc->jenis == 'video'){
                    $atc->url = $this->cdn_url($atc->url);
                    $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                    $pd->videos[] = $atc;
                }else{
                    $pd->locations[] = $atc;
                }
            }
            unset($attachments);
        }
        unset($ddata,$pd);

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }

    //START by Donny Dennison - 01 November 2022 14:42
    //report user feature
    public function report()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
          die();
        }

        $b_user_id_reported = $this->input->post("b_user_id_reported");
        $deskripsi = $this->input->post("deskripsi");
        if (!$deskripsi) {
            $this->status = 1107;
            $this->message = 'Reason is mandatory';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        $reportedUserData = $this->bu->getById($nation_code, $b_user_id_reported);
        if (isset($reportedUserData->id)) {

            //start transaction and lock table
            $this->burm->trans_start();

            $lastID = $this->burm->getLastId($nation_code);

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $lastID;
            $di['b_user_id_reported'] = $b_user_id_reported;
            $di['b_user_id'] = $pelanggan->id;
            $di['deskripsi'] = $deskripsi;
            $di['cdate'] = 'NOW()';
            $res = $this->burm->set($di);
            if (!$res) {
              $this->burm->trans_rollback();
              $this->burm->trans_end();
              $this->status = 1108;
              $this->message = "Error while report user, please try again later";
              $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
              die();
            }

            $this->burm->trans_commit();

            //end transaction
            $this->burm->trans_end();

        }

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");

    }
    //END by Donny Dennison - 01 November 2022 14:42
    //report user feature

    public function profilebyuserwalletcode() {
        //default result
        $data = array();
        $user_wallet_code_new = $this->input->get('user_wallet_code');

        $nation_code = 62;
        $data['user'] = $this->bu->getByUserWalletCodeNew($nation_code, $user_wallet_code_new);
        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
    }
}
