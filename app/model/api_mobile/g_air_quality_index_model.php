<?php
class G_Air_Quality_Index_Model extends JI_Model
{
    public $tbl = 'g_air_quality_index';
    public $tbl_as = 'gaqi';
    
    // public $tbl2 = 'c_community';
    // public $tbl2_as = 'cc';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function trans_start() {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }

    public function trans_commit() {
        return $this->db->commit();
    }

    public function trans_rollback() {
        return $this->db->rollback();
    }

    public function trans_end() {
        return $this->db->autocommit(1);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function getTbl2As()
    // {
    //     return $this->tbl2_as;
    // }

    // public function getLastId($nation_code)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
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

    // public function update($nation_code, $id, $du) {
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function countAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4'");
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", '1');

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
    //     $this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
    //     $this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
    //     $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
    //     $this->db->select_as("$this->tbl2_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl2_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.alamat2").',"")', "alamat2", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

    //     //by Donny Dennison - 14 july 2021 14:14
    //     //add-general-location-in-address
    //     // $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), 'left');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));;
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1')); 
        
    //     // //advanced filter
    //     // if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {

    //     //     $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);

    //     // }
    //     // if (intval($b_user_id)>0) {
    //     //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     // }
    //     // if (mb_strlen($keyword)>0) {
    //     //     $this->db->where_as("LOWER($this->tbl_as.title)", strtolower($keyword), 'or', '%like%', 1, 0);
    //     //     $this->db->where_as("LOWER($this->tbl_as.deskripsi)", strtolower($keyword), 'or', '%like%');
    //     //     $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
    //     // }

    //     // $this->db->order_by($sort_col, $sort_direction);
    //     // $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    public function getName($nation_code, $pm2_5, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("IF($this->tbl_as.custom_name IS NULL OR $this->tbl_as.custom_name = '', $this->tbl_as.original_name, $this->tbl_as.custom_name)", "name", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.custom_name IS NOT NULL AND $this->tbl_as.custom_name != '', $this->tbl_as.custom_name, $this->tbl_as.original_name)", "name", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.range_min", $pm2_5, "AND", "<=");
        $this->db->where_as("$this->tbl_as.range_max", $pm2_5, "AND", ">=");
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1')); 
        
        return $this->db->get_first('object', 0);
    }

}