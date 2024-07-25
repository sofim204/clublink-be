<?php
class G_Convert_History_Model extends JI_Model
{
    public $tbl = 'g_convert_history';
    public $tbl_as = 'gch';
    // public $tbl2 = 'c_community_category';
    // public $tbl2_as = 'ccc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_community_category_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
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

    // public function set2($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert_ignore($this->tbl, $di, 0, 0);
    // }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function updateMass($nation_code, $b_user_id, $ids, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $ids);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function deleteMass($nation_code, $b_user_id, $pids)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $pids);
    //     return $this->db->delete($this->tbl);
    // }

    // public function countAll($nation_code, $b_user_id, $fromdate, $todate)
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     // $this->db->where_as("$this->tbl_as.type", $this->db->esc("spt to bbt"));
    //     // $this->db->where_as("$this->tbl_as.status", $this->db->esc("completed"));
    //     if($fromdate != ''){
    //         $this->db->where_as("DATE($this->tbl_as.completed_date)", $this->db->esc($fromdate), "AND", ">=");
    //         $this->db->where_as("DATE($this->tbl_as.completed_date)", $this->db->esc($todate), "AND", "<=");
    //     }

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return "0";
    // }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="DESC", $b_user_id, $fromdate, $todate)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("CONCAT($this->tbl_as.title, ' (', $this->tbl_as.status, ')')", "title", 0);
        $this->db->select_as("CONCAT($this->tbl_as.plusorminus, $this->tbl_as.amount_spt)", "amount_spt", 0);
        $this->db->select_as("$this->tbl_as.status", "status");
        $this->db->select_as("$this->tbl_as.cdate", "cdate");

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // $this->db->where_as("$this->tbl_as.type", $this->db->esc("spt to bbt"));
        // $this->db->where_as("$this->tbl_as.status", $this->db->esc("completed"));
        if($fromdate != ''){
            $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($fromdate), "AND", ">=");
            $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($todate), "AND", "<=");
        }

        $this->db->order_by($sort_col, $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    // public function getById($nation_code, $id)
    // {
    //     // $this->db->select_as("*,$this->tbl_as.id", "id", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     return $this->db->get_first('object', 0);
    // }

    public function getByUserIdStatusProcessing($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.status", $this->db->esc("processing"));
        // $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 1 MINUTE)", "NOW()", "AND", ">=");
        return $this->db->get_first('object', 0);
    }
}
