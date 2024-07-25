<?php
class G_Leaderboard_Ranking_Model extends JI_Model
{
    public $tbl = 'g_leaderboard_ranking';
    public $tbl_as = 'glr';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    /**
     * Composite join for multiple PK on table 2
     * @return array composites join
     */
    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    // private function __joinTbl3()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.b_user_id");
    //     $composites[] = $this->db->composite_create("1", "=", "$this->tbl3_as.is_default");
    //     return $composites;
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function getLastId($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($kelurahan));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", $this->db->esc($kecamatan));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", $this->db->esc($kabkota));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_provinsi", $this->db->esc($provinsi));
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    /**
     * Insert into database
     * @param array $di name value pair describes column and value to insert into table
     */
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function updateTotal($nation_code, $b_user_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id';");
    }

    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function delAll($nation_code)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     return $this->db->delete($this->tbl);
    // }

    public function getByUserId($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="All")
    {
        // $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "ranking", 0);
        // $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_nama", 0);
        // $this->db->select_as("$this->tbl2_as.image", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_post,0)", "total_post", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_point,0)", "total_point", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('old'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));

        return $this->db->get_first('object', 0);
    }

    public function countAll($nation_code, $page=0, $page_size=0, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('old'));
        $this->db->where_as("$this->tbl_as.total_point", $this->db->esc('0'),"AND",">");
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page=0, $page_size=0, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "ranking", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_nama", 0);
        $this->db->select_as("$this->tbl2_as.image", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_post,0)", "total_post", 0);
        $this->db->select_as("COALESCE($this->tbl_as.total_point,0)", "total_point", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('old'));
        $this->db->where_as("$this->tbl_as.total_point", $this->db->esc('0'),"AND",">");
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));

        $this->db->order_by("id", "ASC");
        
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

}
