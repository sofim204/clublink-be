<?php
class B_User_Model extends JI_Model
{
    public $maks_data = 9999999;
    public $tbl = 'b_user';
    public $tbl_as = 'bu';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'dor';
    public $tbl3 = 'b_user_alamat';
    public $tbl3_as = 'bum';
    public $tbl4 = 'f_verification_phone_number';
    public $tbl4_as = 'fvn';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl3_as.b_user_id");
        return $cps;
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getAll()
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
        $this->db->select_as("$this->tbl_as.device", "device");
        $this->db->select_as("$this->tbl_as.device_id", "device_id");
        $this->db->select_as("COALESCE($this->tbl_as.fcm_token,'-')", "fcm_token");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", 1, "AND", "=", 0, 0);
        return $this->db->get("object", 0);
    }
    
    /**
     * get a row by id from table b_user
     * @param  int $nation_code [description]
     * @param  int $id          [description]
     * @return object              [description]
     */
    public function getById($nation_code, $id)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as("$this->tbl_as.fb_id", "fb_id");
        $this->db->select_as("$this->tbl_as.register_from", "register_from");
        $this->db->select_as("COALESCE($this->tbl_as.inactive_text,'-')", "inactive_text", 0);
        $this->db->select_as("$this->tbl_as.offer_rating_seller_avg", "offer_rating_seller_avg", 0);
        $this->db->select_as("$this->tbl_as.offer_rating_seller_total", "offer_rating_seller_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }
    
    // by Muhammad Sofi 15 February 2022 11:07 | show verification number in detail customer
    public function getByIdVerifCode($nation_code, $b_user_id) {
        $this->db->select_as("$this->tbl4_as.verification_number", "verif_number", 0);
        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->where("$this->tbl4_as.nation_code", $nation_code);
        $this->db->where("$this->tbl4_as.b_user_id", $b_user_id);
        $this->db->order_by("$this->tbl4_as.cdate", "DESC");
        // $this->db->where("$this->tbl4_as.is_confirmed", $this->db->esc(1));
        return $this->db->get_first();
    }

    // Improve By Aditya Adi Prabowo 18/8/2020 14:14 
    // Add Print Email and Name User
    // Start Improve
    public function exportXlsPayment($nation_code, $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select('id');
        $this->db->select('image');
        /*$this->db->select_as("COALESCE(AES_DECRYPT($this->tbl_as.nama,''),' ')", "nama", 0);
        $this->db->select_as("COALESCE(AES_DECRYPT($this->tbl_as.email,''),' ')", "email", 0);*/
       /* $this->db->select_as("AES_DECRYPT($this->tbl_as.nama)", "nama", 0);
        $this->db->select_as("AES_DECRYPT($this->tbl_as.email)", "email", 0);*/
        $this->db->select_as($this->__decrypt('fnama'), 'nama', 0);
        $this->db->select_as($this->__decrypt('email'), 'email', 0);
        $this->db->select('is_active');
        $this->db->select('is_confirmed');
        $this->db->select_as('CONCAT(bdate)', 'bdate', 0);
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as('DATE(cdate)', 'cdate', 0);
        $this->db->select_as('nation_code', 'nation_code', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (strlen($keyword)>1) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.email"), addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get('',0);
    }
    // End Improve

    // Improve By Aditya Adi Prabowo 8/18/2020 14:14
    // Add button to print Xls Detail Data User
    // Start Improve

    public function exportXlsDetail($nation_code, $keyword="", $is_confirmed="", $is_active="")
    {
        $is_down = 1;
        $this->db->flushQuery();
        /*$this->db->select_as("COALESCE(AES_DECRYPT($this->tbl_as.nama,''),' ')", "nama", 0);
        $this->db->select_as("COALESCE(AES_DECRYPT($this->tbl_as.email,''),' ')", "email", 0);*/
       /* $this->db->select_as("AES_DECRYPT($this->tbl_as.nama)", "nama", 0);
        $this->db->select_as("AES_DECRYPT($this->tbl_as.email)", "email", 0);*/
        $this->db->select_as($this->__decrypt('fnama'), 'nama', 0);
        $this->db->select_as($this->__decrypt('email'), 'email', 0);
        $this->db->select_as($this->__decrypt('telp'), 'telp', 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.alamat2"),'alamat2',0);
        $this->db->select_as("$this->tbl3_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl3_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->select_as("$this->tbl_as.is_confirmed", "is_confirmed", 0);
        $this->db->select_as('CONCAT(bdate)', 'bdate', 0);
        $this->db->select_as('DATE(cdate)', 'cdate', 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.device", "device", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (strlen($keyword)>1) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.email"), addslashes($keyword), "OR", "%like%", 0, 1);
        }
        //$this->db->group_by('bu.id');
        return $this->db->get('', 0);
    }

    // End Improve

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        $this->db->insert($this->tbl, $di, 0, 0);
        return $this->db->last_id;
    }
    public function update($id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }

    // public function checkUserRegistration($b_user_id)
    // {
    //     $this->db->select_as('id', 'b_user_id', 0);
    //     $this->db->select_as('cdate', 'cdate', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
    //     $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('2023-10-16')", 'AND', '>=');
    //     $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('2023-10-31')", 'AND', '<=');
    //     return $this->db->get_first('', 1);
    // }

    public function getDataFromUser($b_user_id)
    {
        $this->db->select_as('id', 'b_user_id', 0);
        $this->db->select_as('cdate', 'cdate', 0);
        $this->db->select_as('b_user_id_recruiter', 'b_user_id_recruiter', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first('', 0);
    }

    public function checkUserExist($b_user_id)
    {
        $this->db->select_as('id', 'b_user_id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first('', 0);
    }
}