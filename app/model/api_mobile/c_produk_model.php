<?php
class C_Produk_Model extends JI_Model
{
    public $tbl = 'c_produk';
    public $tbl_as = 'cp';
    public $tbl2 = 'b_kategori';
    public $tbl2_as = 'bk';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = 'b_kondisi';
    public $tbl4_as = 'bkon';
    public $tbl5 = 'b_berat';
    public $tbl5_as = 'bber';
    public $tbl6 = 'd_wishlist';
    public $tbl6_as = 'dwl';
    public $tbl7 = 'c_produk_detail_automotive';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    public $tbl7_as = 'cpda';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    // public $tbl8 = 'b_kategori';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    // public $tbl8_as = 'bk_par';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    //by Donny Dennison - 14 july 2021 14:14
    //add-general-location-in-address
    // public $tbl9 = 'b_user_alamat_location';
    // public $tbl9_as = 'bual';

    //by Donny Dennison - 15 November 2021 16:28
    //change car and motorcycle to main category
    public $tbl10 = 'b_kategori';
    public $tbl10_as = 'bk_brand';

    public $tbl20 = 'b_user_alamat';
    public $tbl20_as = 'bua';
    public $tbl21 = 'b_lokasi';
    public $tbl21_as = 'blok';
    public $tbl22 = 'b_kodepos';
    public $tbl22_as = 'bkp';
    public $tbl23 = 'a_negara';
    public $tbl23_as = 'an';

    //START by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list
    public $tbl24 = 'c_produk_foto';
    public $tbl24_as = 'cpf';
    //END by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list


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
        $composites[] = $this->db->composite_create("$this->tbl_as.b_kategori_id", "=", "$this->tbl2_as.id");
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

    /**
     * Composite join for multiple PK on table 4
     * @return array composites join
     */
    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("COALESCE($this->tbl_as.b_kondisi_id,0)", "=", "$this->tbl4_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 5
     * @return array composites join
     */
    private function __joinTbl5()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $composites[] = $this->db->composite_create("COALESCE($this->tbl_as.b_berat_id,0)", "=", "$this->tbl5_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 6
     * @return array composites join
     */
    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl6_as.c_produk_id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 7
     * @return array composites join
     */
    private function __joinTbl7()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl7_as.c_produk_id");
        return $composites;
    }

    // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    /**
     * Composite join for multiple PK on table 8
     * @return array composites join
     */
    // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    // private function __joinTbl8()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.parent_nation_code", "=", "$this->tbl8_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.parent_b_kategori_id", "=", "$this->tbl8_as.id");
    //     return $composites;
    // }

    //by Donny Dennison - 14 july 2021 14:14
    //add-general-location-in-address
    // private function __joinTbl9()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl9_as.nation_code", "=", "$this->tbl20_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl9_as.postal_sector", "=", "SUBSTR($this->tbl20_as.kodepos,1,2)");
    //     return $composites;
    // }

    //by Donny Dennison - 15 November 2021 16:28
    //change car and motorcycle to main category
    private function __joinTbl10()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl10_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.brand", "=", "$this->tbl10_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 20
     * @return array composites join
     */
    private function __joinTbl20()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl20_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl20_as.b_user_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl20_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 21
     * @return array composites join
     */
    private function __joinTbl21()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl20_as.nation_code", "=", "COALESCE($this->tbl21_as.nation_code,0)");
        $composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_id", "=", "COALESCE($this->tbl21_as.id,0)");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 22
     * @return array composites join
     */
    private function __joinTbl22()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl20_as.nation_code", "=", "$this->tbl22_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_id", "=", "$this->tbl22_as.id");
        return $composites;
    }

