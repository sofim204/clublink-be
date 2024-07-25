<?php
class B_User_model extends SENE_Model
{
    public $tbl = 'b_user';
    public $tbl_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getByApiWeb($api_web_token)
    {
        $this->db->where('api_web_token', $api_web_token);
        $this->db->from($this->tbl, $this->tbl_as);
        return $this->db->get_first('object', 0);
    }

    public function getByApiRegToken($api_reg_token)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->where('api_reg_token', $api_reg_token);
        $this->db->from($this->tbl, $this->tbl_as);
        return $this->db->get_first('object', 0);
    }

    public function edit($nation_code, $id, $data)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $data, 0);
    }
    
    public function getYangAdaNotifnya()
    {
        $this->db->where_as("fcm_token", "", "AND", "!=");
        $this->db->where_as("device", "ios", "OR", "like%%", 1, 0);
        $this->db->where_as("device", "android", "OR", "like%%", 0, 1);
        return $this->db->get();
    }
}
