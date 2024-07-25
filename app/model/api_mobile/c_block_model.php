<?php
class C_Block_Model extends JI_Model
{
    public $tbl = 'c_block';
    public $tbl_as = 'cb';
    public $tbl2 = 'c_community';
    public $tbl2_as = 'ccom';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = 'b_user';
    public $tbl4_as = 'bu';
    public $tbl5 = 'c_produk';
    public $tbl5_as = 'cp';
    public $tbl6 = 'b_user';
    public $tbl6_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    private function __joinTbl5()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl5_as.id");
        return $composites;
    }

    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl5_as.b_user_id", "=", "$this->tbl6_as.id");
        return $composites;
    }

    public function getLastId($nation_code, $type)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("type", $type);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
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

    public function update($nation_code, $id, $type, $du)
    {
        if (!is_array($du)) {
            return 0;
        }

        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        $this->db->where("type", $type);
        return $this->db->update($this->tbl, $du, 0);
    }

    // public function del($nation_code, $id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    public function countAll($nation_code, $b_user_id="", $type="community")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        if($type == "community"){

            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
            $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        }

        if($type == "account"){

            $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

        }

        if($type == "product"){

            $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
            $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');

        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        if($type == "community"){

            $this->db->where_as("$this->tbl2_as.is_published", $this->db->esc(1));
            $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
            $this->db->where_as("$this->tbl2_as.is_take_down", $this->db->esc('0'));
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

        }

        if($type == "account"){

            $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));

        }

        if($type == "product"){

            $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc(1));
            $this->db->where_as("$this->tbl6_as.is_active", $this->db->esc('1'));

        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $b_user_id="", $type="community")
    {
        $this->db->select_as("$this->tbl_as.id", "block_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);

        if($type == "community"){

            $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
            $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        }

        if($type == "account"){

            $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

        }

        if($type == "product"){

            $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
            $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');

        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        if($type == "community"){

            $this->db->where_as("$this->tbl2_as.is_published", $this->db->esc(1));
            $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
            $this->db->where_as("$this->tbl2_as.is_take_down", $this->db->esc('0'));
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

        }

        if($type == "account"){

            $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));

        }

        if($type == "product"){

            $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc(1));
            $this->db->where_as("$this->tbl6_as.is_active", $this->db->esc('1'));

        }

        $this->db->order_by($sort_col, $sort_direction);

        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $block_id=0, $b_user_id, $type, $custom_id=0)
    {
        $this->db->select_as("$this->tbl_as.id", "block_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($block_id != 0){
            $this->db->where_as("$this->tbl_as.id", $this->db->esc($block_id));
        }

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        if($custom_id != '0'){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('', 0);
    }

    public function getAllByUserId($nation_code, $block_id=0, $b_user_id=0, $type, $custom_id=0)
    {
        $this->db->select_as("$this->tbl_as.id", "block_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if($block_id != 0){
            $this->db->where_as("$this->tbl_as.id", $this->db->esc($block_id));
        }

        if($b_user_id != '0'){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }

        if($custom_id != '0'){
            $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        }

        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        return $this->db->get('', 0);
    }

    // public function getByUserId($nation_code, $b_user_id, $type, $custom_id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "block_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
    //     $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

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
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function countMyProduct($nation_code, $b_user_id, $keyword="", $is_published="", $product_type="All", $show_soldout="")
    // {
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

    //     //by Donny Dennison - 15 November 2021 16:28
    //     //change car and motorcycle to main category
    //     $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');

    //     if ($product_type != 'All') {
    //         if ($product_type == 'AutomotiveCar') {
    //             $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
    //             $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc(32));
    //         }else if ($product_type == 'AutomotiveMotorcycle') {
    //             $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Automotive"));
    //             $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc(33));
    //         }else{
    //             $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
    //         }
    //     }

    //     //by Donny Dennison - 3 june 2022 13:10
    //     //new feature, product type santa
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    //     if($show_soldout == 'yes'){
    //         $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
    //     }else if($show_soldout == 'no'){
    //         $this->db->where_as("$this->tbl_as.stok", 1, "AND", ">=");
    //     }

    //     if (strlen($is_published)>0) {
    //         $is_published = (int) $is_published;
    //         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published), 'AND', '=');
    //     }

    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
            
    //         //by Donny Dennison - 15 february 2022 9:50
    //         //category product and category community have more than 1 language
    //         // $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", $keyword, 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getByProdukIds($ids)
    // {
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getByIds($nation_code, $ids)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getByIdsForCart($nation_code, $ids)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'default.png')", "b_user_image_seller", 0);
    //     $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.is_fashion,'0')", "is_fashion", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_in("$this->tbl_as.id", $ids);
    //     $this->db->order_by("$this->tbl_as.courier_services", "desc");
    //     $this->db->order_by("$this->tbl_as.is_include_delivery_cost", "asc");
    //     $this->db->order_by("$this->tbl2_as.is_fashion", "asc");
    //     return $this->db->get('', 0);
    // }
    // public function getHomePage($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_dir="asc", $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);

    //     // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

    //     $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);

    //     // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // END by Muhammad Sofi - 15 November 2021 10:17 
        
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
    //     // //by Donny Dennison - 15 November 2021 16:28
    //     // //change car and motorcycle to main category
    //     // // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     // $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

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
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    //     //by Donny Dennison - 15 November 2021 16:28
    //     //change car and motorcycle to main category
    //     $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     //by Donny Dennison
    //     //show product even the stock is 0 from Mr. Jackie
    //     // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

    //     //by Donny Dennison - 3 june 2022 13:10
    //     //new feature, product type santa
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (count($b_kondisi_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
    //     }
    //     if (count($b_kategori_ids)>0) {
    //         $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'or');

    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

    //         $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }
    //     //end advanced filter

    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }

    //     //by Donny Dennison - 13-07-2020 16:08
    //     //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    //     // $this->db->order_by($sort_col, $sort_dir);
    //     $this->db->order_by("$this->tbl_as.".$sort_col, $sort_dir);
        
    //     $this->db->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countHomePage($nation_code, $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select_as("COUNT(*)", 'total', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //     // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    //     //by Donny Dennison - 15 November 2021 16:28
    //     //change car and motorcycle to main category
    //     $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     //by Donny Dennison - 3 june 2022 13:10
    //     //new feature, product type santa
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (count($b_kondisi_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
    //     }
    //     if (count($b_kategori_ids)>0) {
    //         $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'or');

    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

    //         $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }
    //     //end advanced filter

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');

    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');
            
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }
    // public function substractStok($nation_code, $c_produk_id, $qty)
    // {
    //     $qty = (int) $qty;
    //     $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`-$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = $c_produk_id;";
    //     return $this->db->exec($sql);
    // }
    // public function addStok($nation_code, $c_produk_id, $qty)
    // {
    //     $qty = (int) $qty;
    //     $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`+$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = $c_produk_id;";
    //     return $this->db->exec($sql);
    // }
    // public function getByIdRaw($nation_code, $id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("id", $this->db->esc($id));
    //     return $this->db->get_first('', 0);
    // }
    // public function getByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     $this->db->where("is_active", 1);
    //     return $this->db->get();
    // }
    // public function checkTakeDown($nation_code, $id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.reported_status", $this->db->esc("takedown"));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     return $this->db->get_first('', 0);
    // }

    // //by Donny Dennison - 28 july 2020 11:39
    // // check the address if there is product using this address then cannot delete
    // public function getActiveByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.b_user_alamat_id", $this->db->esc($b_user_alamat_id));
    //     // $this->db->where_as("$this->tbl_as.is_published", '1');
    //     // $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     return $this->db->get();
    // }

    // //by Donny Dennison - 29 july 2020 - 15:47
    // //prevent insert product duplication
    // public function getActiveByUserIdProductNameWeightDimensionPrice($nation_code, $b_user_id, $product_name, $weight, $dimension_long, $dimension_width, $dimension_height, $harga_jual)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
    //     $this->db->where_as("$this->tbl_as.berat", $this->db->esc($weight));
    //     $this->db->where_as("$this->tbl_as.dimension_long", $this->db->esc($dimension_long));
    //     $this->db->where_as("$this->tbl_as.dimension_width", $this->db->esc($dimension_width));
    //     $this->db->where_as("$this->tbl_as.dimension_height", $this->db->esc($dimension_height));
    //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Protection'));
    //     return $this->db->get();
    // }
    
    // public function getActiveByUserIdProductNameCategoryDescriptionPrice($nation_code, $b_user_id, $product_name, $b_kategori_id, $deskripsi, $harga_jual)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
    //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
    //     $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
    //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('MeetUp'));
    //     return $this->db->get();
    // }
    
    // public function getActiveByUserIdProductNameDescriptionTelephone($nation_code, $b_user_id, $product_name, $deskripsi, $telp)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
    //     $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
    //     $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), $this->db->esc($telp));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Free'));
    //     return $this->db->get();
    // }

    // public function getActiveByUserIdProductNameBrandModelColorYearDescriptionPrice($nation_code, $b_user_id, $product_name, $brand, $model, $color, $year, $deskripsi, $harga_jual)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.nama", $this->db->esc($product_name));
    //     $this->db->where_as("$this->tbl_as.brand", $this->db->esc($brand));
    //     $this->db->where_as("$this->tbl7_as.model", $this->db->esc($model));
    //     $this->db->where_as("$this->tbl7_as.color", $this->db->esc($color));
    //     $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
    //     $this->db->where_as("$this->tbl_as.deskripsi", $this->db->esc($deskripsi));
    //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
    //     return $this->db->get();
    // }

    // public function getIdSeller($nation_code, $produkid)
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("$this->tbl_as.b_user_id", "user_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($produkid));
    //     return $this->db->get_first();
    // }

    // //by Donny Dennison - 26 november 2021 16:43
    // //get product automotive car list
    // public function countAllAutomotive($nation_code, $harga_jual_min="", $harga_jual_max="", $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $b_brand_id="", $year="", $b_kategori_id=32, $keyword="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4'");
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     // $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
    //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));

    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (is_array($b_brand_id)) {
    //         $this->db->where_as("$this->tbl_as.brand", $this->db->esc($b_brand_id['b_brand_id']), 'OR', '=', 1, 0);
    //         $this->db->where_as("LOWER($this->tbl_as.brand)", $b_brand_id['brand_name'], 'AND', '%like%', 0, 1);
    //     }
    //     if ($year>0) {
    //         $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
    //     }

    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as($this->__decrypt("$this->tbl_as.alamat2"), $keyword, 'OR', '%like%', 1, 0);
    //         $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $keyword, 'AND', '%like%', 0, 1);
    //     }
        
    //     if($kelurahan != 'All'){
    //         $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //     }
        
    //     $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAllAutomotive($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $harga_jual_min="", $harga_jual_max="", $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $b_brand_id="", $year="", $b_kategori_id=32, $keyword="", $pelanggan, $language_id=1)
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);

    //     //by Donny Dennison - 15 february 2022 9:50
    //     //category product and category community have more than 1 language
    //     // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
    //     $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
    //     // $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("'0'", "b_berat_id", 0);
    //     // $this->db->select_as("''", "b_berat_nama", 0);
    //     // $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     // $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
    //     // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
    //     $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
    //     //by Donny Dennison - 2 july 2021 9:37
    //     //move-campaign-to-sponsored
    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

    //     //by Donny Dennison - 8 july 2021 11:02
    //     //add-like-product
    //     //START by Donny Dennison - 8 july 2021 11:02
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

    //     if(isset($pelanggan->id)){
    //         $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
    //     }else{
    //         $this->db->select_as("(0)", "is_liked", 0);
    //     }

    //     $this->db->select_as("$this->tbl20_as.negara", "negara", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc('Automotive'));
    //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        
    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (is_array($b_brand_id)) {
    //         $this->db->where_as("$this->tbl_as.brand", $this->db->esc($b_brand_id['b_brand_id']), 'OR', '=', 1, 0);
    //         $this->db->where_as("LOWER($this->tbl_as.brand)", $b_brand_id['brand_name'], 'AND', '%like%', 0, 1);
    //     }
    //     if ($year>0) {
    //         $this->db->where_as("$this->tbl7_as.year", $this->db->esc($year));
    //     }

    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as($this->__decrypt("$this->tbl_as.alamat2"), $keyword, 'OR', '%like%', 1, 0);
    //         $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $keyword, 'AND', '%like%', 0, 1);
    //     }

    //     if($kelurahan != 'All'){
    //         $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //     }
        
    //     $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));
        
    //     if($sort_col == "$this->tbl_as.harga_jual"){
    //         $this->db->order_by("CAST(".$sort_col." AS DECIMAL(21,2))", $sort_direction);
    //     }else{
    //         $this->db->order_by($sort_col, $sort_direction);
    //     }
        
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }
    
    // //by Donny Dennison - 10 december 2021 13:36
    // //add feature hot item di homepage
    // public function getAllHomepage($nation_code, $page=1, $page_size=8, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $pelanggan, $option="option1", $product_type = "Protection", $type="All", $language_id)
    // {
    //     // $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
        
    //     //by Donny Dennison - 15 february 2022 9:50
    //     //category product and category community have more than 1 language
    //     // $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);
    //     $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");

    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
    //     // $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     // $this->db->select_as("'0'", "b_berat_id", 0);
    //     // $this->db->select_as("''", "b_berat_nama", 0);
    //     // $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     // $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     // $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     // $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     // $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     // $this->db->select_as("COALESCE($this->tbl_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
    //     // $this->db->select_as("COALESCE($this->tbl_as.b_lokasi_id,0)", "b_lokasi_id", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
    //     $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
    //     //by Donny Dennison - 2 july 2021 9:37
    //     //move-campaign-to-sponsored
    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);

    //     //by Donny Dennison - 8 july 2021 11:02
    //     //add-like-product
    //     //START by Donny Dennison - 8 july 2021 11:02
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

    //     if(isset($pelanggan->id)){
    //         $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
    //     }else{
    //         $this->db->select_as("(0)", "is_liked", 0);
    //     }
        
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     // $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.stok", $this->db->esc('1'),'AND','>=');

    //     //by Donny Dennison - 3 june 2022 13:10
    //     //new feature, product type santa
    //     $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    //     if($type == 'neighborhood'){

    //         $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'district'){

    //         $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'city'){
            
    //         $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'province'){
            
    //         $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }

    //     if($option == 'option1'){
    //         $this->db->where_as("$this->tbl_as.product_type", $this->db->esc("Free"),"AND","!=");
    //         // $this->db->where_as("(SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1)", $this->db->esc('5'),"AND",">=");
    //         $this->db->where_as("$this->tbl_as.total_likes", $this->db->esc('5'),"AND",">=");
    //         $this->db->where_as("(SELECT COUNT(DISTINCT b_user_id) FROM f_discussion WHERE product_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1 AND parent_f_discussion_id = 0 AND b_user_id != $this->tbl_as.b_user_id)", $this->db->esc('10'),"AND",">=");
    //         $this->db->where_as("(SELECT COUNT(*) FROM c_product_share_history WHERE c_produk_id = $this->tbl_as.id AND nation_code = $nation_code AND b_user_id != $this->tbl_as.b_user_id)", $this->db->esc('5'),"AND",">=");

    //         $this->db->order_by("$this->tbl_as.total_likes", "DESC");
    //         $this->db->order_by("(SELECT COUNT(DISTINCT b_user_id) FROM f_discussion WHERE product_id = $this->tbl_as.id AND nation_code = $nation_code AND is_active = 1 AND parent_f_discussion_id = 0 AND b_user_id != $this->tbl_as.b_user_id)", "DESC");
    //         $this->db->order_by("(SELECT COUNT(*) FROM c_product_share_history WHERE c_produk_id = $this->tbl_as.id AND nation_code = $nation_code AND b_user_id != $this->tbl_as.b_user_id)", "DESC");
    //     }

    //     if($option == 'option2'){
    //         // $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
    //         $this->db->where_in("$this->tbl_as.product_type", array(0=>"MeetUp",1=>"Automotive"));
    //         $this->db->where_as("$this->tbl_as.b_kategori_id", 32, "AND", "!=");
            
    //         $this->db->order_by("$this->tbl_as.cdate", "DESC");
    //         $this->db->order_by("$this->tbl_as.harga_jual", "ASC");
    //     }

    //     if($page != 0 && $page_size !=0){
    //         $this->db->page($page, $page_size);
    //     }
    //     return $this->db->get('object', 0);
    // }

    // public function updateTotal($nation_code, $product_id, $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = $parameter $operator $total
    //         WHERE nation_code = '$nation_code' AND id = '$product_id';");
    // }

    // public function getAllForMigrationAddress($nation_code)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl20_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("$this->tbl20_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl20_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl20_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl20_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl20_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl20_as.latitude", "latitude", 0);
    //     $this->db->select_as("$this->tbl20_as.longitude", "longitude", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.kodepos",$this->db->esc(''));

    //     return $this->db->get('object', 0);
    // }

    // //START by Donny Dennison - 14 july 2022 14:28
    // //new api product/video_list
    // public function getAllVideo($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $type="", $pelangganAddress, $language_id=1, $watched_video)
    // {

    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", "kategori");
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
    //     // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
    //     $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
    //     $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

    //     if(isset($pelangganAddress->b_user_id)){
    //         $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl_as.id AND b_user_id = $pelangganAddress->b_user_id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
    //     }else{
    //         $this->db->select_as("(0)", "is_liked", 0);
    //     }

    //     $this->db->select_as("$this->tbl24_as.id", "video_id", 0);
    //     $this->db->select_as("$this->tbl24_as.url", "url", 0);
    //     $this->db->select_as("$this->tbl24_as.url_thumb", "url_thumb", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl24, $this->tbl24_as, $this->__joinTbl24(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
    //     $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())", "CURRENT_DATE()", "AND", ">=", 0, 0);
    //     $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");
    //     $this->db->where_as("$this->tbl24_as.jenis", $this->db->esc('video'));
    //     $this->db->where_as("$this->tbl24_as.convert_status", $this->db->esc('processed'));
    //     $this->db->where_as("$this->tbl24_as.is_active", $this->db->esc(1));

    //     if(is_array($watched_video)){

    //         if(count($watched_video) > 0){

    //           foreach ($watched_video as $key => $watched) {

    //             if(isset($watched['product_id']) && isset($watched['video_id'])){

    //               if($watched['product_id'] && $watched['video_id']){

    //                 $this->db->where_as("CONCAT($this->tbl_as.id,'-',$this->tbl24_as.id)", $this->db->esc($watched['product_id'].'-'.$watched['video_id']), 'and', '!=');

    //               }

    //             }

    //           }

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

    //     // $this->db->order_by($sort_col, $sort_direction);
    //     $this->db->order_by("RAND()");
    //     // $this->db->page($page, $page_size);
    //     $this->db->limit($page_size);

    //     return $this->db->get('object', 0);

    // }
    // //END by Donny Dennison - 14 july 2022 14:28
    // //new api product/video_list

}
