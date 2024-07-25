<?php
class B_User_Card_Type_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'b_user_card_type';
    public $tbl_as = 'buct';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }
    
    // public function getLastId($nation_code, $b_user_id)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return $d->last_id;
    //     }
    //     return 0;
    // }
    // public function getById($nation_code, $b_user_id, $id)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->get_first();
    // }
    public function getAll($nation_code)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.url", "url", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        return $this->db->get();
    }
    // public function set($di)
    // {
    //     if (isset($di['nomor'])) {
    //         if (strlen($di['nomor'])) {
    //             $di['nomor'] = $this->__encrypt($di['nomor']);
    //         }
    //     }
    //     return $this->db->insert($this->tbl, $di);
    // }
    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (isset($du['nomor'])) {
    //         if (strlen($du['nomor'])) {
    //             $du['nomor'] = $this->__encrypt($du['nomor']);
    //         }
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->update($this->tbl, $du);
    // }
    // public function delete($nation_code, $b_user_id, $id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->delete($this->tbl);
    // }
}
