<?php
class C_Produk_Model extends JI_Model{
  var $tbl = 'c_produk';
  var $tbl_as = 'cp';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function emptyStok(){
    $du = array();
    $du['is_published'] = 0;
    $this->db->where("stok","0",'AND','<=');
    return $this->db->update($this->tbl,$du);
  }
  public function addStok($nation_code,$c_produk_id,$qty){
    $qty = (int) $qty;
    $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`+$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = ".$this->db->esc($c_produk_id).";";
    return $this->db->exec($sql);
  }
  public function getUpdated(){
    $this->db->select("nation_code");
    $this->db->select("b_user_id");
    $this->db->select("id");
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->order_by("nation_code","desc");
    $this->db->order_by("b_user_id","asc");
    $this->db->order_by("id","asc");
    return $this->db->get();
  }
  
  //START by Donny Dennison - 19 january 2022 10:35
  //merge table free product to table product
  public function getNationCodes(){
    $this->db->select("nation_code");
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->group_by("nation_code");
    return $this->db->get();
  }

  public function countAll($nation_code, $keyword=""){
    $this->db->select_as("COUNT(*)","total",0);
    $this->db->from($this->tbl,$this->tbl_as);

    //default filter
    $this->db->where_as("$this->tbl_as.nation_code",$nation_code);
    $this->db->where_as("COALESCE($this->tbl_as.end_date,'-')",$this->db->esc("-"),"AND","<>",0,0);
    $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())","CURRENT_DATE()","AND",">=",0,0);
    $this->db->where_as("$this->tbl_as.is_published",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.product_type",$this->db->esc("Free"),"AND","=",0,0);

    //std filter
    if(strlen($keyword)>0){
      $this->db->where_as("$this->tbl_as.nama",addslashes($keyword),'or','%like%',1,0);
      $this->db->where_as("$this->tbl_as.deskripsi",addslashes($keyword),'or','%like%');
      $this->db->where_as("$this->tbl_as.brand",addslashes($keyword),'or','%like%',0,1);
    }
    $d = $this->db->get_first('object',0);
    if(isset($d->total)) return $d->total;
    return 0;
  }

  public function getFirstOldest($nation_code){
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code",$nation_code);
    $this->db->where_as("COALESCE($this->tbl_as.end_date,'-')",$this->db->esc("-"),"AND","<>",0,0);
    $this->db->where_as("COALESCE($this->tbl_as.end_date,CURRENT_DATE())","CURRENT_DATE()","AND",">=",0,0);
    $this->db->where_as("$this->tbl_as.is_published",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.product_type",$this->db->esc("Free"),"AND","=",0,0);
    $this->db->order_by("COALESCE(start_date,CURRENT_DATE())","ASC");
    $this->db->order_by("$this->tbl_as.id","ASC");
    return $this->db->get_first('',0);
  }

  public function update($nation_code, $id, $du){
    if(!is_array($du)) return 0;
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
  }
  //END by Donny Dennison - 19 january 2022 10:35

  public function getuncheckWanted(){
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("$this->tbl_as.check_wanted",$this->db->esc("0"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.is_published",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc("1"),"AND","=",0,0);
    $this->db->where_as("$this->tbl_as.product_type",$this->db->esc("Free"),"AND","!=",0,0);
    $this->db->limit(5);
    return $this->db->get();
  }

  //by Donny Dennison - 12 july 2022 14:56
  //new offer system
  public function getById($nation_code, $pid)
  {
    $this->db->select_as("*, $this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    return $this->db->get_first('', 0);
  }

  public function getByIdIgnoreActive($nation_code, $pid)
  {
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    return $this->db->get_first('', 0);
  }

    public function getAllVideoManualQuery($nation_code, $limit)
    {
        $sql = "    SELECT 
    cp.id AS 'id',
    cpf.id AS 'video_id',
    cp.cdate AS 'cdate'
FROM
    `c_produk_foto` cpf
        LEFT JOIN
    `c_produk` cp ON cp.nation_code = cpf.nation_code AND cp.id = cpf.c_produk_id
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

        $sql .= "ORDER BY RAND() ASC 
        LIMIT ".$limit;

        return $this->db->query($sql);
    }

}
