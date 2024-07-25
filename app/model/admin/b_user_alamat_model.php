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

    public function getById($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", 'id', 0);
        // $this->db->select_as($this->__decrypt($this->tbl_as.".alamat2"), 'alamat2', 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan,', ',$this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as($this->__decrypt($this->tbl_as.".penerima_nama"), 'penerima_nama', 0);
        $this->db->select_as($this->__decrypt($this->tbl_as.".penerima_telp"), 'penerima_telp', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->get_first('', 0);
    }

    public function getDetailAddress($nation_code,$is_active,$b_user_id)
    {
        $this->db->select_as("COALESCE($this->tbl_as.judul,'-')", "judul", 0);
        $this->db->select_as("COALESCE($this->tbl_as.catatan,'-')", "catatan", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan,', ',$this->tbl_as.kecamatan,', ',$this->tbl_as.kabkota)", "alamat2_full", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        $this->db->order_by("id", "DESC");
        return $this->db->get("object",0); 
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
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
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function getByUserId($nation_code, $b_user_id)
    {
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.*, $this->tbl_as.alamat", "alamat1", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        return $this->db->get();
    }
    public function getDetailByUserIdAndId($nation_code, $b_user_id, $id)
    {
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.*, $this->tbl_as.alamat", "alamat1", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("$this->tbl_as.catatan", "address_notes", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.id", $id);
        return $this->db->get_first();
    }
    public function getDefaultAddress($nation_code, $b_user_id)
    {
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.*, $this->tbl_as.alamat", "alamat1", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->order_by("$this->tbl_as.is_default", "desc");
        return $this->db->get_first();
    }
}
