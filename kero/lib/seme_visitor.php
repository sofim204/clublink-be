<?php
	class Seme_Visitor extends SQLite3 {
		var $db;
		var $dbname;
		var $reso;
		var $resa;

		var $table_struct;

		var $user_id,$nama,$picture,$last_login;

		var $is_sql,$is_select,$is_from,$is_join,$is_where,$is_order_by,$is_group_by,$is_limit_a,$is_limit_b;

		var $timeout_login;

		public function __construct($dbname="visitor.db"){
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
			$res = $this->check_table('visitors');
			if(!$res){
				$sql = 'CREATE TABLE "visitors" (
"id"  INTEGER NOT NULL,
"sess" TEXT(64) NOT NULL ,
"user_id"  INTEGER,
"user_name"  TEXT(255),
"cookie"  TEXT(255),
"ipaddress"  TEXT(86),
"browser_name"  TEXT(32),
"browser_version"  TEXT(32),
"page_type"  TEXT(24),
"page_url"  TEXT(255),
"page_name"  TEXT(255),
"cdate"  TEXT(20),
"stime"  INTEGER,
"etime"  INTEGER,
"referer"  TEXT(255),
"country_code"  TEXT(4),
"jml"  INTEGER,
PRIMARY KEY ("id" ASC)
);';
				$this->exec($sql);
			}
			$res = $this->check_table('online');
			if(!$res){
				$sql = 'CREATE TABLE "online" (
"sess" TEXT(64) NOT NULL ,
"browser_name"  TEXT(32),
"browser_version"  TEXT(32),
"url" TEXT(255),
"ipaddress"  TEXT(86),
"negara"  TEXT(4),
"cdate"  TEXT(20),
"is_online"  INTEGER,
PRIMARY KEY ("sess" ASC)
);';
				$this->exec($sql);
			}
		}
		public function drop(){
			$sql = 'DROP TABLE IF EXISTS visitors;';
			$this->exec($sql);
			$sql = 'DROP TABLE IF EXISTS online;';
			$this->exec($sql);
		}
		public function updateTableVisitors(){
			$sql = 'ALTER TABLE "visitors" ADD "sess" TEXT(64)';
			$this->exec($sql);
		}

		public function table_struct_reset(){
			$this->table_struct = '';
			return $this;
		}
		public function exec($sql){
			$this->dbname($this->dbname);
			$this->db->exec('PRAGMA journal_mode = wal;');
			$d = $this->db->exec($sql);
			$this->db->close();
			return $d;
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
				$d = $this->db->lastInsertRowID();
				$this->db->close();
				return $d;
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
			$d = $this->exec($sql);
			return $d;
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
			$this->dbname($this->dbname);
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
				$dbresult = null;
				$this->db->close();
				if(strtolower($utype) == "object") return $this->reso;
				return $this->resa;
			}else{
				trigger_error('Cant execute sql '.$sql.' because db connection not found');
			}
		}
		public function get_first($sql,$utype="object"){
			$this->dbname($this->dbname);
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
				$this->db->close();
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
		public function visitor_last_id(){
			$sql = 'SELECT MAX(id)+1 id FROM visitors';
			$res = $this->get_first($sql);
			if(isset($res->id)){
				return $res->id;
			}else{
				return 1;
			}
		}
		public function visitor_add($id,$user_id,$user_name,$cookie,$ipaddress,$page_type,$page_url,$page_name,$cdate,$stime,$etime,$referer,$country_code="ID",$sess="",$browser_name="",$browser_version=""){
			$sql  = 'INSERT INTO visitors(id,user_id,user_name,cookie,ipaddress,page_type,page_url,page_name,cdate,stime,etime,referer,country_code,jml,sess,browser_name,browser_version)';
			$sql .= 'VALUES("'.$id.'","'.$user_id.'","'.$user_name.'","'.$cookie.'","'.$ipaddress.'","'.$page_type.'","'.$page_url.'","'.$page_name.'","'.$cdate.'","'.$stime.'","'.$etime.'","'.$referer.'","'.$country_code.'",1,"'.$sess.'","'.$browser_name.'","'.$browser_version.'")';
			$d = $this->exec($sql);
			//error_log('SQLite visitor_add: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function visitor_check($user_id,$cookie,$page_url,$ipaddress,$cdate,$referer,$sess=""){
			$sql = 'SELECT * FROM visitors WHERE user_id = "'.$user_id.'" AND cookie LIKE "'.$cookie.'" AND page_url LIKE "'.$page_url.'" AND ipaddress LIKE "'.$ipaddress.'" AND cdate LIKE "'.$cdate.'" AND referer LIKE "'.$referer.'" AND sess LIKE "'.$sess.'" ORDER BY id DESC';
			$d = $this->get_first($sql);
			//error_log('SQLite visitor_check: '.$this->db->lastErrorMsg());
			return $d;
		}
    public function visitor_update_time($id,$etime){
      $sql = 'UPDATE visitors SET etime = "'.$etime.'", jml = jml+1 WHERE id = "'.$id.'" ';
      $d = $this->exec($sql);
			//error_log('SQLite visitor_update_time: '.$sql);
			return $d;
    }
		public function visitor_delete_by_id($id){
			$sql = 'DELETE FROM visitors WHERE id = '.$user_id.'';
			$d = $this->exec($sql);
			//error_log('SQLite visitor_delete_by_id: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function visitor_clean($limit='-360 days'){
			$date = date("Y-m-d",strtotime($limit));
			$sql = 'DELETE FROM visitors WHERE cdate <= DATE("'.$date.'")';
			$d = $this->exec($sql);
			//error_log('SQLite visitor_clean: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function search_term_count(){
			$sql = 'SELECT page_name, COUNT(*) kali, SUM(jml) total FROM visitors WHERE page_type ="Search"  GROUP BY page_name ORDER BY COUNT(*) DESC LIMIT 5';
			$d = $this->get($sql);
			//error_log('SQLite search_term_count: '.$this->db->lastErrorMsg());
			return $d;
		}

		//session
		public function session_total($adate,$edate=""){
			$sql = 'SELECT strftime("%Y-%m-%d", cdate) cdate, COUNT(*) total FROM visitors WHERE ';
			if(strlen($adate)>=10 && strlen($edate)>=10){
				$sql .= '(cdate BETWEEN DATE("'.$edate.'") AND DATE("'.$adate.'"))';
			}else if(strlen($adate)>=10 && strlen($edate)<10){
				$sql .= '(cdate BETWEEN DATE("'.$adate.'","-1 day") AND DATE("'.$adate.'"))';
			}else{
				$sql .= ' 1 ';
			}
			$sql .= 'GROUP BY DATE(cdate) ORDER BY cdate ASC';
			$d = $this->get($sql);
			//error_log('SQLite visitor_check: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function session_total_jam($adate,$edate=""){
			$sql = 'SELECT DATE(cdate) cdate, strftime("%H", cdate) jam, COUNT(*) total FROM visitors WHERE ';
			if(strlen($adate)>=10 && strlen($edate)>=10){
				$sql .= ' (cdate BETWEEN DATE("'.$edate.'") AND DATE("'.$adate.'")) ';
			}else if(strlen($adate)>=10 && strlen($edate)<10){
				$sql .= ' (cdate BETWEEN DATE("'.$adate.'","-1 day") AND DATE("'.$adate.'")) ';
			}else{
				$sql .= ' (cdate BETWEEN DATE("'.$adate.'","-1 day") AND DATE("'.$adate.'")) ';
			}
			$sql .= 'GROUP BY DATE(cdate) ORDER BY cdate ASC';
			$d = $this->get($sql);
			//error_log('SQLite visitor_check: '.$this->db->lastErrorMsg());
			return $d;
		}

		//online visitor
		public function online_check($sess){
			$sql = 'SELECT * FROM online WHERE sess = "'.$sess.'"  ORDER BY sess DESC';
			$d = $this->get_first($sql);
			//error_log('SQLite visitor_check: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function online_clear(){
			$sql = "UPDATE online SET is_online = 0 WHERE strftime('%s','now') - strftime('%s',cdate) > 50 ";
			$d = $this->exec($sql);
		}
    public function online_update_time($sess,$url){
			$this->online_clear();
      $sql = 'UPDATE online SET url="'.$url.'", cdate = DATETIME("NOW"), is_online = 1 WHERE sess = "'.$sess.'" ';
      $d = $this->exec($sql);
			//error_log('SQLite online_update_time: '.$sql);
			return $d;
    }
		public function online_add($sess,$url,$ipaddress,$negara,$cdate,$is_online=1){
			$sql  = 'INSERT INTO online(sess,url,ipaddress,negara,cdate,is_online)';
			$sql .= 'VALUES("'.$sess.'","'.$url.'","'.$ipaddress.'","'.$negara.'",DATETIME("NOW"),"1")';
			$d = $this->exec($sql);
			//error_log('SQLite online_add: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function online_count(){
			$sql = 'SELECT COUNT(*) total FROM online WHERE is_online = "1"';
			$d = $this->get_first($sql);
			//error_log('SQLite online_count: '.$this->db->lastErrorMsg());
			return $d->total;
		}
		public function online_list(){
			$sql = 'SELECT negara, url, COUNT(*) total FROM online WHERE is_online = "1" GROUP BY url ORDER BY cdate ASC';
			$d = $this->get($sql);
			//error_log('SQLite online_list: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function getSessionPer($adate,$edate=""){
			$g = '%Y-%m-%d';
			$sql = 'SELECT STRFTIME("%s",cdate), count(*) total FROM visitors WHERE ';
			if(strlen($edate)<=9){
				$g = '%Y-%m-%d-%H';
				$sql .= ' DATE(cdate) = DATE("'.$adate.'")';
			}else{
				$sql .= ' DATE(cdate) BETWEEN DATE("'.$adate.'") AND DATE("'.$edate.'") ';
			}
			$sql .=' GROUP BY STRFTIME(cdate,"'.$g.'")||sess';
			$sql .=' ORDER BY cdate ASC';

			$d = $this->get($sql);
			//error_log('SQLite getSessionPer: '.$this->db->lastErrorMsg());
			return $d;
		}
		public function getVisitPer($adate,$edate=""){
			$g = '%Y-%m-%d';
			$sql = 'SELECT STRFTIME("%s",cdate), SUM(jml) total FROM visitors WHERE ';
			if(strlen($edate)<=9){
				$g = '%Y-%m-%d-%H';
				$sql .= ' DATE(cdate) = DATE("'.$adate.'")';
			}else{
				$sql .= ' DATE(cdate) BETWEEN DATE("'.$adate.'") AND DATE("'.$edate.'") ';
			}
			$sql .=' GROUP BY STRFTIME(cdate,"'.$g.'")';
			$sql .=' ORDER BY cdate ASC';

			$d = $this->get($sql);
			//error_log('SQLite getVisitPer: '.$this->db->lastErrorMsg());
			return $d;
		}
	}
