<?php
//api_admin
	class A_Pengguna_Module_Model extends SENE_Model
	{
		var $tbl 	= 'a_pengguna_module';
		var $tbl_as = 'apm';

		public function __construct()
		{
			parent::__construct();
			$this->db->from($this->tbl,$this->tbl_as);
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

		public function check_access($nation_code, $a_pengguna_id, $identifier){
			$this->db->select_as("COUNT(*)", "jumlah", 0);
			$this->db->where("nation_code", $nation_code);
			$this->db->where("a_pengguna_id", $a_pengguna_id);
			$this->db->where("a_modules_identifier", $identifier);
			$d = $this->db->from($this->tbl)->get_first("object",0);
			if(isset($d->jumlah)) return $d->jumlah;
			return 0;
		}

		public function pengguna_module($nation_code,$id){
			$this->db->select();
			$this->db->from($this->tbl, $this->tbl_as);
			$this->db->where("nation_code", $nation_code);
			$this->db->where("a_pengguna_id", $id);
			$this->db->where("rule", "allowed_except", "AND", "!=");
			return $this->db->get("object", 0);
		}

		public function set($di){
			if(!is_array($di)) return 0;
			$this->db->insert($this->tbl, $di, 0, 0);
			return $this->db->last_id;
		}

		public function update($nation_code, $id, $du, $filter="")
		{
			if (!is_array($du)) return 0;
			if (empty($filter))
			{
				$this->db->where("nation_code",$nation_code);
				$this->db->where("id", $id);
			}
			else
			{
				foreach ($filter as $flt => $flt_val)
				{
					$this->db->where($flt, $flt_val);
				}
			}
			return $this->db->update($this->tbl, $du, 0);
		}

		public function del($nation_code,$id, $filter="")
		{
			if (empty($filter))
			{
				$this->db->where("nation_code",$nation_code);
				$this->db->where("id", $id);
			}
			else
			{
				foreach ($filter as $flt => $flt_val)
				{
					$this->db->where($flt, $flt_val);
				}
			}
			return $this->db->delete($this->tbl);
		}

		public function updateModule($du, $nation_code, $pengguna_id){
			if (!is_array($du)) return 0;
			$this->db->where("nation_code", $nation_code);
			$this->db->where("a_pengguna_id", $pengguna_id);
			$this->db->where("rule", "allowed_except", "AND", "!=");
			return $this->db->update($this->tbl, $du, 0);
		}
		public function getLastId($nation_code){
			$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
			$this->db->from($this->tbl, $this->tbl_as);
			$this->db->where("nation_code",$nation_code);
			$d = $this->db->get_first('',0);
			if(isset($d->last_id)) return $d->last_id;
			return 0;
		}

		public function delModule($nation_code,$pengguna_id)
		{
			$this->db->where("nation_code", $nation_code);
			$this->db->where("a_pengguna_id", $pengguna_id);
			$this->db->where("tmp_active", "N");
			$this->db->where("rule", "allowed_except", "AND", "!=");
			return $this->db->delete($this->tbl);
		}
		public function getUserModules($nation_code,$a_pengguna_id){
			//$sql = "SELECT *, COALESCE(`a_modules_identifier`,'') AS module FROM $this->tbl WHERE `nation_code` = ".$this->db->esc($nation_code)." AND `a_pengguna_id` = ".$this->db->esc($a_pengguna_id)." ORDER BY a_modules_identifier ASC";
			//return $this->select($sql);
			$this->db->select_as("*, COALESCE(`a_modules_identifier`,'')","module",0);
			$this->db->from($this->tbl);
			$this->db->where("nation_code",$nation_code);
			$this->db->where("a_pengguna_id",$a_pengguna_id);
			$this->db->order_by("a_modules_identifier","ASC");
			return $this->db->get();

		}
	}
