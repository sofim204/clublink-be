<?php
class B_User_Model extends JI_Model
{
    public $tbl = 'b_user';
    public $tbl_as = 'bu';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'do';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';
    public $tbl4 = 'g_leaderboard_point_total';
    public $tbl4_as = 'glpt';
    public $tbl5 = 'e_chat_room';
    public $tbl5_as = 'ecr';
    public $tbl6 = 'c_community';
    public $tbl6_as = 'cc';
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
    public function getTblAlias2()
    {
        return $this->tbl2_as;
    }
    public function getTblAlias7()
    {
        return $this->tbl7_as;
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl2_as.b_user_id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl3_as.b_user_id");
        return $cps;
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

    public function getKode($a_company_inisial, $a_company_id="")
    {
        $this->db->flushQuery();
        $this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'urutan', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('kode', $a_company_inisial, 'and', 'like%');
        $this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)', 'desc');
        if (mb_strlen($a_company_id)>0) {
            $this->db->where('a_company_id', $a_company_id, 'and', '=');
        }
        return $this->db->get_first('object', 0);
    }

    public function getKodeOnline($fnama_inisial)
    {
        $this->db->flushQuery();
        $this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'urutan', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('kode', $fnama_inisial, 'and', 'like%');
        $this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'desc');
        return $this->db->get_first('object', 0);
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.image", "image");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "nama", 0);
        $this->db->select_as("COALESCE($this->tbl_as.is_admin, '')", "is_admin", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as("COALESCE($this->tbl_as.ip_address, '')", "ip_address", 0);
        $this->db->select_as("COALESCE($this->tbl_as.is_emulator, '')", "is_emulator", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active");
        $this->db->select_as("$this->tbl_as.is_permanent_inactive", "is_permanent_inactive");
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl7_as.fnama").',"-")', "fnama_recommender", 0);
        // $this->db->select_as("(SELECT bu_recommender.fnama FROM b_user bu_recommender JOIN b_user bu ON bu_recommender.b_user_id_recruiter = bu.id)", "fnama_recruiter");
        // $this->db->select_as("$this->tbl_as.b_user_id_recruiter", "b_user_id_recruiter");
        $this->db->select_as("$this->tbl_as.device", "device");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("COALESCE($this->tbl_as.fcm_token,'-')", "fcm_token");
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl8_as.alamat2").',"-")', "alamat2", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl8_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl8_as.kelurahan,', ',$this->tbl8_as.kecamatan,', ',$this->tbl8_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("$this->tbl_as.fb_id", "fb_id");
        // $this->db->select_as("$this->tbl_as.apple_id", "apple_id");
        // $this->db->select_as("$this->tbl_as.google_id", "google_id");
        // $this->db->select_as("$this->tbl_as.device_id", "device_id");
        // $this->db->select_as("IF((($this->tbl_as.fb_id IS NULL or $this->tbl_as.fb_id = '') and ($this->tbl_as.apple_id IS NULL or $this->tbl_as.apple_id = '') and ($this->tbl_as.google_id IS NULL or $this->tbl_as.google_id = '') and ($this->tbl_as.register_from != 'phone')), 'yes', 'no')", "email_id");
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
        // $this->db->select('id');
        // $this->db->select('image');
        // $this->db->select_as($this->__decrypt('fnama'), 'nama', 0);
        // $this->db->select_as($this->__decrypt('email'), 'email', 0);
        // $this->db->select_as('is_get_point', 'is_get_point', 0);
        // $this->db->select('is_active');
        //Improve By Aditya Adi Prabowo 7/9/2020
        //Add field device
        // $this->db->select_as("$this->tbl_as.is_permanent_inactive", 'is_permanent_inactive', 0);
        // $this->db->select_as($this->__decrypt('bu2.fnama'), 'fnama_recruiter', 0);
        // $this->db->select_as('b_user_id_recruiter', 'b_user_recommender', 0);
        // $this->db->select_as('device', 'device', 0);
        // $this->db->select_as('device_id', 'device_id', 0);
        // End Of Improve

        //START by Donny Dennison - 15 august 2022 13:16
        //Add fb_id, google_id, apple_id, and email status in cms
        // $this->db->select('fb_id');
        // $this->db->select('apple_id');
        // $this->db->select('google_id');
        // $this->db->select_as('IF(((fb_id IS NULL or fb_id = "") and (apple_id IS NULL or apple_id = "") and (google_id IS NULL or google_id = "") and (register_from != "phone")), "yes", "no")', 'email_id');
        //END by Donny Dennison - 15 august 2022 13:16
        //Add fb_id, google_id, apple_id, and email status in cms

        //by Donny Dennison - 23 august 2022 12:11
        //Add phone status in cms
        // $this->db->select('register_from');

        // $this->db->select('is_confirmed');
        //by Donny Dennison - 29 august 2020 12:26
        //add label 2 step verified or not yet
        // $this->db->select('telp_is_verif');
        // $this->db->select_as('CONCAT(bdate)', 'bdate', 0);
        // $this->db->select_as($this->__decrypt('telp'), 'telp');
        // $this->db->select_as('cdate', 'cdate', 0);
        // $this->db->select_as('nation_code', 'nation_code', 0);

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

    public function countAll($nation_code, $keyword="", $is_confirmed="", $is_active="")
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

    public function getByIdRedeem($nation_code, $id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.image", "image");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate");
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->select_as("COALESCE($this->tbl_as.ip_address, '')", "ip_address", 0);
        $this->db->select_as("$this->tbl_as.is_permanent_inactive", "is_permanent_inactive");
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl7_as.fnama").',"-")', "fnama_recommender", 0);
        $this->db->select_as("$this->tbl_as.device", "device");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("COALESCE($this->tbl_as.fcm_token,'-')", "fcm_token");
        $this->db->select_as("CONCAT($this->tbl8_as.kelurahan,', ',$this->tbl8_as.kecamatan,', ',$this->tbl8_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.fb_id", "fb_id");
        $this->db->select_as("$this->tbl_as.apple_id", "apple_id");
        $this->db->select_as("$this->tbl_as.google_id", "google_id");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("IF((($this->tbl_as.fb_id IS NULL or $this->tbl_as.fb_id = '') and ($this->tbl_as.apple_id IS NULL or $this->tbl_as.apple_id = '') and ($this->tbl_as.google_id IS NULL or $this->tbl_as.google_id = '') and ($this->tbl_as.register_from != 'phone')), 'yes', 'no')", "email_id");
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        $this->db->select_as("$this->tbl_as.b_user_id_recruiter", "b_user_id_recruiter");
        $this->db->select_as("$this->tbl_as.total_recruited", "total_recruited");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->where("$this->tbl_as.nation_code", $nation_code);
        $this->db->where("$this->tbl_as.id", $id);
        return $this->db->get_first();
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

    public function getByIdTakedown($nation_code, $b_user_id)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as("$this->tbl_as.device", "device", 0);
        $this->db->select_as("$this->tbl_as.fcm_token", "fcm_token", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $b_user_id);
        return $this->db->get_first();
    }

    public function checkKode($kode, $id=0)
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->where("kode", $kode);
        if (!empty($id)) {
            $this->db->where("id", $id, 'AND', '!=');
        }
        $d = $this->db->from($this->tbl)->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function updateUmur($id=0)
    {
        $sql = "UPDATE $this->tbl SET `umur` = DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), bdate)), '%Y')+0 WHERE";
        if (!empty($id)) {
            $sql .= " id = $id";
        } else {
            $sql .= " 1";
        }
        return $this->db->exec($sql);
    }

    public function select2($keyword="", $is_active="1")
    {
        $this->db->select("id");
        $this->db->select_as($this->__decrypt('fnama'));
        $this->db->select("kode");
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where("is_active", $is_active);
        if (mb_strlen($keyword)>1) {
            $this->db->where($this->__decrypt('fnama'), ''.$keyword, "OR", "%like%", 1, 0);
            $this->db->where($this->__decrypt("email"), ''.$keyword, "OR", "%like%", 0, 0);
            $this->db->where("kode", ''.$keyword, "OR", "%like%", 0, 1);
        }
        $this->db->limit(100);
        return $this->db->get("object", 0);
    }

    public function cekEmail($email, $b_user_id='')
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->where($this->__decrypt("email"), $email);
        if (!empty($b_user_id)) {
            $this->db->where("id", $b_user_id, 'AND', '!=');
        }
        $d = $this->db->from($this->tbl)->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function massUpdateStat($dus=array())
    {
        $updated = new stdClass();
        $updated->count = 0;
        $updated->success = 0;
        $updated->failed = 0;
        foreach ($dus as $du) {
            $updated->count++;
            if (isset($du['b_user_id'])) {
                $this->db->where('id', $du['b_user_id']);
                $res = $this->db->update($this->tbl, $du);
                if ($res) {
                    $updated->success++;
                } else {
                    $updated->failed++;
                }
            }
        }
        return $updated;
    }

    public function cari($nation_code, $keyword)
    {
        $this->db->select("id")->select('fnama')->select("email")->select("is_active");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        if (mb_strlen($keyword)>1) {
            $this->db->where($this->__decrypt('fnama'), $keyword, "OR", "%like%");
            $this->db->where($this->__decrypt("email"), $keyword, "OR", "%like%");
        }
        return $this->db->get('', 0);
    }

    public function setToken($nation_code, $id, $token, $kind="api_web")
    {
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
        $this->db->where("nation_code", $nation_code)->where("id", $id);
        $du = array($kind.'_token'=>$token);
        return $this->db->update($this->tbl, $du);
    }
    
    public function getYangAdaNotifnya()
    {
        $this->db->where_as("fcm_token", "", "AND", "!=");
        $this->db->where_as("device", "ios", "OR", "like%%", 1, 0);
        $this->db->where_as("device", "android", "OR", "like%%", 0, 1);
        return $this->db->get();
    }

    public function update_status_user($nation_code, $id, $du) {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function delete_user($id, $nation_code){
		$this->db->from($this->tbl);
		$this->db->where("id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete($this->tbl, 0);
	}

    public function delete_user_alamat($id, $nation_code){
		$this->db->from("b_user_alamat");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_alamat", 0);
	}

    public function delete_user_bankacc($id, $nation_code){
		$this->db->from("b_user_bankacc");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_bankacc", 0);
	}

    public function delete_user_card($id, $nation_code){
		$this->db->from("b_user_card");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_card", 0);
	}

    public function delete_user_follow($id, $nation_code){
		$this->db->from("b_user_follow");
        $this->db->where("nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("b_user_id", $id, "OR", "=", 1, 0);
		$this->db->where("b_user_id_follow", $id, "OR", "=", 0, 1);
		return $this->db->delete("b_user_follow", 0);
	}
    
    public function delete_user_productwanted($id, $nation_code){
		$this->db->from("b_user_productwanted");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_productwanted", 0);
	}
    
    public function delete_user_setting($id, $nation_code){
		$this->db->from("b_user_setting");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_setting", 0);
	}
    
    public function delete_user_wish_product($id, $nation_code){
		$this->db->from("b_user_wish_product");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("b_user_wish_product", 0);
	}

    public function delete_user_community($id, $nation_code){
		$this->db->from("c_community");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community", 0);
	}

    public function delete_user_community_report($id, $nation_code){
		$this->db->from("c_community_report");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_report", 0);
	}

    public function delete_user_community_like($id, $nation_code){
		$this->db->from("c_community_like");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_like", 0);
	}

    public function delete_user_community_discussion($id, $nation_code){
		$this->db->from("c_community_discussion");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_discussion", 0);
	}

    public function delete_user_community_discussion_report($id, $nation_code){
		$this->db->from("c_community_discussion_report");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_discussion_report", 0);
	}

    public function delete_user_product_share_history($id, $nation_code){
		$this->db->from("c_product_share_history");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_product_share_history", 0);
	}

    public function delete_user_product($id, $nation_code){
		$this->db->from("c_produk");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_produk", 0);
	}

    public function delete_user_product_laporan($id, $nation_code){
		$this->db->from("c_produk_laporan");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_produk_laporan", 0);
	}
    
    public function delete_user_cart($id, $nation_code){
		$this->db->from("d_cart");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_cart", 0);
	}

    public function delete_user_order($id, $nation_code){
		$this->db->from("d_order");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order", 0);
	}

    public function delete_user_order_alamat($id, $nation_code){
		$this->db->from("d_order_alamat");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order_alamat", 0);
	}

    public function delete_user_order_detail_pickup($id, $nation_code){
		$this->db->from("d_order_detail_pickup");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order_detail_pickup", 0);
	}

    public function delete_user_wishlist($id, $nation_code){
		$this->db->from("d_wishlist");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_wishlist", 0);
	}

    public function delete_user_chat($id, $nation_code){
		$this->db->from("e_chat");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_chat", 0);
	}

    public function delete_user_chat_participant($id, $nation_code){
		$this->db->from("e_chat_participant");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_chat_participant", 0);
	}

    public function delete_user_chat_read($id, $nation_code){
		$this->db->from("e_chat_read");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_chat_read", 0);
	}
    
    public function delete_user_chat_room($id, $nation_code){
		$this->db->from("e_chat_room");
		$this->db->where("b_user_id_starter", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_chat_room", 0);
	}
    
    public function delete_user_complain($id, $nation_code){
		$this->db->from("e_complain");
        $this->db->where("nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where("b_user_id_seller", $id, "OR", "=", 1, 0);
		$this->db->where("b_user_id_buyer", $id, "OR", "=", 0, 1);
		return $this->db->delete("e_complain", 0);
	}

    public function delete_user_likes($id, $nation_code){
		$this->db->from("e_likes");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_likes", 0);
	}

    public function delete_user_rating($id, $nation_code){
		$this->db->from("e_rating");
        $this->db->where("nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("b_user_id_seller", $id, "OR", "=", 1, 0);
		$this->db->where("b_user_id_buyer", $id, "OR", "=", 0, 1);
		return $this->db->delete("e_rating", 0);
	}

    public function delete_user_discussion($id, $nation_code){
		$this->db->from("f_discussion");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("f_discussion", 0);
	}

    public function delete_user_discussion_report($id, $nation_code){
		$this->db->from("f_discussion_report");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("f_discussion_report", 0);
	}

    public function delete_user_verification_phone_number($id, $nation_code){
		$this->db->from("f_verification_phone_number");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("f_verification_phone_number", 0);
	}

    public function delete_user_leaderboard_point_area($id, $nation_code){
		$this->db->from("g_leaderboard_point_area");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_leaderboard_point_area", 0);
	}

    public function delete_user_leaderboard_point_history($id, $nation_code){
		$this->db->from("g_leaderboard_point_history");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_leaderboard_point_history", 0);
	}

    public function delete_user_leaderboard_point_limit($id, $nation_code){
		$this->db->from("g_leaderboard_point_limit");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_leaderboard_point_limit", 0);
	}

    public function delete_user_leaderboard_point_total($id, $nation_code){
		$this->db->from("g_leaderboard_point_total");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_leaderboard_point_total", 0);
	}

    public function delete_user_leaderboard_ranking($id, $nation_code){
		$this->db->from("g_leaderboard_ranking");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_leaderboard_ranking", 0);
	}

    public function get_user_community($id, $nation_code){
        $this->db->select_as("id", 'community_id',0);
        $this->db->select_as('b_user_id', 'b_user_id', 0);
        $this->db->from("c_community");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
        return $this->db->get("", 0);
    }

    public function delete_user_community_attachment($c_community_id, $nation_code){
		$this->db->from("c_community_attachment");
		$this->db->where("c_community_id", $c_community_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_attachment", 0);
	}

    public function delete_user_highlight_community($c_community_id, $nation_code){
		$this->db->from("g_highlight_community");
		$this->db->where("c_community_id", $c_community_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("g_highlight_community", 0);
	}

    public function get_user_community_discussion($id, $nation_code){
        $this->db->select_as("id", 'community_discussion_id',0);
        $this->db->select_as('b_user_id', 'b_user_id', 0);
        $this->db->from("c_community_discussion");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
        return $this->db->get("", 0);
    }

    public function delete_user_community_discussion_attachment($community_discussion_id, $nation_code){
		$this->db->from("c_community_discussion_attachment");
		$this->db->where("c_community_discussion_id", $community_discussion_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_community_discussion_attachment", 0);
	}

    public function get_user_product($id, $nation_code){
        $this->db->select_as("id", 'c_produk_id',0);
        $this->db->select_as('b_user_id', 'b_user_id', 0);
        $this->db->from("c_produk");
		$this->db->where("b_user_id", $id);
		$this->db->where("nation_code", $nation_code);
        return $this->db->get("", 0);
    }

    public function delete_user_product_detail_automotive($product_id, $nation_code){
		$this->db->from("c_produk_detail_automotive");
		$this->db->where("c_produk_id", $product_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_produk_detail_automotive", 0);
	}

    public function delete_user_product_foto($product_id, $nation_code){
		$this->db->from("c_produk_foto");
		$this->db->where("c_produk_id", $product_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("c_produk_foto", 0);
	}

    public function get_user_order($b_user_id, $nation_code){
        $this->db->select_as("id", 'order_id',0);
        $this->db->from("d_order");
		$this->db->where("b_user_id", $b_user_id);
		$this->db->where("nation_code", $nation_code);
        return $this->db->get("", 0);
		// $sql = " SELECT 
        //             a.id as order_id,
        //         FROM d_order a INNER JOIN d_order_detail b
        //         ON a.id = b.d_order_id 
        //         WHERE a.b_user_id = '$b_user_id' AND a.nation_code = '$nation_code'
        // ";
		// return $this->db->query($sql);
    }

    public function delete_user_order_detail($order_id, $nation_code){
		$this->db->from("d_order_detail");
		$this->db->where("d_order_id", $order_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order_detail", 0);
	}

    public function delete_user_order_detail_item($order_id, $nation_code){
		$this->db->from("d_order_detail_item");
		$this->db->where("d_order_id", $order_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order_detail_item", 0);
	}

    public function delete_user_order_process($order_id, $nation_code){
		$this->db->from("d_order_proses");
		$this->db->where("d_order_id", $order_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("d_order_proses", 0);
	}

    public function get_user_chat_room($id, $nation_code){
        $this->db->select_as("id ", 'chat_room_id ',0);
        $this->db->select_as('b_user_id_starter', 'b_user_id_starter', 0);
        $this->db->from("e_chat_room");
		$this->db->where("b_user_id_starter", $id);
		$this->db->where("nation_code", $nation_code);
        return $this->db->get("", 0);
    }

    public function delete_user_chat_attachment($chat_room_id, $nation_code){
		$this->db->from("e_chat_attachment");
		$this->db->where("e_chat_room_id", $chat_room_id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete("e_chat_attachment", 0);
	}
    /**
     * 
     * using manual query
     * 
    */

    // public function delete_user_community_attachment_and_highlight($id, $nation_code){
    //     return $this->db->exec("DELETE `c_community`, `c_community_attachment`, `g_highlight_community` FROM `c_community` 
    //         INNER JOIN `c_community_attachment` INNER JOIN` g_highlight_community`
    //         WHERE c_community.id = c_community_attachment.c_community_id AND 
    //               c_community.id = g_highlight_community.c_community_id AND
    //               c_community.b_user_id = '$id' AND 
    //               c_community.nation_code = '$nation_code';
    //     ");
    // }

    //by Donny Dennison - 6 september 2022 17:50
    //integrate api blockchain
    public function checkWalletCode($nation_code, $user_wallet_code)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.user_wallet_code", $this->db->esc($user_wallet_code));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    //by Donny Dennison - 20 october 2022 11:20
    //integrate api blockchain
    public function getAllDontHaveWallet($nation_code)
    {
        $this->db->select('id');

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.user_wallet_code", $this->db->esc(""));

        return $this->db->get("object", 0);
    }

    //by Donny Dennison - 12 september 2022 14:59
    //kode referral
    public function checkKodeReferral($nation_code, $kode_referral)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.kode_referral", $this->db->esc($kode_referral));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function countAllUnfinisedOffer($nation_code, $chat_type = 'offer', $type="buyer", $b_user_id = 0, $offer_status = ""){

        $this->db->select_as("COUNT(*)", "total", 0);
    
        $this->db->from($this->tbl5, $this->tbl5_as);
    
        $this->db->where("$this->tbl5_as.nation_code", $nation_code);
    
        if($chat_type){
          $this->db->where("$this->tbl5_as.chat_type", $chat_type);
        }
    
        if($b_user_id != 0){
          if($type == "buyer"){
            $this->db->where("$this->tbl5_as.b_user_id_starter",$b_user_id);
          }else{
            $this->db->where("$this->tbl5_as.b_user_id_seller",$b_user_id);
          }
        }
    
        if (is_array($offer_status) && count($offer_status)>0) {
          $this->db->where_in("$this->tbl5_as.offer_status", $offer_status);
        }else if($offer_status){
          $this->db->where("$this->tbl5_as.offer_status",$offer_status);
        }
    
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getLastRegisterFrom($nation_code, $b_user_id) {
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first();
    }

    // public function inactive_user_from_fcm_token($nation_code, $fcm_token, $du) {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("fcm_token", $fcm_token);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function inactive_user_from_fcm_token($nation_code, $pids=array(), $du) {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where_in("id", $pids);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function inactive_user_from_ip_address($nation_code,  $pids=array(), $du) {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where_in("id", $pids);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getByFcmToken($nation_code, $fcm_token)
    {
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("is_permanent_inactive", 1);
        $this->db->where("fcm_token", $fcm_token);
        $this->db->order_by("id", "ASC");
        return $this->db->get();
    }

    public function getByIpAddress($nation_code, $ip_address)
    {
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("is_permanent_inactive", 1);
        $this->db->where("ip_address", $ip_address);
        $this->db->order_by("id", "ASC");
        return $this->db->get();
    }

    public function getByIdData($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get();
    }

    public function getRecommendeeDataById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id_recruiter", $id);
        $this->db->order_by("id", "ASC");
        return $this->db->get();
    }

    public function getByEmail($nation_code, $email)
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
        $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', $this->db->esc($email), "AND", "=", 0, 0);
        return $this->db->get_first();
    }

    public function getByNameChat($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "text", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (mb_strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (mb_strlen($keyword)>1) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
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

        return $this->db->get("array", 0);
    }
}
