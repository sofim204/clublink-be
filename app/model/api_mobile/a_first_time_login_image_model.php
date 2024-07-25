<?php
class A_First_Time_Login_Image_Model extends JI_Model
{
    public $tbl = 'a_first_time_login_image';
    public $tbl_as = 'aftli';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getAll($nation_code)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->order_by("$this->tbl_as.priority", "ASC");
        return $this->db->get();
    }

}
