<?php
class Product_Model extends JI_Model
{
    public $tbl_category = 'b_kategori';
    public $tbl_condition = 'b_kondisi';
    public $tbl_country = 'a_negara';
    public $tbl_detail_automotive = 'c_produk_detail_automotive';
    public $tbl_product = 'c_produk';
    public $tbl_sub_category = 'b_kategori';
    public $tbl_user = 'b_user';
    public $tbl_user_address = 'b_user_alamat';
    public $tbl_weight = 'b_berat';

    private function __join_category_product()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_category.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_product.b_kategori_id", "=", "$this->tbl_category.id");
        return $composites;
    }

    private function __join_user_product()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_user.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_product.b_user_id", "=", "$this->tbl_user.id");
        return $composites;
    }

    private function __join_condition_product()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_condition.nation_code");
        $composites[] = $this->db->composite_create("COALESCE($this->tbl_product.b_kondisi_id,0)", "=", "$this->tbl_condition.id");
        return $composites;
    }

    private function __join_weight_product()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_weight.nation_code");
        $composites[] = $this->db->composite_create("COALESCE($this->tbl_product.b_berat_id,0)", "=", "$this->tbl_weight.id");
        return $composites;
    }

    private function __join_address_user()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_user_address.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_product.b_user_id", "=", "$this->tbl_user_address.b_user_id");
        $composites[] = $this->db->composite_create("$this->tbl_product.b_user_alamat_id", "=", "$this->tbl_user_address.id");
        return $composites;
    }

    private function __join_country_product()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_product.nation_code", "=", "$this->tbl_country.nation_code");
        return $composites;
    }

    public function get_by_id($nation_code, $id) {
        $this->db->select_as("DISTINCT $this->tbl_product.id", "id", 0);
        $this->db->select_as("IF(STRCMP($this->tbl_category.utype, 'kategori'), $this->tbl_sub_category.id, $this->tbl_product.b_kategori_id)", "b_kategori_id", 0);
        $this->db->select_as("$this->tbl_product.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_product.b_user_alamat_id", "b_user_alamat_id_seller", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_user.fnama").',"")', "b_user_fnama_seller", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_user.telp").',"")', "telp", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_user.email").',"")', "email", 0);
        $this->db->select_as("COALESCE($this->tbl_user.image,'')", "b_user_image_seller", 0);
        $this->db->select_as("COALESCE($this->tbl_condition.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl_condition.nama,'')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl_condition.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        $this->db->select_as("COALESCE($this->tbl_weight.id,'0')", "b_berat_id", 0);
        $this->db->select_as("COALESCE($this->tbl_weight.nama,'')", "b_berat_nama", 0);
        $this->db->select_as("COALESCE($this->tbl_weight.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
        $this->db->select_as("IF(STRCMP($this->tbl_category.utype, 'kategori'), $this->tbl_sub_category.nama, $this->tbl_category.nama)", "kategori", 0);
        $this->db->select_as("IF(STRCMP($this->tbl_category.utype, 'kategori'), $this->tbl_sub_category.image_icon, $this->tbl_category.image_icon)", "kategori_icon", 0);
        $this->db->select_as("$this->tbl_product.nama", "nama", 0);
        $this->db->select_as("$this->tbl_product.brand", "brand", 0);
        $this->db->select_as("$this->tbl_product.deskripsi_singkat", "deskripsi_singkat", 0);
        $this->db->select_as("$this->tbl_product.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl_product.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_product.berat", "berat", 0);
        $this->db->select_as("$this->tbl_product.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl_product.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl_product.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_product.stok", "stok", 0);
        $this->db->select_as("$this->tbl_product.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_product.foto", "foto", 0);
        $this->db->select_as("$this->tbl_product.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_product.vehicle_types", "vehicle_types", 0);
        $this->db->select_as("$this->tbl_product.courier_services", "courier_services", 0);
        $this->db->select_as("$this->tbl_product.services_duration", "services_duration", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("COALESCE($this->tbl_user_address.alamat,'')", "alamat1", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_user_address.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.latitude,'0.0')", "latitude", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.longitude,'0.0')", "longitude", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.provinsi,'')", "provinsi", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.kabkota,'')", "kabkota", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.kecamatan,'')", "kecamatan", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.kelurahan,'')", "kelurahan", 0);
        $this->db->select_as("COALESCE($this->tbl_user_address.kodepos,'')", "kodepos", 0);
        $this->db->select_as("COALESCE($this->tbl_country.nama,'')", "negara", 0);
        $this->db->select_as("$this->tbl_product.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl_product.is_published", "is_published", 0);
        $this->db->select_as("$this->tbl_product.is_visible", "is_visible", 0);
        $this->db->select_as("$this->tbl_product.is_active", "is_active", 0);
        $this->db->select_as("(0)", "is_liked", 0);
        $this->db->select_as("IF($this->tbl_product.stok = 0, 1, 0)", "is_sold_out", 0);

        $this->db->from($this->tbl_product, $this->tbl_product);
        $this->db->join_composite($this->tbl_category, $this->tbl_category, $this->__join_category_product(), 'left');
        $this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_user_product(), 'left');
        $this->db->join_composite($this->tbl_condition, $this->tbl_condition, $this->__join_condition_product(), 'left');
        $this->db->join_composite($this->tbl_weight, $this->tbl_weight, $this->__join_weight_product(), 'left');
        $this->db->join_composite($this->tbl_user_address, $this->tbl_user_address, $this->__join_address_user(), 'left');
        $this->db->join_composite($this->tbl_country, $this->tbl_country, $this->__join_country_product(), 'left');

        $this->db->where_as("$this->tbl_product.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_product.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_product.is_published",1);
        $this->db->where_as("$this->tbl_product.is_active",1);
        return $this->db->get_first();
    }

}
