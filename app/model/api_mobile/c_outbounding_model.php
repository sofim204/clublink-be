<?php
class C_Outbounding_Model extends JI_Model
{
    public $tbl = 'c_outbounding';
    public $tbl_as = 'co';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getAll($nation_code,$page=1,$pagesize=10,$sortCol="cdate",$sortDir="desc"){
        $this->db->select_as("$this->tbl_as.nation_code","nation_code",0);
        $this->db->select_as("'0'","b_user_id",0);
        $this->db->select_as("$this->tbl_as.id","id",0);
        $this->db->select_as("$this->tbl_as.judul","judul",0);
        $this->db->select_as("$this->tbl_as.teks","teks",0);
        $this->db->select_as("'outbounding'","type",0);
        $this->db->select_as("'media/pemberitahuan/outbounding.png'","gambar",0);
        $this->db->select_as("concat('{\"id\":',$this->tbl_as.id,'}')","extras",0);
        $this->db->select_as("$this->tbl_as.cdate","cdate",0);
        $this->db->select_as("'1'","is_read",0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where("nation_code",$nation_code);
        $this->db->where("is_active",1);
        $this->db->order_by($sortCol,$sortDir)->page($page,$pagesize);
        return $this->db->get("object",0);
    }

    public function getById($nation_code,$id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as("$this->tbl_as.teks", "teks", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first();
    }

    public function updateTotalData($field_data, $plusormin, $total, $outbound_id)
    {
        $this->db->exec("UPDATE `$this->tbl` SET $field_data = $field_data $plusormin $total WHERE id = '$outbound_id';");
    }

}
