<?php
	class Manager{
		public function __execute(){
			global $db;
			$this->db = $db;
			$_POST = json_decode(file_get_contents('php://input'), true);
			$oper = (isset($_GET['oper'])) ? $_GET['oper']:$_POST['oper'];
			call_user_method($oper, $this);	
		}
		
		protected function get_agents(){
			//$this->db->_exec("BEGIN");
			$result = $this->db->_query("SELECT id, name, cid, service, subscription FROM agents");
			//$this->db->_exec("COMMIT");
			$results = $this->db->getResultArray($result);
			//$results = $db->getLastErrMsg();
			//while($row = $result->fetchArray(SQLITE3_NUM)){
				//$results[] = $row;			
			//}
			//$this->results = array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"");
			//$this->results['data'] = array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"");
			//$this->results['count'] = 5;
			$this->results = array(
				'data' => $results,
            //'data'  => [array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"")],
            'count' => 2,
        );	
		}
		
		protected function remove_agent(){
			$id = $_POST['id'];
			$this->db->_query("DELETE FROM agents WHERE id='$id'");		
		}		
		
		protected function edit_agent(){
			//global $db;
			//$this->db->_exec("BEGIN");
			$v = json_decode($_POST['data']);
			//$this->db->_exec("UPDATE agents SET name=?, cid=?, service=?, subscription=? WHERE id=?)", array($v->name,$v->cid,$v->service,$v->subscription,$v->id));
			//$this->db->exec("UPDATE agents SET name='{$v->name}', cid='{$v->cid}', service='{$v->service}', subscription='{$v->subscription}' WHERE id='{$v->id}'");
			$this->db->_query("UPDATE agents SET name='{$v->name}', cid='{$v->cid}', service='{$v->service}', subscription='{$v->subscription}' WHERE id='{$v->id}'");		
			//$this->db->_exec("END");			
			//$this->results = $db->getLastErrMsg();
		}
		
		protected function add_agent(){
			$data = $_POST["data"];
			//$this->results = $data;
			$this->db->_exec("BEGIN"); 
			$this->db->_query("INSERT INTO agents(name, cid, service, subscription) VALUES('{$data['acompanyname']}', '{$data['anum']}', '{$data['aservice']}', '{$data['subscription']}')");
			$this->db->_exec("COMMIT");				
			//$this->results = $db->getLastErrMsg();		
		}
		
		public function output(){
			return json_encode($this->results);		
		}	
	}
?>