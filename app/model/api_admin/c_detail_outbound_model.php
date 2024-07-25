<?php
class C_detail_outbound_model extends JI_Model
{
    public $tbl = 'c_detail_outbounding';
    public $tbl_as = 'cdo';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getAll($nation_code, $id="", $page=0, $pagesize=10, $sortCol="", $sortDir="ASC", $keyword="")
    {
    	/*var_dump($id);*/
        $this->db->flushQuery();
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.c_outbound_id", "id_outbound", 0);
		$this->db->select_as("$this->tbl_as.type", "type", 0);
		$this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.url", "url", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		$this->db->where("$this->tbl_as.c_outbound_id",$id,"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where("name",$keyword,"OR","%like%",0,0);
			$this->db->where("type",$keyword,"OR","%like%",0,0);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get('',0);
    }

    public function countAll($nation_code, $id, $keyword="")
    {
        $this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		$this->db->where("c_outbound_id",$id,"AND","=",0,0);
		/*if(strlen($keyword)>0){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,1);
		}*/
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
    }

    // public function getByIdEdit($nation_code,$ids)
    // {
    //     $parent=0;
    //     $this->db->flushQuery();
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
    //     $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
    //     $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
    //     $this->db->select_as("$this->tbl_as.is_take_down", "takedown", 0);
    //     $this->db->select_as("$this->tbl_as.is_active", "active", 0);
    //     //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
    //     $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
    //     $this->db->select_as("$this->tbl3_as.nama", "product", 0);
    //     $this->db->select_as("$this->tbl_as.text", "text", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($ids), "AND", "=", 0, 0);
    //     return $this->db->get_first();
    // }

    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function update($nation_code, $id,$du)
    {
        if(!is_array($du)) return 0;
        $this->db->where("nation_code",$nation_code);
            $this->db->where("id",$id);
        return $this->db->update($this->tbl,$du,0);
    }

    public function set1($detail){
        /*var_dump($detail);*/
		if(!is_array($detail)) return 0;
		return $this->db->insert($this->tbl,$detail,0,0);
	}

    public function set2($detail2){
        if(!is_array($detail2)) return 0;
        return $this->db->insert($this->tbl,$detail2,0,0);
    }

    public function set3($detail3){
        if(!is_array($detail3)) return 0;
        return $this->db->insert($this->tbl,$detail3,0,0);
    }

    public function del($nation_code, $id){
        $this->db->where("nation_code",$nation_code);
        $this->db->where("c_outbound_id",$id);
        return $this->db->delete($this->tbl);
    }

    public function delDetail($nation_code, $id){
        $this->db->where("nation_code",$nation_code);
        $this->db->where("id",$id);
        return $this->db->delete($this->tbl);
    }

}
