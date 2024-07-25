<?php
class B_User_BankAcc_Model extends JI_Model
{
    public $tbl = 'b_user_bankacc';
    public $tbl_as = 'buba';
    public $tbl2 = 'a_bank';
    public $tbl2_as = 'ab';
        
    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.a_bank_id", "=", "$this->tbl2_as.id");
        return $cps;
    }
    
    public function getLastId($nation_code, $b_user_id, $a_bank_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("a_bank_id", $a_bank_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
    public function getById($nation_code, $b_user_id, $a_bank_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "norek", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.a_bank_id", $this->db->esc($a_bank_id));
        return $this->db->get_first();
    }
    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.a_bank_id", "id", 0);
        $this->db->select_as("$this->tbl2_as.nama", "bank", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "norek", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        return $this->db->get_first();
    }
    public function countByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as, 0);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getAll($nation_code, $b_user_id)
    {
        $this->db->select("*");
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "norek", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->order_by("a_bank_id", "DESC");
        return $this->db->get();
    }
    public function set($di)
    {
        if (isset($di['nama'])) {
            if (strlen($di['nama'])) {
                $di['nama'] = $this->__encrypt($di['nama']);
            }
        }
        if (isset($di['nomor'])) {
            if (strlen($di['nomor'])) {
                $di['nomor'] = $this->__encrypt($di['nomor']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    public function update($nation_code, $b_user_id, $du)
    {
        if (isset($du['nama'])) {
            if (strlen($du['nama'])) {
                $du['nama'] = $this->__encrypt($du['nama']);
            }
        }
        if (isset($du['nomor'])) {
            if (strlen($du['nomor'])) {
                $du['nomor'] = $this->__encrypt($du['nomor']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function delete($nation_code, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }
}
