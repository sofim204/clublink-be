<?php
class C_Produk_Laporan_Model extends JI_Model
{
    public $tbl = 'c_produk_laporan';
    public $tbl_as = 'cpl';
    public $tbl2 = 'c_produk';
    public $tbl2_as = 'cp';
    public $tbl3 = 'b_user'; //reporter
    public $tbl3_as = 'bu';
    public $tbl4 = 'b_user'; //seller
    public $tbl4_as = 'bup';
    public $tbl5 = 'b_kategori';
    public $tbl5_as = 'bk';
    public $tbl6 = 'b_user_alamat';
    public $tbl6_as = 'bua';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl_as.c_produk_id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl_as.b_user_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl4_as.id", "=", "$this->tbl2_as.b_user_id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.id", "=", "$this->tbl2_as.b_kategori_id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl6_as.b_user_id");
        $cps[] = $this->db->composite_create("1", "=", "$this->tbl6_as.is_default");
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
        return $this->tbl3_as;
    }
    public function getTableAlias5()
    {
        return $this->tbl3_as;
    }

    //by Donny Dennison - 1 march 2021 14:47
    //add need action column in dashboard
    // public function countAll($nation_code, $keyword="", $b_kondisi_id="", $courier_services="", $is_include_delivery_cost="", $b_kategori_id="", $price_min="", $price_max="", $c_produk_id="", $b_user_id="", $reported_status="")
    public function countAll(
        $nation_code, 
        $keyword="", 
        $b_kondisi_id="", 
        $courier_services="", 
        $is_include_delivery_cost="", 
        $b_kategori_id="", 
        $price_min="", 
        $price_max="", 
        $c_produk_id="", 
        $b_user_id="", 
        $reported_status="", 
        $reported_status_custom="", 
        $s_admin_name="",
        $fromDate="",
        $toDate=""
    ){
        $this->db->flushQuery();
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.c_produk_id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

        if ($c_produk_id>0) {
            $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        }
        if ($b_user_id>0) {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.b_kondisi_id,'-')", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.courier_services,'-')", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.is_include_delivery_cost,'-')", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.b_kategori_id,'-')", $this->db->esc($b_kategori_id));
        }
        if (strlen($reported_status)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.reported_status,'-')", $this->db->esc($reported_status));
        }

        if (strlen($s_admin_name)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.admin_name)", $this->db->esc($s_admin_name));
        }

        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        //by Donny Dennison - 1 march 2021 14:47
        //add need action column in dashboard
        if (strlen($reported_status_custom)>0) {
            $this->db->where_as("$this->tbl_as.reported_status", $this->db->esc(''));
            $this->db->where_as("$this->tbl2_as.reported_status", $this->db->esc(''), "OR", "=", 1, 0);
            $this->db->where_as("$this->tbl2_as.reported_status", $this->db->esc('ignore'), "AND", "=", 0, 1);
        }

        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }
        if (strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"-")', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl2_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori_sub", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getAll(
        $nation_code, 
        $page=0, 
        $pagesize=10, 
        $sortCol="sku", 
        $sortDir="DESC", 
        $keyword="", 
        $b_kondisi_id="", 
        $courier_services="", 
        $is_include_delivery_cost="", 
        $b_kategori_id="", 
        $price_min="", 
        $price_max="", 
        $c_produk_id="", 
        $b_user_id="", 
        $reported_status="", 
        $s_admin_name="",
        $fromDate="",
        $toDate=""
    ){
        $this->db->flushQuery();
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
        $this->db->select_as("$this->tbl_as.c_produk_id", 'c_produk_id', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.thumb,'-')", 'thumb', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.nama,'-')", 'c_produk_nama', 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", 'b_user_nama_seller', 0);
        $this->db->select_as("$this->tbl_as.deskripsi", 'deskripsi', 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl3_as.fnama").",'-')", 'b_user_nama_reporter', 0);
        $this->db->select_as("COALESCE($this->tbl_as.admin_name,'0')", 'admin_name', 0);
        $this->db->select_as("COUNT(*)", 'jumlah_lapor', 0);
        $this->db->select_as("COALESCE($this->tbl_as.reported_status,'-')", 'reported_status', 0);
        // $this->db->select_as("$this->tbl_as.reported_status", 'reported_status', 0);
        // $this->db->select_as("COALESCE($this->tbl_as.reported_status,'')", 'reported_status', 0);
        // $this->db->select_as("'-'", 'action', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.harga_jual,'-')", 'harga_jual', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", 'kategori', 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl3_as.email").",'-')", 'b_user_email_reporter', 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl3_as.telp").",'-')", 'b_user_telp_reporter', 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.email").",'-')", 'b_user_email_seller', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.harga_jual,'-')", 'c_produk_harga_jual', 0);
        // $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl2_as.alamat2").",'-')", 'b_user_address_seller', 0);
        $this->db->select_as("CONCAT($this->tbl2_as.kelurahan,', ',$this->tbl2_as.kecamatan,', ',$this->tbl2_as.kabkota)", "b_user_address_seller", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl6_as.alamat2").",'-')", 'b_user_address_reporter', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.product_type,'-')", 'product_type', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        // tbl3 = reporter user product
        // tbl4 = reported user product

        if ($c_produk_id>0) {
            $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        }
        if ($b_user_id>0) {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (strlen($b_kondisi_id)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.b_kondisi_id,'-')", $this->db->esc($b_kondisi_id));
        }
        if (strlen($courier_services)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.courier_services,'-')", $this->db->esc($courier_services));
        }
        if (strlen($is_include_delivery_cost)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.is_include_delivery_cost,'-')", $this->db->esc($is_include_delivery_cost));
        }
        if (strlen($b_kategori_id)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.b_kategori_id,'-')", $this->db->esc($b_kategori_id));
        }
        if (strlen($reported_status)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.reported_status,'-')", $this->db->esc($reported_status));
        }
        if (strlen($price_min)>0 && strlen($price_max)>0) {
            $this->db->between("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), $this->db->esc($price_max));
        } elseif (strlen($price_min)>0 && strlen($price_max)==0) {
            $this->db->where_as("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_min), 'AND', '>=');
        } elseif (strlen($price_min)==0 && strlen($price_max)>0) {
            $this->db->where_as("CAST($this->tbl2_as.harga_jual AS UNSIGNED)", $this->db->esc($price_max), 'AND', '<=');
        }
        if (strlen($s_admin_name) > 0) {
            $this->db->where_as("COALESCE($this->tbl_as.admin_name)", $this->db->esc($s_admin_name));
        }
        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

        if (strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.telp").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl4_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl4_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl2_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.c_produk_id)");
        
        // switch ($sortCol) {
        //     case 0:
        //         $sortCol = "$this->tbl_as.c_produk_id";
        //         break;
        //     case 1:
        //         $sortCol = "$this->tbl2_as.nama";
        //         break;
        //     case 2:
        //         $sortCol = "$this->tbl2_as.nama";
        //         break;
        //     case 3:
        //         $sortCol = $this->__decrypt("$this->tbl3_as.fnama");
        //         break;
        //     case 4:
        //         $sortCol = "$this->tbl_as.cdate";
        //         break;
        //     case 5:
        //         $sortCol = "COUNT(*)";
        //         break;
        //     default:
        //         $sortCol = "$this->tbl_as.cdate";
        // }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function countByProdukId($nation_code, $c_produk_id="", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.c_produk_id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if ($c_produk_id>0) {
            $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        }
        if (strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"-")', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori_sub", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getByProdukId($nation_code, $c_produk_id="", $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"-")', 'b_user_nama', 0);
        $this->db->select_as("$this->tbl_as.kategori", 'kategori', 0);
        $this->db->select_as("$this->tbl_as.kategori_sub", 'kategori_sub', 0);
        $this->db->select_as("$this->tbl_as.deskripsi", 'deskripsi', 0);

        //by Donny Dennison - 2 march 2021 10:52
        //add need action column in dashboard
        // $this->db->select_as("$this->tbl_as.foto", 'foto', 0);
        $this->db->select_as("$this->tbl_as.reported_status", 'reported_status', 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        if ($c_produk_id>0) {
            $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        }
        if (strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"-")', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.kategori_sub", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
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

        //by Donny Dennison - 2 march 2021 10:52
        //add need action column in dashboard
        // $this->db->where("id", $id);
        $this->db->where("c_produk_id", $id);
        // $this->db->where_as("reported_status", $this->esc(''));

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

    public function getUserIdReporterByProductId($nation_code, $c_produk_id)
    {
		$this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
		return $this->db->get_first();
    }

    public function delete_by_product_id($nation_code, $c_produk_id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("c_produk_id", $c_produk_id);
		return $this->db->delete($this->tbl);
	}

    public function getAllBy($field_name = "", $admin_name = "", $fromDate="", $toDate="")
    {
        // select field
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.c_produk_id", 'c_produk_id', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.thumb,'-')", 'thumb', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.nama,'-')", 'c_produk_nama', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl4_as.fnama") . ",'-')", 'b_user_nama_seller', 0);
        $this->db->select_as("$this->tbl_as.deskripsi", 'deskripsi', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl3_as.fnama") . ",'-')", 'b_user_nama_reporter', 0);
        $this->db->select_as("COALESCE($this->tbl_as.admin_name,'0')", 'admin_name', 0);
        $this->db->select_as("COUNT(*)", 'jumlah_lapor', 0);
        $this->db->select_as("COALESCE($this->tbl_as.reported_status,'-')", 'reported_status', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", 'kategori', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl3_as.email") . ",'-')", 'b_user_email_reporter', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl3_as.telp") . ",'-')", 'b_user_telp_reporter', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl4_as.email") . ",'-')", 'b_user_email_seller', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.harga_jual,'-')", 'c_produk_harga_jual', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl2_as.alamat2") . ",'-')", 'b_user_address_seller', 0);
        $this->db->select_as("COALESCE(" . $this->__decrypt("$this->tbl6_as.alamat2") . ",'-')", 'b_user_address_reporter', 0);
        $this->db->select_as("COALESCE($this->tbl2_as.product_type,'-')", 'product_type', 0);

        // form
        $this->db->from($this->tbl, $this->tbl_as);

        // joint table
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');

        
        // where
        if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		$this->db->where("$this->tbl_as.$field_name", $admin_name, "AND", "=", 0, 0);


        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.c_produk_id)");
        $this->db->order_by("$this->tbl_as.cdate", "DESC");

        return $this->db->get();
    }
}
