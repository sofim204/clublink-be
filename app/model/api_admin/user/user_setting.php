<?php
class User_Setting extends SENE_Model
{
    public $tbl_user_setting = 'b_user_setting';
    public $tbl_common = 'common_code';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl_user_setting, $this->tbl_user_setting);
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

    public function get_last_id($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_user_setting.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl_user_setting, $this->tbl_user_setting);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function get_address_type($nation_code)
    {
        $this->db->from($this->tbl_common, $this->tbl_common);
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
        return $this->db->from($this->tbl_user_setting)->get_first();
    }

    public function set($di)
    {
        return $this->db->insert($this->tbl_user_setting, $di, 0, 0);
    }
    public function update($nation_code, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl_user_setting, $du);
    }
    public function updateByIdUserId($nation_code, $id, $b_user_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl_user_setting, $du);
    }
    public function delByUserId($nation_code, $id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl_user_setting);
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
        return $this->db->update($this->tbl_user_setting, array("setting_value"=>$value));
    }
    public function get_value($nation_code, $b_user_id, $classified, $code)
    {
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("classified", $this->db->esc($classified));
        $this->db->where_as("code", $this->db->esc($code));
        return $this->db->get_first();
    }
}
