<?php
class Discuss extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/a_notification_model", 'anot');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/d_order_model", 'dom');
        $this->load("api_admin/d_order_detail_model", 'dodm');
        $this->load("api_admin/d_order_detail_item_model", 'dodim');
        $this->load("api_admin/d_pemberitahuan_model", 'dpem');
        $this->load("api_admin/c_discuss_model","cdm");
        $this->load("api_admin/common_code_model", 'ccm');

    }

    /*private function __dataEcp($nation_code, $d_order_id, $d_order_detail_item_id,$chat_type, $b_user_id)
    {
        if ($this->is_log) {
            $this->seme_log->write("api_admin", "API_Mobile/buyer/chat::__dataEcp");
        }
        $ecp = array();
        $ecp['nation_code'] = $nation_code;
        $ecp['d_order_id'] = $d_order_id;
        $ecp['c_produk_id'] = $d_order_detail_item_id;
        $ecp['b_user_id'] = $b_user_id;
        $ecp['is_read'] = 0;
        $ecp['chat_type'] = $chat_type;
        $ecps[] = $ecp;
        return $ecp;
    }*/

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
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $nation_code = $d['sess']->admin->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");
        $sortCol = "cdate";



        $tbl_as = $this->cdm->getTableAlias();
        $tbl2_as = $this->cdm->getTableAlias2();
        $tbl3_as = $this->cdm->getTableAlias3();

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
            $sortCol = "$tbl_as.id";
            break;
            case 1:
            $sortCol = "$tbl3_as.nama";
            break;
            case 2:
            $sortCol = "$tbl2_as.fnama";
            break;
            case 3:
            $sortCol = "$tbl_as.user_type";
            break;
            case 4:
            $sortCol = "$tbl_as.cdate";
            break;
            case 5:
            $sortCol = "$tbl_as.is_active";
            break;
            case 6:
            $sortCol = "$tbl_as.text";
            break;
            default:
            $sortCol = "$tbl_as.id";
        }
        
        $keyword = $sSearch;

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->cdm->countAll($nation_code, $keyword);
        $ddata = $this->cdm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword);

        foreach ($ddata as &$dt) {
            if (isset($dt->cdate)) {
                $dt->cdate = date("d/m/Y H:i", strtotime($dt->cdate));
            }
            $dt->action = '<button class="btn btn-default" data-id="'.$dt->id.'">View Options</button>';
        }


        $return = array();
        foreach ($ddata as $key => $dts) {
            $return[$key]['id'] = $dts->id;
            $return[$key]['nama'] = $this->__convertToEmoji($dts->nama);
            $return[$key]['b_user_fnama'] = $dts->b_user_fnama;
            $return[$key]['user_type'] = $dts->user_type;
            $return[$key]['cdate'] = $dts->cdate;
            if($dts->takedown==0)
            {
                $status = "Active";
            }
            else
            {
                $status = "Takedown";
            }
            $return[$key]['takedown'] = $status;
            $return[$key]['message'] = $this->__convertToEmoji($dts->message);
            $return[$key]['action'] = $dt->action;
            // End Edit
        }
        
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($return, $dcount);
    }

    public function detail($ieid)
    {
        $id = (int) $ieid; 
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



        $tbl_as = $this->cdm->getTableAlias();
        $tbl2_as = $this->cdm->getTableAlias2();
        $tbl3_as = $this->cdm->getTableAlias3();

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
            $sortCol = "$tbl_as.id";
            break;
            case 1:
            $sortCol = "$tbl3_as.nama";
            break;
            case 2:
            $sortCol = "$tbl2_as.fnama";
            break;
            case 3:
            $sortCol = "$tbl_as.user_type";
            break;
            case 4:
            $sortCol = "$tbl_as.cdate";
            break;
            case 5:
            $sortCol = "$tbl_as.is_active";
            break;
            case 6:
            $sortCol = "$tbl_as.text";
            break;
            default:
            $sortCol = "$tbl_as.id";
        }
        
        $keyword = $sSearch;

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cdm->getByIds($nation_code, $id, $page, $pagesize, $sortCol, $sortDir, $keyword);
        $dcount = $this->cdm->countAlls($nation_code, $id, $keyword);

        foreach ($data as &$dt) {
            if (isset($dt->cdate)) {
                $dt->cdate = date("m/d/y H:i", strtotime($dt->cdate));
            }
            $dt->action = '<button class="btn btn-info" data-id="'.$dt->id.'">View Options</button>';
        }
        $return = array();
        foreach ($data as $key => $dts) {
            $return[$key]['id'] = $dts->id;
            $return[$key]['nama'] = $this->__convertToEmoji($dts->nama);
            $return[$key]['b_user_fnama'] = $dts->b_user_fnama;
            $return[$key]['user_type'] = $dts->user_type;
            $return[$key]['cdate'] = $dts->cdate;
            if($dts->takedown==0)
            {
                $status = "Active";
            }
            else
            {
                $status = "Takedown";
            }
            $return[$key]['takedown'] = $status;
            $return[$key]['message'] = $this->__convertToEmoji($dts->message);
            $return[$key]['action'] = $dts->action;
            /*$return[$key]['action'] = $dt->action;*/
            // End Edit
        }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($return, $dcount);
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
        $data = $this->cdm->getByIdEdit($nation_code,$id);
        // by Muhammad Sofi 14 January 2022 10:03 | fix when edit data, text convert to special character code
        
        if(isset($data->product)){
			$data->product = html_entity_decode($this->__convertToEmoji($data->product), ENT_QUOTES);
		}

        if(isset($data->text)){
			$data->text = html_entity_decode($this->__convertToEmoji($data->text), ENT_QUOTES);
		}
        $this->__json_out($data);
    }

    public function takedown($id)
    {
        //die('edit');
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        $this->cdm->trans_start();

        $nation_code = $d['sess']->admin->nation_code;
        $takedown = 1;
        $res = $this->cdm->takedown($nation_code, $id, $takedown);
        
        if ($res) {
            $this->cdm->trans_commit();
            $this->status = 200;
            $this->message = 'Data Has Been Takedown';
            $takedown = $this->cdm->getByIdNotif($nation_code, $id);
            $b_user_id = (int)$takedown->b_user_id;
            $produkS = $takedown->product;
            $id_takedown = (int)$takedown->id;
            $product_id = (int)$takedown->product_id;
            $parentid = (int)$takedown->parent_id;

            $detailProduct = $this->cpm->getByIdTakedown($nation_code, $product_id);

            $users = $this->bum->getByIdTakedown($nation_code,$b_user_id);
            if (strtolower($users->device) == 'ios') {
                $ios = $users->fcm_token;
            } else {
                $android = $users->fcm_token;
            }
            $dpe = array();
            $dpe['nation_code'] = $nation_code;
            $dpe['b_user_id'] = $b_user_id;
            $dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id);
            $dpe['type'] = "discussion";
            $dpe['judul'] = "Diskusi Produk";
            $dpe['teks'] = "Komentar Anda tentang ".$takedown->product. " sudah di hapus oleh Admin karena tidak sesuai";
            $dpe['gambar'] = 'media/pemberitahuan/productdiscussion.png';
            $dpe['cdate'] = "NOW()";
            $extras = new stdClass();
            $extras->id = $parentid;
            $extras->judul = "Diskusi Produk";
            $extras->nama = $detailProduct->nama;
            $extras->product_id = $takedown->product_id;
            $extras->harga_jual = $detailProduct->harga_jual;
            $extras->foto = base_url().$detailProduct->thumb;
            $extras->teks = "Komentar Anda tentang ".$takedown->product. " sudah di hapus oleh Admin karena tidak sesuai";
            $dpe['extras'] = json_encode($extras);
            $this->dpem->set($dpe);
            $this->cdm->trans_commit();
        
            if (strtolower($users->device) == 'ios'){
                    
                $device = "ios"; //jenis device
                $tokens = array($ios); //device token
                $title = "Diskusi Produk";
                $message = "Komentar Anda tentang ".$this->convertEmoji($takedown->product). " sudah di hapus oleh Admin karena tidak sesuai";
                $image = '';
                $type = 'discussion';
                $payload = new stdClass();
                $payload->id = $parentid;
                $payload->judul = "Diskusi Produk";
                $payload->nama = $detailProduct->nama;
                $payload->product_id = $takedown->product_id;
                $payload->harga_jual = $detailProduct->harga_jual;
                $payload->foto = base_url().$detailProduct->thumb;
                $payload->teks =  "Komentar Anda tentang ".$this->convertEmoji($takedown->product). " sudah di hapus oleh Admin karena tidak sesuai";
                $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                if ($this->is_log) {
                    $this->seme_log->write("api_admin", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
                }
            }elseif(strtolower($users->device) == 'android'){   

                $device = "android"; //jenis device
                $tokens = array($android); //device token
                $title = "Diskusi Produk";
                $message = "Komentar Anda tentang ".$this->convertEmoji($takedown->product). " sudah di hapus oleh Admin karena tidak sesuai";
                $image = '';
                $type = 'discussion';
                $payload = new stdClass();
                $payload->id = $parentid;
                $payload->judul = "Diskusi Produk";
                $payload->nama = $detailProduct->nama;
                $payload->product_id = $takedown->product_id;
                $payload->harga_jual = $detailProduct->harga_jual;
                $payload->foto = base_url().$detailProduct->thumb;
                $payload->teks = "Komentar Anda tentang ".$this->convertEmoji($takedown->product). " sudah di hapus oleh Admin karena tidak sesuai";
                $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    if ($this->is_log) {
                        $this->seme_log->write("api_admin", 'API_Admin/Discussion::baru __pushNotifAndroid: '.json_encode($res));
                }
                
            }
            
            //update total_discussion
            $this->cpm->updateTotal($nation_code, $takedown->product_id, 'total_discussion', '-', 1);
            $this->cdm->trans_commit();

            //if discussion is a parent, child also deleted
            if($takedown->parent_id == 0){

                $getTotalChildIsActive = $this->cdm->countAllChild($nation_code, $takedown->id, $takedown->product_id);

                //update total_discussion
                $this->cpm->updateTotal($nation_code, $takedown->product_id, 'total_discussion', '-', $getTotalChildIsActive);
                $this->cdm->trans_commit();

                $di = array();
                $di['edate'] = 'NOW()';
                $di['is_active'] = 0;
                $this->cdm->updateByParentDiscussionId($nation_code, $takedown->id, $di);
                $this->cdm->trans_commit();

            }


            $this->cdm->trans_end();
        } else {
            $this->cdm->trans_rollback(); // by Muhammad Sofi 14 January 2022 9:47 | bug fix error when open product q&a menu
            $this->cdm->trans_end();
            $this->status = 901;
            $this->message = 'Failed to make data changes';
        }
        
        $this->__json_out($data);
    }

    public function edit($id){
        //die('edit');
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
            $id = (int) $id;
            $du = $_POST;
            /*var_dump($du); die();*/
            /*var_dump($du); die();
*/          $nation_code = $d['sess']->admin->nation_code;
            $res = $this->cdm->update($nation_code,$id,$du);

            if($res){
                $this->status = 200;
                $this->message = 'Success';
            }else{
                $this->status = 901;
                $this->message = 'Cannot edit data to database';
            }
        $this->__json_out($data);

    }

    public function reported()
    {
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

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");
        $sortCol = "cdate";



        $tbl_as = $this->cdm->getTableAlias();
        $tbl2_as = $this->cdm->getTableAlias2();
        $tbl3_as = $this->cdm->getTableAlias3();

        //by Donny Dennison - 21 January 2021 10:32
        //show last report cdate
        $tbl4_as = $this->cdm->getTableAlias4();

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
            $sortCol = "$tbl_as.id";
            break;
            case 1:
            $sortCol = "$tbl3_as.nama";
            break;
            case 2:
            $sortCol = "$tbl2_as.fnama";
            break;
            case 3:
            $sortCol = "$tbl_as.user_type";
            break;
            case 4:
            $sortCol = "$tbl_as.cdate";
            break;

            //by Donny Dennison - 21 January 2021 10:32
            //show last report cdate
            case 5:
            $sortCol = "$tbl4_as.cdate";
            break;

            case 6:
            $sortCol = "$tbl_as.is_active";
            break;
            case 7:
            $sortCol = "$tbl_as.text";
            break;
            default:
            $sortCol = "$tbl4_as.cdate";
        }
        
        $keyword = $sSearch;

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cdm->getByIdReport($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword);
        $dcount = $this->cdm->countAllReport($nation_code, $keyword);
        foreach ($data as &$dt) {
            $dt->action = '<button class="btn btn-info" data-id="'.$dt->id.'">Process</button>';
        }
        $return = array();
        foreach ($data as $key => $dts) {
            if($dts->takedown==0)
            {
                $status = "Active";
            }
            else
            {
                $status = "Takedown";
            }

            $return[$key]['id'] = $dts->id;
            $return[$key]['nama'] = $this->__convertToEmoji($dts->nama);
            $return[$key]['b_user_fnama'] = $dts->b_user_fnama;
            $return[$key]['user_type'] = $dts->user_type;

            //by Donny Dennison - 21 January 2021 10:32
            //show last report cdate
            // $return[$key]['cdate'] = $dts->cdate;
            $return[$key]['cdate'] = date("m/d/y H:i", strtotime($dts->cdate));

            //by Donny Dennison - 21 January 2021 10:32
            //show last report cdate
            $return[$key]['last_report_cdate'] = date("m/d/y H:i", strtotime($dts->last_report_cdate));
            
            $return[$key]['takedown'] = $status;
            $return[$key]['message'] = $this->__convertToEmoji($dts->message);
            $return[$key]['action'] = $dt->action;
            /*$return[$key]['action'] = $dt->action;*/
            // End Edit
        }
        //sleep(3);
        // $another = array();

        $this->__jsonDataTable($return, $dcount);
    }

    public function active($id)
    {
        //die('edit');
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $nation_code = $d['sess']->admin->nation_code;
        $takedown = 1;
        $res = $this->cdm->active($nation_code, $id, $takedown);
        if ($res) {
            $this->status = 200;
            $this->message = 'Data Has Been Takedown';
        } else {
            $this->status = 901;
            $this->message = 'Failed to make data changes';
        }
        
        $this->__json_out($data);
    }

    public function ignore($id)
    {
        //die('edit');
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $nation_code = $d['sess']->admin->nation_code;
        $takedown = 1;
        $res = $this->cdm->ignore($nation_code, $id, $takedown);
        if ($res) {
            $this->status = 200;
            $this->message = 'Data Has Been Takedown';
        } else {
            $this->status = 901;
            $this->message = 'Failed to make data changes';
        }
        
        $this->__json_out($data);
    }

    public function hapus($id)
    {
        $id = (int) $id;
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
        $this->status = 200;
        $this->message = 'Success';

        $res = $this->ecm->del($nation_code, $id);
        if (!$res) {
            $this->status = 902;
            $this->message = 'Failed while deleting data from database';
        }
        $this->__json_out($data);
    }
}