    /**
     * Composite join for multiple PK on table 23
     * @return array composites join
     */
    private function __joinTbl23()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl23_as.nation_code");
        return $composites;
    }

    //START by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list
    private function __joinTbl24()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl24_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl24_as.c_produk_id");
        return $composites;
    }
    //END by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list

    /**
     * Get Last ID for sequencer on column id in c_produk table
     * @param  integer $nation_code [description]
     * @return integer              last id + 1
     */
    // public function getLastId($nation_code)
    // {
    //     // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where("nation_code", $nation_code);
    //     // $d = $this->db->get_first('', 0);
    //     // if (isset($d->last_id)) {
    //     //     return (int) $d->last_id;
    //     // }
    //     // return 0;
    //     // $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' FOR UPDATE;";
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."') AND nation_code = '".$nation_code."' FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
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

        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }

        if (isset($di['telp'])) {
            if (strlen($di['telp'])) {
                $di['telp'] = $this->__encrypt($di['telp']);
            }
        }

        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    /**
     * Insert into database, wiht ignore option (slower)
     * @param array $di name value pair describes column and value to insert into table
     */
    public function set2($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert_ignore($this->tbl, $di, 0, 0);
    }

    /**
     * Update table c_produk by user id and c_produk id
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    public function update($nation_code, $b_user_id, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }

        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }

        if (isset($di['telp'])) {
            if (strlen($di['telp'])) {
                $di['telp'] = $this->__encrypt($di['telp']);
            }
        }

        $this->db->where('nation_code', $nation_code);
        $this->db->where('b_user_id', $b_user_id);
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
    public function update2($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    /**
     * Update multiple rows on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  string $ids          ID(s) from c_produk separated by commas
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    public function updateMass($nation_code, $b_user_id, $ids, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where_in("id", $ids);
        return $this->db->update($this->tbl, $du, 0);
    }

    /**
     * Update a row on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    public function updateByUserId($nation_code, $b_user_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where('b_user_id', $b_user_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }

        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }

        $this->db->where("nation_code", $nation_code);
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where("b_user_alamat_id", $b_user_alamat_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $id, $b_user_id)
    {
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('id', $id);
        $this->db->where('b_user_id', $b_user_id);
        return $this->db->delete($this->tbl);
    }
    public function deleteMass($nation_code, $b_user_id, $pids)
    {
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where_in("id", $pids);
        return $this->db->delete($this->tbl);
    }

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

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    // public function countAll($nation_code, $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array())
    // public function countAll($nation_code, $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='')
    // public function countAll($nation_code, $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='', $language_id=1)
    public function countAll($nation_code, $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $b_brand_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='', $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $language_id=1, $c_brand_name= "")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        if (mb_strlen($keyword)>0) {
            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        }
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        if (mb_strlen($keyword)>0) {
            $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');
        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

        //by Donny Dennison - 19 january 2022 10:35
        //merge table free product to table product
        $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())", "CURRENT_DATE()", "AND", ">=", 0, 0);
        
        //by Donny Dennison - 28 june 2020 11:06
        //request by Mr Jackie, still show prodcut even the stock is zero
        // only show stok qty above zero
        // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        // if($product_type == 'Protection' || $product_type == 'MeetUp' || $product_type == 'Free'){
        if($product_type == 'Protection' || $product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Santa'){

            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
            // $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

        // }else if($product_type == 'ProtectionAndMeetUp'){

        //     $this->db->where_in("$this->tbl_as.product_type", array(0=>"Protection",1=>"MeetUp"));
            // $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

        }else if($product_type == 'ProtectionAndMeetUpAndAutomotive'){

            $this->db->where_in("$this->tbl_as.product_type", array(0=>"Protection",1=>"MeetUp",2=>"Automotive"));

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        }else{
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");
        }

        // if($show_car == 0){
        //     if (!in_array("32", $b_kategori_ids)){
        //         $this->db->where_as("$this->tbl_as.b_kategori_id", 32, "AND", "!=");
        //     }
        // }

        if($product_type == 'MeetUp'){
            if($soldout_meetup == 'yes'){
                $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
            }else if($soldout_meetup == 'no'){
                $this->db->where_as("$this->tbl_as.stok", $this->db->esc('1'));
            }
        }

        //advanced filter
        if($product_type != 'Free'){

            if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
                $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
            } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
                $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
            } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
                $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
            }
            if (is_array($b_kondisi_ids) && count($b_kondisi_ids)>0) {
                // $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
                $kondisiString = "";
                foreach ($b_kondisi_ids as $kondisi) {
                    $kondisiString .= $this->db->esc($kondisi).", ";
                }
                unset($kondisi);
                $kondisiString = rtrim($kondisiString, ", ");
                $this->db->where_as("IF($this->tbl_as.product_type = 'Protection', $this->tbl_as.b_kondisi_id IN(".$kondisiString."), 1", 1, 'AND', '=', 0, 1);
            }
        }

        if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
            $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'OR');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

            $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }

        if (is_array($b_brand_ids) && count($b_brand_ids)>0) {
            $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.brand", $b_brand_ids, 0, 'OR');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_brand_ids, 0, 'or');

            $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }

        if (!empty($c_brand_name)) {
            $this->db->where_as("$this->tbl_as.brand", $this->db->esc($c_brand_name));
        }

        if (intval($kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);

            //by Donny Dennison - 15 february 2022 9:50
            //category product and category community have more than 1 language
            // $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", addslashes($keyword), 'or', '%like%');

            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');
            
            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
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

        //START by Donny Dennison - 08 november 2022 11:03
        //new feature, block product
        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 08 november 2022 11:03
        //new feature, block product

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

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array())
    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='')
    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='', $language_id=1)
    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $b_brand_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='', $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $language_id=1, $c_brand_name ="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        
        // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // START by Muhammad Sofi - 15 November 2021 10:17
        // remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // End by Muhammad Sofi - 15 November 2021 10:17
        // $this->db->select_as("'0'", "b_berat_id", 0);
        // $this->db->select_as("''", "b_berat_nama", 0);
        // $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        if (mb_strlen($keyword)>0 || is_array($b_brand_ids) || count($b_brand_ids)>0 || !empty($c_brand_name)) {
            $this->db->select_as("IF($this->tbl10_as.nama IS NULL && $this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, $this->tbl10_as.nama)", "brand", 0);
        }else{
            $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        }

        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        // $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        // $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        // $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("''", "alamat", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("UPPER(CAST(CONCAT('0 ', SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2").")))) AS CHAR(50)))", "alamat3", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        //by Donny Dennison - 7 december 2020 11:03
        //add new product type(meetup)
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);

        //by Donny Dennison - 4 january 2021 10:23
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 4 january 2021 10:23

        if(isset($pelangganAddress->alamat2)){
            
            $this->db->select_as("2 * 6371000 * ASIN(
    sqrt(
        SIN(6.28319 * (".$pelangganAddress->latitude." - $this->tbl_as.latitude) / 360 / 2) * SIN(6.28319 * (".$pelangganAddress->latitude." - $this->tbl_as.latitude) / 360 / 2)
        +
        COS( 6.28319 * ".$pelangganAddress->latitude." / 360  ) * COS( 6.28319 * $this->tbl_as.latitude / 360  )
        *
        SIN(6.28319 * (".$pelangganAddress->longitude." - $this->tbl_as.longitude) / 360 / 2) * SIN(6.28319 * (".$pelangganAddress->longitude." - $this->tbl_as.longitude) / 360 / 2)
    )
)", "dist_in_meters", 0);

        }

        //END by Donny Dennison - 4 january 2021 10:23

        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        // $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        // if(isset($pelangganAddress->b_user_id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
            // $this->db->select_as("(0)", "is_liked", 0);
        // }

        //END by Donny Dennison - 8 july 2021 11:02

        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        
        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        if (mb_strlen($keyword)>0 || is_array($b_brand_ids) || count($b_brand_ids)>0 || !empty($c_brand_name)) {
            $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');
        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        
        //by Donny Dennison - 19 january 2022 10:35
        //merge table free product to table product
        $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())", "CURRENT_DATE()", "AND", ">=", 0, 0);
        
        //by Donny Dennison
        //show product even the stock is 0 from Mr. Jackie
        // only show stok qty above zero
        // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        // if($product_type == 'Protection' || $product_type == 'MeetUp' || $product_type == 'Free'){
        if($product_type == 'Protection' || $product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Santa'){

            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
            // $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

        // }else if($product_type == 'ProtectionAndMeetUp'){

        //     $this->db->where_in("$this->tbl_as.product_type", array(0=>"Protection",1=>"MeetUp"));
        //     $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

        }else if($product_type == 'ProtectionAndMeetUpAndAutomotive'){

            $this->db->where_in("$this->tbl_as.product_type", array(0=>"Protection",1=>"MeetUp",2=>"Automotive"));

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        }else{
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");
        }

        // if($show_car == 0){
        //     if (!in_array("32", $b_kategori_ids)){
        //         $this->db->where_as("$this->tbl_as.b_kategori_id", 32, "AND", "!=");
        //     }
        // }

        if($product_type == 'MeetUp'){
            if($soldout_meetup == 'yes'){
                $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
            }else if($soldout_meetup == 'no'){
                $this->db->where_as("$this->tbl_as.stok", $this->db->esc('1'));
            }
        }

        //advanced filter
        if($product_type != 'Free'){

            if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
                $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
            } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
                $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
            } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
                $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
            }
            if (is_array($b_kondisi_ids) && count($b_kondisi_ids)>0) {
                // $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
                $kondisiString = "";
                foreach ($b_kondisi_ids as $kondisi) {
                    $kondisiString .= $this->db->esc($kondisi).", ";
                }
                unset($kondisi);
                $kondisiString = rtrim($kondisiString, ", ");
                $this->db->where_as("IF($this->tbl_as.product_type = 'Protection', $this->tbl_as.b_kondisi_id IN(".$kondisiString."), 1", 1, 'AND', '=', 0, 1);
            }
        
        }

        if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
            $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'AND');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');
        }

        if (is_array($b_brand_ids) && count($b_brand_ids)>0 && !empty($c_brand_name)) {
            $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.brand", $b_brand_ids, 0, 'OR');

            $this->db->where_as("$this->tbl_as.brand", $this->db->esc($c_brand_name));
            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_brand_ids, 0, 'or');

            $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }else if(is_array($b_brand_ids) && count($b_brand_ids)>0){
            $this->db->where_in("$this->tbl_as.brand", $b_brand_ids, 0, 'AND');
        }else if(!empty($c_brand_name)){
            $this->db->where_as("$this->tbl_as.brand", $this->db->esc($c_brand_name));
        }

        if (intval($kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        }
        if (strlen($kategori_id)) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        }
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);

            //by Donny Dennison - 15 february 2022 9:50
            //category product and category community have more than 1 language
            // $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", addslashes($keyword), 'or', '%like%');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_as("$this->tbl8_as.nama", addslashes($keyword), 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'and', '%like%', 0, 1);
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

        //START by Donny Dennison - 08 november 2022 11:03
        //new feature, block product
        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 08 november 2022 11:03
        //new feature, block product

        //by Donny Dennison - 1 desember 2020 16:29
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 23 desember 2020 15:44
        if(isset($pelangganAddress->alamat2) && $sort_col == "$this->tbl_as.kodepos"){
            $this->db->order_by("$this->tbl_as.cdate", "DESC");
            $this->db->order_by("dist_in_meters", "ASC");
            $this->db->order_by("alamat4", "DESC");
            // $this->db->order_by("alamat3", "DESC");
        }else if($sort_col == "$this->tbl_as.harga_jual"){
            $this->db->order_by("CAST(".$sort_col." AS DECIMAL(21,2))", $sort_direction);
        }else{
            $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        }
        //END by Donny Dennison - 23 desember 2020 15:44
        
        //by Donny Dennison - 4 january 2021 10:23
        //list-produt-sameStreet-neighborhood-all-from-user-address
        //START by Donny Dennison - 4 january 2021 10:23
        // $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
        $this->db->page($page, $page_size);
        //END by Donny Dennison - 4 january 2021 10:23

        return $this->db->get('object', 0);
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // public function getById($nation_code, $pid)
    public function getById($nation_code, $pid, $pelanggan, $getProductType, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
        $this->db->select_as("$this->tbl_as.brand", "b_brand_id", 0);
        
        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl2_as.image_icon", "kategori_icon", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl3_as.is_online", "is_online", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);

        if($getProductType == "Automotive"){

            // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        }

        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // End by Muhammad Sofi - 15 November 2021 10:17

        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

        // $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        // $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        // $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
        // $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
        // $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
        // $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
        // $this->db->select_as("$this->tbl_as.is_visible", "is_visible", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

        //by Donny Dennison - 30 july 2020 19:25
        // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        
        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
            // $this->db->select_as("(0)", "is_liked", 0);
        // }

        //by Donny Dennison - 19 january 2022 10:35
        //merge table free product to table product
        // $this->db->select_as("IF($this->tbl_as.product_type = 'Free',".$this->__decrypt("$this->tbl_as.telp").",".$this->__decrypt("$this->tbl3_as.telp").")", "telp", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.email").',"")', "email", 0);
        $this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
        $this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
        $this->db->select_as("$this->tbl3_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

        if($getProductType == "Automotive"){

            $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        }

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        return $this->db->get_first('', 0);
    }

    //by Donny Dennison - 31 august 2022 21:18
    //deleted product can still show in chat
    public function getByIdIgnoreActive($nation_code, $pid, $pelanggan, $getProductType, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
        
        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl2_as.image_icon", "kategori_icon", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        // $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl3_as.is_online", "is_online", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);

        if($getProductType == "Automotive"){

            // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        }

        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // End by Muhammad Sofi - 15 November 2021 10:17
        
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

        // $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        // $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        // $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
        // $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
        // $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
        // $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
        // $this->db->select_as("$this->tbl_as.is_visible", "is_visible", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

        //by Donny Dennison - 30 july 2020 19:25
        // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        
        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
        //     $this->db->select_as("(0)", "is_liked", 0);
        // }

        //by Donny Dennison - 19 january 2022 10:35
        //merge table free product to table product
        // $this->db->select_as("IF($this->tbl_as.product_type = 'Free',".$this->__decrypt("$this->tbl_as.telp").",".$this->__decrypt("$this->tbl3_as.telp").")", "telp", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.email").',"")', "email", 0);
        $this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
        $this->db->select_as("$this->tbl_as.end_date", "end_date", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

        if($getProductType == "Automotive"){

            $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        }

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        // $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        return $this->db->get_first('', 0);
    }

    // public function getByUserId($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="")
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);

    //     // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);

    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        
    //     //by Donny Dennison - 28 august 2020 15:14
    //     //add new api for best shop in homepage
    //     // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama"), ',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);

    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);

    //     // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // END by Muhammad Sofi - 15 November 2021 10:17

    //     $this->db->select_as("COALESCE($this->tbl_as.alamat2,'')", "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
    //     // //by Donny Dennison - 15 November 2021 16:28
    //     // //change car and motorcycle to main category
    //     // // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     // $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');

    //     //by Donny Dennison - 15 November 2021 16:28
    //     //change car and motorcycle to main category
    //     $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_as("$this->tbl8_as.nama", addslashes($keyword), 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //         $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
    //     }
    //     $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countByUserId($nation_code, $b_user_id, $keyword="")
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');

    //     //by Donny Dennison - 15 November 2021 16:28
    //     //change car and motorcycle to main category
    //     $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    //by Donny Dennison - 28 august 2020 15:14
    //add new api for best shop in homepage
    // public function countByUserIdForBestShop($nation_code, $b_user_id)
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     $this->db->where_as("COALESCE($this->tbl_as.stok,0)", $this->db->esc(0), 'AND', '>');
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    public function getMyProduk($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $is_published="", $product_type="All", $show_soldout="", $pelanggan, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);

        // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // END by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        
        $this->db->select_as("'0'", "b_berat_id", 0);
        $this->db->select_as("''", "b_berat_nama", 0);
        $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);

        // //by Donny Dennison - 15 November 2021 16:28
        // //change car and motorcycle to main category
        // // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        // $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

        $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
        $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        
        if(isset($pelanggan->id)){
            $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = '$pelanggan->id' AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        }else{
            $this->db->select_as("(0)", "is_liked", 0);
        }

        //by Donny Dennison - 14 december 2020 11:10
        //add new product type(meetup)
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        
        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'), 'AND', '=');
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));

        if ($product_type != 'All') {
            if ($product_type == 'AutomotiveCar') {
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
                $this->db->where_as("$this->tbl_as.b_kategori_id", 32);
            }else if ($product_type == 'AutomotiveMotorcycle') {
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
                $this->db->where_as("$this->tbl_as.b_kategori_id", 33);
            }else{
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
            }
        }

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

        if($show_soldout == 'yes'){
            $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
        }else if($show_soldout == 'no'){
            $this->db->where_as("$this->tbl_as.stok", 1, "AND", ">=");
        }

        if (strlen($is_published)>0) {
            $is_published = (int) $is_published;
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published), 'AND', '=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            
            //by Donny Dennison - 15 february 2022 9:50
            //category product and category community have more than 1 language
            // $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", addslashes($keyword), 'or', '%like%');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_as("$this->tbl8_as.nama", addslashes($keyword), 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }

        $this->db->order_by($sort_col, $sort_direction);
        
        if($page != 0 && $page_size !=0){
            $this->db->page($page, $page_size);
        }
        
        return $this->db->get('object', 0);
    }

    public function countMyProduct($nation_code, $b_user_id, $keyword="", $is_published="", $product_type="All", $show_soldout="")
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');

        if ($product_type != 'All') {
            if ($product_type == 'AutomotiveCar') {
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
                $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc(32));
            }else if ($product_type == 'AutomotiveMotorcycle') {
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
                $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc(33));
            }else{
                $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
            }
        }

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

        if($show_soldout == 'yes'){
            $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
        }else if($show_soldout == 'no'){
            $this->db->where_as("$this->tbl_as.stok", 1, "AND", ">=");
        }

        if (strlen($is_published)>0) {
            $is_published = (int) $is_published;
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published), 'AND', '=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            
            //by Donny Dennison - 15 february 2022 9:50
            //category product and category community have more than 1 language
            // $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", addslashes($keyword), 'or', '%like%');

            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function setTerjuals($pids)
    {
        if (is_array($pids) && count($pids)) {
            $sql = '';
            //building multi query
            foreach ($pids as $pid) {
                $sql .= 'UPDATE '.$this->tbl.' SET terjual = terjual + '.$pid->qty.', stok = stok - '.$pid->qty.' WHERE id = '.$pid->id.';';
                $sql .= 'UPDATE '.$this->tbl.' SET sales_rate = ((sales_count / terjual)*100) WHERE id = '.$pid->id.';';
            }
            $this->db->query_multi($sql);
        }
    }
    public function getByProdukIds($ids)
    {
        $this->db->where_in('id', $ids);
        return $this->db->get();
    }
    public function getByIds($nation_code, $ids)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_in('id', $ids);
        return $this->db->get();
    }
    public function getByIdsActive($nation_code, $ids)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as('is_active', $this->db->esc(1));
        $this->db->where_in('id', $ids);
        return $this->db->get();
    }
    public function getActiveByUserIdAndIds($nation_code, $b_user_id, $ids)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('is_active', 1);
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where_in('id', $ids);
        return $this->db->get();
    }
    public function getByIdsForCart($nation_code, $ids)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'default.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.is_fashion,'0')", "is_fashion", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_in("$this->tbl_as.id", $ids);
        $this->db->order_by("$this->tbl_as.courier_services", "desc");
        $this->db->order_by("$this->tbl_as.is_include_delivery_cost", "asc");
        $this->db->order_by("$this->tbl2_as.is_fashion", "asc");
        return $this->db->get('', 0);
    }
    public function getHomePage($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_dir="asc", $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    {
        $this->db->flushQuery();
        $this->db->cache_save = 0;
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

        $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);

        // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // END by Muhammad Sofi - 15 November 2021 10:17 
        
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
        // //by Donny Dennison - 15 November 2021 16:28
        // //change car and motorcycle to main category
        // // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        // $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("(0)", "is_liked", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", '1');
        $this->db->where_as("$this->tbl_as.is_visible", '1');
        $this->db->where_as("$this->tbl_as.is_active", '1');
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        //by Donny Dennison
        //show product even the stock is 0 from Mr. Jackie
        // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

        //advanced filter
        if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
            $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        }
        if (count($b_kondisi_ids)>0) {
            $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
        }
        if (count($b_kategori_ids)>0) {
            $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'OR');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

            $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        //end advanced filter

        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_as("$this->tbl8_as.nama", addslashes($keyword), 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');

            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }

        //by Donny Dennison - 13-07-2020 16:08
        //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
        // $this->db->order_by($sort_col, $sort_dir);
        $this->db->order_by("$this->tbl_as.".$sort_col, $sort_dir);
        
        $this->db->page($page, $page_size);
        return $this->db->get('object', 0);
    }

    public function countHomePage($nation_code, $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    {
        $this->db->flushQuery();
        $this->db->cache_save = 0;
        $this->db->select_as("COUNT(*)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        //by Donny Dennison - 15 November 2021 16:28
        //change car and motorcycle to main category
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

        //advanced filter
        if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
            $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        }
        if (count($b_kondisi_ids)>0) {
            $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
        }
        if (count($b_kategori_ids)>0) {
            $this->db->where_as("1", "1", 'or', '<>', 1, 0);
            $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'OR');

            // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
            // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

            $this->db->where_as("1", "1", 'and', '<>', 0, 1);
        }
        //end advanced filter

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", '1');
        $this->db->where_as("$this->tbl_as.is_visible", '1');
        $this->db->where_as("$this->tbl_as.is_active", '1');
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), 'or', '%like%');
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), 'or', '%like%');

            //by Donny Dennison - 15 November 2021 16:28
            //change car and motorcycle to main category
            $this->db->where_as("$this->tbl10_as.nama", addslashes($keyword), 'or', '%like%');
            
            $this->db->where_as("$this->tbl_as.brand", addslashes($keyword), 'or', '%like%', 0, 1);
        }
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function substractStok($nation_code, $c_produk_id, $qty)
    {
        $qty = (int) $qty;
        $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`-$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = ".$this->db->esc($c_produk_id).";";
        return $this->db->exec($sql);
    }
    public function addStok($nation_code, $c_produk_id, $qty)
    {
        $qty = (int) $qty;
        $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`+$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = ".$this->db->esc($c_produk_id).";";
        return $this->db->exec($sql);
    }
    public function getByIdRaw($nation_code, $id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("id", $this->db->esc($id));
        return $this->db->get_first('', 0);
    }
    public function getByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where("b_user_alamat_id", $b_user_alamat_id);
        $this->db->where("is_active", 1);
        return $this->db->get();
    }
    public function checkTakeDown($nation_code, $id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.reported_status", $this->db->esc("takedown"));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first('', 0);
    }

    //by Donny Dennison - 28 july 2020 11:39
    // check the address if there is product using this address then cannot delete
    public function getActiveByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.b_user_alamat_id", $this->db->esc($b_user_alamat_id));
        // $this->db->where_as("$this->tbl_as.is_published", '1');
        // $this->db->where_as("$this->tbl_as.is_visible", '1');
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        return $this->db->get();
    }

    //by Donny Dennison - 29 july 2020 - 15:47
    //prevent insert product duplication
    public function getActiveByUserIdProductNameWeightDimensionPrice($nation_code, $b_user_id, $product_name, $weight, $dimension_long, $dimension_width, $dimension_height, $harga_jual)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
        $this->db->where_as("$this->tbl_as.berat", $this->db->esc($weight));
        $this->db->where_as("$this->tbl_as.dimension_long", $this->db->esc($dimension_long));
        $this->db->where_as("$this->tbl_as.dimension_width", $this->db->esc($dimension_width));
        $this->db->where_as("$this->tbl_as.dimension_height", $this->db->esc($dimension_height));
        $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Protection'));
        return $this->db->get();
    }
    
    public function getActiveByUserIdProductNameCategoryDescriptionPrice($nation_code, $b_user_id, $product_name, $b_kategori_id, $deskripsi, $harga_jual)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
        $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
        $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('MeetUp'));
        return $this->db->get();
    }
    
    public function getActiveByUserIdProductNameDescriptionTelephone($nation_code, $b_user_id, $product_name, $deskripsi, $telp)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
        $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
        $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), $this->db->esc($telp));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Free'));
        return $this->db->get();
    }

    public function getActiveByUserIdProductNameBrandModelColorYearDescriptionPrice($nation_code, $b_user_id, $product_name, $brand, $model, $color, $year, $deskripsi, $harga_jual)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
        $this->db->where_as("$this->tbl_as.brand", $this->db->esc($brand));
        $this->db->where_as("$this->tbl7_as.model", $this->db->esc($model));
        $this->db->where_as("$this->tbl7_as.color", $this->db->esc($color));
        $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
        $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
        $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
        return $this->db->get();
    }

    //by Donny Dennison - 26 november 2021 16:43
    //get product automotive car list
    public function countAllAutomotive($nation_code, $harga_jual_min="", $harga_jual_max="", $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $b_brand_id="", $year="", $b_kategori_id=32, $keyword="", $blockDataAccount, $blockDataAccountReverse, $blockDataProduct)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
        // $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", '1');
        $this->db->where_as("$this->tbl_as.is_visible", '1');
        $this->db->where_as("$this->tbl_as.is_active", '1');
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
        $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));

        //advanced filter
        if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
            $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        }
        if (is_array($b_brand_id)) {
            $this->db->where_as("$this->tbl_as.brand", $this->db->esc($b_brand_id['b_brand_id']), 'OR', '=', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.brand)", addslashes($b_brand_id['brand_name']), 'AND', '%like%', 0, 1);
        }
        if ($year>0) {
            $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.alamat2"), addslashes($keyword), 'OR', '%like%', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", addslashes($keyword), 'AND', '%like%', 0, 1);
        }
        
        if($kelurahan != 'All'){
            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
        }
        
        $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        //START by Donny Dennison - 08 november 2022 15:12
        //new feature, block product(block account or product in automotive product)
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

        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 08 november 2022 15:12
        //new feature, block product(block account or product in automotive product)

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAllAutomotive($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $harga_jual_min="", $harga_jual_max="", $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $b_brand_id="", $year="", $b_kategori_id=32, $keyword="", $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan, $language_id=1)
    {
        $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("'0'", "b_berat_id", 0);
        // $this->db->select_as("''", "b_berat_nama", 0);
        // $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        // $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
        //     $this->db->select_as("(0)", "is_liked", 0);
        // }

        $this->db->select_as("$this->tbl20_as.negara", "negara", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
        $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        
        //advanced filter
        if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
            $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
            $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        }
        if (is_array($b_brand_id)) {
            $this->db->where_as("$this->tbl_as.brand", $this->db->esc($b_brand_id['b_brand_id']), 'OR', '=', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.brand)", addslashes($b_brand_id['brand_name']), 'AND', '%like%', 0, 1);
        }
        if ($year>0) {
            $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl_as.alamat2"), addslashes($keyword), 'OR', '%like%', 1, 0);
            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", addslashes($keyword), 'AND', '%like%', 0, 1);
        }

        if($kelurahan != 'All'){
            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
        }
        
        $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        //START by Donny Dennison - 08 november 2022 15:12
        //new feature, block product(block account or product in automotive product)
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

        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 08 november 2022 15:12
        //new feature, block product(block account or product in automotive product)

        if($sort_col == "$this->tbl_as.harga_jual"){
            $this->db->order_by("CAST(".$sort_col." AS DECIMAL(21,2))", $sort_direction);
        }else{
            $this->db->order_by($sort_col, $sort_direction);
        }
        
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    //by Donny Dennison - 02 november 2022 14:21
    //new feature, block community post or account

    //by Donny Dennison - 10 december 2021 13:36
    //add feature hot item di homepage
    // public function getAllHomepage($nation_code, $page=1, $page_size=8, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $pelanggan, $option="option1", $product_type = "Protection", $type="All", $language_id)
    public function getAllHomepage($nation_code, $page=1, $page_size=8, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $pelanggan, $option="option1", $product_type = "Protection", $type="All", $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $language_id)
    {
        $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
        
        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("'0'", "b_berat_id", 0);
        // $this->db->select_as("''", "b_berat_nama", 0);
        // $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
        // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
        // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.stok", "stok", 0);
        // $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        // $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
        // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);
        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        // $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        // $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
        //     $this->db->select_as("(0)", "is_liked", 0);
        // }
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        // $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.stok", $this->db->esc('1'),'AND','>=');

        //by Donny Dennison - 3 june 2022 13:10
        //new feature, product type santa
        $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

        if($type == 'neighborhood'){

            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'district'){

            $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'city'){
            
            $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'province'){
            
            $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }

        //START by Donny Dennison - 02 november 2022 14:21
        //new feature, block community post or account
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
        //END by Donny Dennison - 02 november 2022 14:21
        //new feature, block community post or account

        //START by Donny Dennison - 08 november 2022 11:03
        //new feature, block product
        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $this->db->where_in("$this->tbl_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 08 november 2022 11:03
        //new feature, block product

        if($option == 'option1'){
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Free"),"AND","!=");
            // $this->db->where_as("(SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1)", $this->db->esc('5'),"AND",">=");
            // $this->db->where_as("$this->tbl_as.total_likes", $this->db->esc('5'),"AND",">=");
            $this->db->where_as("(SELECT COUNT(DISTINCT b_user_id) FROM f_discussion WHERE product_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1 AND parent_f_discussion_id = 0 AND b_user_id != $this->tbl_as.b_user_id)", $this->db->esc('10'),"AND",">=");
            $this->db->where_as("(SELECT COUNT(*) FROM c_product_share_history WHERE c_produk_id = $this->tbl_as.id AND nation_code = $nation_code AND b_user_id != $this->tbl_as.b_user_id)", $this->db->esc('5'),"AND",">=");

            // $this->db->order_by("$this->tbl_as.total_likes", "DESC");
            $this->db->order_by("(SELECT COUNT(DISTINCT b_user_id) FROM f_discussion WHERE product_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1 AND parent_f_discussion_id = 0 AND b_user_id != $this->tbl_as.b_user_id)", "DESC");
            $this->db->order_by("(SELECT COUNT(*) FROM c_product_share_history WHERE c_produk_id = $this->tbl_as.id AND nation_code = $nation_code AND b_user_id != $this->tbl_as.b_user_id)", "DESC");
        }

        if($option == 'option2'){
            // $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
            $this->db->where_in("$this->tbl_as.product_type", array(0=>"MeetUp",1=>"Automotive"));
            // $this->db->where_as("$this->tbl_as.b_kategori_id", 32, "AND", "!=");
            $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-1 month"))), "AND", ">=");

            $this->db->order_by("$this->tbl_as.cdate", "DESC");
        }

        if($page != 0 && $page_size !=0){
            $this->db->page($page, $page_size);
        }
        return $this->db->get('object', 0);
    }

    public function updateTotal($nation_code, $product_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$product_id';");
    }

    public function getAllForMigrationAddress($nation_code)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl20_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl20_as.kelurahan, ', ', $this->tbl20_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl20_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl20_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl20_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl20_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl20_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl20_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl20_as.longitude", "longitude", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.kodepos",$this->db->esc(''));

        return $this->db->get('object', 0);
    }

    //START by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list
    // public function getAllVideo($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $type="", $pelangganAddress, $language_id=1, $watched_video, $b_kategori_ids)
    // {

    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     // $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);

    //     // if(isset($pelangganAddress->b_user_id)){
    //     //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
    //     // }else{
    //     //     $this->db->select_as("(0)", "is_liked", 0);
    //     // }

    //     $this->db->select_as("$this->tbl24_as.id", "video_id", 0);
    //     $this->db->select_as("$this->tbl24_as.url", "url", 0);
    //     $this->db->select_as("$this->tbl24_as.url_thumb", "url_thumb", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl24, $this->tbl24_as, $this->__joinTbl24(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())", "CURRENT_DATE()", "AND", ">=", 0, 0);
    //     $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");
    //     $this->db->where_as("$this->tbl24_as.jenis", $this->db->esc('video'));
    //     // $this->db->where_as("$this->tbl24_as.convert_status", $this->db->esc('processed'));
    //     $this->db->where_as("$this->tbl24_as.is_active", $this->db->esc(1));

    //     if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
    //         // $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);

    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

    //         // $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }

    //     if(is_array($watched_video)){
    //         if(count($watched_video) > 0){
    //             $listArray = array();
    //             foreach ($watched_video as $key => $watched) {
    //                 if(isset($watched['product_id']) && isset($watched['video_id'])){
    //                     if($watched['product_id'] && $watched['video_id']){
    //                         $listArray[] = $watched['product_id'].'-'.$watched['video_id'];
    //                     }
    //                 }
    //             }
    //             unset($watched_video, $watched);

    //             $this->db->where_in("CONCAT($this->tbl_as.id,'-',$this->tbl24_as.id)", $listArray, 1);
    //             unset($listArray);
    //         }
    //     }

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
    //             $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%');
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

    //     // $this->db->order_by($sort_col, $sort_direction);
    //     $this->db->order_by("RAND()");
    //     // $this->db->page($page, $page_size);
    //     $this->db->limit($page_size);

    //     return $this->db->get('object', 0);

    // }
    //END by Donny Dennison - 14 july 2022 14:28
    //new api product/video_list

    public function getAllVideoManualQuery($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $type="", $pelangganAddress, $language_id=1, $watched_video, $b_kategori_ids)
    {
        //pagination logic
        $page = ($page * $page_size) - $page_size;

        $sql = "SELECT
        *
FROM(
SELECT 
    cp.id AS 'id',
    cp.b_kategori_id AS 'b_kategori_id',
    IF(".$language_id." = 4 AND bk.thailand IS NOT NULL AND bk.thailand != '', bk.thailand, IF(".$language_id." = 3 AND bk.korea IS NOT NULL AND bk.korea != '', bk.korea, IF(".$language_id." = 2 AND bk.indonesia IS NOT NULL AND bk.indonesia != '', bk.indonesia, bk.nama))) AS 'kategori',
    cp.b_user_id AS 'b_user_id_seller',
    cp.nama AS 'nama',
    cp.harga_jual AS 'harga_jual',
    cp.product_type AS 'product_type',
    cpf.id AS 'video_id',
    cpf.url AS 'url',
    cpf.url_thumb AS 'url_thumb'
FROM
    `c_produk_foto` cpf
        LEFT JOIN
    `c_produk` cp ON cp.nation_code = cpf.nation_code AND cp.id = cpf.c_produk_id
        LEFT JOIN
    `b_kategori` bk ON cp.nation_code = bk.nation_code AND cp.b_kategori_id = bk.id
        LEFT JOIN
    `b_user` bu ON cp.nation_code = bu.nation_code AND cp.b_user_id = bu.id
WHERE
    cp.nation_code = ".$nation_code." ";
        $sql .= "AND cp.is_published = '1'
        AND cp.is_visible = '1'
        AND cp.is_active = '1'
        AND bu.is_active = '1'
        AND COALESCE(cp.end_date,CURRENT_DATE()) >= CURRENT_DATE() 
        AND cp.stok > '0'
        AND cpf.jenis = 'video'
        AND cpf.is_active = '1' ";

        //advanced filter
        if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
            $sql .= "AND cp.b_kategori_id IN (";
            foreach ($b_kategori_ids as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
        }

        if(is_array($watched_video)){
            if(count($watched_video) > 0){
                $listArray = array();
                foreach ($watched_video as $key => $watched) {
                    if(isset($watched['product_id']) && isset($watched['video_id'])){
                        if($watched['product_id'] && $watched['video_id']){
                            $listArray[] = $watched['product_id'].'-'.$watched['video_id'];
                        }
                    }
                }
                unset($watched_video, $watched);

                $sql .= "AND CONCAT(cp.id,'-',cpf.id) NOT IN (";
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
                $sql .= "AND LOWER(CAST(".$this->__decrypt("cp.alamat2")." AS CHAR(50))) LIKE '%".addslashes(strtolower($pelangganAddress->alamat2))."%' ";
                $sql .= "AND LOWER(cp.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'neighborhood'){
                $sql .= "AND LOWER(cp.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'district'){
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'city'){
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'province'){
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }
        }

        if(date("j") % 2 == 0){
            $sql .= "AND MOD(MONTH(cp.cdate),2)=0 ";
        }else{
            $sql .= "AND MOD(MONTH(cp.cdate),2)=1 ";
        }

        $sql .= "LIMIT 500) AS a 
        ORDER BY RAND() ASC 
        LIMIT ".$page.", ".$page_size;

        return $this->db->query($sql);
    }

    public function getAllVideoManualQueryV2($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $type="", $pelangganAddress, $language_id=1, $watched_video, $b_kategori_ids, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $start_position)
    {
        //pagination logic
        $page = ($page * $page_size) - $page_size;

        $sql = "SELECT 
    cp.id AS 'id',
    cp.b_kategori_id AS 'b_kategori_id',
    IF(".$language_id." = 4 AND bk.thailand IS NOT NULL AND bk.thailand != '', bk.thailand, IF(".$language_id." = 3 AND bk.korea IS NOT NULL AND bk.korea != '', bk.korea, IF(".$language_id." = 2 AND bk.indonesia IS NOT NULL AND bk.indonesia != '', bk.indonesia, bk.nama))) AS 'kategori',
    cp.b_user_id AS 'b_user_id_seller',
    cp.nama AS 'nama',
    cp.harga_jual AS 'harga_jual',
    cp.product_type AS 'product_type',
    cpf.id AS 'video_id',
    cpf.url AS 'url',
    cpf.url_thumb AS 'url_thumb'
FROM
    `c_produk_attachment_video_list` cpavl
        LEFT JOIN
    `c_produk_foto` cpf ON cpf.nation_code = cpavl.nation_code AND cpf.c_produk_id = cpavl.c_produk_id AND cpf.id = cpavl.c_produk_attachment_id
        LEFT JOIN
    `c_produk` cp ON cp.nation_code = cpf.nation_code AND cp.id = cpf.c_produk_id
        LEFT JOIN
    `b_kategori` bk ON cp.nation_code = bk.nation_code AND cp.b_kategori_id = bk.id
        LEFT JOIN
    `b_user` bu ON cp.nation_code = bu.nation_code AND cp.b_user_id = bu.id
WHERE
    cp.nation_code = ".$nation_code." ";
        $sql .= "AND cp.is_published = '1'
        AND cp.is_visible = '1'
        AND cp.is_active = '1'
        AND bu.is_active = '1'
        AND COALESCE(cp.end_date,CURRENT_DATE()) >= CURRENT_DATE()
        AND cp.stok > '0'
        AND cpf.jenis = 'video'
        AND cpf.is_active = '1'
        AND cpavl.id >= '".$start_position."' ";

        //advanced filter
        if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
            $sql .= "AND cp.b_kategori_id IN (";
            foreach ($b_kategori_ids as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
        }

        if(is_array($watched_video)){
            if(count($watched_video) > 0){
                $listArray = array();
                foreach ($watched_video as $key => $watched) {
                    if(isset($watched['product_id']) && isset($watched['video_id'])){
                        if($watched['product_id'] && $watched['video_id']){
                            $listArray[] = $watched['product_id'].'-'.$watched['video_id'];
                        }
                    }
                }
                unset($watched_video, $watched);

                $sql .= "AND CONCAT(cp.id,'-',cpf.id) NOT IN (";
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
                $sql .= "AND LOWER(CAST(".$this->__decrypt("cp.alamat2")." AS CHAR(50))) LIKE '%".addslashes(strtolower($pelangganAddress->alamat2))."%' ";
                $sql .= "AND LOWER(cp.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'neighborhood'){
                $sql .= "AND LOWER(cp.kelurahan) = ".$this->db->esc(strtolower($pelangganAddress->kelurahan))." ";
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'district'){
                $sql .= "AND LOWER(cp.kecamatan) = ".$this->db->esc(strtolower($pelangganAddress->kecamatan))." ";
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'city'){
                $sql .= "AND LOWER(cp.kabkota) = ".$this->db->esc(strtolower($pelangganAddress->kabkota))." ";
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }else if($type == 'province'){
                $sql .= "AND LOWER(cp.provinsi) = ".$this->db->esc(strtolower($pelangganAddress->provinsi))." ";
            }
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

        if(count($blockDataProduct)>0){

            $listArray = array();
            foreach($blockDataProduct AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataProduct, $block);

            $sql .= "AND cp.id NOT IN (";
            foreach ($listArray as $v) {
                $sql .= $this->db->esc($v).", ";
            }
            $sql = rtrim($sql, ", ");
            $sql .= ') ';
            unset($listArray);

        }

        $sql .= "ORDER BY cpavl.id ASC
        LIMIT ".$page.", ".$page_size;

        return $this->db->query($sql);
    }

    public function getProductType($nation_code, $pid)
    {

        $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);

    }

    // public function getForRegisterBefore($nation_code)
    // {
    //     // $this->db->select_as("COUNT(*)", "jumlah");
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     // $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 1 SECOND)", "NOW()", "AND", ">=");
    //     // $d = $this->db->get_first("object", 0);
    //     // if (isset($d->jumlah)) {
    //     //     return $d->jumlah;
    //     // }
    //     // return 0;
    //     $sql ="SELECT COUNT(*) AS jumlah FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND DATE_ADD(cdate, INTERVAL 1 SECOND) >= NOW() FOR UPDATE;";
    //     return $this->db->query($sql)[0]->jumlah;
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

}
