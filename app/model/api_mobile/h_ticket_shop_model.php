<?php
class H_Ticket_Shop_Model extends JI_Model
{
    public $tbl = 'h_ticket_shop';
    public $tbl_as = 'hts';
    // public $tbl2 = 'b_user';
    // public $tbl2_as = 'bu';

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

    // public function getLastId($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     // $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     // $d = $this->db->get_first('', 0);
    //     // if (isset($d->last_id)) {
    //     //     return (int) $d->last_id;
    //     // }
    //     // return 0;
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND  b_user_id = '".$b_user_id."' AND  LOWER(b_user_alamat_location_kelurahan) = '".strtolower($kelurahan)."' AND  LOWER(b_user_alamat_location_kecamatan) = '".strtolower($kecamatan)."' AND  LOWER(b_user_alamat_location_kabkota) = '".strtolower($kabkota)."' AND  LOWER(b_user_alamat_location_provinsi) = '".strtolower($provinsi)."') AND nation_code = '".$nation_code."' AND  b_user_id = '".$b_user_id."' AND  LOWER(b_user_alamat_location_kelurahan) = '".strtolower($kelurahan)."' AND  LOWER(b_user_alamat_location_kecamatan) = '".strtolower($kecamatan)."' AND  LOWER(b_user_alamat_location_kabkota) = '".strtolower($kabkota)."' AND  LOWER(b_user_alamat_location_provinsi) = '".strtolower($provinsi)."' FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
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

    // public function del($nation_code, $id, $b_user_id, $kelurahan, $kecamatan, $kabkota, $provinsi)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("LOWER(b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     $this->db->where_as("LOWER(b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     return $this->db->delete($this->tbl);
    // }

    // public function countAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $typeDateCompare)
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        
    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

    //     //by Donny Dennison - 25 july 2022 11:40
    //     //change point get rule for group chat community and upload video product
    //     // $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
    //     if($dateCompare != ""){

    //         if($typeDateCompare == "check in"){
    //             $this->db->where_as("MONTH($this->tbl_as.cdate)", $this->db->esc(date("m", strtotime($dateCompare))));
    //             $this->db->where_as("YEAR($this->tbl_as.cdate)", $this->db->esc(date("Y", strtotime($dateCompare))));
    //         }else{
    //             $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
    //         }

    //     }

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    public function getAll($nation_code)
    {
        // $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        $this->db->order_by("$this->tbl_as.earned_ticket", "ASC");

        return $this->db->get('object', 0);

    }

    public function getById($nation_code, $id)
    {
        // $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        return $this->db->get_first('object', 0);

    }

}
