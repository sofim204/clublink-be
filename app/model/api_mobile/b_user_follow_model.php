<?php
class B_User_Follow_model extends JI_Model
{
    public $tbl = 'b_user_follow';
    public $tbl_as = 'buf';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }
    public function getTbl2As()
    {
        return $this->tbl2_as;
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id_follow", "=", "$this->tbl2_as.id");
        return $composites;
    }

    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $b_user_id, $b_user_id_follow, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("b_user_id_follow", $b_user_id_follow);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function countAll($nation_code, $pelanggan=array(), $keyword)
    public function countAll($nation_code, $pelanggan=array(), $keyword, $blockDataAccount, $blockDataAccountReverse)
    {
        $this->db->select_as("COUNT($this->tbl2_as.id)", "total", 0);
        
        $this->db->from($this->tbl2, $this->tbl2_as);

        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        if(isset($pelanggan->id)){
            $this->db->where_as("$this->tbl2_as.id", $this->db->esc($pelanggan->id),"AND", "!=");
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', $keyword, 'and', '%like%');
        }

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function getAll($nation_code, $pelanggan=array(), $keyword, $page, $page_size, $sort_col, $sort_dir)
    public function getAll($nation_code, $pelanggan=array(), $keyword, $page, $page_size, $sort_col, $sort_dir, $blockDataAccount, $blockDataAccountReverse)
    {
        $this->db->select_as("$this->tbl2_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.lnama"), "lnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'-')", "image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);

        if(isset($pelanggan->id)){
            $this->db->select_as("IF( (SELECT COALESCE(id,0) FROM b_user_follow WHERE b_user_id = '$pelanggan->id' AND b_user_id_follow = $this->tbl2_as.id AND nation_code= $nation_code AND is_active= 1 LIMIT 0,1) > 0, 1, 0)", "is_follow", 0);
        }else{
            $this->db->select_as("(0)", "is_follow", 0);
        }

        $this->db->from($this->tbl2, $this->tbl2_as);

        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        if(isset($pelanggan->id)){
            $this->db->where_as("$this->tbl2_as.id", $this->db->esc($pelanggan->id),"AND", "!=");
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
        }

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        $this->db->order_by("LOWER(CAST(".$this->__decrypt("$this->tbl2_as.fnama")." AS CHAR(50)))", $sort_dir);
        $this->db->page($page, $page_size);

        return $this->db->get();
    }

    public function countAllByUserId($nation_code, $type, $b_user_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);

        if($type == 'following'){
            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl3(), 'right');
        }else{
            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'right');

        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        
        if($type == 'following'){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }else{
            $this->db->where_as("$this->tbl_as.b_user_id_follow", $this->db->esc($b_user_id));
        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function getListByTypeUserId($nation_code, $type, $b_user_id, $pelanggan=array())
    public function getListByTypeUserId($nation_code, $type, $b_user_id, $pelanggan=array(), $blockDataAccount, $blockDataAccountReverse)
    {
        // $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.lnama"), "lnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'-')", "image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);

        if(isset($pelanggan->id)){
            $this->db->select_as("IF( (SELECT COALESCE(id,0) FROM b_user_follow WHERE b_user_id = '$pelanggan->id' AND b_user_id_follow = $this->tbl2_as.id AND nation_code= $nation_code AND is_active= 1 LIMIT 0,1) > 0, 1, 0)", "is_follow", 0);
        }else{
            $this->db->select_as("(0)", "is_follow", 0);
        }

        $this->db->from($this->tbl, $this->tbl_as);
        
        if($type == 'following'){
            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl3(), 'right');
        }else{
            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'right');

        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        
        if($type == 'following'){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }else{
            $this->db->where_as("$this->tbl_as.b_user_id_follow", $this->db->esc($b_user_id));
        }

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl2_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        return $this->db->get();
    }

    public function checkFollow($nation_code, $b_user_id, $b_user_id_follow)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.b_user_id_follow", $this->db->esc($b_user_id_follow));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        $d = $this->db->get_first();

        if(isset($d->nation_code)){
            return '1';
        }else{
            return '0';
        }
    }

    // public function setToken($nation_code, $id, $token, $kind="api_web")
    // {
    //     $this->db->where("nation_code", $nation_code)->where("id", $id);
    //     $du = array($kind.'_token'=>$token);
    //     return $this->db->update($this->tbl, $du);
    // }

    // public function getByToken($nation_code, $token, $kind="api_web")
    // {
    //     if (strlen($token)<=4) {
    //         return new stdClass();
    //     }
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where($kind.'_token', $token);
    //     return $this->db->get_first('object', 0);
    // }

    // public function setAgree($id)
    // {
    //     $du = array('is_agree'=>'1');
    //     return $this->db->where("id", $id)->update($this->tbl, $du);
    // }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     if (isset($du['fnama'])) {
    //         if (strlen($du['fnama'])) {
    //             $du['fnama'] = $this->__encrypt($du['fnama']);
    //         }
    //     }
    //     if (isset($du['lnama'])) {
    //         if (strlen($du['lnama'])) {
    //             $du['lnama'] = $this->__encrypt($du['lnama']);
    //         }
    //     }
    //     if (isset($du['email'])) {
    //         if (strlen($du['email'])) {
    //             $du['email'] = $this->__encrypt($du['email']);
    //         }
    //     }
    //     if (isset($du['telp'])) {
    //         if (strlen($du['telp'])) {
    //             $du['telp'] = $this->__encrypt($du['telp']);
    //         }
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function getByEmail($nation_code, $email)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as($this->__decrypt("$this->tbl_as.email"), $this->db->esc($email));
    //     return $this->db->get_first();
    // }

    // public function getById($nation_code, $id)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     return $this->db->get_first();
    // }

    // public function getByEmailAndSocialID($email, $social_id)
    // {
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama");
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama");
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email");
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp");
    //     $this->db->where($this->__decrypt("email"), $email);
    //     $this->db->where("fb_id", $social_id, 'or', 'like', 1, 0);
    //     $this->db->where("google_id", $social_id, 'or', 'like', 0, 1);
    //     $d = $this->db->get_first();
    //     if (isset($d->id)) {
    //         return $d;
    //     }
    //     return new stdClass();
    // }

    // public function getKode($a_company_inisial, $a_company_id="", $fnama="")
    // {
    //     $a_company_inisial = strtoupper($a_company_inisial);
    //     $kode = $a_company_inisial;
    //     if (strlen($fnama)>0) {
    //         $fnama = strtoupper($fnama);
    //         $kode = $a_company_inisial.''.$fnama[0];
    //     }
    //     $this->db->flushQuery();
    //     $this->db->select_as('COUNT(*) total, CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)+1', 'urutan', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where('kode', $kode, 'and', 'like%');
    //     $this->db->order_by('CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)', 'desc');
    //     if (strlen($a_company_id)>0) {
    //         if (strtolower($a_company_id)=='null') {
    //             $this->db->where_as('COALESCE(a_company_id,"-")', $this->db->esc('-'), 'and', '=');
    //         } else {
    //             $this->db->where('a_company_id', $a_company_id, 'and', '=');
    //         }
    //     }
    //     return $this->db->get_first('object', 0);
    // }

    // public function getKodeOnline($fnama_inisial)
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'urutan', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where('kode', $fnama_inisial, 'and', 'like%');
    //     $this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'desc');
    //     return $this->db->get_first('object', 0);
    // }

    // public function flushFcm($fcm_token="")
    // {
    //     if (strlen($fcm_token)>50) {
    //         $sql = 'UPDATE `'.$this->tbl.'` SET fcm_token = "" WHERE fcm_token LIKE "'.$fcm_token.'"';
    //         $this->db->exec($sql);
    //     }
    // }

    // public function auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp)
    // {
    //     $this->db->select("*");
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(`api_web_token`,'-')", 'api_web_token', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), 'AND', 'LIKE', 0, 0);
    //     $this->db->where_as("$this->tbl_as.fb_id", $this->db->esc($fb_id), 'OR', 'LIKE', 1, 0);
    //     $this->db->where_as("$this->tbl_as.apple_id", $this->db->esc($apple_id), 'AND', 'LIKE', 0, 0);
    //     $this->db->where_as("$this->tbl_as.google_id", $this->db->esc($google_id), 'AND', 'LIKE', 0, 1);
    //     $this->db->where_as($this->__decrypt("$this->tbl_as.email"), $this->db->esc($email), 'OR', 'LIKE', 1, 0);
    //     $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), $this->db->esc($telp), 'AND', 'LIKE', 0, 1);
    //     $this->db->order_by("id", "ASC");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkEmail($nation_code, $email)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), "AND", "LIKE");
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkTelp($nation_code, $telp)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), "AND", "LIKE");
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkEmailTelp($nation_code, $email, $telp)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), 'AND', 'LIKE', 1, 0);
    //     $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), 'AND', 'LIKE', 0, 1);
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkFBID($nation_code, $fb_id)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as("COALESCE(fb_id,'-')", $this->db->esc($fb_id), 'AND', 'LIKE', 0, 0);
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkAppleID($nation_code, $apple_id)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as("COALESCE(apple_id,'-')", $this->db->esc($apple_id), 'AND', 'LIKE', 0, 0);
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function checkGoogleID($nation_code, $google_id)
    // {
    //     $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
    //     $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
    //     $this->db->where_as("COALESCE(google_id,'-')", $this->db->esc($google_id), 'AND', 'LIKE', 0, 0);
    //     $this->db->order_by("id", "asc");
    //     return $this->db->get_first('', 0);
    // }

    // public function detail($nation_code, $id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.id", "b_user_id_seller", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("'0'", "rating", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id), "AND", "=");
    //     return $this->db->get_first('', 0);
    // }
    // public function flushFcmToken($fcm_token_old)
    // {
    //     $du = array("fcm_token"=>'');
    //     $this->db->where("fcm_token", $fcm_token_old, 'AND', 'like%');
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function getFcmToken($nation_code, $userid)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as("$this->tbl_as.fcm_token", "fcm_token", 0);
    //     $this->db->select_as("$this->tbl_as.device", "device", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($userid));
    //     $this->db->group_by("$this->tbl_as.id");
    //     return $this->db->get('',0);
    // }

    // public function getFcmTokenSeller($nation_code, $sellerid)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as("$this->tbl_as.fcm_token", "fcm_token", 0);
    //     $this->db->select_as("$this->tbl_as.device", "device", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($sellerid));
    //     return $this->db->get_first();
    // }
    
}
