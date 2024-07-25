<?php
class C_Community_Attachment_Video_List_Model extends JI_Model
{
    public $tbl = 'c_community_attachment_video_list';
    public $tbl_as = 'ccavl';

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

    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    public function setMass($ds)
    {
        return $this->db->insert_multi($this->tbl, $ds, 0);
    }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
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

    public function del()
    {
        // $this->db->where_as('nation_code', $this->db->esc($nation_code));
        // $this->db->where('id', $id);
        // $this->db->where('b_user_id', $b_user_id);
        // return $this->db->delete($this->tbl);
        $sql = 'TRUNCATE `c_community_attachment_video_list`';
        return $this->db->exec($sql);
    }

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

    // public function countAll($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal")
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

    //     //START by Donny Dennison - 30 november 2022 16:31
    //     //new feature, manage group member
    //     if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
    //         $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
    //         $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
    //     }
    //     //END by Donny Dennison - 30 november 2022 16:31
    //     //new feature, manage group member

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

    //     //advanced filter
    //     if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
    //         // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
    //         // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }
    //     if (intval($b_user_id)>0) {
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as("LOWER($this->tbl_as.title)", strtolower($keyword), 'or', '%like%', 1, 0);
    //         $this->db->where_as("LOWER($this->tbl_as.deskripsi)", strtolower($keyword), 'and', '%like%', 0, 1);
    //         // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
    //         // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
    //         // $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }

    //     //by Donny Dennison - 1 desember 2020 16:29
    //     //list-produt-sameStreet-neighborhood-all-from-user-address
    //     //START by Donny Dennison - 1 desember 2020 16:29

    //     if(isset($pelangganAddress->alamat2)){

    //         // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
    //         $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

    //         if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
    //         }
            
    //         if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
    //         }

    //         if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
    //             $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
    //         }

    //         if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                
    //             $totalSpace = strpos($pelangganAddress->alamat2," ");

    //             $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

    //             if (strpos($tempAlamat2, ' ') !== false) {

    //                 $totalSpace += strpos($tempAlamat2, ' ');

    //                 $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
    //             }
    //             unset($totalSpace, $tempAlamat2);
            
    //         }
            
    //         if($type == 'sameStreet'){

    //             $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', strtolower($pelangganAddress->alamat2), 'and', '%like%', 1, 1);
    //             $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
    //             $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
    //             $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
    //             $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

    //         }else if($type == 'neighborhood'){

    //             $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
    //             $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
    //             $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
    //             $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

    //         }else if($type == 'district'){

    //             $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
    //             $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
    //             $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

    //         }else if($type == 'city'){
                
    //             $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
    //             $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

    //         }else if($type == 'province'){
                
    //             $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

    //         }

    //     }
    //     //END by Donny Dennison - 1 desember 2020 16:29

    //     //START by Donny Dennison - 29 july 2022 13:22
    //     //new feature, block community post or account
    //     if(count($blockDataCommunity)>0){

    //         $listArray = array();
    //         foreach($blockDataCommunity AS $block){

    //             $listArray[] = $block->custom_id;

    //         }
    //         unset($blockDataCommunity, $block);

    //         $this->db->where_in("$this->tbl_as.id", $listArray, 1);
    //         unset($listArray);

    //     }

    //     if(count($blockDataAccount)>0){

    //         $listArray = array();
    //         foreach($blockDataAccount AS $block){

    //             $listArray[] = $block->custom_id;

    //         }
    //         unset($blockDataAccount, $block);

    //         $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
    //         unset($listArray);

    //     }

    //     if(count($blockDataAccountReverse)>0){

    //         $listArray = array();
    //         foreach($blockDataAccountReverse AS $block){

    //             $listArray[] = $block->b_user_id;

    //         }
    //         unset($blockDataAccountReverse, $block);

    //         $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
    //         unset($listArray);

    //     }
    //     //END by Donny Dennison - 29 july 2022 13:22
    //     //new feature, block community post or account

    //     //START by Donny Dennison - 30 november 2022 16:31
    //     //new feature, manage group member
    //     if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
    //         $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
    //         $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
    //     }
    //     //END by Donny Dennison - 30 november 2022 16:31
    //     //new feature, manage group member

    //     // if($query_type == "normal" && $type == "" && intval($b_user_id) == 0){
    //     //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-5 days"))), "AND", ">=");
    //     // }

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $max_likes, $type, $from="", $to="")
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
    //     $this->db->where_as("$this->tbl3_as.jenis", $this->db->esc('video'));
    //     // $this->db->where_as("$this->tbl3_as.convert_status", $this->db->esc('processed'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

    //     if($max_likes != 0){
    //         $this->db->where_as("$this->tbl_as.total_likes", $max_likes, "AND", "<");
    //     }

    //     if($type == "one_min"){
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d")));
    //         $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 1 MINUTE)", "NOW()", "AND", "<=");
    //         $this->db->where_as("$this->tbl4_as.c_community_id", "IS NULL");
    //     }else if($type == "night"){
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-1 day"))));
    //     }else if($type == "custom"){
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($from), "AND", ">=");
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($to), "AND", "<=");
    //     }

    //     return $this->db->get('object', 0);
    // }

    // public function getById($nation_code, $pid, $pelanggan, $language_id=1)
    // {
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
    //     $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);
    //     $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");
    //     $this->db->select_as("$this->tbl2_as.image_cover", "category_image_cover", 0);

    //     $this->db->select_as("$this->tbl_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
    //     $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
    //     $this->db->select_as("''", "top_like_image_2", 0);
    //     $this->db->select_as("''", "top_like_image_3", 0);
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
    //     $this->db->select_as("$this->tbl_as.total_dislikes", "total_dislikes", 0);
    //     $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people_group_chat", "total_people_group_chat", 0);
    //     $this->db->select_as("IF(($this->tbl_as.c_community_category_id = 24),(100),(50))", "max_people_group_chat", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //     return $this->db->get_first('', 0);
    // }

    // public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
    //         WHERE nation_code = '$nation_code' AND id = '$id';");
    // }

}
