<?php
class G_Coverage_Area_Model extends SENE_Model {
	var $tbl = 'g_map_coverage';
	var $tbl_as = 'gmc';
	var $tbl2 = 'b_user_alamat_location_original';
	var $tbl2_as = 'bual_original';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
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

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	// START by Muhammad Sofi 27 January 2022 16:42 | adding form add data
	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
	// END by Muhammad Sofi 27 January 2022 16:42 | adding form add data

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="", $keyword="", $provinsi="", $kabkota="", $kecamatan="", $kelurahan="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY provinsi)", "no");
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.type", "type", 0);
		$this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
		$this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl_as.jalan", "jalan", 0);
		$this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
		$this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
		$this->db->select_as("$this->tbl_as.radius", "radius", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("'-'", "edit_text", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		// if(strlen($type)>0) {
		// 	$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		// }

		if(strlen($provinsi)>0) {
			$this->db->where_as("$this->tbl_as.provinsi", $this->db->esc($provinsi));
		}

		if(strlen($kabkota)>0) {
			$this->db->where_as("$this->tbl_as.kabkota", $this->db->esc($kabkota));
		}

		if(strlen($kecamatan)>0) {
			$this->db->where_as("$this->tbl_as.kecamatan", $this->db->esc($kecamatan));
		}

		if(strlen($kelurahan)>0) {
			$this->db->where_as("$this->tbl_as.kelurahan", $this->db->esc($kelurahan));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.type", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.provinsi", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kabkota", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kecamatan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kelurahan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.jalan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.latitude", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.longitude", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.radius", $keyword, "OR", "%like%", 0, 1);
		}

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $provinsi="", $kabkota="", $kecamatan="", $kelurahan="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		// if(strlen($type)>0) {
		// 	$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		// }

		if(strlen($provinsi)>0) {
			$this->db->where_as("$this->tbl_as.provinsi", $this->db->esc($provinsi));
		}

		if(strlen($kabkota)>0) {
			$this->db->where_as("$this->tbl_as.kabkota", $this->db->esc($kabkota));
		}

		if(strlen($kecamatan)>0) {
			$this->db->where_as("$this->tbl_as.kecamatan", $this->db->esc($kecamatan));
		}

		if(strlen($kelurahan)>0) {
			$this->db->where_as("$this->tbl_as.kelurahan", $this->db->esc($kelurahan));
		}
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.type", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.provinsi", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kabkota", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kecamatan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.kelurahan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.jalan", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.latitude", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.longitude", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.radius", $keyword, "OR", "%like%", 0, 1);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function getProvinsiData($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl2_as.provinsi", "provinsi_id", 0);
		$this->db->select_as("$this->tbl2_as.provinsi", "provinsi", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where("$this->tbl2_as.provinsi", $keyword, "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getKabupatenkotaData($nation_code, $keyword="", $is_active="", $provinsi_id) {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl2_as.kabkota", "kabkota_id", 0);
		$this->db->select_as("$this->tbl2_as.kabkota", "kabkota", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.provinsi", $this->db->esc($provinsi_id), "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl2_as.kabkota", addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getKecamatanData($nation_code, $keyword="", $is_active="", $provinsi_id, $kabkota_id) {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl2_as.kecamatan", "kecamatan_id", 0);
		$this->db->select_as("$this->tbl2_as.kecamatan", "kecamatan", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.provinsi", $this->db->esc($provinsi_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.kabkota", $this->db->esc($kabkota_id), "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl2_as.kecamatan", addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getKelurahanData($nation_code, $keyword="", $is_active="", $provinsi_id, $kabkota_id, $kecamatan_id) {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl2_as.kelurahan", "kelurahan_id", 0);
		$this->db->select_as("$this->tbl2_as.kelurahan", "kelurahan", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.provinsi", $this->db->esc($provinsi_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.kabkota", $this->db->esc($kabkota_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.kecamatan", $this->db->esc($kecamatan_id), "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl2_as.kelurahan", addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function checkDataIfExist($type="", $provinsi="", $kabkota="", $kecamatan="", $kelurahan="", $jalan="", $latitude="", $longitude="", $radius="") {
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.provinsi", $this->db->esc($provinsi), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.kabkota", $this->db->esc($kabkota), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.kecamatan", $this->db->esc($kecamatan), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.kelurahan", $this->db->esc($kelurahan), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.jalan", $this->db->esc($jalan), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.latitude", $this->db->esc($latitude), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.longitude", $this->db->esc($longitude), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.radius", $this->db->esc($radius), "AND", "=", 0, 0);
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
    }
}