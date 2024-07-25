<?php
	class Seme_Chat extends SQLite3 {
		var $db;
		var $dbname;
		var $reso;
		var $resa;
		
		var $table_struct;
		
		var $user_id,$nama,$picture,$last_login;
		
		var $is_sql,$is_select,$is_from,$is_join,$is_where,$is_order_by,$is_group_by,$is_limit_a,$is_limit_b;
		
		var $timeout_login;
		
		public function __construct($dbname="chat.db"){
			$this->timeout_login = strtotime('-5 minutes');
			
			$this->user_id = '';
			$this->nama = '';
			$this->picture = '';
			$this->last_login = 0;
			
			$this->is_sql = '';
			$this->is_select = '';
			$this->is_from = '';
			$this->is_join = '';
			$this->is_where = '';
			$this->is_group_by = '';
			$this->is_order_by = '';
			$this->is_limit_a = '';
			$this->is_limit_b = '';
			
			$this->dbname = $dbname;
			$this->db = 0;
			$this->table_struct = '';
			$this->reso = array();
			$this->resa = array();
			if(!empty($this->dbname)){
				$this->db = new SQLite3($dbname);
			}
		}
		private function __table_utype($uytpe){
			$uytpe = strtoupper($utype);
			$res = new stdClass();
			$res->key = 'TEXT';
			$res->value = 0;
			switch($utype){
				case 'DATE':
					$res->key = 'TEXT';
					$res->value = 10;
					break;
				case 'TIMESTAMP':
					$res->key = 'INTEGER';
					break;
				case 'DATETIME':
					$res->key = 'TEXT';
					$res->value = 19;
					break;
				case 'INT':
					$res->key = 'INTEGER';
					break;
				case 'INTEGER':
					$res->key = 'INTEGER';
					break;
				case 'VARCHAR':
					$res->key = 'TEXT';
					$res->value = 255;
					break;
				case 'BOOL':
					$res->key = 'TEXT';
					$res->value = 1;
					break;
				case 'FLOAT':
					$res->key = 'REAL';
					break;
				case 'REAL':
					$res->key = 'REAL';
					break;
				default:
					$res->key = 'TEXT';
			}
			return $res;
		}
		public function dbname($dbname){
			if(!empty($this->dbname)){
				$this->dbname = $dbname;
				$this->db = new SQLite3($dbname);
				if(!$this->db){
					trigger_error('Db connection to '.$dbname.' cant established');
				}
			}
			return $this;
		}
		
		
		public function install(){
			$res = $this->check_table('pesan');
			if(!$res){
				$sql = 'CREATE TABLE "pesan" (
"id"  INTEGER NOT NULL,
"from_user_id"  INTEGER,
"to_grup_id"  INTEGER,
"to_user_id"  INTEGER,
"cdate"  TEXT(20),
"from_nama"  TEXT(50),
"to_nama"  TEXT(50),
"from_picture"  TEXT(255),
"to_picture"  TEXT(255),
"from_grup_nama"  TEXT(255),
"pesan"  TEXT(255),
"attachment"  TEXT(255),
"ttype"  TEXT(8) DEFAULT personal,
"utype"  TEXT(8) DEFAULT pesan,
"is_read"  INTEGER,
"is_deleted"  INTEGER,
"balas_pesan_id"  INTEGER,
PRIMARY KEY ("id" ASC)
);';
				$this->exec($sql);
			}
			$res = $this->check_table('grup');
			if(!$res){
				$sql = 'CREATE TABLE "grup" (
"id"  INTEGER NOT NULL,
"admin_user_id"  INTEGER,
"nama"  TEXT(255),
"gambar"  TEXT(255),
"anggota_user_id"  INTEGER,
"sticky_pesan_id"  INTEGER,
"grup_id"  INTEGER,
PRIMARY KEY ("id" ASC)
);';
				$this->exec($sql);
			}
			$res = $this->check_table('user');
			if(!$res){
				$sql = 'CREATE TABLE "user" (
"id"  INTEGER NOT NULL,
"nama"  TEXT(255),
"picture"  TEXT(255),
"last_login"  INTEGER,
"status_message"  TEXT(28),
"is_online"  INTEGER DEFAULT 0,
PRIMARY KEY ("id")
);';
				$this->exec($sql);
			}
		}
		
		
		public function table_struct_reset(){
			$this->table_struct = '';
			return $this;
		}
		public function exec($sql){
			return $this->db->exec($sql);
		}
		public function esc($value){
			if(is_string($value)) 
				return $this->db->escapeString($value);
		}
		
		public function insert($tblname, $di=array(),$is_debug=0){
			$inner = '';
			$outer = '';
			
			foreach($di as $key=>$val){
				$inner .= $key.',';
				$outer .= '"'.$this->esc($val).'",';
			}
			
			$inner = rtrim($inner,',');
			$outer = rtrim($outer,',');
			
			$sql = 'INSERT INTO '.$tblname.'('.$inner.') VALUES('.$outer.')';
			if($is_debug) die($sql);
			$res = $this->exec($sql);
			if($res){
				return $this->db->lastInsertRowID();
			}else{
				return 0;
			}
		}
		public function update($tblname, $du=array(),$is_debug=0){
			$sql = 'UPDATE '.$tblname.' SET ';
			foreach($du as $k=>$v){
				$sql .= $k.' = '.$v.',';
			}
			$sql = rtrim($sql,',');
			if(strlen($this->is_where)){
				$this->is_where = rtrim($this->is_where,' AND ');
				$this->is_where = rtrim($this->is_where,' OR ');
				$sql .= ' WHERE '.$this->is_where;
			}
			if($is_debug) die($sql);
			return $this->exec($sql);
		}
		
		
		public function flush_query(){
			$this->is_sql = '';
			$this->is_select = '';
			$this->is_from = '';
			$this->is_join = '';
			$this->is_where = '';
			$this->is_group_by = '';
			$this->is_order_by = '';
			$this->is_limit_a = '';
			$this->is_limit_b = '';
			return $this;
		}
		
		public function where($key,$val="",$operand="AND",$comparison="=",$start_bracket=0,$end_bracket=0){
			$operand = strtoupper($operand);
			$comparison = strtoupper($comparison);
			if(!is_array($key) && !empty($val)){
				if($start_bracket) $this->is_where .= '(';
				$this->is_where .= ''.$key.' '.$comparison.' '.$this->esc($val).' '.$operand.' ';
				if($end_bracket){
					$this->is_where = rtrim($this->is_where,' AND ');
					$this->is_where = rtrim($this->is_where,' OR ');
					$this->is_where .= ')';
				} 
			}else if(is_array($key) && empty($val)){
				if($start_bracket) $this->is_where .= '(';
				foreach($key as $k=>$v){
					$this->is_where .= ''.$k.' '.$comparison.' '.$this->esc($v).' '.$operand.' ';
				}
				if($end_bracket){
					$this->is_where = rtrim($this->is_where,' AND ');
					$this->is_where = rtrim($this->is_where,' OR ');
					$this->is_where .= ')';
				} 
			}else{
				trigger_error('Where method cant applicable, please check your params');
			}
			return $this;
		}
		
		public function table_struct($name,$utype,$uval="",$pkey="",$is_null=0){
			$name = strtolower($name);
			$ut = $this->__table_utype($utype);
			$utype = $ut->key;
			$pkey = strtoupper($pkey);
			$null = 'NOT NULL';
			if($is_null){
				$null = '';
			}
			$this->table_struct .= ''.$name.' '.$utype.' '.$uval.' '.$pkey.',';
			return $this;
		}
		public function table_create($table_name,$act=0){
			//act = 1 if_not_exist, act=2 drop table first
			if(strlen($this->table_struct)<=3) 
				trigger_error('Cant create table '.$table_name.' because db connection not found');
			
			$table_name = strtolower($table_name);
			$sql = 'CREATE TABLE table_name';
			if($act){
				if($act == 1){
					$sql .= ' IF NOT EXIST ';
				}else if($act == 2){
					$this->db->exec($sql);
				}
			}
			$this->table_struct = rtrim($this->table_struct,',');
			$sql .= '('.$this->table_struct.');';
			if($con){
				$this->db->exec($sql);
			}else{
				trigger_error('Cant create table '.$table_name.' because db connection not found');
			}
			return $this;
		}
		public function get($sql="",$utype="object"){
			if($this->db){
				if(empty($sql)){
					$sql = $this->sql;
				}
				//clear
				$this->reso = array();
				$this->resa = array();
				
				$dbresult = $this->db->query($sql);
				while ($result = $dbresult->fetchArray(SQLITE3_ASSOC)) {
					$res = new stdClass();
					foreach($result as $key=>$val){
						$res->{$key} = $val;
					}
					$this->reso[]= $res;
					$this->resa[]= $result;
				}
				if(strtolower($utype) == "object") return $this->reso;
				return $this->resa;
			}else{
				trigger_error('Cant execute sql '.$sql.' because db connection not found');
			}
		}
		public function get_first($sql,$utype="object"){
			if($this->db){
				//clear
				$this->reso = array();
				$this->resa = array();
				
				$dbresult = $this->db->query($sql);
				while ($result = $dbresult->fetchArray(SQLITE3_ASSOC)) {
					$res = new stdClass();
					foreach($result as $key=>$val){
						$res->{$key} = $val;
					}
					$this->reso[]= $res;
					$this->resa[]= $result;
				}
				if(strtolower($utype) == "object"){
					if(isset($this->reso[0])){
						return $this->reso[0];
					}else{
						return new stdClass();
					}
				}else{
					if(isset($this->resa[0])) return $this->reso[0];
					return array();
				}
			}else{
				trigger_error('Cant execute sql '.$sql.' because db connection not found');
			}
		}
		public function check_table($tablename){
			if($this->db){
				$sql = 'SELECT COUNT(*) total FROM sqlite_master WHERE type="table" AND name LIKE "'.$tablename.'";';
				$res = $this->get_first($sql);
				if(isset($res->total)){
					return $res->total;
				}
			}
			return 0;
		}
		public function get_pesan_last_id(){
			$sql = 'SELECT MAX(id)+1 id FROM pesan';
			$res = $this->get_first($sql);
			if(isset($res->id)){
				return $res->id;
			}else{
				return 1;
			}
		}
		public function check_user($user_id){
			$sql = 'SELECT * FROM user WHERE id = '.$user_id.'';
			return $this->get_first($sql);
		}
		public function set_offline($user_id){
			$sql = 'UPDATE user SET is_online = 0 WHERE id = '.$user_id.'';
			return $this->exec($sql);
		}
		public function insert_user($user_id,$nama,$last_login,$status_message,$is_online=0){
			$sql  = 'INSERT INTO user(id,nama,last_login,status_message,is_online)';
			$sql .= 'VALUES("'.$user_id.'","'.$nama.'","'.$last_login.'","'.$status_message.'","'.$is_online.'")';
			$this->exec($sql);
		}
		public function update_user($user_id,$nama,$last_login,$status_message){
			$sql = 'UPDATE user SET nama = "'.$nama.'", last_login = "'.$last_login.'", status_message = "'.$status_message.'" WHERE id = '.$user_id.'';
			$this->exec($sql);
		}
		public function update_user_login(){
			$timeout_login = $this->timeout_login;
			$sql = 'UPDATE user SET is_login = 0 WHERE last_login <= '.$timeout_login.'';
			$this->exec($sql);
			$sql = 'UPDATE user SET is_login = 1 WHERE last_login > '.$timeout_login.'';
			$this->exec($sql);
		}
		public function get_user_by_id($user_id){
			$sql = 'SELECT * FROM user WHERE id = '.$user_id.'';
			return $this->get_first($sql);
		}
		public function sender($user_id,$nama,$picture="",$status_message=""){
			$this->user_id = $user_id;
			$this->nama = $nama;
			$this->picture = $picture;
			$this->last_login = strtotime('now');
			
			$last_login = $this->last_login;
			
			$u = $this->check_user($user_id);
			if(!isset($u->id)){
				$this->insert_user($user_id,$nama,$last_login,$status_message,$is_online=1);
			}else{
				//$this->update_user($user_id,$nama,$last_login,$status_message);
			}
			return $this;
		}
		
		public function get_user(){
			$sql = 'SELECT * FROM user ORDER BY is_online DESC;';
			return $this->get($sql);
		}
		
		public function set_read($pesan_id="",$from_user_id=""){
			$sql = 'UPDATE pesan SET is_read = 1 WHERE ';
			if(!empty($pesan_id) && empty($from_user_id)){
				$sql .= ' id = '.$id.' AND from_user_id = '.$from_user_id.';';
				$this->exec($sql);
			}else if(!empty($pesan_id) && empty($from_user_id)){
				$sql .= ' id = '.$id.'; ';
				$this->exec($sql);
			}else if(empty($pesan_id) && !empty($from_user_id)){
				$sql .= ' from_user_id = '.$from_user_id.'; ';
				$this->exec($sql);
			}
		}
		public function send_pm($to_user_id,$to_nama="",$pesan,$to_picture="",$utype="pesan",$attachment="",$balasan_ke_id=""){
			$di = array();
			//$lid = $this->get_pesan_last_id();
			//$di['id'] = $lid;
			
			//$di['id'] = strtotime('now').$this->user_id.$to_user_id;
			$di['from_user_id'] = $this->user_id;
			$di['to_grup_id'] = '';
			$di['to_user_id'] = $to_user_id;
			$di['cdate'] = date('Y-m-d H:i:s');
			$di['from_nama'] = $this->nama;
			$di['to_nama'] = $to_nama;
			$di['from_picture'] = $this->picture;
			$di['to_picture'] = $to_picture;
			$di['pesan'] = $pesan;
			$di['attachment'] = $balasan_ke_id;
			$di['ttype'] = 'private';
			$di['utype'] = $utype;
			$di['is_read'] = 0;
			$di['is_deleted'] = 0;
			$di['balas_pesan_id'] = $balasan_ke_id;
			return $this->insert('pesan',$di,0);
		}
		public function get_my_pm($from_user_id,$to_user_id="",$is_read="1",$last_pesan_id="",$limit='250'){
			if(empty($to_user_id)){
				$sql = 'SELECT * FROM pesan WHERE ttype LIKE "private" AND from_user_id = '.$from_user_id.' GROUP BY to_user_id ORDER BY id DESC';
			}else{
				$last = '';
				if(!empty($last_pesan_id)){
					$last = ' AND id > '.$last_pesan_id;
				}
				$sql = 'SELECT * FROM pesan WHERE (ttype LIKE "private" '.$last.') AND (from_user_id = '.$from_user_id.' AND to_user_id = '.$to_user_id.') OR (from_user_id = '.$to_user_id.' AND to_user_id = '.$from_user_id.') ORDER BY id ASC';
				$sql  = 'SELECT * FROM pesan WHERE (ttype LIKE "private" '.$last.')';
				$sql .= ' AND ((from_user_id = '.$from_user_id.' AND to_user_id = '.$to_user_id.') OR (from_user_id = '.$to_user_id.' AND to_user_id = '.$from_user_id.'))';
				$sql .= ' LIMIT '.$limit;
				//die($sql);
			}
			return $this->get($sql);
		}
		
		public function group_create($admin_user_id,$nama,$anggota_user_id=array(),$gambar="",$sticky_pesan_id=""){
			$this->flush_query();
			
			$di = array();
			$di['admin_user_id'] = $admin_user_id;
			$di['nama'] = $nama;
			$di['gambar'] = $gambar;
			$di['anggota_user_id'] = $admin_user_id;
			$di['sticky_pesan_id'] = $sticky_pesan_id;
			$di['grup_id'] = '';
			$gid = $this->insert('grup',$di,0);
			
			$du = array();
			$du['grup_id'] = $gid;
			
			$this->where('id',$gid);
			$this->update('grup',$du,1);
			$this->flush_query();
			
			if($gid && count($anggota_id)){
				foreach($anggota_user_id as $auid){
					$di['anggota_user_id'] = $auid;
					$this->flush_query();
					$this->insert('grup',$di);
				}
			}
			return $gid;
		}
		public function group_list($anggota_user_id){
			$sql = 'SELECT g.*, COUNT(p.id) total FROM grup g LEFT JOIN pesan p ON p.to_grup_id = g.id WHERE g.anggota_user_id = "'.$anggota_user_id.'" GROUP BY g.id ;';
			//die($sql);
			return $this->get($sql);
		}
		public function send_group($to_group_id,$to_grup_nama,$pesan,$utype="pesan",$attachment="",$balasan_ke_id="",$to_picture=""){
			$di = array();
			//$lid = $this->get_pesan_last_id();
			//$di['id'] = $lid;
			
			//$di['id'] = strtotime('now').$this->user_id.$to_user_id;
			$di['from_user_id'] = $this->user_id;
			$di['to_grup_id'] = $to_group_id;
			$di['to_user_id'] = '';
			$di['cdate'] = date('Y-m-d H:i:s');
			$di['from_nama'] = $this->nama;
			$di['to_nama'] = '';
			$di['from_picture'] = $this->picture;
			$di['to_picture'] = $to_picture;
			$di['to_grup_nama'] = $to_grup_nama;
			$di['pesan'] = $pesan;
			$di['attachment'] = $balasan_ke_id;
			$di['ttype'] = 'group';
			$di['utype'] = $utype;
			$di['is_read'] = 0;
			$di['is_deleted'] = 0;
			$di['balas_pesan_id'] = $balasan_ke_id;
			return $this->insert('pesan',$di,0);
		}
		
		public function get_my_group($from_user_id,$to_group_id="",$is_read="1"){
			if(empty($to_group_id)){
				$sql = 'SELECT * FROM pesan WHERE ttype LIKE "group" AND from_user_id = '.$from_user_id.' GROUP BY to_user_id ORDER BY id DESC';
			}else{
				$sql = 'SELECT * FROM pesan WHERE ttype LIKE "group" AND from_user_id = '.$from_user_id.' AND to_group_id = '.$to_group_id.' ORDER BY id DESC';
			}
			return $this->get($sql);
		}
		
		public function cleanMessage($limit='-3 days'){
			$date = date("Y-m-d",strtotime($limit));
			$sql = 'DELETE FROM pesan WHERE cdate <= DATE("'.$date.'")';
			//die($sql);
			return $this->exec($sql);
		}
	}