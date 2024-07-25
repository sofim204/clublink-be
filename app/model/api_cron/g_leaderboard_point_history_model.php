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

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    //START by Donny Dennison - 11 October 2022 14:28
    //integrate api blockchain
    // public function updateByMainTransactionId($nation_code, $main_transaction_ids, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where_in('main_transaction_id', $main_transaction_ids);
    //     return $this->db->update($this->tbl, $du, 0);
    // }
    //END by Donny Dennison - 11 October 2022 14:28
    //integrate api blockchain

    // public function del($nation_code, $id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="", $dateCompare)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($kelurahan != ""){
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        if($b_user_id != ""){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }

        if($custom_id != ""){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        if($plusorminus != ""){
            $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
        }

        if($custom_type != ""){
            $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
        }

        if($custom_type_sub != ""){
            $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
        }

        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare),"AND","<=");
        $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("0"));

        $this->db->order_by("CONCAT($this->tbl_as.b_user_id, '-', $this->tbl_as.custom_type, '-', $this->tbl_as.custom_type_sub)", "ASC");

        $this->db->limit("200");
        return $this->db->get('object', 0);
    }

    // public function checkAlreadyInDB($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="", $dateCompare="", $main_transaction_id="", $detail_transaction_id="")
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     if($b_user_id != ""){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     if($plusorminus != ""){
    //         $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     }

    //     if($custom_type != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     }

    //     if($custom_type_sub != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     }

    //     if($dateCompare != ""){
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare),"AND","<=");
    //     }

    //     if($main_transaction_id != ""){
    //         $this->db->where_as("$this->tbl_as.main_transaction_id", $this->db->esc($main_transaction_id));
    //     }

    //     if($detail_transaction_id != ""){
    //         $this->db->where_as("$this->tbl_as.detail_transaction_id", $this->db->esc($detail_transaction_id));
    //     }

    //     // $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("0"));

    //     // $this->db->order_by("CONCAT($this->tbl_as.b_user_id, '-', $this->tbl_as.custom_type, '-', $this->tbl_as.custom_type_sub)", "ASC");

    //     return $this->db->get_first('object', 0);
    // }

    //START by Donny Dennison - 11 October 2022 14:28
    //integrate api blockchain
    // public function getDataForApiBlockChain($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="")
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     if($b_user_id != ""){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     if($plusorminus != ""){
    //         $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     }

    //     if($custom_type != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     }

    //     if($custom_type_sub != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     }

    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("1"));
    //     $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc("0"));
    //     $this->db->where_as("$this->tbl_as.main_transaction_id", "IS NOT NULL");
    //     $this->db->where_as("$this->tbl_as.detail_transaction_id", "IS NOT NULL");

    //     $this->db->order_by("CONCAT($this->tbl_as.main_transaction_id, '|', $this->tbl_as.b_user_id, '|', $this->tbl_as.detail_transaction_id)", "ASC");

    //     return $this->db->get('object', 0);
    // }
    //END by Donny Dennison - 11 October 2022 14:28
    //integrate api blockchain

    // public function getMainTransactionIdForApiBlockChain($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="")
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.main_transaction_id", "main_transaction_id", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     if($b_user_id != ""){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     if($plusorminus != ""){
    //         $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     }

    //     if($custom_type != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     }

    //     if($custom_type_sub != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     }

    //     // $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($maxDate), "AND", "<=");
    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("1"));
    //     $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc("0"));
    //     $this->db->where_as("$this->tbl_as.main_transaction_id", "IS NOT NULL");
    //     $this->db->where_as("$this->tbl_as.detail_transaction_id", "IS NOT NULL");

    //     $this->db->order_by("$this->tbl_as.cdate", "DESC");
    //     $this->db->limit("1");

    //     return $this->db->get('object', 0);
    // }

    // public function getDataForApiBlockChainByMainTransactionId($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="", $main_transaction_ids)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     if($b_user_id != ""){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     if($plusorminus != ""){
    //         $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     }

    //     if($custom_type != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     }

    //     if($custom_type_sub != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     }

    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("1"));
    //     $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc("0"));
    //     $this->db->where_as("$this->tbl_as.main_transaction_id", "IS NOT NULL");
    //     $this->db->where_as("$this->tbl_as.detail_transaction_id", "IS NOT NULL");
    //     $this->db->where_in("$this->tbl_as.main_transaction_id", $main_transaction_ids);

    //     $this->db->order_by("CONCAT($this->tbl_as.main_transaction_id, '|', $this->tbl_as.b_user_id, '|', $this->tbl_as.detail_transaction_id)", "ASC");

    //     return $this->db->get('object', 0);
    // }

    public function getAllStuck($nation_code)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.blockchain_api_called", $this->db->esc(0));
        // $this->db->where_as("$this->tbl_as.main_transaction_id", "IS NULL", "OR", "=", 1, 0);
        // $this->db->where_as("$this->tbl_as.detail_transaction_id", "IS NULL", "AND", "=", 0, 1);

        // $this->db->order_by("$this->tbl_as.cdate", "DESC");
        return $this->db->get('object', 0);
    }
}
