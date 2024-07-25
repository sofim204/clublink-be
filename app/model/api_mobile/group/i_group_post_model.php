<?php
class I_Group_Post_Model extends JI_Model
{
    public $tbl = 'i_group_post';
    public $tbl_as = 'igp';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'i_group_participant';
    public $tbl3_as = 'igp2';
    public $tbl4 = 'i_group';
    public $tbl4_as = 'ig';
    public $tbl5 = 'i_group_bookmark_post';
    public $tbl5_as = 'igbp';
    public $tbl6 = 'i_group_post_attachment';
    public $tbl6_as = 'igpa';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.i_group_id", "=", "$this->tbl3_as.i_group_id");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.i_group_id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    private function __joinTbl5()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl5_as.i_group_post_id");
        return $composites;
    }

    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl6_as.i_group_post_id");
        return $composites;
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

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

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

    // public function updateByUserId($nation_code, $b_user_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function updateByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$id';");
    }

//     public function incrementView($nation_code, $id, $video_id, $parameter, $operator, $total)
//     {
//         return $this->db->exec("UPDATE `$this->tbl31` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
//             WHERE nation_code = '$nation_code' AND jenis = 'video' AND id = '$video_id' AND c_community_id = '$id';");
//     }

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

    public function getTblAs()
    {
        return $this->tbl_as;
    }

//     public function countAllByUserId($nation_code, $b_user_id)
//     {
//         $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        
//         $this->db->from($this->tbl, $this->tbl_as);
//         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
//         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
//         $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
//         $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
//         $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

//         $d = $this->db->get_first('object', 0);
//         if (isset($d->total)) {
//             return $d->total;
//         }
//         return 0;
//     }

//     public function countAll($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal")
//     {
//         $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
//         $this->db->from($this->tbl, $this->tbl_as);
//         $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
//         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

//         //START by Donny Dennison - 30 november 2022 16:31
//         //new feature, manage group member
//         if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
//             $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
//             $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
//         }
//         //END by Donny Dennison - 30 november 2022 16:31
//         //new feature, manage group member

//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
//         $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
//         $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
//         $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

//         //advanced filter
//         if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
//             // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
//             $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
//             // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
//             // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
//         }
//         if ($b_user_id>'0') {
//             $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
//         }
//         if (mb_strlen($keyword)>0) {
//             $this->db->where_as("LOWER($this->tbl_as.title)", addslashes(strtolower($keyword)), 'or', '%like%', 1, 0);
//             $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 1);
//             // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
//             // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
//             // $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
//         }

//         //by Donny Dennison - 1 desember 2020 16:29
//         //list-produt-sameStreet-neighborhood-all-from-user-address
//         //START by Donny Dennison - 1 desember 2020 16:29

//         if(isset($pelangganAddress->alamat2)){

//             // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
//             $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

//             if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
//             }
            
//             if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
//             }

//             if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
//                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
//             }

//             if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                
//                 $totalSpace = strpos($pelangganAddress->alamat2," ");

//                 $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

//                 if (strpos($tempAlamat2, ' ') !== false) {

//                     $totalSpace += strpos($tempAlamat2, ' ');

//                     $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
//                 }
//                 unset($totalSpace, $tempAlamat2);
            
//             }
            
//             if($type == 'sameStreet'){

//                 $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
//                 $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
//                 $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
//                 $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
//                 $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

//             }else if($type == 'neighborhood'){

//                 $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
//                 $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
//                 $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
//                 $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

//             }else if($type == 'district'){

//                 $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
//                 $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
//                 $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

//             }else if($type == 'city'){
                
//                 $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
//                 $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

//             }else if($type == 'province'){
                
//                 $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

//             }

//         }
//         //END by Donny Dennison - 1 desember 2020 16:29

//         //START by Donny Dennison - 29 july 2022 13:22
//         //new feature, block community post or account
//         if(count($blockDataCommunity)>0){

//             $listArray = array();
//             foreach($blockDataCommunity AS $block){

//                 $listArray[] = $block->custom_id;

//             }
//             unset($blockDataCommunity, $block);

