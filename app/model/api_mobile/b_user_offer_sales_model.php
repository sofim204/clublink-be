<?php
class B_User_Offer_Sales_Model extends JI_Model
{
    public $tbl = 'b_user_offer_sales';
    public $tbl_as = 'buos';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // public function getLastId($nation_code)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("year", date("Y"));
    //     $this->db->where("month", date("m"));
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return $d->last_id;
    //     }
    //     return 0;
    // }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }

        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function update($nation_code, $b_user_id, $year, $month, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }

    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("year", $year);
    //     $this->db->where("month", $month);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function updateTotal($nation_code, $b_user_id, $year, $month, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id' AND year = '$year' AND month = '$month';");
    }

    public function getByUserId($nation_code, $b_user_id, $year, $month)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.year", $this->db->esc($year));
        $this->db->where_as("$this->tbl_as.month", $this->db->esc($month));
        return $this->db->get_first();
    }

    // public function detail($nation_code, $id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.id", "b_user_id_seller", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
    //     $this->db->select_as("'0'", "rating", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.is_online", "is_online", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id), "AND", "=");
    //     return $this->db->get_first('', 0);
    // }

}
