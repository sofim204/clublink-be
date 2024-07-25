<?php
class G_Leaderboard_Point_Total_Model extends JI_Model
{
    public $tbl = 'g_leaderboard_point_total';
    public $tbl_as = 'glpt';
    // public $tbl2 = 'b_user';
    // public $tbl2_as = 'bu';
    // public $tbl3 = 'b_user_alamat';
    // public $tbl3_as = 'bua';
    // public $tbl4 = 'b_user_alamat_location';
    // public $tbl4_as = 'bual';

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

    // private function __joinTbl3()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.b_user_id");
    //     $composites[] = $this->db->composite_create("1", "=", "$this->tbl3_as.is_default");
    //     return $composites;
    // }

    // private function __joinTbl4()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $composites[] = $this->db->composite_create("SUBSTR($this->tbl3_as.kodepos,1,2)", "=", "$this->tbl4_as.postal_sector");
    //     return $composites;
    // }

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function update($nation_code, $b_user_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function updateTotal($nation_code, $b_user_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = $parameter $operator $total
            WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id';");
    }

    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_post,0)", "total_post", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_point,0)", "total_point", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        return $this->db->get_first('object', 0);
    }

    // public function getAll($nation_code, $page=0, $page_size=0, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $type="All")
    // {
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_nama", 0);
    //     $this->db->select_as("$this->tbl2_as.image", "b_user_image", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.total_post,0)", "total_post", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.total_point,0)", "total_point", 0);
    //     $this->db->select_as("ROW_NUMBER() OVER(ORDER BY COALESCE($this->tbl_as.total_point,0) DESC)", "ranking", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

    //     if($type != "All"){
    //         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     }

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

    //     if($type == 'neighborhood'){
    //         $this->db->where_as("LOWER($this->tbl3_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }else if($type == 'district'){
    //         $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }else if($type == 'city'){
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }else if($type == 'province'){
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     $this->db->order_by("total_point", "DESC");

    //     if($page != 0){
    //         $this->db->page($page, $page_size);
    //     }

    //     return $this->db->get('object', 0);
    // }
}
