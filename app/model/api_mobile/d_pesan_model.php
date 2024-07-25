<?php
class D_Pesan_Model extends SENE_Model {
	var $tbl = 'd_pesan';
	var $tbl_as = 'dpm';
  public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAllByUserId($page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$b_user_id=""){
		$this->db->flushQuery();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("reply_from",'user');
		if(strlen($keyword)>1){
			$this->db->where("id",$keyword,"OR","%like%",1,0);
			$this->db->where("is_read",$keyword,"OR","%like%",0,0);
			$this->db->where("is_complain",$keyword,"OR","%like%",0,0);
			$this->db->where("utype",$keyword,"OR","%like%",0,0);
			$this->db->where("b_user_nama",$keyword,"OR","%like%",0,0);
			$this->db->where("isi",$keyword,"OR","%like%",0,1);
		}
		$this->db->where_as("COALESCE(`d_pesan_id`,'-')",$this->db->esc("-"));
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAllByUserId($keyword="",$b_user_id=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("reply_from",'user');
		if(strlen($keyword)>1){
			$this->db->where("id",$keyword,"OR","%like%",1,0);
			$this->db->where("is_read",$keyword,"OR","%like%",0,0);
			$this->db->where("is_complain",$keyword,"OR","%like%",0,0);
			$this->db->where("utype",$keyword,"OR","%like%",0,0);
			$this->db->where("b_user_nama",$keyword,"OR","%like%",0,0);
			$this->db->where("isi",$keyword,"OR","%like%",0,1);
		}
		$this->db->where_as("COALESCE(`d_pesan_id`,'-')",$this->db->esc("-"));
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
  public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
  public function update($id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function getLatestUnRead(){
		$sql = "SELECT 'text-success' style, 'belum dibaca' status, COUNT(*) total FROM $this->tbl WHERE is_read = 0 AND COALESCE(d_pesan_id,0) = 0";
		return $this->db->query($sql);
	}
	public function getStatusCount(){
		$sql = "SELECT IF(is_read=1,'Sudah Dibaca','Belum Dibaca') 'status', IF(is_read=1,'text-muted','text-success') 'style', COUNT(*) 'total' FROM d_pesan WHERE d_pesan_id IS NULL GROUP BY is_read ";
		return $this->db->query($sql);
	}
	public function getReplyCount(){
		$sql = "SELECT COUNT(*) 'total' FROM `d_pesan` WHERE is_reply=1 AND COALESCE(d_pesan_id,0) = 0";
		return $this->db->query($sql);
	}
	public function getIdCount(){
		$sql = "SELECT id, COUNT(*) 'total' FROM d_pesan";
		return $this->db->query($sql);
	}
	public function getUtypeCount(){
		$sql = "SELECT utype, COUNT(*) total FROM $this->tbl WHERE COALESCE(d_pesan_id,0) = 0 GROUP BY utype ";
		return $this->db->query($sql);
	}
	public function getByIdAndUserId($id,$b_user_id){
		$this->db->where('id',$id)->where('b_user_id',$b_user_id)->order_by('id','asc');
		return $this->db->get_first();
	}
	public function getReplyByPesanId($d_pesan_id){
		$this->db->where('d_pesan_id',$d_pesan_id);
		$this->db->order_by('id','asc');
		$this->db->limit(4999);
		return $this->db->get();
	}
	public function countBalasan($d_pesan_id){
		$this->db->select_as('COUNT(*)','total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('d_pesan_id',$d_pesan_id);
		$this->db->where('reply_from','user');
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}
}
<?php
class D_Pesan_Model extends SENE_Model {
	var $tbl = 'd_pesan';
	var $tbl_as = 'dpm';
  public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAllByUserId($page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$b_user_id=""){
		$this->db->flushQuery();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("reply_from",'user');
		if(strlen($keyword)>1){
			$this->db->where("id",$keyword,"OR","%like%",1,0);
			$this->db->where("is_read",$keyword,"OR","%like%",0,0);
			$this->db->where("is_complain",$keyword,"OR","%like%",0,0);
			$this->db->where("utype",$keyword,"OR","%like%",0,0);
			$this->db->where("b_user_nama",$keyword,"OR","%like%",0,0);
			$this->db->where("isi",$keyword,"OR","%like%",0,1);
		}
		$this->db->where_as("COALESCE(`d_pesan_id`,'-')",$this->db->esc("-"));
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAllByUserId($keyword="",$b_user_id=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("reply_from",'user');
		if(strlen($keyword)>1){
			$this->db->where("id",$keyword,"OR","%like%",1,0);
			$this->db->where("is_read",$keyword,"OR","%like%",0,0);
			$this->db->where("is_complain",$keyword,"OR","%like%",0,0);
			$this->db->where("utype",$keyword,"OR","%like%",0,0);
			$this->db->where("b_user_nama",$keyword,"OR","%like%",0,0);
			$this->db->where("isi",$keyword,"OR","%like%",0,1);
		}
		$this->db->where_as("COALESCE(`d_pesan_id`,'-')",$this->db->esc("-"));
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
  public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
  public function update($id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function getLatestUnRead(){
		$sql = "SELECT 'text-success' style, 'belum dibaca' status, COUNT(*) total FROM $this->tbl WHERE is_read = 0 AND COALESCE(d_pesan_id,0) = 0";
		return $this->db->query($sql);
	}
	public function getStatusCount(){
		$sql = "SELECT IF(is_read=1,'Sudah Dibaca','Belum Dibaca') 'status', IF(is_read=1,'text-muted','text-success') 'style', COUNT(*) 'total' FROM d_pesan WHERE d_pesan_id IS NULL GROUP BY is_read ";
		return $this->db->query($sql);
	}
	public function getReplyCount(){
		$sql = "SELECT COUNT(*) 'total' FROM `d_pesan` WHERE is_reply=1 AND COALESCE(d_pesan_id,0) = 0";
		return $this->db->query($sql);
	}
	public function getIdCount(){
		$sql = "SELECT id, COUNT(*) 'total' FROM d_pesan";
		return $this->db->query($sql);
	}
	public function getUtypeCount(){
		$sql = "SELECT utype, COUNT(*) total FROM $this->tbl WHERE COALESCE(d_pesan_id,0) = 0 GROUP BY utype ";
		return $this->db->query($sql);
	}
	public function getByIdAndUserId($id,$b_user_id){
		$this->db->where('id',$id)->where('b_user_id',$b_user_id)->order_by('id','asc');
		return $this->db->get_first();
	}
	public function getReplyByPesanId($d_pesan_id){
		$this->db->where('d_pesan_id',$d_pesan_id);
		$this->db->order_by('id','asc');
		$this->db->limit(4999);
		return $this->db->get();
	}
	public function countBalasan($d_pesan_id){
		$this->db->select_as('COUNT(*)','total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('d_pesan_id',$d_pesan_id);
		$this->db->where('reply_from','user');
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}
}
