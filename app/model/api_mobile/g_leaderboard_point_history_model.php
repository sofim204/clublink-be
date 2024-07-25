<?php
class G_Leaderboard_Point_History_Model extends JI_Model
{
    public $tbl = 'g_leaderboard_point_history';
    public $tbl_as = 'glph';
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
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = ".$this->db->esc($nation_code)." AND  b_user_id = ".$this->db->esc($b_user_id)." AND  LOWER(b_user_alamat_location_kelurahan) = ".$this->db->esc(strtolower($kelurahan))." AND  LOWER(b_user_alamat_location_kecamatan) = ".$this->db->esc(strtolower($kecamatan))." AND  LOWER(b_user_alamat_location_kabkota) = ".$this->db->esc(strtolower($kabkota))." AND  LOWER(b_user_alamat_location_provinsi) = ".$this->db->esc(strtolower($provinsi)).") AND nation_code = ".$this->db->esc($nation_code)." AND  b_user_id = ".$this->db->esc($b_user_id)." AND  LOWER(b_user_alamat_location_kelurahan) = ".$this->db->esc(strtolower($kelurahan))." AND  LOWER(b_user_alamat_location_kecamatan) = ".$this->db->esc(strtolower($kecamatan))." AND  LOWER(b_user_alamat_location_kabkota) = ".$this->db->esc(strtolower($kabkota))." AND  LOWER(b_user_alamat_location_provinsi) = ".$this->db->esc(strtolower($provinsi))." FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
    // }

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

    public function update($nation_code, $b_user_id, $id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="All", $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where('id', $id);
        $this->db->where_as("LOWER(b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER(b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER(b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER(b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        return $this->db->update($this->tbl, $du, 0);
    }

    public function del($nation_code, $id, $b_user_id, $kelurahan, $kecamatan, $kabkota, $provinsi)
    {
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('id', $id);
        $this->db->where_as("b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("LOWER(b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER(b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER(b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER(b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        return $this->db->delete($this->tbl);
    }

    public function checkAlreadyInDB($nation_code, $id="", $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $custom_id, $custom_type, $custom_type_sub, $dateCompare="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_calculated", "is_calculated", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($id != ""){
            $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        }

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        if($dateCompare != ""){
            $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
        }

        return $this->db->get_first('object', 0);
    }

    public function countAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $typeDateCompare)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        
        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        //by Donny Dennison - 25 july 2022 11:40
        //change point get rule for group chat community and upload video product
        // $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
        if($dateCompare != ""){

            if($typeDateCompare == "check in"){
                $this->db->where_as("MONTH($this->tbl_as.cdate)", $this->db->esc(date("m", strtotime($dateCompare))));
                $this->db->where_as("YEAR($this->tbl_as.cdate)", $this->db->esc(date("Y", strtotime($dateCompare))));
            }else{
                $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
            }

        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $typeDateCompare)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        
        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        //by Donny Dennison - 25 july 2022 11:40
        //change point get rule for group chat community and upload video product
        // $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
        if($dateCompare != ""){

            if($typeDateCompare == "check in"){
                $this->db->where_as("MONTH($this->tbl_as.cdate)", $this->db->esc(date("m", strtotime($dateCompare))));
                $this->db->where_as("YEAR($this->tbl_as.cdate)", $this->db->esc(date("Y", strtotime($dateCompare))));
            }else{
                $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
            }

        }

        return $this->db->get('object', 0);
    }

    public function countCheckIn($nation_code, $b_user_id, $startDate, $endDate, $plusorminus="+", $custom_type="check in", $custom_type_sub="daily")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($startDate), "AND", ">=");
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($endDate), "AND", "<=");

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function sumCheckIn($nation_code, $b_user_id, $custom_type="check in", $dateCompare)
    {
        $this->db->select_as("SUM($this->tbl_as.point)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));

        $this->db->where_as("MONTH($this->tbl_as.cdate)", $this->db->esc(date("m", strtotime($dateCompare))));
        $this->db->where_as("YEAR($this->tbl_as.cdate)", $this->db->esc(date("Y", strtotime($dateCompare))));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //START by Donny Dennison - 5 october 2022 15:47
    //activity dashboard feature
    public function sumAccomplishment($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus="", $custom_id="", $custom_type, $custom_type_sub, $dateCompare)
    {
        $this->db->select_as("SUM(CONCAT(plusorminus,point))", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($plusorminus != ""){
            $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        }

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
    }

    public function countAccomplishment($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus="", $custom_id="", $custom_type, $custom_type_sub, $dateCompare)
    {
        $this->db->select_as("SUM(IF(plusorminus = '+', 1, -1))", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($plusorminus != ""){
            $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        }

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
    }

    public function sumAccomplishmentOfferReview($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus="", $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $point)
    {
        $this->db->select_as("SUM(IF(point = $point, CONCAT(plusorminus,point), 0))", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($plusorminus != ""){
            $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        }

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
    }

    public function countAccomplishmentOfferReview($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus="", $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $point)
    {
        $this->db->select_as("SUM(IF(point = $point, 1, 0))", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($plusorminus != ""){
            $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        }

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
    }
    //END by Donny Dennison - 5 october 2022 15:47
    //activity dashboard feature

    // public function getAllStuck($nation_code)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc(0));
    //     $this->db->where_as("$this->tbl_as.main_transaction_id", "IS NULL", "OR", "=", 1, 0);
    //     $this->db->where_as("$this->tbl_as.detail_transaction_id", "IS NULL", "AND", "=", 0, 1);

    //     // $this->db->order_by("$this->tbl_as.cdate", "DESC");
    //     return $this->db->get('object', 0);
    // }

    public function sumPending($nation_code, $b_user_id, $custom_type, $custom_type_sub)
    {
        $this->db->select_as("SUM($this->tbl_as.point)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc(0));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    // public function dataCommunity($nation_code, $b_user_id, $custom_type_sub, $dateCompare)
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.plusorminus)", "total_count", 0);
    //     $this->db->select_as("COALESCE(SUM(CONCAT($this->tbl_as.plusorminus,$this->tbl_as.point)), 0)", "total_sum", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc("+"));
    //     $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc("community"));
    //     // $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("post"), "OR", "=", 1, 0);
    //     // $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload video"), "OR", "=", 0, 0);
    //     // $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload image"), "AND", "=", 0, 1);
    //     $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
    //     $this->db->order_by("$this->tbl_as.id", "ASC");

    //     return $this->db->get_first('object', 0);
    // }

    public function getLatestRecord($nation_code, $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub)
    {
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        $this->db->order_by("$this->tbl_as.cdate", "DESC");

        return $this->db->get_first('object', 0);
    }

    public function getRecordGroupPost($nation_code, $custom_id)
    {
        $this->db->select_as("*, $this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc("club"));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("post"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload image"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload video"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("attendance sheet"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("location"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("commission"), "AND", "=", 0, 1);
        return $this->db->get('object', 0);
    }

    public function countCreateClub($nation_code, $b_user_id, $custom_type="club", $custom_type_sub="create club")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));

        $this->db->where_as("MONTH($this->tbl_as.cdate)", $this->db->esc(date("m", strtotime("NOW"))));
        $this->db->where_as("YEAR($this->tbl_as.cdate)", $this->db->esc(date("Y", strtotime("NOW"))));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
}
