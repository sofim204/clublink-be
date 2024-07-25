<?php
class C_Community_Like_History_Model extends JI_Model
{
    public $tbl = 'c_community_like_history';
    public $tbl_as = 'cclh';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    // public function update($nation_code, $b_user_id, $id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="All", $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where('id', $id);
    //     $this->db->where_as("LOWER(b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function del()
    {
        // $this->db->where_as('nation_code', $this->db->esc($nation_code));
        // $this->db->where_as("custom_id", $this->db->esc($custom_id));
        // $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        // return $this->db->delete($this->tbl);
        $sql = 'TRUNCATE `c_community_like_history`';
        return $this->db->exec($sql);
    }

    // public function checkAlreadyInDB($nation_code, $custom_id, $b_user_id)
    // {
    //     // $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     // $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     // $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
    //     // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     // $this->db->select_as("$this->tbl_as.is_calculated", "is_calculated", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     return $this->db->get_first('object', 0);
    // }

    // public function countAll($nation_code, $b_user_id)
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $b_user_id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     return $this->db->get('object', 0);
    // }

}
