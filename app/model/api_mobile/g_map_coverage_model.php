<?php
class G_Map_Coverage_Model extends JI_Model
{
    public $tbl = 'g_map_coverage';
    public $tbl_as = 'gmc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.b_kategori_id", "=", "$this->tbl2_as.id");
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

    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }

    //     if (isset($du['alamat2'])) {
    //         if (strlen($du['alamat2'])) {
    //             $du['alamat2'] = $this->__encrypt($du['alamat2']);
    //         }
    //     }

    //     if (isset($di['telp'])) {
    //         if (strlen($di['telp'])) {
    //             $di['telp'] = $this->__encrypt($di['telp']);
    //         }
    //     }

    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

    // public function countAll($nation_code, $keyword="", $kategori_id="", $b_user_id="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $type="", $pelangganAddress, $product_type="All", $show_car=0, $soldout_meetup='', $language_id=1)
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     if (mb_strlen($keyword)>0) {
    //         $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     }
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     if (mb_strlen($keyword)>0) {
    //         $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');
    //     }

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_visible", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

    //     //by Donny Dennison - 19 january 2022 10:35
    //     //merge table free product to table product
    //     $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())", "CURRENT_DATE()", "AND", ">=", 0, 0);
        
    //     //by Donny Dennison - 28 june 2020 11:06
    //     //request by Mr Jackie, still show prodcut even the stock is zero
    //     // only show stok qty above zero
    //     // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");
        
    //     if($product_type == 'Protection' || $product_type == 'MeetUp' || $product_type == 'Free'){

    //         $this->db->where_as("$this->tbl_as.product_type", $this->db->esc($product_type));
    //         $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

    //     }else if($product_type == 'ProtectionAndMeetUp'){

    //         $this->db->where_in("$this->tbl_as.product_type", array(0=>"Protection",1=>"MeetUp"));
    //         $this->db->where_as("$this->tbl_as.b_kategori_id", 33, "AND", "!=");

    //     }

    //     if($show_car == 0){
    //         if (!in_array("32", $b_kategori_ids)){
    //             $this->db->where_as("$this->tbl_as.b_kategori_id", 32, "AND", "!=");
    //         }
    //     }

    //     if($product_type == 'MeetUp'){
    //         if($soldout_meetup == 'yes'){
    //             $this->db->where_as("$this->tbl_as.stok", $this->db->esc('0'));
    //         }else if($soldout_meetup == 'no'){
    //             $this->db->where_as("$this->tbl_as.stok", $this->db->esc('1'));
    //         }
    //     }

    //     //advanced filter
    //     if($product_type != 'Free'){

    //         if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //             $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //         } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //             $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //         } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //             $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //         }
    //         if (is_array($b_kondisi_ids) && count($b_kondisi_ids)>0) {
    //             // $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
    //             $kondisiString = "";
    //             foreach ($b_kondisi_ids as $kondisi) {
    //                 $kondisiString .= $this->db->esc($kondisi).", ";
    //             }
    //             unset($kondisi);
    //             $kondisiString = rtrim($kondisiString, ", ");
    //             $this->db->where_as("IF($this->tbl_as.product_type = 'Protection', $this->tbl_as.b_kondisi_id IN(".$kondisiString."), 1", 1, 'AND', '=', 0, 1);
    //         }
    //     }

    //     if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
    //         $this->db->where_as("1", "1", 'or', '<>', 1, 0);
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids, 0, 'or');

    //         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    //         // $this->db->where_in("$this->tbl8_as.id", $b_kategori_ids, 0, 'or');

    //         $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    //     }

    //     if (intval($kategori_id)>0) {
    //         $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
    //     }
    //     if (intval($b_user_id)>0) {
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);

    //         //by Donny Dennison - 15 february 2022 9:50
    //         //category product and category community have more than 1 language
    //         // $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("IF($language_id = 4 AND $this->tbl2_as.thailand IS NOT NULL AND $this->tbl2_as.thailand != '', $this->tbl2_as.thailand, IF($language_id = 3 AND $this->tbl2_as.korea IS NOT NULL AND $this->tbl2_as.korea != '', $this->tbl2_as.korea, IF($language_id = 2 AND $this->tbl2_as.indonesia IS NOT NULL AND $this->tbl2_as.indonesia != '', $this->tbl2_as.indonesia, $this->tbl2_as.nama)))", $keyword, 'or', '%like%');

    //         $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
            
    //         //by Donny Dennison - 15 November 2021 16:28
    //         //change car and motorcycle to main category
    //         $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');

    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
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

    public function getAll($nation_code)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        
        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("*,$this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

}
