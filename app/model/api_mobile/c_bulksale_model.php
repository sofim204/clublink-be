<?php
class C_BulkSale_Model extends SENE_Model{
	var $tbl = 'c_bulksale';
	var $tbl_as = 'cbs';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';
	var $tbl20 = 'b_user_alamat';
	var $tbl20_as = 'bua';
	var $tbl21 = 'b_lokasi';
	var $tbl21_as = 'blok';
	var $tbl22 = 'b_kodepos';
	var $tbl22_as = 'bkp';
	var $tbl23 = 'a_negara';
	var $tbl23_as = 'an';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	private function __joinTbl3(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl_as.b_user_id","=","$this->tbl3_as.id");
		return $composites;
	}
	private function __joinTbl20(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl20_as.nation_code");
		$composites[] = $this->db->composite_create("COALESCE($this->tbl_as.b_user_alamat_id,0)","=","$this->tbl20_as.id");
		return $composites;
	}
	private function __joinTbl21(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl20_as.nation_code","=","COALESCE($this->tbl21_as.nation_code,0)");
		$composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_Id","=","COALESCE($this->tbl21_as.id,0)");
		return $composites;
	}
	private function __joinTbl22(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl20_as.nation_code","=","$this->tbl22_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_Id","=","$this->tbl22_as.id");
		return $composites;
	}
	private function __joinTbl23(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl23_as.nation_code");
		return $composites;
	}

	public function trans_start(){
		$r = $this->db->autocommit(0);
		if($r) return $this->db->begin();
		return false;
	}

	public function trans_commit(){
		return $this->db->commit();
	}

	public function trans_rollback(){
		return $this->db->rollback();
	}

