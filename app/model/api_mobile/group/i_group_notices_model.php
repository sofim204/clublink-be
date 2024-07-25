<?php
class I_Group_Notices_Model extends JI_Model {
	var $tbl = 'd_pemberitahuan';
	var $tbl_as = 'dpem';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getLastId($nation_code,$b_user_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getAll($nation_code,$b_user_id,$page=0,$pagesize=10,$sortCol="id",$sortDir="desc",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.nation_code","nation_code",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id",0);
		$this->db->select_as("$this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.judul","judul",0);
		$this->db->select_as("$this->tbl_as.teks","teks",0);
		$this->db->select_as("$this->tbl_as.type","type",0);
		$this->db->select_as("COALESCE($this->tbl_as.gambar,'')","gambar",0);
		$this->db->select_as("COALESCE($this->tbl_as.extras,'{}')","extras",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.is_read","is_read",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("type","outbounding","AND","!=");

		//by Donny Dennison - 10-09-2021 16:57
		//community-feature
		$this->db->where("is_active",1);

		if(strlen($keyword)>1){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,0);
			$this->db->where("type",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->page($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function update($nation_code,$b_user_id,$id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}

	public function countUnRead($nation_code,$b_user_id){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
		$this->db->where_as("$this->tbl_as.is_read",$this->db->esc("0"));

		//by Donny Dennison - 10-09-2021 16:57
		//community-feature
		$this->db->where_as("$this->tbl_as.is_active",$this->db->esc("1"));

		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}

	public function updateUnRead($nation_code,$b_user_id){
		$du=array("is_read"=>1);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		return $this->db->update($this->tbl,$du);
	}


	public function getAllCustom($nation_code,$b_user_id,$page=0,$pagesize=10,$sortCol="id",$sortDir="desc"){
		if($page > 11) $page = 1;
        $page = ($page * $pagesize) - $pagesize;
		$query = "select *
from ( (SELECT 
    dpem.nation_code AS 'nation_code',
    dpem.b_user_id AS 'b_user_id',
    dpem.id AS 'id',
    dpem.judul AS 'judul',
    dpem.teks AS 'teks',
    dpem.type AS 'type',
    COALESCE(dpem.gambar, '') AS 'gambar',
    COALESCE(dpem.extras, '{}') AS 'extras',
    dpem.cdate AS 'cdate',
    dpem.is_read AS 'is_read'
FROM
    `d_pemberitahuan` dpem
WHERE
    `nation_code` = '".$nation_code."'
        AND `b_user_id` = '".$b_user_id."'
        AND `type` <> 'outbounding'
        AND `is_active` = '1'
ORDER BY cdate DESC
LIMIT 0 , 100)
UNION ALL
 (SELECT co.nation_code AS 'nation_code', '0' AS 'b_user_id', co.id AS 'id', co.judul AS 'judul', co.teks AS 'teks',
'outbounding' AS 'type', 'media/pemberitahuan/outbounding.png' AS 'gambar', concat('{\"id\":',co.id,'}') AS 'extras',
co.cdate AS 'cdate', '1' AS 'is_read' FROM `c_outbounding` co WHERE `nation_code` = '".$nation_code."' AND `is_active` = '1' ORDER BY
cdate DESC LIMIT 0, 10) ) a
order by cdate desc LIMIT ".$page.",".$pagesize;
		return $this->db->query($query);
	}

}
