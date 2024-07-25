<?php
class H_Ticket_History_Model extends JI_Model
{
    public $tbl = 'h_ticket_history';
    public $tbl_as = 'hth';
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

    public function countAll($nation_code, $keyword="")
    {
        // $sql = "SELECT COUNT(*) AS total FROM (SELECT date(a.cdate) AS cdate, a.game_name, SUM(a.total) AS total FROM (SELECT cdate, game_name, count(distinct b_user_id) AS total FROM h_ticket_history WHERE game_name != '' ";

        // if (mb_strlen($keyword)>0) {
        //     $sql .= "AND game_name LIKE '%".$keyword."%' ";
        // }

        // $sql .= "GROUP BY DATE(cdate), HOUR(cdate), game_name) a GROUP BY date(a.cdate)) b ";

        // $d = $this->db->query($sql);
        // if (isset($d[0]->total)) {
        //     return $d[0]->total;
        // }
        // return "0";
        $this->db->select_as("COUNT(DISTINCT CONCAT(DATE($this->tbl_as.cdate), $this->tbl_as.game_name))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.game_name", $this->db->esc(""), "and", "!=");

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.game_name)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 0);
        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="a.cdate", $sort_direction="DESC", $keyword="")
    {
        // //pagination logic
        // $page = ($page * $page_size) - $page_size;

        // $sql = "SELECT date(a.cdate) AS cdate, a.game_name, SUM(a.total) AS total FROM (SELECT cdate, game_name, count(distinct b_user_id) AS total FROM h_ticket_history WHERE game_name != '' ";

        // if (mb_strlen($keyword)>0) {
        //     $sql .= "AND game_name LIKE '%".$keyword."%' ";
        // }

        // $sql .= "GROUP BY DATE(cdate), HOUR(cdate), game_name) a GROUP BY date(a.cdate) ";

        // $sql .= "ORDER BY ".$sort_col." ". $sort_direction." ";

        // $sql .= "LIMIT ".$page.",".$page_size;

        // return $this->db->query($sql);
        $this->db->select_as("DATE($this->tbl_as.cdate)", "cdate", 0);
        $this->db->select_as("$this->tbl_as.game_name", "game_name", 0);
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.game_name", $this->db->esc(""), "and", "!=");

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.game_name)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 0);
        }

        $this->db->group_by("DATE(cdate), game_name");
        $this->db->order_by($sort_col, $sort_direction);
        $this->db->limit($page, $page_size);

        return $this->db->get('object', 0);
    }

    // public function checkId($nation_code, $id)
    // {
    //     $this->db->select_as("COUNT(*)", "jumlah");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

}
