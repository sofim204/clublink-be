<?php
class B_User_Alamat_Model extends JI_Model
{
    public $tbl = 'b_user_alamat';
    public $tbl_as = 'buam';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function countAll($keyword='')
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $d = $this->db->from($this->tbl, $this->tbl_as)->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function del($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }

    public function getAll($page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="")
    {
        $d = $this->db->query("
            SELECT  a.*, b.nama_provinsi, c.nama_kabkota
            FROM    b_user_alamat a
            INNER   JOIN d_provinsi b ON a.provinsi = b.id
            INNER   JOIN d_kabkota c ON a.kabkota = c.id
        ");
        return $d;
    }

    public function getById($id)
    {
        $this->db->select();
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        if (isset($di['penerima_nama'])) {
            if (strlen($di['penerima_nama'])) {
                $di['penerima_nama'] = $this->__encrypt($di['penerima_nama']);
            }
        }
        if (isset($di['penerima_telp'])) {
            if (strlen($di['penerima_telp'])) {
                $di['penerima_telp'] = $this->__encrypt($di['penerima_telp']);
            }
        }
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        
        $di = array_merge(array('is_active' => 1), $di);
        $this->db->insert($this->tbl, $di, 0, 0);
        return $this->db->last_id;
    }

    public function update($id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        if (isset($du['penerima_nama'])) {
            if (strlen($du['penerima_nama'])) {
                $du['penerima_nama'] = $this->__encrypt($du['penerima_nama']);
            }
        }
        if (isset($du['penerima_telp'])) {
            if (strlen($du['penerima_telp'])) {
                $du['penerima_telp'] = $this->__encrypt($du['penerima_telp']);
            }
        }
        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        return $this->db->get();
    }
}
