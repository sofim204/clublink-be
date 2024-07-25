<?php
class C_Community_Model extends JI_Model
{
    public $tbl = 'c_community';
    public $tbl_as = 'cc';
    public $tbl2 = 'c_community_category';
    public $tbl2_as = 'ccc';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = 'c_community_hashtag_history_for_search';
    public $tbl4_as = 'cchhfs';

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    public $tbl24 = 'c_community_discussion';
    public $tbl24_as = 'ccd';
    public $tbl25 = 'e_chat_room';
    public $tbl25_as = 'ecr';
    public $tbl26 = 'e_chat_participant';
    public $tbl26_as = 'ecp';

    public $tbl30 = 'b_user_follow';
    public $tbl30_as = 'buf';

    //START by Donny Dennison 20 may 2022 17:23
    //new api community/video_list
    public $tbl31 = 'c_community_attachment';
    public $tbl31_as = 'cca';
    //END by Donny Dennison 20 may 2022 17:23

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    /**
     * Composite join for multiple PK on table 2
     * @return array composites join
     */
    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_community_category_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 3
     * @return array composites join
     */
    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl4_as.c_community_id");
        return $composites;
    }

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    private function __joinTbl24()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl24_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl24_as.c_community_id");
        $composites[] = $this->db->composite_create("1", "=", "$this->tbl24_as.is_active");
        return $composites;
    }

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    private function __joinTbl25()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl25_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl25_as.c_community_id");
        $composites[] = $this->db->composite_create("'community'", "=", "$this->tbl25_as.chat_type");
        return $composites;
    }

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    private function __joinTbl26()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl25_as.nation_code", "=", "$this->tbl26_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl25_as.id", "=", "$this->tbl26_as.e_chat_room_id");
        $composites[] = $this->db->composite_create("1", "=", "$this->tbl26_as.is_active");
        return $composites;
    }

    private function __joinTbl30()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl30_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl30_as.b_user_id_follow");
        return $composites;
    }

    //START by Donny Dennison 20 may 2022 17:23
    //new api community/video_list
    private function __joinTbl31()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl31_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl31_as.c_community_id");
        return $composites;
    }
    //END by Donny Dennison 20 may 2022 17:23

    private function join_CaToCc()
    {
        $composites = [];
        $composites[] = $this->db->composite_create("$this->tbl31_as.c_community_id", "=", "$this->tbl_as.id");
        return $composites;
    }

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

    /**
     * Insert into database
     * @param array $di name value pair describes column and value to insert into table
     */
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        // by Muhammad Sofi - 10 November 2021 09:19
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    /**
     * Insert into database, wiht ignore option (slower)
     * @param array $di name value pair describes column and value to insert into table
     */
    // public function set2($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert_ignore($this->tbl, $di, 0, 0);
    // }

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    /**
     * Update table c_produk by id
     * @param  integer $nation_code [description]
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    // public function update2($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    /**
     * Update multiple rows on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  string $ids          ID(s) from c_produk separated by commas
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
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

    /**
     * Update a row on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
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
    public function getTbl2As()
    {
        return $this->tbl2_as;
    }
    public function getTblAs3()
    {
        return $this->tbl3_as;
    }
    public function getTblAs4()
    {
        return $this->tbl4_as;
    }
    public function getTblAs5()
    {
        return $this->tbl5_as;
    }

    //by Donny Dennison - 08-09-2021 11:35
    //revamp-profile
    public function countAllByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function countAll($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount)
    public function countAll($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        //START by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member
        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
            $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
        }
        //END by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
            // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.title)", addslashes(strtolower($keyword)), 'or', '%like%', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 1);
            // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
            // $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
        }

        //by Donny Dennison - 1 desember 2020 16:29
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 1 desember 2020 16:29

        if(isset($pelangganAddress->alamat2)){

            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }

            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                
                $totalSpace = strpos($pelangganAddress->alamat2," ");

                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

                if (strpos($tempAlamat2, ' ') !== false) {

                    $totalSpace += strpos($tempAlamat2, ' ');

                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            
            }
            
            if($type == 'sameStreet'){

                $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'neighborhood'){

                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'district'){

                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'city'){
                
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'province'){
                
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }

        }
        //END by Donny Dennison - 1 desember 2020 16:29

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataCommunity)>0){

            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //START by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member
        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
            $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
            $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
        }
        //END by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member

        // if($query_type == "normal" && $type == "" && intval($b_user_id) == 0){
        //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-5 days"))), "AND", ">=");
        // }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $language_id=1)
    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal", $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_community_id", 0);
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "community_category", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);

        $this->db->select_as("$this->tbl_as.kodepos", "kodepos");

        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //START by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelangganAddress->b_user_id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelangganAddress->b_user_id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
            // $this->db->select_as("(0)", "is_liked", 0);
            // $this->db->select_as("(0)", "is_disliked", 0);
        }

        //END by Donny Dennison - 8 july 2021 11:02

        //by Donny Dennison - 28 november 2022 11:10
        //new feature, manage group member
        $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);
        $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
        $this->db->select_as("$this->tbl_as.total_people_group_chat", "total_people_group_chat", 0);
        $this->db->select_as("IF(($this->tbl_as.c_community_category_id = 24),(100),(50))", "max_people_group_chat", 0);
        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        //START by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member
        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
            $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
        }
        //END by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
            // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.title)", addslashes(strtolower($keyword)), 'or', '%like%', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 1);
            // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
            // $this->db->where_as("$this->tbl_as.brand", $keyword, 'and', '%like%', 0, 1);
        }

        //by Donny Dennison - 1 desember 2020 16:29
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 1 desember 2020 16:29
        if(isset($pelangganAddress->alamat2)){              

            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }
            
            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                
                $totalSpace = strpos($pelangganAddress->alamat2," ");

                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

                if (strpos($tempAlamat2, ' ') !== false) {

                    $totalSpace += strpos($tempAlamat2, ' ');

                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            
            }
            
            if($type == 'sameStreet'){

                $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'neighborhood'){

                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'district'){

                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'city'){
                
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'province'){
                
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }

        }
        //END by Donny Dennison - 1 desember 2020 16:29

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataCommunity)>0){

            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        //START by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member
        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
            $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
            $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
        }
        //END by Donny Dennison - 30 november 2022 16:31
        //new feature, manage group member

        // if($query_type == "normal" && $type == "" && intval($b_user_id) == 0){
        //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-5 days"))), "AND", ">=");
        // }

        //by Donny Dennison - 1 desember 2020 16:29
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 23 desember 2020 15:44
        // if(isset($pelangganAddress->alamat2) && $sort_col == "$this->tbl_as.kodepos"){
            // $this->db->order_by("$this->tbl_as.cdate", "DESC");
            // $this->db->order_by("dist_in_meters", "ASC");
            // $this->db->order_by("alamat3", "DESC");
        // }else{
            $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        // }
        //END by Donny Dennison - 23 desember 2020 15:44

        //by Donny Dennison - 4 january 2021 10:23
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 4 january 2021 10:23
        // $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
        $this->db->page($page, $page_size);
        //END by Donny Dennison - 4 january 2021 10:23

        return $this->db->get('object', 0);
    }

    public function countAllHashtag($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
            $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
            // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            // $this->db->where_as("LOWER($this->tbl_as.title)", addslashes(strtolower($keyword)), 'or', '%like%', 1, 0);
            // $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 1);
            // // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
            // // $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
            $this->db->where_as("LOWER($this->tbl4_as.hashtag)", $this->db->esc(addslashes(strtolower($keyword))));
        }

        if(isset($pelangganAddress->alamat2)){
            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));
            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }

            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                $totalSpace = strpos($pelangganAddress->alamat2," ");
                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                if (strpos($tempAlamat2, ' ') !== false) {
                    $totalSpace += strpos($tempAlamat2, ' ');
                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            }

            if($type == 'sameStreet'){
                $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'neighborhood'){
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'district'){
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'city'){
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'province'){
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }
        }

        if(count($blockDataCommunity)>0){
            $listArray = array();
            foreach($blockDataCommunity AS $block){
                $listArray[] = $block->custom_id;
            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);
        }

        if(count($blockDataAccount)>0){
            $listArray = array();
            foreach($blockDataAccount AS $block){
                $listArray[] = $block->custom_id;
            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);
        }

        if(count($blockDataAccountReverse)>0){
            $listArray = array();
            foreach($blockDataAccountReverse AS $block){
                $listArray[] = $block->b_user_id;
            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);
        }

        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
            $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
            $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAllHashtag($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal", $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_community_id", 0);
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos");
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelangganAddress->b_user_id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelangganAddress->b_user_id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
        }

        $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);
        $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
        $this->db->select_as("$this->tbl_as.total_people_group_chat", "total_people_group_chat", 0);
        $this->db->select_as("IF(($this->tbl_as.c_community_category_id = 24),(100),(50))", "max_people_group_chat", 0);
        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
            $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
            // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            // $this->db->where_as("LOWER($this->tbl_as.title)", addslashes(strtolower($keyword)), 'or', '%like%', 1, 0);
            // $this->db->where_as("LOWER($this->tbl_as.deskripsi)", addslashes(strtolower($keyword)), 'and', '%like%', 0, 1);
            // // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
            // // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');
            // // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
            // // $this->db->where_as("$this->tbl_as.brand", $keyword, 'and', '%like%', 0, 1);
            $this->db->where_as("LOWER($this->tbl4_as.hashtag)", $this->db->esc(addslashes(strtolower($keyword))));
        }

        if(isset($pelangganAddress->alamat2)){              
            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));
            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }
            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }

            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                $totalSpace = strpos($pelangganAddress->alamat2," ");
                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                if (strpos($tempAlamat2, ' ') !== false) {
                    $totalSpace += strpos($tempAlamat2, ' ');
                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            }

            if($type == 'sameStreet'){
                $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'neighborhood'){
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'district'){
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'city'){
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }else if($type == 'province'){
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));
            }
        }
        if(count($blockDataCommunity)>0){
            $listArray = array();
            foreach($blockDataCommunity AS $block){
                $listArray[] = $block->custom_id;
            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);
        }

        if(count($blockDataAccount)>0){
            $listArray = array();
            foreach($blockDataAccount AS $block){
                $listArray[] = $block->custom_id;
            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);
        }

        if(count($blockDataAccountReverse)>0){
            $listArray = array();
            foreach($blockDataAccountReverse AS $block){
                $listArray[] = $block->b_user_id;
            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);
        }

        if($query_type == "group_joined" && isset($pelangganAddress->alamat2)){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id), 'AND', '!=');
            $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id));
            $this->db->where_as("$this->tbl26_as.is_active", $this->db->esc(1));
        }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);
        return $this->db->get('object', 0);
    }

    //START by Donny Dennison 20 may 2022 17:23
    //new api community/video_list
    // public function countAllVideo($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="")
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl31, $this->tbl31_as, $this->__joinTbl31(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
    //     $this->db->where_as("$this->tbl31_as.jenis", $this->db->esc('video'));
    //     $this->db->where_as("$this->tbl31_as.convert_status", $this->db->esc('uploading'), "AND", "!=");

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

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAllVideo($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $language_id=1, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
    //     $this->db->select_as("$this->tbl_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
    //     $this->db->select_as("(0)", "total_dislikes", 0);
    //     $this->db->select_as("$this->tbl31_as.id", "video_id", 0);
    //     $this->db->select_as("$this->tbl31_as.url", "url", 0);
    //     $this->db->select_as("$this->tbl31_as.url_thumb", "url_thumb", 0);
    //     $this->db->select_as("$this->tbl31_as.total_views", "total_views", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl31, $this->tbl31_as, $this->__joinTbl31(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
    //     $this->db->where_as("$this->tbl31_as.jenis", $this->db->esc('video'));
    //     // $this->db->where_as("$this->tbl31_as.convert_status", $this->db->esc('processed'));
    //     $this->db->where_as("$this->tbl31_as.is_active", $this->db->esc(1));

    //     //advanced filter
    //     if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
    //         // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.c_community_category_id", $c_community_category_ids);
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
    //         // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }
    //     if ($b_user_id>'0') {
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     // if (mb_strlen($keyword)>0) {
    //     //     $this->db->where_as("LOWER($this->tbl_as.title)", strtolower($keyword), 'or', '%like%', 1, 0);
    //     //     $this->db->where_as("LOWER($this->tbl_as.deskripsi)", strtolower($keyword), 'and', '%like%', 0, 1);
    //     //     // $this->db->where_as("LOWER($this->tbl2_as.nama)", strtolower($keyword), 'and', '%like%', 0, 1);
    //     //     // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     //     // $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
    //     //     // $this->db->where_as("$this->tbl_as.brand", $keyword, 'and', '%like%', 0, 1);
    //     // }

    //     if(is_array($watched_video)){

    //         if(count($watched_video) > 0){

    //             $listArray = array();
    //             foreach ($watched_video as $key => $watched) {

    //                 if(isset($watched['community_id']) && isset($watched['video_id'])){

    //                     if($watched['community_id'] && $watched['video_id']){

    //                         $listArray[] = $watched['community_id'].'-'.$watched['video_id'];

    //                     }

    //                 }

    //             }
    //             unset($watched_video, $watched);

    //             $this->db->where_in("CONCAT($this->tbl_as.id,'-',$this->tbl31_as.id)", $listArray, 1);
    //             unset($listArray);

    //         }

    //     }

    //     //START by Donny Dennison - 1 desember 2020 16:29
    //     //list-produt-sameStreet-neighborhood-all-from-user-address
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

    //             $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
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
    //     //list-produt-sameStreet-neighborhood-all-from-user-address

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

    //     // $this->db->order_by($sort_col, $sort_direction);
    //     $this->db->order_by("RAND()");

    //     // $this->db->page($page, $page_size);
    //     $this->db->limit($page_size);

    //     return $this->db->get('object', 0);
    // }
    //END by Donny Dennison 20 may 2022 17:23
    //new api community/video_list

    public function getAllVideoManualQuery($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $language_id=1, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse)
    {

        // switch ($sort_col) {
        //   case 'cc.id':
        //   $sort_col = "subquery2.id";
        //   break;
        //   case 'cc.cdate':
        //   $sort_col = "subquery2.cdate";
        //   break;

        //   default:
        //   $sort_col = "subquery2.cdate";
        // }

        //pagination logic
        $page = ($page * $page_size) - $page_size;

        $sql = "SELECT
        *
FROM(
SELECT 
    cc.id AS 'id',
    cc.c_community_category_id AS 'c_community_category_id',
    cc.b_user_id AS 'b_user_id_starter',
    COALESCE(AES_DECRYPT(bu.fnama,
                    '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'),
            '') AS 'b_user_nama_starter',
    COALESCE(bu.image, '') AS 'b_user_image_starter',
    cc.title AS 'title',
    cc.cdate AS 'cdate',
    cc.deskripsi AS 'deskripsi',
    cc.total_discussion AS 'total_discussion',
    cc.total_likes AS 'total_likes',
    (0) AS 'total_dislikes',
    cca.id AS 'video_id',
    cca.url AS 'url',
    cca.url_thumb AS 'url_thumb',
    cca.total_views AS 'total_views'
FROM
    `c_community_attachment` cca
        LEFT JOIN
    `c_community` cc ON cc.nation_code = cca.nation_code AND cc.id = cca.c_community_id
        LEFT JOIN
    `b_user` bu ON cc.nation_code = bu.nation_code AND cc.b_user_id = bu.id
WHERE
    cc.nation_code = ".$nation_code." ";
        $sql .= "AND cc.is_published = '1'
        AND cc.is_active = '1'
        AND bu.is_active = '1'
        AND cc.is_take_down = '0' 
        AND cca.jenis = 'video'
        AND cca.is_active = '1' ";

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            $sql .= "AND cc.c_community_category_id IN (";
            foreach ($c_community_category_ids as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
        }

        if ($b_user_id>'0') {
            $sql .= "AND cc.b_user_id = ".$this->db->esc($b_user_id)." ";
        }

        if(is_array($watched_video)){
            if(count($watched_video) > 0){
                $listArray = array();
                foreach ($watched_video as $key => $watched) {
                    if(isset($watched['community_id']) && isset($watched['video_id'])){
                        if($watched['community_id'] && $watched['video_id']){
                            $listArray[] = $watched['community_id'].'-'.$watched['video_id'];
                        }
                    }
                }
                unset($watched_video, $watched);

                $sql .= "AND CONCAT(cc.id,'-',cca.id) NOT IN (";
                foreach ($listArray as $v) {
                    $sql .= $this->db->esc($v).", ";
                }
                $sql = rtrim($sql, ", ");
                $sql .= ') ';
                unset($listArray);
            }
        }

        if(isset($pelangganAddress->alamat2)){
            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }
            
            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                $totalSpace = strpos($pelangganAddress->alamat2," ");

                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

                if (strpos($tempAlamat2, ' ') !== false) {

                    $totalSpace += strpos($tempAlamat2, ' ');

                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            }

            if($type == 'sameStreet'){
                $sql .= "AND LOWER(CAST(".$this->__decrypt("cc.alamat2")." AS CHAR(50))) LIKE '%".addslashes(strtolower($pelangganAddress->alamat2))."%' ";
                $sql .= "AND LOWER(cc.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'neighborhood'){
                $sql .= "AND LOWER(cc.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'district'){
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'city'){
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'province'){
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }
        }

        if(count($blockDataCommunity)>0){
            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $sql .= "AND cc.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        if(count($blockDataAccount)>0){
            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $sql .= "AND bu.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        if(count($blockDataAccountReverse)>0){
            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $sql .= "AND bu.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        if(date("j") % 2 == 0){
            $sql .= "AND MOD(MONTH(cc.cdate),2)=0 ";
        }else{
            $sql .= "AND MOD(MONTH(cc.cdate),2)=1 ";
        }

        $sql .= "LIMIT 500) AS a 
        ORDER BY RAND() ASC 
        LIMIT ".$page.", ".$page_size;

        return $this->db->query($sql);
    }

    public function getAllVideoManualQueryV2($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $language_id=1, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $start_position)
    {

        // switch ($sort_col) {
        //   case 'cc.id':
        //   $sort_col = "subquery2.id";
        //   break;
        //   case 'cc.cdate':
        //   $sort_col = "subquery2.cdate";
        //   break;

        //   default:
        //   $sort_col = "subquery2.cdate";
        // }

        //pagination logic
        $page = ($page * $page_size) - $page_size;

        $sql = "SELECT 
    cc.id AS 'id',
    cc.c_community_category_id AS 'c_community_category_id',
    cc.b_user_id AS 'b_user_id_starter',
    COALESCE(AES_DECRYPT(bu.fnama,
                    '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'),
            '') AS 'b_user_nama_starter',
    COALESCE(bu.image, '') AS 'b_user_image_starter',
    cc.title AS 'title',
    cc.cdate AS 'cdate',
    cc.deskripsi AS 'deskripsi',
    cc.total_discussion AS 'total_discussion',
    cc.total_likes AS 'total_likes',
    (0) AS 'total_dislikes',
    cca.id AS 'video_id',
    cca.url AS 'url',
    cca.url_thumb AS 'url_thumb',
    cca.total_views AS 'total_views'
FROM
    `c_community_attachment_video_list` ccavl
        LEFT JOIN
    `c_community_attachment` cca ON cca.nation_code = ccavl.nation_code AND cca.c_community_id = ccavl.c_community_id AND cca.id = ccavl.c_community_attachment_id
        LEFT JOIN
    `c_community` cc ON cc.nation_code = cca.nation_code AND cc.id = cca.c_community_id
        LEFT JOIN
    `b_user` bu ON cc.nation_code = bu.nation_code AND cc.b_user_id = bu.id
WHERE
    cc.nation_code = ".$nation_code." ";
        $sql .= "AND cc.is_published = '1'
        AND cc.is_active = '1'
        AND bu.is_active = '1'
        AND cc.is_take_down = '0'
        AND cca.jenis = 'video'
        AND cca.is_active = '1'
        AND ccavl.id >= '".$start_position."' ";

        //advanced filter
        if (is_array($c_community_category_ids) && count($c_community_category_ids)>0) {
            $sql .= "AND cc.c_community_category_id IN (";
            foreach ($c_community_category_ids as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
        }

        if ($b_user_id>'0') {
            $sql .= "AND cc.b_user_id = ".$this->db->esc($b_user_id)." ";
        }

        if(is_array($watched_video)){
            if(count($watched_video) > 0){
                $listArray = array();
                foreach ($watched_video as $key => $watched) {
                    if(isset($watched['community_id']) && isset($watched['video_id'])){
                        if($watched['community_id'] && $watched['video_id']){
                            $listArray[] = $watched['community_id'].'-'.$watched['video_id'];
                        }
                    }
                }
                unset($watched_video, $watched);

                $sql .= "AND CONCAT(cc.id,'-',cca.id) NOT IN (";
                foreach ($listArray as $v) {
                    $sql .= $this->db->esc($v).", ";
                }
                $sql = rtrim($sql, ", ");
                $sql .= ') ';
                unset($listArray);
            }
        }

        if(isset($pelangganAddress->alamat2)){
            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }
            
            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                $totalSpace = strpos($pelangganAddress->alamat2," ");

                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

                if (strpos($tempAlamat2, ' ') !== false) {

                    $totalSpace += strpos($tempAlamat2, ' ');

                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            }

            if($type == 'sameStreet'){
                $sql .= "AND LOWER(CAST(".$this->__decrypt("cc.alamat2")." AS CHAR(50))) LIKE '%".addslashes(strtolower($pelangganAddress->alamat2))."%' ";
                $sql .= "AND LOWER(cc.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'neighborhood'){
                $sql .= "AND LOWER(cc.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'district'){
                $sql .= "AND LOWER(cc.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'city'){
                $sql .= "AND LOWER(cc.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'province'){
                $sql .= "AND LOWER(cc.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }
        }

        if(count($blockDataCommunity)>0){
            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $sql .= "AND cc.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        if(count($blockDataAccount)>0){
            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $sql .= "AND bu.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        if(count($blockDataAccountReverse)>0){
            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $sql .= "AND bu.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);
        }

        $sql .= "ORDER BY ccavl.id ASC
        LIMIT ".$page.", ".$page_size;

        return $this->db->query($sql);
    }

    public function getAllVideoCA(
        $nation_code,
        $page=1,
        $page_size=10,
        $sort_col="",
        $sort_direction="ASC",
        $keyboard="",
        $c_community_category_id=[],
        $type="",
        $pelangganAddress,
        $b_user_id="",
        $language_id=1,
        $watched_video,
        $blockDataCommunity,
        $blockDataAccount,
        $blockDataAccountReverse
    ){
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl31_as.c_community_id", "c_community_id", 0);
        $this->db->select_as("$this->tbl31_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelangganAddress->b_user_id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelangganAddress->b_user_id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
            // $this->db->select_as("(0)", "is_liked", 0);
            // $this->db->select_as("(0)", "is_disliked", 0);
        }

        $this->db->select_as("$this->tbl31_as.id", "video_id", 0);
        $this->db->select_as("$this->tbl31_as.url", "url", 0);
        $this->db->select_as("$this->tbl31_as.url_thumb", "url_thumb", 0);

        $this->db->from($this->tbl31, $this->tbl31_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->join_CaToCc(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl31_as.jenis", $this->db->esc('video'));
        $this->db->where_as("$this->tbl31_as.is_active", $this->db->esc(1));

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl31_as.b_user_id", $this->db->esc($b_user_id));
        }

        if(is_array($watched_video)){

            if(count($watched_video) > 0){

              foreach ($watched_video as $key => $watched) {

                if(isset($watched['community_id']) && isset($watched['video_id'])){

                  if($watched['community_id'] && $watched['video_id']){

                    $this->db->where_as("CONCAT($this->tbl_as.id,'-',$this->tbl31_as.id)", $this->db->esc($watched['community_id'].'-'.$watched['video_id']), 'and', '!=');

                  }

                }

              }

            }

        }

        if(count($blockDataCommunity)>0){

            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        // $this->db->order_by($sort_col, $sort_direction);
        // $this->db->order_by("RAND()");
        $this->db->order_by("$this->tbl31_as.cdate", "DESC");

        // $this->db->page($page, $page_size);
        $this->db->limit($page_size);

        return $this->db->get('object', 0);
        
    }

//     public function resetRankVariableMysql($variable)
//     {
       
//         //credit : https://stackoverflow.com/a/11754790/7578520
//         $sql = "SET @".$variable." = 0;";

//         $this->db->query($sql);
//     }

//     public function countAllHomepage($nation_code, $pelanggan)
//     {
//         // $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
//         // $this->db->from($this->tbl, $this->tbl_as);
//         // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
//         // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

//         // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         // $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
//         // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
//         // $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
//         // $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
//         // $this->db->join_composite($this->tbl30, $this->tbl30_as, $this->__joinTbl30(), 'left');

//         // //advanced filter
//         // if (strlen($pelanggan->id)>0) {
//         //     $this->db->where_as("$this->tbl30_as.b_user_id", $pelanggan->id);
//         // }

//         // $d = $this->db->get_first('object', 0);
//         // if (isset($d->total)) {
//         //     return $d->total;
//         // }
//         // return 0;

//         //credit : https://stackoverflow.com/a/48572092/7578520
//         $sql = "SELECT COUNT(*) AS total FROM( SELECT *,
//     @f_rank1 := IF(@f_b_user_id_starter1 = b_user_id_starter, @f_rank1 + 1, 1) AS 'rank', 
//     @f_b_user_id_starter1 := b_user_id_starter
// FROM (
// SELECT 
//     DISTINCT cc.id AS 'id',
//     cc.b_user_id AS 'b_user_id_starter'
// FROM
//     `c_community` cc
//         LEFT JOIN
//     `c_community_category` ccc ON cc.nation_code = ccc.nation_code
//         AND cc.c_community_category_id = ccc.id
//         LEFT JOIN
//     `b_user` bu ON cc.nation_code = bu.nation_code
//         AND cc.b_user_id = bu.id
//         LEFT JOIN
//     `c_community_like_category` cclc ON cc.nation_code = cclc.nation_code
//         AND cc.top_like_image_1 = cclc.id
//         LEFT JOIN
//     `c_community_like_category` cclc_2 ON cc.nation_code = cclc_2.nation_code
//         AND cc.top_like_image_2 = cclc_2.id
//         LEFT JOIN
//     `c_community_like_category` cclc_3 ON cc.nation_code = cclc_3.nation_code
//         AND cc.top_like_image_3 = cclc_3.id
//         LEFT JOIN
//     `b_user_follow` buf ON cc.nation_code = buf.nation_code
//         AND cc.b_user_id = buf.b_user_id_follow
// WHERE
//     cc.nation_code = ".$nation_code." ";
//         $sql .= "AND cc.is_published = '1'
//         AND cc.is_active = '1'
//         AND bu.is_active = '1'
//         AND buf.is_active = '1'
//         AND cc.is_take_down = '0' ";

//         //advanced filter
//         if (strlen($pelanggan->id)>0) {
//             $sql .= "AND buf.b_user_id = ".$pelanggan->id." ";
//         }

//         $sql .= ") AS subquery1 
//         ORDER BY subquery1.b_user_id_starter DESC , subquery1.id DESC ";

//         $sql .= ") AS subquery2
//         WHERE subquery2.rank <= 5 ";

//         $d = $this->db->query($sql);
//         if (isset($d[0]->total)) {
//             return $d[0]->total;
//         }
//         return "0";
//     }

    public function getAllHomepage($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="DESC", $pelanggan, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos");
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("(0)", "total_dislikes", 0);

        // if(isset($pelanggan->id)){
            // $this->db->select_as("$this->tbl_as.total_dislikes", "total_dislikes", 0);
            // $this->db->select_as("IF((SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
            // $this->db->select_as("(0)", "total_dislikes", 0);
        //     $this->db->select_as("(0)", "is_liked", 0);
        // }

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl30, $this->tbl30_as, $this->__joinTbl30(), 'inner');
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-2 weeks"))), "AND", ">");
        
        //advanced filter
        // if (strlen($pelanggan->id)>0) {
            $this->db->where_as("$this->tbl30_as.b_user_id", $this->db->esc($pelanggan->id));
            $this->db->where_as("$this->tbl30_as.is_active", $this->db->esc(1));
        // }

        $this->db->order_by($sort_col, $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);

//         switch ($sort_col) {
//           case 'cc.id':
//           $sort_col = "subquery2.id";
//           break;
//           case 'cc.cdate':
//           $sort_col = "subquery2.cdate";
//           break;

//           default:
//           $sort_col = "subquery2.cdate";
//         }

//         //pagination logic
//         $page = ($page * $page_size) - $page_size;

//         //credit : https://stackoverflow.com/a/48572092/7578520
//         $sql = "SELECT
//         *
// FROM(
// SELECT 
//         *,
//          @f_rank2:=IF(@f_b_user_id_starter2 = b_user_id_starter,
//         @f_rank2 + 1,
//         1) AS 'rank',
//     @f_b_user_id_starter2:=b_user_id_starter 
// FROM (
// SELECT 
//     DISTINCT cc.id AS 'id',
//     cc.c_community_category_id AS 'c_community_category_id',
//     IF(".$language_id." = 4 AND ccc.thailand IS NOT NULL AND ccc.thailand != '', ccc.thailand, IF(".$language_id." = 3 AND ccc.korea IS NOT NULL AND ccc.korea != '', ccc.korea, IF(".$language_id." = 2 AND ccc.indonesia IS NOT NULL AND ccc.indonesia != '', ccc.indonesia, ccc.nama))) AS 'community_category',
//     cc.b_user_id AS 'b_user_id_starter',
//     COALESCE(AES_DECRYPT(bu.fnama,
//                     '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'),
//             '') AS 'b_user_nama_starter',
//     COALESCE(bu.image, '') AS 'b_user_image_starter',
//     cc.title AS 'title',
//     cc.cdate AS 'cdate',
//     cc.deskripsi AS 'deskripsi',
//     -- COALESCE(AES_DECRYPT(cc.alamat2,
//     --                '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'),
//     --        '') AS 'alamat2',
//     CAST(SUBSTRING(AES_DECRYPT(cc.alamat2,
//                     '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'),
//             1,
//             IF(LOCATE(',',
//                         AES_DECRYPT(cc.alamat2,
//                                 '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556')) != 0,
//                 LOCATE(',',
//                         AES_DECRYPT(cc.alamat2,
//                                 '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556')) - 1,
//                 CHAR_LENGTH(AES_DECRYPT(cc.alamat2,
//                                 '2629BB0836F6CC8CDDEA7585B974CD6C7314744E91A76E5ECD9CB7EB19792556'))))
//         AS CHAR (50)) AS 'alamat4',
//     -- cc.kodepos AS 'kodepos',
//     cc.total_discussion AS 'total_discussion',
//     COALESCE(cclc.image_icon, '') AS 'top_like_image_1',
//     COALESCE(cclc_2.image_icon, '') AS 'top_like_image_2',
//     COALESCE(cclc_3.image_icon, '') AS 'top_like_image_3',
//     cc.total_likes AS 'total_likes',
//     IF((SELECT 
//                 COUNT(*)
//             FROM
//                 c_community_like
//             WHERE
//                 type = 'community' AND custom_id = cc.id
//                     AND b_user_id = '".$pelanggan->id."'
//                     AND nation_code = '".$nation_code."'
//                     AND is_active = '1'
//             LIMIT 0 , 1) > 0,
//         1,
//         0) AS 'is_liked'
//     -- COALESCE((SELECT 
//     --                cclc.image_icon
//     --            FROM
//     --                c_community_like AS ccl
//     --                    JOIN
//     --                c_community_like_category AS cclc ON ccl.nation_code = cclc.nation_code
//     --                    AND ccl.c_community_like_category_id = cclc.id
//     --            WHERE
//     --                ccl.type = 'community'
//     --                    AND ccl.custom_id = cc.id
//     --                    AND ccl.b_user_id = '".$pelanggan->id."'
//     --                    AND ccl.nation_code = '".$nation_code."'
//     --                    AND ccl.is_active = '1'
//     --            LIMIT 0 , 1),
//     --        '') AS 'is_liked_image'
// FROM
//     `c_community` cc
//         LEFT JOIN
//     `c_community_category` ccc ON cc.nation_code = ccc.nation_code
//         AND cc.c_community_category_id = ccc.id
//         LEFT JOIN
//     `b_user` bu ON cc.nation_code = bu.nation_code
//         AND cc.b_user_id = bu.id
//         LEFT JOIN
//     `c_community_like_category` cclc ON cc.nation_code = cclc.nation_code
//         AND cc.top_like_image_1 = cclc.id
//         LEFT JOIN
//     `c_community_like_category` cclc_2 ON cc.nation_code = cclc_2.nation_code
//         AND cc.top_like_image_2 = cclc_2.id
//         LEFT JOIN
//     `c_community_like_category` cclc_3 ON cc.nation_code = cclc_3.nation_code
//         AND cc.top_like_image_3 = cclc_3.id
//         LEFT JOIN
//     `b_user_follow` buf ON cc.nation_code = buf.nation_code
//         AND cc.b_user_id = buf.b_user_id_follow
// WHERE
//     cc.nation_code = ".$nation_code." ";
//         $sql .= "AND cc.is_published = '1'
//         AND cc.is_active = '1'
//         AND bu.is_active = '1'
//         AND buf.is_active = '1'
//         AND cc.is_take_down = '0' 
//         AND cc.cdate >= (DATE(NOW()) - INTERVAL 1 WEEK) ";  // interval 1 month to interval 1 week from today

//         //advanced filter
//         if (strlen($pelanggan->id)>0) {
//             $sql .= "AND buf.b_user_id = ".$pelanggan->id." ";
//         }

//         $sql .= ") AS subquery1 
//         ORDER BY subquery1.b_user_id_starter DESC , subquery1.id DESC ";


//         $sql .= ") AS subquery2
//         WHERE subquery2.rank <= 3 "; // from 5 post to 3 post

//         $sql .= "ORDER BY ".$sort_col." ". $sort_direction." ";

//         // $sql .= "LIMIT
//         // ".$page.", ".$page_size;
//         $sql .= "LIMIT ".$page_size;

//         return $this->db->query($sql);
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // public function getById($nation_code, $pid, $pelanggan)
    public function getById($nation_code, $pid, $pelanggan, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0); // by Muhammad Sofi - 10 November 2021 09:19
        // START by Muhammad Sofi - 10 November 2021 09:19
        // change join query
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);

        // END by Muhammad Sofi - 10 November 2021 09:19
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);

        //by Donny Dennison - 23 november 2022 13:42
        //new feature, manage group member
        $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "community_category", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");

        //by muhammad sofi - 19 September 2022 | 10:14
        //add category image(cover)
        $this->db->select_as("$this->tbl2_as.image_cover", "category_image_cover", 0);

        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        //by Donny Dennison - 1 july 2021 14:42
        //add-general-location-in-address

        // by Muhammad Sofi - 10 November 2021 09:19
        // change join query
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);

        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //START by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl_as.total_dislikes", "total_dislikes", 0);

        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF($this->tbl_as.b_user_id = $pelanggan->id, $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
        //     // $this->db->select_as("$this->tbl_as.total_dislikes", "total_dislikes", 0);
        //     // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        //     // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        // }else{
        //     $this->db->select_as("(0)", "total_dislikes", 0);
        //     // $this->db->select_as("(0)", "is_liked", 0);
        //     // $this->db->select_as("(0)", "is_disliked", 0);
        // }

        //END by Donny Dennison - 8 july 2021 11:02

        // by Muhammad Sofi - 5 November 2021 13:38
        // remove subquery get chat_room_id, add new column e_chat_room_id in community
        // $this->db->select_as("(SELECT id FROM e_chat_room WHERE c_community_id = $this->tbl_as.id AND chat_type = 'community' AND nation_code= $nation_code)", "chat_room_id", 0);
        $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);

        // by Muhammad Sofi - 8 November 2021 12:00
        // remove subquery get total_people_group_chat, add new column total_people_group_chat in community table
        // $this->db->select_as("(SELECT COUNT(*) FROM e_chat_participant WHERE e_chat_room_id  = chat_room_id AND nation_code= $nation_code AND is_active= 1)", "total_people_group_chat", 0);
        $this->db->select_as("$this->tbl_as.total_people_group_chat", "total_people_group_chat", 0);

        //by Donny Dennison - 21 july 2022 16:13
        //group buying
        // $this->db->select_as("(50)", "max_people_group_chat", 0);
        $this->db->select_as("IF(($this->tbl_as.c_community_category_id = 24),(100),(50))", "max_people_group_chat", 0);
        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);
        $this->db->select_as("$this->tbl_as.is_double_spt", "is_double_spt", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    public function getByIdIgnoreActive($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first('', 0);
    }

    public function getActiveByUserIdTitleCategoryDescription5Minutes($nation_code, $b_user_id, $title, $c_community_category_id, $deskripsi)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.title", $this->db->esc($title));
        $this->db->where_as("$this->tbl_as.c_community_category_id", $this->db->esc($c_community_category_id));
        $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
        $this->db->where_as("$this->tbl_as.cdate", $this->db->esc(date('Y-m-d H:i:s',strtotime("-5 minutes"))),"AND",">=");
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        return $this->db->get();
    }

    //START by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function countAllMyWishPost($nation_code, $pelangganAddress, $b_user_id="")
    public function countAllMyWishPost($nation_code, $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        //by Donny Dennison 13 september 2021 - 10:38
        //revamp-profile
        // $this->db->join_composite($this->tbl24, $this->tbl24_as, $this->__joinTbl24(), 'left');
        // $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
        // $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //advanced filter

        //by Donny Dennison 13 september 2021 - 10:38
        //revamp-profile
        // $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // $this->db->where_as("$this->tbl24_as.b_user_id", $this->db->esc($b_user_id),'OR','=',1,0);
        // $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($b_user_id),'AND','=',0,1);

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        // if(count($blockDataCommunity)>0){

        //     foreach($blockDataCommunity AS $block){

        //         $this->db->where_as("$this->tbl_as.id", $this->db->esc($block->custom_id), 'AND', '!=');

        //     }
        //     unset($blockDataCommunity, $block);

        // }

        // if(count($blockDataAccount)>0){

        //     foreach($blockDataAccount AS $block){

        //         $this->db->where_as("$this->tbl3_as.id", $this->db->esc($block->custom_id), 'AND', '!=');

        //     }
        //     unset($blockDataAccount, $block);

        // }

        // if(count($blockDataAccountReverse)>0){

        //     foreach($blockDataAccountReverse AS $block){

        //         $this->db->where_as("$this->tbl3_as.id", $this->db->esc($block->b_user_id), 'AND', '!=');

        //     }
        //     unset($blockDataAccountReverse, $block);

        // }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // public function getAllMyWishPost($nation_code, $page=1, $page_size=10, $pelangganAddress, $b_user_id="")
    // public function getAllMyWishPost($nation_code, $page=1, $page_size=10, $pelangganAddress, $b_user_id="", $language_id=1)
    public function getAllMyWishPost($nation_code, $page=1, $page_size=10, $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "community_category", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0); // by Muhammad Sofi - 11 November 2021 13:00 | not used
        // by Muhammad Sofi - 10 November 2021 09:19
        // change join query
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        
        // by Muhammad Sofi - 10 November 2021 09:19
        // change join query
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);

        // by Muhammad Sofi - 10 November 2021 09:19
        // change join query
        // $this->db->select_as("$this->tbl_as.kodepos", "kodepos");

        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelangganAddress->b_user_id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelangganAddress->b_user_id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
            // $this->db->select_as("(0)", "is_liked", 0);
            // $this->db->select_as("(0)", "is_disliked", 0);
        }

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        //by Donny Dennison 13 september 2021 - 10:38
        //revamp-profile
        $this->db->join_composite($this->tbl24, $this->tbl24_as, $this->__joinTbl24(), 'left');
        // $this->db->join_composite($this->tbl25, $this->tbl25_as, $this->__joinTbl25(), 'left');
        // $this->db->join_composite($this->tbl26, $this->tbl26_as, $this->__joinTbl26(), 'left');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        
        //advanced filter

        //by Donny Dennison 13 september 2021 - 10:38
        //revamp-profile
        // $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // $this->db->where_as("$this->tbl24_as.b_user_id", $this->db->esc($b_user_id),'OR','=',1,0);
        // $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($b_user_id),'AND','=',0,1);
        
        // by muhammad sofi 6 december 2022 | reduce query for is_liked_image and
        $this->db->where_as("$this->tbl24_as.b_user_id", $this->db->esc($b_user_id),'AND','=',0,0);
        // $this->db->where_as("$this->tbl26_as.b_user_id", $this->db->esc($b_user_id),'AND','=',0,1);

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataCommunity)>0){

            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        $this->db->order_by("$this->tbl_as.cdate", "DESC");
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }
    //END by Donny Dennison 13 september 2021 - 10:38

    public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$id';");
    }

    public function getLatestIdByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->order_by("$this->tbl_as.id", "DESC");
        return $this->db->get_first('', 0);
    }

    public function incrementView($nation_code, $id, $video_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl31` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND jenis = 'video' AND id = '$video_id' AND c_community_id = '$id';");
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

    public function getAllByids($nation_code, $ids, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "community_category");
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_nama_starter", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
        $this->db->select_as("$this->tbl_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos");
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
        $this->db->select_as("(0)", "total_dislikes", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_in("$this->tbl_as.id", $ids);
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        return $this->db->get('object', 0);
    }
}
