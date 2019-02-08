<?php
	class Installer{
		
		function __construct(){
							
			}
		
		public static function install($rewrite_system_tables = false){
			global $config;
			global $db;
			$rewrite_system_tables = false;
			//echo "dbschema=".$config->dbschema;
			$sql_lines = file("../".$config['dbschema'], FILE_IGNORE_NEW_LINES);
			if($rewrite_system_tables){
				$db->create_db('system', true);
			}
			for($i=0;$i<count($sql_lines);$i++){
				$sql = "CREATE TABLE IF NOT EXISTS " . $sql_lines[$i];
				$db->_query($sql);				
				}
			//$this->viewsInstall();
			//$this->triggersInstall();
			//$this->IndexesInstall();
			}	
		
		 public static function IndexesInstall(){		 	
			$sql_lines = file($this->indexes, FILE_IGNORE_NEW_LINES);
			$db = Application::app()->getDB();
			for($i=0;$i<count($sql_lines);$i++){
				$db->_query($sql_lines[$i]);
				}
		 	}	
		
		 public static function viewsInstall(){
				$sql_lines = file($this->db_views, FILE_IGNORE_NEW_LINES);
				$db = Application::app()->getDB();
				for($i=0;$i<count($sql_lines);$i++){
					$sql = "CREATE VIEW IF NOT EXISTS " . $sql_lines[$i];
					$db->_query($sql);
					}			
				}
			public static function triggersInstall(){
				$sql_lines = file($this->db_triggers, FILE_IGNORE_NEW_LINES);
				$db = Application::app()->getDB();
				for($i=0;$i<count($sql_lines);$i++){
					$sql = "CREATE TRIGGER IF NOT EXISTS " . $sql_lines[$i];
					$db->_query($sql);				
					}
//create trigger if not exists roleInProject5 INSTEAD OF INSERT ON project_role_2_user FOR EACH ROW WHEN NOT EXISTS(SELECT * FROM project_role_2_user WHERE project_id=new.project_id) BEGIN INSERT INTO project_role_2_user(project_id, user_id, role_id) VALUES(new.project_id, new.user_id, new.role_id); END;
				//$sql = "CREATE TRIGGER IF NOT EXISTS productImport3 INSTEAD OF INSERT ON sizeView FOR EACH ROW WHEN NOT EXISTS(SELECT * FROM sizes WHERE id_color=new.id_color AND size=new.size) BEGIN INSERT INTO sizes(id_color,size,num) VALUES(new.id_color,new.size,new.num); END;";				
				//$db->_query($sql);
				}
			public static function UpdateDB(){
				
				}	
		
		}
?>