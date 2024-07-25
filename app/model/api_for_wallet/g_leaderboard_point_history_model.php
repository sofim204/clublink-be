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

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function getAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub, $dateCompare, $typeDateCompare)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
        
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

    //     return $this->db->get('object', 0);
    // }

//     public function getById($nation_code, $pid)
//     {
//         $this->db->select_as("$this->tbl_as.id", "id", 0);

//         $this->db->from($this->tbl, $this->tbl_as);
//         $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
//         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
//         $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
//         $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
//         $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');
//         // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');
//         $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
//         $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
//         $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

//         $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
//         return $this->db->get_first('', 0);
//     }

    // public function getByMaintransactionidDetailtransactionid($nation_code, $main_transaction_id, $detail_transaction_id)
    // {
    //     $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.main_transaction_id", $this->db->esc($main_transaction_id));
    //     $this->db->where_as("$this->tbl_as.detail_transaction_id", $this->db->esc($detail_transaction_id));

    //     return $this->db->get_first('object', 0);
    // }

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

    public function getCustom($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.custom_type", "custom_type", 0);
        $this->db->select_as("$this->tbl_as.custom_type_sub", "custom_type_sub", 0);
        $this->db->select_as("COUNT($this->tbl_as.custom_type)", "total_count_transaction", 0);
        $this->db->select_as("SUM(CONCAT($this->tbl_as.plusorminus, $this->tbl_as.point))", "total_spt_get", 0);
        $this->db->select_as("MAX($this->tbl_as.cdate)", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->group_by("CONCAT($this->tbl_as.custom_type,'-',$this->tbl_as.custom_type_sub)");

        return $this->db->get('object', 0);
    }
}
