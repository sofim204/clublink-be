<?php
class C_Produk_Laporan_Model extends JI_Model
{
    public $tbl = 'c_produk_laporan';
    public $tbl_as = 'cp';
    public $tbl2 = 'c_produk';
    public $tbl2_as = 'cp';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->is_debug = 0;
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    public function getTbl2As()
    {
        return $this->tbl2_as;
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
        }
        return 0;
    }
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    public function update($nation_code, $b_user_id, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $id, $b_user_id)
    {
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }
    public function getByUserId($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="")
    {
        $this->db->select_as("$this->tbl2_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl2_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }
        $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
        return $this->db->get('object', 0);
    }
    public function countByUserId($nation_code, $b_user_id, $keyword="")
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $b_user_id, 'AND', '=');
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl2_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function check($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->get_first();
    }
}
