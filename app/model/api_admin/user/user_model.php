<?php
class User_Model extends JI_Model
{
    public $tbl_user = 'b_user';
    public $tbl_order = 'd_order';
    public $tbl_product = 'c_produk';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl_user, $this->tbl_user);
    }

    private function __join_user_order()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_user.nation_code", "=", "$this->tbl_order.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_user.id", "=", "$this->tbl_order.b_user_id");
        return $cps;
    }

    private function __join_user_product()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_user.nation_code", "=", "$this->tbl_product.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_user.id", "=", "$this->tbl_product.b_user_id");
        return $cps;
    }

    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }

    public function trans_commit()
    {
        return $this->db->commit();
    }

    public function trans_rollback()
    {
        return $this->db->rollback();
    }

    public function trans_end()
    {
        return $this->db->autocommit(1);
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_user.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl_user, $this->tbl_user);
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
        return $this->db->insert($this->tbl_user, $di, 0, 0);
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
        return $this->db->update($this->tbl_user, $du, 0);
    }
    public function del($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl_user);
    }

    public function getKode($a_company_inisial, $a_company_id="")
    {
        $this->db->flushQuery();
        $this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'urutan', 0);
        $this->db->from($this->tbl_user, $this->tbl_user);
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
        $this->db->from($this->tbl_user, $this->tbl_user);
        $this->db->where('kode', $fnama_inisial, 'and', 'like%');
        $this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'desc');
        return $this->db->get_first('object', 0);
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select('id');
        $this->db->select('image');
        $this->db->select_as($this->__decrypt('fnama'), 'nama', 0);
        $this->db->select_as($this->__decrypt('email'), 'email', 0);
        $this->db->select('is_active');
        //Improve By Aditya Adi Prabowo 7/9/2020
        //Add field device
        $this->db->select_as('device', 'device', 0);
        // End Of Improve
        $this->db->select('is_confirmed');
        //by Donny Dennison - 29 august 2020 12:26
        //add label 2 step verified or not yet
        $this->db->select('telp_is_verif');
        $this->db->select_as('CONCAT(bdate)', 'bdate', 0);
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as('DATE(cdate)', 'cdate', 0);
        $this->db->select_as('nation_code', 'nation_code', 0);
        $this->db->from($this->tbl_user, $this->tbl_user);
        $this->db->where_as("$this->tbl_user.nation_code", $nation_code, "AND", "=", 0, 0);
        if (mb_strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_user.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_user.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (mb_strlen($keyword)>1) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
        }

        //by Donny Dennison - 27 January 2021 17:17
        //change chat to open chatting
        if($page != -1 && $pagesize != -1){
            
            $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        
        }

        return $this->db->get("object", 0);
    }

    public function countAll($nation_code, $keyword="", $is_confirmed="", $is_active="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl_user, $this->tbl_user);
        $this->db->where_as("$this->tbl_user.nation_code", $nation_code, "AND", "=", 0, 0);
        if (mb_strlen($is_confirmed)) {
            $this->db->where_as("$this->tbl_user.is_confirmed", $this->db->esc($is_confirmed), "AND", "=", 0, 0);
        }
        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_user.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }
        if (mb_strlen($keyword)>1) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_user.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function get_by_id($nation_code, $id)
    {
        $this->db->select_as("*, $this->tbl_user.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->from($this->tbl_user, $this->tbl_user);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getByIdTakedown($nation_code, $b_user_id)
    {
        $this->db->select_as("*, $this->tbl_user.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->select_as("$this->tbl_user.device", "device", 0);
        $this->db->select_as("$this->tbl_user.fcm_token", "fcm_token", 0);
        $this->db->from($this->tbl_user, $this->tbl_user);
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
        $d = $this->db->from($this->tbl_user)->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function updateUmur($id=0)
    {
        $sql = "UPDATE $this->tbl_user SET `umur` = DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), bdate)), '%Y')+0 WHERE";
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
        $this->db->from($this->tbl_user, $this->tbl_user);

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
        $d = $this->db->from($this->tbl_user)->get_first("object", 0);
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
                $res = $this->db->update($this->tbl_user, $du);
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
        $this->db->from($this->tbl_user, $this->tbl_user);
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
        return $this->db->update($this->tbl_user, $du);
    }
    
    public function getYangAdaNotifnya()
    {
        $this->db->where_as("fcm_token", "", "AND", "!=");
        $this->db->where_as("device", "ios", "OR", "like%%", 1, 0);
        $this->db->where_as("device", "android", "OR", "like%%", 0, 1);
        return $this->db->get();
    }
}
