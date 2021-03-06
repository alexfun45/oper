<?php
	class DbConnection{
		
		protected $_connections = array();
		public $atcive_connection;
		public $system_db_name;
		protected $qdb;		
		public $save_errors = true;
		
		public function __construct($id, $config){
			//parent::__construct($id, $config);
			//if($db_name == ''){
				$db_name = ( ($n = $this->config['db_name']) == '') ? 'system' : $n;
				//}
			$this->system_db_name = $db_name;
			//$this->open($db_name);
			}
		public function create_db($db_name, $rewrite = false){
			//$filename = RFPLAN_DB_CHARTS . $db_name;
			$filename = $db_name;
			if($rewrite) unlink($filename);
			$this->_connections[$db_name] = new SQLite3($filename);			
			}
			
		public function exists(){
			return $this->existed;		
		}			
		public function backup($backupname = false){
			if(!file_exists(RFPLAN_DB_BACKUP))
				mkdir(RFPLAN_DB_BACKUP);
			$backup_file_name = date("d_m_Y_H_i");
			mkdir(RFPLAN_DB_BACKUP.$backup_file_name);
			if(!$backupname)
				$backupfile = RFPLAN_DB_BACKUP.$backup_file_name;
			else
				$backupfile = RFPLAN_DB_BACKUP.$backupname;
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"users\"' > ".$backupfile."/system_users.sql");
			//exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"projects\", \"admin_in_project\", \"project_role_2_user\", \"users_in_project\"' > ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"projects\"' > ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"users_in_project\"' >> ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"admin_in_project\"' >> ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"project_role_2_user\"' >> ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"users_in_project\"' >> ".$backupfile."/projects.sql");	
			exec("sqlite3 ".RFPLAN_DB_PATH."system '.dump \"assignees\"' >> ".$backupfile."/projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."system .dump > ".$backupfile."/system_projects.sql");
			exec("sqlite3 ".RFPLAN_DB_PATH."tasks .dump > ".$backupfile."/tasks.sql");	
		}
		
		public function restore($backup_date, $tables, $no_reset = true){
			$backup_file_name = RFPLAN_DB_BACKUP.$backup_date;
			//echo "backup_file = ".$backup_file_name."/tasks.sql<br>";
			$system_db = RFPLAN_DB_PATH."system";
			$tasks_db = RFPLAN_DB_PATH."tasks";
			//echo "tasks_db = ".$tasks_db."<br>";
			$old_db_backup = 	date("d_m_Y_H_i");
			$this->backup();
			//echo $backup_file_name."/system_projects.sql";
			if(file_exists($backup_file_name."/system_projects.sql")){	
				if($tables['all'] === true){
					if(!$no_reset){
						unlink($system_db);
						unlink($tasks_db);					
					}
					exec("sqlite3 ".$system_db." < " . $backup_file_name."/system_projects.sql");	
					exec("sqlite3 ".$system_db." < " . $backup_file_name."/tasks.sql");			
				}
				else{
					 if($tables['users']===true){
					 	if(!$no_reset)
					 		$this->_query('delete from users');
						exec("sqlite3 ".$system_db." < " . $backup_file_name."/system_users.sql");	
					}				 
					 if($tables['tasks']===true){
					 	if(!$no_reset)
							unlink($tasks_db);	
						//echo $backup_file_name."/tasks.sql";
					 	exec("sqlite3 ".$tasks_db." < " . $backup_file_name."/tasks.sql");
					 }
					 if($tables['system_projects']===true){
					 	if(!$no_reset)
							unlink($system_db);
					 	exec("sqlite3 ".$system_db." < " . $backup_file_name."/system_projects.sql");
					 	}
					 if($tables['projects']===true){
					 	if(!$no_reset){
					 		$this->_query('delete from projects');
					 		$this->_query('delete from project_role_2_user');
					 	}
					 	exec("sqlite3 ".$system_db." < " . $backup_file_name."/projects.sql");
					 }
					 	
					}
				}
					
		}
		
		public function open($db_name, $return_db = false){
			if(!isset($this->_connections[$db_name])){
				if(file_exists($db_name)){
					$this->_connections[$db_name] = new SQLite3($db_name);
					$this->existed = true;
					}
				else{
					$this->create_db($db_name);
					$this->existed = false;
					return true;	
					}
				$this->_query("PRAGMA encoding = 'UTF-8'", $db_name);
				//$this->_connections[$db_name]->busyTimeout(1000);
				//$this->_connections[$db_name]->exec('PRAGMA journal_mode = wal;');
				if($return_db === true)
					return $this->_connections[$db_name];
				else
					return true;		
				}
			else{
				$this->existed = true;
				if($return_db === true)
					return $this->_connections[$db_name];
				else
					return true;		
				}
			}
		public function select_db_q($db_name){
			if($this->open($db_name))
				$this->qdb = $db_name;
			else
				$this->qdb = false;									
			}	
				
		public function _q($sql){
			return $this->_query($sql, $this->qdb);	
			}
			
		public function _exec($sql, $vars, $db_name = ''){
			//$db = ($db_name=='') ? $this->_connections[$this->system_db_name] : $this->_connections[$db_name];
			$db = ($db_name=='' || $db_name==false) ? $this->_connections[$this->system_db_name] : $this->_connections[$db_name];
			//if($db===false)
				//$ex=$this->db->prepare($sql);
			//else
				//$ex=$db->prepare($sql);
			$ex=$db->prepare($sql);
			for($i=0;$i<count($vars);$i++){
				//$val = quotemeta($vars[$i-1]);
				//$ex->bindParam($i, $val, SQLITE3_TEXT);
				//$j = $i + 1;
				$ex->bindParam($i + 1, SQLite3::escapeString($vars[$i]), SQLITE3_TEXT);			
				}			
			$result = $ex->execute();
			//return $db->lastErrorMsg();
			return $result;
			}
		public function exec($sql){
			$db = ($db_name=='') ? $this->_connections[$this->system_db_name] : $this->_connections[$db_name];
			$db->exec($sql);
			if($db->changes()>0)
				return true;
			else
				return false;			
			}
		protected function is_open($db_name){
			if(isset($this->_connections[$db_name]))
				return true;
			else
				return false;			
			}
		public function attachedDB($db_name1, $db_name2 = false, $db_name3 = false){
			$db = $this->open($db_name1, true);
			if( !empty($db) && $this->open($db_name2)){
				$db_name2 = RFPLAN_DB_PATH . $db_name2;
				$db->exec("ATTACH DATABASE '".$db_name2."' as db2");
				}
			if($db_name3){
				if($this->open($db_name3))
					$db->exec("ATTACH DATABASE '".$db_name3."' as db3");				
				}
			}		
		public function detachDB($db_name1, $db_name2, $db_name3){
			if( ($db=$this->open($db_name1, true)) && $this->open($db_name2))
				$db->exec("DETACH DATABASE '".$db_name2."'");
			if($db_name3){
				if($this->open($db_name3))
					$db->exec("DETACH DATABASE '".$db_name3."'");				
				}			
			}
		public function _query($sql, $db_name = ''){
			$this->open($this->system_db_name);
			$db = ($db_name=='' || $db_name==false) ? $this->_connections[$this->system_db_name] : $this->_connections[$db_name];
			$res = $db->query($sql);
			if ($res === false) {
				$this->err_msg = $db->lastErrorMsg();
				$this->_errorNo =  $db->lastErrorCode();
				$this->successfully = false;
				//if($this->save_errors)
					//Application::app()->sql_error_reporting($this->err_msg, $this->_errorNo);
				}
			else
				$this->successfully = true;
			$this->close();
			return $res;			
			}
			
			
		public function exist($res){
			if($this->getResultArray($res, true) === false)
				return false;
			else
				return true;			
			}
		
		public function num($res){
			return $res->numColumns();			
			}		
		
		public function getResultArray($res, $one_col = false, $fetch_mode = SQLITE3_ASSOC){
			if($res === false)
				return false;
			if($res->numColumns() == 0)
				return false;
			if($one_col){
				$row = $res->fetchArray($fetch_mode);
				$result_array = $row[0];	
				}
			else{
				$result_array = array();
				while($row = $res->fetchArray($fetch_mode))	
					$result_array[] = $row;
				return $result_array;
				}
			return $row;
			}		
		
		public function getResCol($res, $col){
			if($res === false)
				return false;
			//if($res->numColumns() == 0)
				//return false;
			$row = $res->fetchArray(SQLITE3_ASSOC);
			if(!empty($row[$col]))
				return $row[$col];
			else
				return false;			
			}
		
		public function getLastErrMsg(){
			return $this->err_msg;			
			}
			
		public function getLastErrCode(){
			return $this->_errorNo;			
			}
				
		public function lastId($db_name = ''){
			$db = ($db_name=='') ? $this->_connections[$this->system_db_name] : $this->_connections[$db_name];
			return $db->lastInsertRowID();			
			}
			
		protected function close(){
			if(isset($this->db)){
				$this->db->close();
				unset($this->db);
				}
			} 			
		}
?>