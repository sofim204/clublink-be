<?php
class B_User_Setting_model extends SENE_Model
{
    public $tbl = 'b_user_setting';
    public $tbl_as = 'bust';
    public $tbl30 = 'common_code';
    public $tbl30_as = 'cc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl_as.b_lokasi_id");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl4_as.id", "=", "COALESCE($this->tbl_as.b_kodepos_id,0)");
        return $composites;
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

    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function getAddressType($nation_code)
    {
        $this->db->from($this->tbl30, $this->tbl30_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("classified", "address");
        $this->db->where("use_yn", 1);
        return $this->db->get();
    }

    public function getByIdUserId($nation_code, $id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("is_active", 1);
        return $this->db->from($this->tbl)->get_first();
    }

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    public function update($nation_code, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du);
    }
    public function updateByIdUserId($nation_code, $id, $b_user_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl, $du);
    }
    public function delByUserId($nation_code, $id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }
    public function getNotificationValue($nation_code, $b_user_id)
    {
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("classified", "'%setting_notification_%'", 'AND', 'like');
        return $this->db->get();
    }
    public function check($nation_code, $b_user_id, $classified, $code)
    {
        $this->db->select_as("COUNT(*)", 'jumlah', 0);
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("classified", $this->db->esc($classified));
        $this->db->where_as("code", $this->db->esc($code));
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function change($nation_code, $b_user_id, $classified, $code, $value)
    {
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("classified", $this->db->esc($classified));
        $this->db->where_as("code", $this->db->esc($code));
        return $this->db->update($this->tbl, array("setting_value"=>$value));
    }
    public function getValue($nation_code, $b_user_id, $classified, $code)
    {
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("classified", $this->db->esc($classified));
        $this->db->where_as("code", $this->db->esc($code));
        return $this->db->get_first();
    }
}
