<?php
//admin
	class B_Gudang_Stok_Model extends SENE_Model {
		
		var $tbl	= 'b_gudang_stok';
		var $tbl_as = 'bgs';
		
		public function __construct()
		{
			parent::__construct();
			$this->db->from($this->tbl, $this->tbl_as);
		}
		
		public function countAll($keyword='')
		{
			$this->db->flushQuery();
			$this->db->select_as("COUNT(*)", "jumlah", 0);
			$d = $this->db->from($this->tbl,$this->tbl_as)->get_first("object",0);
			if (isset($d->jumlah)) return $d->jumlah;
			return 0;
		}
		
		public function del($id)
		{
			$this->db->where('id', $id);
			return $this->db->delete($this->tbl);
		}
		
		public function getAll($page=0, $pagesize=10, $sortCol="id", $sortDir="ASC", $keyword="")
		{
			$d = $this->db->query("
				SELECT	a.*, b.nama as nama_gudang, c.nama as nama_produk
				FROM	b_gudang_stok a
				INNER	JOIN a_gudang b ON a.a_gudang_id = b.id
				INNER	JOIN c_produk c ON a.c_produk_id = c.id
			");
			return $d;
		}
		
		public function getById($id)
		{
			$this->db->where('id', $id);
			return $this->db->get_first();
		}
		
		public function set($di)
		{
			if (!is_array($di)) return 0;
			$di = array_merge(array('date_create' => date('Y-m-d H:i:s')), $di);
			$this->db->insert($this->tbl, $di, 0, 0);
			return $this->db->last_id;
		}
		
		public function stokBarang($filter="")
		{
			$this->db->flushQuery();
			$this->db->select_as("IFNULL(SUM(qty), 0)", "jumlah", 0);
			$this->db->from($this->tbl, $this->tbl_as);
			if (!empty($filter))
			{
				foreach ($filter as $ftr => $ftr_val)
				{
					$this->db->where($ftr, $ftr_val);
				}
			}
			$d = $this->db->get_first("object", 0);
			if (isset($d->jumlah)) return $d->jumlah;
			return 0;
		}
		
		public function update($id, $du)
		{
			if (!is_array($du)) return 0;
			$du = array_merge(array('date_modified' => date('Y-m-d H:i:s')), $du);
			$this->db->where('id', $id);
			return $this->db->update($this->tbl, $du, 0);
		}
		public function getByCompanyId($a_company_id){
			$this->db->from($this->tbl,$this->tbl_as);
			$this->db->order_by('c_produk_id','asc');
			$dts = $this->db->get('',0);
			$data = array();
			foreach($dts as $dt){
				$data[$dt->c_produk_id] = $dt;
			}
			return $data;
		}
		
	}
	
