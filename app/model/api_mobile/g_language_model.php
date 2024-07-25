<?php
class G_Language_Model extends JI_Model
{
    public $tbl = 'g_language';
    public $tbl_as = 'gl';
    // public $tbl2 = 'c_community';
    // public $tbl2_as = 'cc';
    // public $tbl3 = 'b_user';
    // public $tbl3_as = 'bu';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    // private function __joinTbl3()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
    //     return $composites;
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function getLastId($nation_code, $b_user_alamat_location_postal_district='00')
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_alamat_location_postal_district", $b_user_alamat_location_postal_district);
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    // public function set($du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $du, 0, 0);
    // }

    // // public function update($nation_code, $id, $b_user_alamat_location_postal_district='00', $du) {
    // //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    // //     $this->db->where('id', $id);
    // //     $this->db->where("b_user_alamat_location_postal_district", $b_user_alamat_location_postal_district);
    // //     return $this->db->update($this->tbl, $du, 0);
    // // }

    // // public function countAll($nation_code, $b_user_id="")
    // // {
    // //     $this->db->exec("SET NAMES 'UTF8MB4'");
    // //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    // //     $this->db->from($this->tbl, $this->tbl_as);
    // //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    // //     $this->db->where_as("$this->tbl_as.is_active", '1');

    // //     $d = $this->db->get_first('object', 0);
    // //     if (isset($d->total)) {
    // //         return $d->total;
    // //     }
    // //     return 0;
    // // }

    public function getAll($nation_code)
    {
        $this->db->select_as("$this->tbl_as.id", "language_id", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->order_by("$this->tbl_as.priority", "asc");
        
        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $language_id)
    {
        $this->db->select_as("$this->tbl_as.id", "language_id", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($language_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        
        return $this->db->get_first('object', 0);
    }

    //  public function countAllByPostalDistrict($nation_code, $postal_district='00')
    // {
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_postal_district", $this->db->esc($postal_district));
    //     $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
    //     $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAllByPostalDistrict($nation_code, $postal_district='00')
    // {
    //     $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    //     $this->db->select_as("$this->tbl2_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl2_as.c_community_category_id", "c_community_category_id", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_postal_district", $this->db->esc($postal_district));
    //     $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
    //     $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');
        
    //     $this->db->order_by("$this->tbl_as.priority", 'ASC');

    //     $this->db->limit(0, 10);
        
    //     return $this->db->get('object', 0);
    // }

}