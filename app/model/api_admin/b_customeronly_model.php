<?php
class B_Customeronly_Model extends JI_Model
{
    public $tbl = 'b_user';
    public $tbl_as = 'bu';
    public $tbl7 = 'b_user';
    public $tbl7_as = 'bu_recommender';
    public $tbl8 = 'b_user_alamat';
    public $tbl8_as = 'bua';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTblAlias()
    {
        return $this->tbl_as;
    }
    public function getTblAlias7()
    {
        return $this->tbl7_as;
    }

    private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_recruiter", "=", "$this->tbl7_as.id");
        return $cps;
    }
    private function __joinTbl8()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl8_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl8_as.b_user_id");
        $composites[] = $this->db->composite_create("1", "=", "$this->tbl8_as.is_default");
        return $composites;
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
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
    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        if (isset($du['fnama'])) {
            if (mb_strlen($du['fnama'])) {
                $du['fnama'] = $this->__encrypt($du['fnama']);
            }
        }
        if (isset($du['lnama'])) {
            if (mb_strlen($du['lnama'])) {
                $du['lnama'] = $this->__encrypt($du['lnama']);
            }
        }
        if (isset($du['email'])) {
            if (mb_strlen($du['email'])) {
                $du['email'] = $this->__encrypt($du['email']);
            }
        }
        if (isset($du['telp'])) {
            if (mb_strlen($du['telp'])) {
                $du['telp'] = $this->__encrypt($du['telp']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }

    public function getAllCustomerOnly($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.image", "image");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as("COALESCE($this->tbl_as.ip_address, '')", "ip_address", 0);
        $this->db->select_as("COALESCE($this->tbl_as.is_emulator, '')", "is_emulator", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active");
        $this->db->select_as("$this->tbl_as.is_permanent_inactive", "is_permanent_inactive");
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl7_as.fnama").',"-")', "fnama_recommender", 0);
        $this->db->select_as("$this->tbl_as.device", "device");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("COALESCE($this->tbl_as.fcm_token,'-')", "fcm_token");
        // $this->db->select_as($this->__decrypt("$this->tbl8_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl8_as.kelurahan,', ',$this->tbl8_as.kecamatan,', ',$this->tbl8_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.fb_id", "fb_id");
        $this->db->select_as("$this->tbl_as.apple_id", "apple_id");
        $this->db->select_as("$this->tbl_as.google_id", "google_id");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("IF((($this->tbl_as.fb_id IS NULL or $this->tbl_as.fb_id = '') and ($this->tbl_as.apple_id IS NULL or $this->tbl_as.apple_id = '') and ($this->tbl_as.google_id IS NULL or $this->tbl_as.google_id = '') and ($this->tbl_as.register_from != 'phone')), 'yes', 'no')", "email_id");
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        $this->db->select_as("$this->tbl_as.is_confirmed", "is_confirmed");
        $this->db->select_as("$this->tbl_as.telp_is_verif", "telp_is_verif");
        $this->db->select_as("$this->tbl_as.bdate", "bdate");
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate");
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code");
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl7_as.email").',"-")', "email_recommender", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl7_as.telp").',"-")', "contact_recommender", 0);
        $this->db->select_as("$this->tbl7_as.kode_referral", "kode_referral");
        $this->db->select_as("$this->tbl_as.b_user_id_recruiter", "b_user_id_recruiter");
        // $this->db->select_as("CONCAT($this->tbl8_as.kelurahan,', ',$this->tbl8_as.kecamatan,', ',$this->tbl8_as.kabkota,', ', $this->tbl8_as.provinsi)", "alamat2_full", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (mb_strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (mb_strlen($keyword)>1) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl7_as.kode_referral", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.device_id", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.ip_address", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.fcm_token", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
        }

        //START by Donny Dennison - 15 august 2022 13:16
        //Add fb_id, google_id, apple_id, and email status in cms
        if($sortCol == "bu.email_id"){
            
            $this->db->order_by("IF((($this->tbl_as.fb_id IS NULL or $this->tbl_as.fb_id = '') and ($this->tbl_as.apple_id IS NULL or $this->tbl_as.apple_id = '') and ($this->tbl_as.google_id IS NULL or $this->tbl_as.google_id = '') and ($this->tbl_as.register_from != 'phone')), 'yes', 'no')", $sortDir);
 
        }else{

            $this->db->order_by($sortCol, $sortDir);

        }
        //END by Donny Dennison - 15 august 2022 13:16
        //Add fb_id, google_id, apple_id, and email status in cms

        //by Donny Dennison - 27 January 2021 17:17
        //change chat to open chatting
        if($page != -1 && $pagesize != -1){

            $this->db->limit($page, $pagesize);

        }

        return $this->db->get("object", 0);
    }

    public function countAllCustomerOnly($nation_code, $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (mb_strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (mb_strlen($keyword)>1) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl7_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl7_as.kode_referral", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.device_id", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.fcm_token", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.ip_address", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as("$this->tbl_as.fb_id", "fb_id");
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function update_status_user($nation_code, $id, $du) {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getLastRegisterFrom($nation_code, $b_user_id) {
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first();
    }

    public function getByIdData($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get();
    }
}