//             $this->db->where_in("$this->tbl_as.id", $listArray, 1);
//             unset($listArray);

//         }

//         if(count($blockDataAccount)>0){

//             $listArray = array();
//             foreach($blockDataAccount AS $block){

//                 $listArray[] = $block->custom_id;

//             }
//             unset($blockDataAccount, $block);

//             $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
//             unset($listArray);

//         }

//         if(count($blockDataAccountReverse)>0){

//             $listArray = array();
//             foreach($blockDataAccountReverse AS $block){

//                 $listArray[] = $block->b_user_id;

//             }
//             unset($blockDataAccountReverse, $block);

//             $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
//             unset($listArray);

//         }
//         //END by Donny Dennison - 29 july 2022 13:22
//         //new feature, block community post or account

//         //START by Donny Dennison - 30 november 2022 16:31
//         //new feature, manage group member
//         if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
//             $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
//             $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
//             $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
//         }
//         //END by Donny Dennison - 30 november 2022 16:31
//         //new feature, manage group member

//         // if($query_type == "normal" && $type == "" && intval($b_user_id) == 0){
//         //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-5 days"))), "AND", ">=");
//         // }

//         $d = $this->db->get_first('object', 0);
//         if (isset($d->total)) {
//             return $d->total;
//         }
//         return 0;
//     }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $group_id="", $keyword="", $logged_in_id, $b_user_id="", $type)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl4_as.name", "group_name", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.is_active, '0')", "is_bookmark", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($logged_in_id));
        $this->db->where_as("$this->tbl3_as.is_active",$this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_kick",$this->db->esc(0));
        $this->db->where_as("$this->tbl3_as.is_accept",$this->db->esc(1));

        if ($group_id>'0') {
            $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
        }

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%');
            // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');
            // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
        }

        if ($type == "feed" || $type == "profile") {
            $this->db->where_as("$this->tbl_as.type", $this->db->esc("welcome message"), 'AND', '!=');
        }

        if ($type == "feed") {
            $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-2 weeks"))), "AND", ">=");
        }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl4_as.name", "group_name", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

//     public function getActiveByUserIdTitleCategoryDescription5Minutes($nation_code, $b_user_id, $title, $c_community_category_id, $deskripsi)
//     {
//         $this->db->from($this->tbl, $this->tbl_as);
//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
//         $this->db->where_as("$this->tbl_as.title", $this->db->esc($title));
//         $this->db->where_as("$this->tbl_as.c_community_category_id", $this->db->esc($c_community_category_id));
//         $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
//         $this->db->where_as("$this->tbl_as.cdate", $this->db->esc(date('Y-m-d H:i:s',strtotime("-5 minutes"))),"AND",">=");
//         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
//         $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
//         return $this->db->get();
//     }

    public function getAllByGroupIdUserId($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $group_id="", $keyword="", $b_user_id="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl4_as.name", "group_name", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.is_active, '0')", "is_bookmark", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        if ($group_id>'0') {
            $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
        }

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%');
            // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');
            // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
        }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getAllVideo($nation_code, $page=1, $page_size=10, $group_id, $watched_video)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl4_as.name", "group_name", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.is_active, '0')", "is_bookmark", 0);
        $this->db->select_as("$this->tbl6_as.id", "video_id", 0);
        $this->db->select_as("$this->tbl6_as.url", "url", 0);
        $this->db->select_as("$this->tbl6_as.url_thumb", "url_thumb", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
        $this->db->where_as("$this->tbl6_as.jenis", $this->db->esc("video"));
        $this->db->where_as("$this->tbl6_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl6_as.convert_status", $this->db->esc("uploading"), "AND", "!=");

        if(is_array($watched_video)){
            if(count($watched_video) > 0){
                $listArray = array();
                foreach ($watched_video as $key => $watched) {
                    if(isset($watched['video_id'])){
                        if($watched['video_id']){
                            $listArray[] = $watched['video_id'];
                        }
                    }
                }
                unset($watched_video, $key, $watched);

                $this->db->where_in("$this->tbl6_as.id", $listArray, 1);
                unset($listArray);
            }
        }

        $this->db->order_by("RAND()");
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }
}
