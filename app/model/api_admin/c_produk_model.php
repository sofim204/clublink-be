<?php
class C_Produk_Model extends JI_Model
{
    public $tbl = 'c_produk';
    public $tbl_as = 'cp';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'b_kategori';
    public $tbl3_as = 'bk';
    public $tbl4 = 'b_kondisi';
    public $tbl4_as = 'kon';
    public $tbl5 = 'b_berat';
    public $tbl5_as = 'ber';
    public $tbl6 = 'c_produk_foto';
    public $tbl6_as = 'cpf';
    public $tbl7 = 'b_user_alamat';
    public $tbl7_as = 'bua';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl_as.b_user_id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl_as.b_kategori_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl4_as.id", "=", "$this->tbl_as.b_kondisi_id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.id", "=", "$this->tbl_as.b_berat_id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl6_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl6_as.c_produk_id", "=", "$this->tbl_as.id");
        return $cps;
    }

    private function __joinTblUser()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl_as.b_user_id");
        return $cps;
    }

    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }

    public function trans_commit()
    {
        return $this->db->commit();
    }

    public function trans_rollback()
    {
        return $this->db->rollback();
    }

    public function trans_end()
    {
        return $this->db->autocommit(1);
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }
    public function getTableAlias2()
    {
        return $this->tbl2_as;
    }
    public function getTableAlias3()
    {
        return $this->tbl3_as;
    }
    public function getTableAlias4()
    {
        return $this->tbl4_as;
    }

    //by Donny Dennison - 8 february 2021 16:44
    //add product type column in product menu
    // public function countAll($nation_code, $keyword="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="")
    public function countAll($nation_code, $keyword="", $fromDate="", $toDate="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="", $product_type="")
    {
        $this->db->flushQuery();
        
        //by Donny Dennison - 2 march 2021 11:35
        //list-produt-sameStreet-neighborhood-all-from-user-address
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");

        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kondisi_id", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("$this->tbl_as.courier_services", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("$this->tbl_as.is_include_delivery_cost", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($is_published)>0) {
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published));
        }
        if (strlen($is_active)>0) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        }
        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        }
        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }

        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        if (strlen($product_type)>0) {
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
        }

        if (strlen($keyword)>0) {

            //by Donny Dennison - 4 august 2020 - 17:57
            //bug fix search case insensitive
            // $this->db->where_as($this->__decrypt("$this->tbl2_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);

            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);

        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    //by Donny Dennison - 8 february 2021 16:44
    //add product type column in product menu
    // public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="")
    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $fromDate="", $toDate="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="", $product_type="")
    {
        $this->db->flushQuery();

        //by Donny Dennison - 2 march 2021 11:35
        //list-produt-sameStreet-neighborhood-all-from-user-address
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'"); // by Muhammad Sofi 21 December 2021 14:43 | exec charset utf8mb4
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.thumb", 'thumb', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), 'b_user_nama', 0);
        $this->db->select_as("$this->tbl_as.nama", 'nama', 0);
        //$this->db->select_as("$this->tbl_as.deskripsi","deskripsi",0);

        //by Donny Dennison - 21 January 2021 17:44
        //add weight and dimension in product table cms
        $this->db->select_as("CONCAT($this->tbl_as.berat, ' KG')", 'berat', 0);
        $this->db->select_as("CONCAT($this->tbl_as.dimension_width, ' x ', $this->tbl_as.dimension_long,' x ', $this->tbl_as.dimension_height, ' CM')", 'dimensi', 0);
        $this->db->select_as("$this->tbl_as.harga_jual", 'harga_jual', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
        $this->db->select_as("$this->tbl_as.is_active", 'is_active', 0);
        $this->db->select_as("$this->tbl_as.is_featured", 'is_featured', 0);
        $this->db->select_as("$this->tbl_as.is_published", 'is_published', 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", 'is_include_delivery_cost', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default.png')", 'b_user_image', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), 'b_user_email', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), 'b_user_telp', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'-')", 'kategori', 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "b_berat_nama", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
        $this->db->select_as("courier_services", "courier_services", 0);

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        $this->db->select_as("$this->tbl_as.product_type", 'product_type', 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kondisi_id", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("$this->tbl_as.courier_services", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("$this->tbl_as.is_include_delivery_cost", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($is_published)>0) {
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published));
        }
        if (strlen($is_active)>0) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        }
        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        }
        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }

        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        if (strlen($product_type)>0) {
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
        }
        
        if (strlen($keyword)>0) {

            //by Donny Dennison - 4 august 2020 - 17:57
            //bug fix search case insensitive
            // $this->db->where_as($this->__decrypt("$this->tbl2_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);

            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);

        }
        $this->db->group_by("$this->tbl_as.id");

        switch ($sortCol) {
            case 0:
                $sortCol = "cp.cdate";
                break;
            case 1:
                $sortCol = "cp.thumb";
                break;
            case 2:
                $sortCol = $this->__decrypt("$this->tbl2_as.fnama");
                break;
            case 3:
                $sortCol = "cp.nama";
                break;
            case 4:
                $sortCol = "cp.harga_jual";
                break;
            case 5:
                $sortCol = "cp.cdate";
                break;
            case 6:
                $sortCol = "cp.is_active";
                break;
            default:

                //by Donny Dennison - 2 march 2021 11:35
                //list-produt-sameStreet-neighborhood-all-from-user-address
                // $sortCol = "$tbl_as.id";
                $sortCol = "cp.cdate";

                break;
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getByIdTakedown($nation_code, $product_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $product_id);
        return $this->db->get_first();
    }


    public function getByIdNotif($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.nama", 'nama', 0);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }
    public function getOwnedById($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "kategori", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.image_icon,'media/icon/default-icon.png')", "kategori_icon", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.id", $id);
        return $this->db->get_first();
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
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }
    public function getByIds($nation_code, $pids=array())
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_in('id', $pids);
        return $this->db->get();
    }

    public function getBySeller($nation_code, $b_user_id, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'-')", 'kategori', 0);
        $this->db->select_as("$this->tbl_as.nama", 'nama', 0);
        $this->db->select_as("$this->tbl_as.harga_jual", 'harga_jual', 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "b_kondisi_nama", 0);
        $this->db->select_as("$this->tbl_as.courier_services", 'courier_services', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image_icon,'media/icon/default-icon.png')", "b_kategori_icon", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->group_by("$this->tbl_as.id");
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function countBySeller($nation_code, $b_user_id, $keyword="")
    {
        $this->db->select_as("COUNT(*)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 3 january 2021 14:19
    //change chat to open chatting
    public function getByUserID($nation_code, $search, $user_id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.thumb", 'thumb', 0);
        $this->db->select_as("$this->tbl_as.nama", 'nama', 0);
        $this->db->select_as("$this->tbl_as.harga_jual", 'harga_jual', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($user_id));
        if (strlen($search)>0) {
            $this->db->where_as("$this->tbl_as.nama", $search, "OR", "%like%", 0, 0);
        }
        $this->db->group_by("$this->tbl_as.id");
        return $this->db->get("object", 0);
    }

    public function updateTotal($nation_code, $product_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = $parameter $operator $total
            WHERE nation_code = '$nation_code' AND id = '$product_id';");
    }
    
    public function countTotalVideoProduct($nation_code) {
        $this->db->select_as("COUNT(*)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl6_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl6_as.convert_status", $this->db->esc("processed"), "AND", "=", 0, 0); // ignore convert_status
        $this->db->where_as("$this->tbl_as.is_active",  $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_published",  $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.stok",  $this->db->esc(1), "AND", ">=", 0, 0);
        $d = $this->db->get_first("", 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function m_get_customer($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "user_id", 0);
		$this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user_name", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_in("$this->tbl2_as.id",array("1","2"));

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
        }

        return $this->db->get("object", 0);
	}

    public function m_get_category_product($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "id_category", 0);
		$this->db->select_as("$this->tbl3_as.indonesia", "name_category", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		$this->db->where_as("$this->tbl3_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl3_as.utype", $this->db->esc("kategori"), "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.indonesia", addslashes($keyword), "OR", "%like%", 1, 1);
        }
        $this->db->order_by("$this->tbl3_as.indonesia", "ASC");
        return $this->db->get("object", 0);
	}

    public function m_get_brand_motorcycle_product($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "id_brand", 0);
		$this->db->select_as("$this->tbl3_as.nama", "name_brand", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		$this->db->where_as("$this->tbl3_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl3_as.utype", $this->db->esc("brand"), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl3_as.parent_b_kategori_id", 33, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "OR", "%like%", 1, 1);
        }
        $this->db->order_by("$this->tbl3_as.nama", "ASC");
        return $this->db->get("object", 0);
	}

    public function m_get_brand_car_product($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "id_brand", 0);
		$this->db->select_as("$this->tbl3_as.nama", "name_brand", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		$this->db->where_as("$this->tbl3_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl3_as.utype", $this->db->esc("brand"), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl3_as.parent_b_kategori_id", 32, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "OR", "%like%", 1, 1);
        }
        $this->db->order_by("$this->tbl3_as.nama", "ASC");
        return $this->db->get("object", 0);
	}

    public function m_get_user_address($nation_code, $field="", $value="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl7_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.alamat2"), 'address', 0);
		$this->db->from($this->tbl7, $this->tbl7_as);
		$this->db->where_as("$this->tbl7_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as($this->tbl7_as.'.'.$field, $value, "AND", "=", 0, 0);


        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as("$this->tbl7_as.nama", addslashes($keyword), "OR", "%like%", 1, 1);
        // }
        // $this->db->order_by("$this->tbl7_as.nama", "ASC");
        return $this->db->get_first("object", 0);
	}
    
    public function getFirstAddress($nation_code, $b_user_id) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl7_as.id", "b_user_alamat_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.alamat2"), 'alamat2', 0);
		$this->db->select_as("$this->tbl7_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl7_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl7_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl7_as.provinsi", "provinsi", 0);
		$this->db->select_as("$this->tbl7_as.kodepos", "kodepos", 0);
		$this->db->select_as("$this->tbl7_as.latitude", "latitude", 0);
		$this->db->select_as("$this->tbl7_as.longitude", "longitude", 0);
		$this->db->from($this->tbl7, $this->tbl7_as);
		$this->db->where_as("$this->tbl7_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl7_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first('', 0);
	}

    public function getByProductId($nation_code, $c_produk_id, $getType="all", $type=""){
		$this->db->select_as("$this->tbl6_as.id",'id',0);
		$this->db->select_as("$this->tbl6_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl6_as.url",'url',0);
		$this->db->select_as("$this->tbl6_as.url_thumb",'url_thumb',0);
		$this->db->select_as("$this->tbl6_as.convert_status",'convert_status',0);
		$this->db->from($this->tbl6, $this->tbl6_as);
		$this->db->where_as("$this->tbl6_as.nation_code",$nation_code);
		$this->db->where("$this->tbl6_as.c_produk_id",$c_produk_id);
		
		if($type != ""){
			$this->db->where("$this->tbl6_as.jenis",$type);
		}

		$this->db->where("$this->tbl6_as.is_active",1);

		$this->db->order_by("$this->tbl6_as.id","ASC");

		if($getType == "first"){
			return $this->db->get_first();
		}else{
			return $this->db->get();
		}
	}

    public function updateByProductId($nation_code, $c_produk_id, $du){
		$this->db->where_as("nation_code", $nation_code);
		$this->db->where_as("c_produk_id", $this->db->esc($c_produk_id));
		return $this->db->update($this->tbl6, $du,0);
	}

    public function countAllProductTakedown($nation_code, $keyword="", $fromDate="", $toDate="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="", $product_type="", $statusFilter="")
    {
        $this->db->flushQuery();
        
        //by Donny Dennison - 2 march 2021 11:35
        //list-produt-sameStreet-neighborhood-all-from-user-address
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");

        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1)); // show only active product
        $this->db->where_as("$this->tbl2_as.is_active","1","AND","=",0,0);
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kondisi_id", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("$this->tbl_as.courier_services", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("$this->tbl_as.is_include_delivery_cost", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($is_published)>0) {
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published));
        }
        // if (strlen($is_active)>0) {
        //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        // }
        switch ($statusFilter) {
			case "active":
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
			case "inactive":
				$this->db->where("$this->tbl_as.is_active","1","AND","<>",1,1);
			break;
			case "reported":
				$this->db->where("$this->tbl_as.is_report","1","AND",">=",1,1);
			break;
			case "takedown":
				$this->db->where("$this->tbl_as.is_take_down","1","AND",">=",1,1);
			break;
			
			default:
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
		}
        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        }
        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }

        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        if (strlen($product_type)>0) {
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
        }

        if (strlen($keyword)>0) {

            //by Donny Dennison - 4 august 2020 - 17:57
            //bug fix search case insensitive
            // $this->db->where_as($this->__decrypt("$this->tbl2_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);

            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);

        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllProductTakedown($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $fromDate="", $toDate="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $is_published="", $is_active="", $b_kategori_id="", $price_min="", $price_max="", $product_type="", $statusFilter="active")
    {
        $this->db->flushQuery();

        //by Donny Dennison - 2 march 2021 11:35
        //list-produt-sameStreet-neighborhood-all-from-user-address
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'"); // by Muhammad Sofi 21 December 2021 14:43 | exec charset utf8mb4
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.thumb", 'thumb', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), 'b_user_nama', 0);
        $this->db->select_as("$this->tbl_as.nama", 'nama', 0);
        //$this->db->select_as("$this->tbl_as.deskripsi","deskripsi",0);

        //by Donny Dennison - 21 January 2021 17:44
        //add weight and dimension in product table cms
        $this->db->select_as("CONCAT($this->tbl_as.berat, ' KG')", 'berat', 0);
        $this->db->select_as("CONCAT($this->tbl_as.dimension_width, ' x ', $this->tbl_as.dimension_long,' x ', $this->tbl_as.dimension_height, ' CM')", 'dimensi', 0);
        $this->db->select_as("$this->tbl_as.harga_jual", 'harga_jual', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
        $this->db->select_as("$this->tbl_as.is_active", 'is_active', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), 'b_user_email', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), 'b_user_owner', 0);

        $this->db->select_as("$this->tbl_as.is_featured", 'is_featured', 0);
        $this->db->select_as("$this->tbl_as.is_published", 'is_published', 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", 'is_include_delivery_cost', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default.png')", 'b_user_image', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), 'b_user_email', 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), 'b_user_telp', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'-')", 'kategori', 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "b_berat_nama", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
        $this->db->select_as("courier_services", "courier_services", 0);

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        $this->db->select_as("$this->tbl_as.product_type", 'product_type', 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1)); // show only active product
        $this->db->where_as("$this->tbl2_as.is_active","1","AND","=",0,0);
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kondisi_id", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("$this->tbl_as.courier_services", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("$this->tbl_as.is_include_delivery_cost", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($is_published)>0) {
            $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published));
        }
        // if (strlen($is_active)>0) {
        //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        // }

        switch ($statusFilter) {
			case "active":
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
			case "inactive":
				$this->db->where("$this->tbl_as.is_active","1","AND","<>",1,1);
			break;
			case "reported":
				$this->db->where("$this->tbl_as.is_report","1","AND",">=",1,1);
			break;
			case "takedown":
				$this->db->where("$this->tbl_as.is_take_down","1","AND",">=",1,1);
			break;
			
			default:
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
		}

        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($b_kategori_id));
        }
        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }

        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        if (strlen($product_type)>0) {
            $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
        }
        
        if (strlen($keyword)>0) {

            //by Donny Dennison - 4 august 2020 - 17:57
            //bug fix search case insensitive
            // $this->db->where_as($this->__decrypt("$this->tbl2_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);

            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);

        }
        $this->db->group_by("$this->tbl_as.id");

        switch ($sortCol) {
            case 0:
                $sortCol = "cp.cdate";
                break;
            case 1:
                $sortCol = "cp.thumb";
                break;
            case 2:
                $sortCol = $this->__decrypt("$this->tbl2_as.fnama");
                break;
            case 3:
                $sortCol = "cp.nama";
                break;
            case 4:
                $sortCol = "cp.harga_jual";
                break;
            case 5:
                $sortCol = "cp.cdate";
                break;
            case 6:
                $sortCol = "cp.is_active";
                break;
            default:

                //by Donny Dennison - 2 march 2021 11:35
                //list-produt-sameStreet-neighborhood-all-from-user-address
                // $sortCol = "$tbl_as.id";
                $sortCol = "cp.cdate";

                break;
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

    // Add by Yopie Hidayat 16 Agustus 2023 10:26:00
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
