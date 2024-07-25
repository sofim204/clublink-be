<?php
class B_User_Bankacc_Model extends JI_Model
{
    public $tbl = 'b_user_bankacc';
    public $tbl_as = 'bub';
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
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl_as.a_bank_id");
        return $cps;
    }
    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="ASC", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select('nation_code');
        $this->db->select('b_user_id');
        $this->db->select('a_bank_id');
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select('is_default');
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.nama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.nomor"), addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function countAll($nation_code, $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.nama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as($this->__decrypt("$this->tbl_as.nomor"), addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE($this->tbl2_as.nama,'-')", "nama_bank", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as("$this->tbl_as.a_bank_id", "a_bank_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        return $this->db->get_first('', 0);
    }
    public function getById($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE($this->tbl2_as.nama,'-')", "nama_bank", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as("$this->tbl_as.a_bank_id", "a_bank_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->get_first();
    }
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
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
        if (!is_array($du)) {
            return 0;
        }
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
        return $this->db->update($this->tbl, $du, 1);
    }
    public function del($nation_code, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
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
        $this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
}