	public function trans_end(){
		return $this->db->autocommit(1);
	}

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return (int) $d->last_id;
		return 0;
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id, $b_user_id, $du){
		if(!is_array($du)) return 0;
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("b_user_id",$b_user_id);
		return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$id,$b_user_id){
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("b_user_id",$b_user_id);
		return $this->db->delete($this->tbl);
	}

	public function getTblAs(){
		return $this->tbl_as;
	}
	public function getTblAs3(){
		return $this->tbl3_as;
	}

	public function countAll($nation_code, $keyword="", $b_user_id="", $action_status=""){
		$this->db->select_as("COUNT(*)","total",0);

		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');

		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		if(strlen($b_user_id)) $this->db->where_as("$this->tbl_as.b_user_id",$b_user_id);
		if(strlen($action_status)>3) $this->db->where_as("$this->tbl_as.action_status",$action_status);

		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),'or','%like%',1,0);
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_license",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.company_name",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.address1",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%',0,1);
		}
		$d = $this->db->get_first('object',0);
		if(isset($d->total)) return $d->total;
		return 0;
	}
	public function getAll($nation_code,$page=1,$page_size=10,$sort_col="id",$sort_direction="ASC",$keyword="",$b_user_id="", $action_status=""){
		$this->db->select_as("DISTINCT $this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id_seller",0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"),"b_user_fnama_seller",0);
		$this->db->select_as("COALESCE($this->tbl3_as.image,'-')","b_user_image_seller",0);
		$this->db->select_as("$this->tbl_as.name","name",0);
    $this->db->select_as("$this->tbl_as.agent_name","agent_name",0);
    $this->db->select_as("$this->tbl_as.agent_license","agent_license",0);
		$this->db->select_as("$this->tbl_as.company_name","company_name",0);
		$this->db->select_as("$this->tbl_as.description_long","description_long",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl_as.address1","address1",0);
		$this->db->select_as("$this->tbl_as.address2","address2",0);
		$this->db->select_as("$this->tbl_as.subdistrict","subdistrict",0);
		$this->db->select_as("$this->tbl_as.district","district",0);
		$this->db->select_as("$this->tbl_as.city","city",0);
		$this->db->select_as("$this->tbl_as.province","province",0);
		$this->db->select_as("$this->tbl_as.country","country",0);
		$this->db->select_as("$this->tbl_as.zipcode","zipcode",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.ldate","ldate",0);
		$this->db->select_as("$this->tbl_as.reason","reason",0);
		$this->db->select_as("COALESCE($this->tbl_as.vdate,'-')","visit_date",0);
		$this->db->select_as("$this->tbl_as.action_status","action_status",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		if(strlen($b_user_id)) $this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
		if(strlen($action_status)>3) $this->db->where_as("$this->tbl_as.action_status",$action_status);

		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),'or','%like%',1,0);
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_license",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.company_name",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.address1",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%',0,1);
		}
		$this->db->order_by($sort_col,$sort_direction)->page($page,$page_size);
		return $this->db->get('object',0);
	}
	public function getById($nation_code, $pid){
		$this->db->select_as("$this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id_seller",0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"),"b_user_fnama_seller",0);
		$this->db->select_as("COALESCE($this->tbl3_as.image,'-')","b_user_image_seller",0);
		$this->db->select_as("$this->tbl_as.name","name",0);
    $this->db->select_as("$this->tbl_as.agent_name","agent_name",0);
		$this->db->select_as("$this->tbl_as.description_long","description_long",0);
		$this->db->select_as("$this->tbl_as.agent_license","agent_license",0);
		$this->db->select_as("$this->tbl_as.company_name","company_name",0);
		$this->db->select_as("$this->tbl_as.address1","address1",0);
		$this->db->select_as("$this->tbl_as.address2","address2",0);
		$this->db->select_as("$this->tbl_as.subdistrict","subdistrict",0);
		$this->db->select_as("$this->tbl_as.district","district",0);
		$this->db->select_as("$this->tbl_as.city","city",0);
		$this->db->select_as("$this->tbl_as.province","province",0);
		$this->db->select_as("$this->tbl_as.country","country",0);
		$this->db->select_as("$this->tbl_as.zipcode","zipcode",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.ldate","ldate",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl_as.vdate","visit_date",0);
		$this->db->select_as("$this->tbl_as.reason","reason",0);
		$this->db->select_as("$this->tbl_as.action_status","action_status",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->where_as("$this->tbl_as.id",$pid);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		return $this->db->get_first('',0);
	}

	public function getOwnedById($nation_code, $pid){
    $this->db->select_as("$this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id_seller",0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"),"b_user_fnama_seller",0);
		$this->db->select_as("COALESCE($this->tbl3_as.image,'-')","b_user_image_seller",0);
		$this->db->select_as("$this->tbl_as.name","name",0);
    $this->db->select_as("$this->tbl_as.agent_name","agent_name",0);
		$this->db->select_as("$this->tbl_as.description_long","description_long",0);
		$this->db->select_as("$this->tbl_as.agent_license","agent_license",0);
		$this->db->select_as("$this->tbl_as.company_name","company_name",0);
		$this->db->select_as("$this->tbl_as.address1","address1",0);
		$this->db->select_as("$this->tbl_as.address2","address2",0);
		$this->db->select_as("$this->tbl_as.subdistrict","subdistrict",0);
		$this->db->select_as("$this->tbl_as.district","district",0);
		$this->db->select_as("$this->tbl_as.city","city",0);
		$this->db->select_as("$this->tbl_as.province","province",0);
		$this->db->select_as("$this->tbl_as.country","country",0);
		$this->db->select_as("$this->tbl_as.zipcode","zipcode",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.ldate","ldate",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl_as.action_status","action_status",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->where_as("$this->tbl_as.id",$pid);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.is_active",1);
		return $this->db->get_first();
	}

	public function setTerjuals($pids){
		if(is_array($pids) && count($pids)){
			$sql = '';
			//building multi query
			foreach($pids as $pid){
				$sql .= 'UPDATE '.$this->tbl.' SET terjual = terjual + '.$pid->qty.', stok = stok - '.$pid->qty.' WHERE id = '.$pid->id.';';
				$sql .= 'UPDATE '.$this->tbl.' SET sales_rate = ((sales_count / terjual)*100) WHERE id = '.$pid->id.';';
			}
			$this->db->query_multi($sql);
		}
	}
	public function getByProdukIds($ids){
		$this->db->where_in('id',$ids);
		return $this->db->get();
	}
	public function getByIds($ids){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_in('id',$ids);
		return $this->db->get();
	}

	public function getHomePage($nation_code, $page=1,$page_size=10,$sort_col="id",$sort_dir="asc",$keyword="",$harga_jual_min="",$harga_jual_max="",$b_kondisi_ids=array(),$b_kategori_ids=array(),$kecamatan=""){
		$this->db->select_as("DISTINCT  $this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.b_kategori_id","b_kategori_id",0);
		$this->db->select_as("COALESCE($this->tbl2_as.nama,'-')","kategori",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id_seller",0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"),"b_user_fnama_seller",0);
		$this->db->select_as("COALESCE($this->tbl3_as.image,'-')","b_user_image_seller",0);
		$this->db->select_as("COALESCE($this->tbl4_as.id,'0')","b_kondisi_id",0);
		$this->db->select_as("COALESCE($this->tbl4_as.nama,'-')","b_kondisi_nama",0);
		$this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')","b_kondisi_icon",0);
		$this->db->select_as("COALESCE($this->tbl5_as.id,'0')","b_berat_id",0);
		$this->db->select_as("COALESCE($this->tbl5_as.nama,'-')","b_berat_nama",0);
		$this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')","b_berat_icon",0);

		$this->db->select_as("$this->tbl_as.name","name",0);
    $this->db->select_as("$this->tbl_as.agent_name","agent_name",0);
		$this->db->select_as("$this->tbl_as.description_long","description_long",0);
		$this->db->select_as("$this->tbl_as.berat","berat",0);
		$this->db->select_as("$this->tbl_as.dimension_long","dimension_long",0);
		$this->db->select_as("$this->tbl_as.dimension_width","dimension_width",0);
		$this->db->select_as("$this->tbl_as.dimension_height","dimension_height",0);
		$this->db->select_as("$this->tbl_as.stok","stok",0);
		$this->db->select_as("$this->tbl_as.satuan","satuan",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),'left');
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->join_composite($this->tbl5,$this->tbl5_as,$this->__joinTbl5(),'left');
		$this->db->join_composite($this->tbl6,$this->tbl6_as,$this->__joinTbl6(),'left');

		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
    $this->db->where_as("$this->tbl_as.is_published",'1');
		$this->db->where_as("$this->tbl_as.is_featured",'1');
		$this->db->where_as("$this->tbl_as.is_active",'1');

    //advanced filter
    if(strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0){
      $this->db->between("($this->tbl_as.harga_jual)",'("'.$harga_jual_min.'")','("'.$harga_jual_max.'")',0);
    }else if(strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0){
      $this->db->where_as("$this->tbl_as.harga_jual",$this->db->esc($harga_jual_max),"AND","<=");
    }else if(strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0){
      $this->db->where_as("$this->tbl_as.harga_jual",$this->db->esc($harga_jual_min),"AND",">=");
    }
    if(count($b_kondisi_ids)>0) $this->db->where_in("$this->tbl_as.b_kondisi_id",$b_kondisi_ids);
    if(count($b_kategori_ids)>0) $this->db->where_in("$this->tbl_as.b_kategori_id",$b_kategori_ids);
    //end advanced filter

    if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),'or','%like%',1,0);
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%',0,1);
		}
		$this->db->order_by($sort_col,$sort_dir);
		$this->db->page($page,$page_size);
		return $this->db->get('object',0);
	}

	public function countHomePage($nation_code,$keyword="",$harga_jual_min="",$harga_jual_max="",$b_kondisi_ids=array(),$b_kategori_ids=array(),$kecamatan=""){
		$this->db->select_as("COUNT(*)",'total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),'left');
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->join_composite($this->tbl5,$this->tbl5_as,$this->__joinTbl5(),'left');
		$this->db->join_composite($this->tbl6,$this->tbl6_as,$this->__joinTbl6(),'left');

    //advanced filter
    if(strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0){
      $this->db->between("($this->tbl_as.harga_jual)",'("'.$harga_jual_min.'")','("'.$harga_jual_max.'")',0);
    }else if(strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0){
      $this->db->where_as("$this->tbl_as.harga_jual",$this->db->esc($harga_jual_max),"AND","<=");
    }else if(strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0){
      $this->db->where_as("$this->tbl_as.harga_jual",$this->db->esc($harga_jual_min),"AND",">=");
    }
    if(count($b_kondisi_ids)>0) $this->db->where_in("$this->tbl_as.b_kondisi_id",$b_kondisi_ids);
    if(count($b_kategori_ids)>0) $this->db->where_in("$this->tbl_as.b_kategori_id",$b_kategori_ids);
    //end advanced filter

		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.is_featured",'1');
		$this->db->where_as("$this->tbl_as.is_active",'1');
		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),'or','%like%',1,0);
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),'or','%like%');
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),'or','%like%',0,1);
		}
		$d = $this->db->get_first('object',0);
		if(isset($d->total)) return $d->total;
		return 0;
	}
}
