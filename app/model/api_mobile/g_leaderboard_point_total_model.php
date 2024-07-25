<?php
class G_Leaderboard_Point_Total_Model extends JI_Model
{
    public $tbl = 'g_leaderboard_point_total';
    public $tbl_as = 'glpt';
    // public $tbl2 = 'b_user';
    // public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

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

    // public function updateTotal($nation_code, $b_user_id, $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = $parameter $operator $total
    //         WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id';");
    // }

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
}
