<?php
	class Manager{
		public function __execute(){
			global $db;
			$this->db = $db;
			if($_POST['oper']!=="fileprepare")
				$_POST = json_decode(file_get_contents('php://input'), true);
			$oper = (isset($_GET['oper'])) ? $_GET['oper']:$_POST['oper'];
			call_user_method($oper, $this);	
		}
		
		protected function fileprepare(){
			$path_file = "./data/";
			 // проверяем загружен ли файл
         if(is_uploaded_file($_FILES['file']["tmp_name"])){
             // сохраняем файл
             move_uploaded_file($_FILES['file']["tmp_name"], $path_file . $_FILES['file']['name']);
             //$this->db->_query("INSERT INTO documents(name) VALUES('{$_FILES['file']['name']}')");
				 //$documentId = $this->db->lastId();
				 //$this->db->_query("UPDATE agents SET contract_id='$documentId'");
         }else{
             // Если файл не загрузился
             $error_array[] = "Ошибка загрузки!";
         }
         $_FILES['file']['rowid'] = $_POST['row'];
	   	$this->results = $_FILES['file'];	
		}
		
		protected function fileupload(){
			$data = $_POST['data'];
        	$this->db->_query("INSERT INTO documents(name) VALUES('{$data['name']}')");
			$documentId = $this->db->lastId();
			$this->db->_query("UPDATE agents SET contract_id='$documentId' WHERE id='{$data['rowid']}'");
			$sql = "UPDATE agents SET contract_id='$documentId' WHERE id='{$data['rowid']}'";
			$err = $this->db->getLastErrMsg();
			$this->results = $err;
		}
		
		protected function getQuery($fields = array()){
			$sql = "";
			$query = $_REQUEST['orderBy'];
			if(isset($query))
				$sql .= " ORDER BY ".$_REQUEST['orderBy'] . " " . (($_REQUEST['ascending']=='1') ? "ASC" : "DESC");		
			$query = $_REQUEST['query'];			
			if(!empty($query)){
				$sql .= " WHERE ";
				for($i=0;$i<count($fields);$i++){
					$sql .= " {$fields[$i]} Like '%{$query}%' ";
					if(($i+1)<count($fields)) $sql .= " OR ";
					}
					//$sql .= " id Like '%{$query}%' OR name Like '%{$query}%' OR cid LIKE '%{$query}%' OR service LIKE '%{$query}%' OR subscription LIKE '%{$query}%'";
				}			
			return $sql;
		}
		
		protected function get_agents(){
			//$this->db->_exec("BEGIN");
			//$query = "";
			//$where = "";
			//$query = $_REQUEST['query'];
			//if(!empty($query))
				//$where .= " WHERE id Like '%{$query}%' OR name Like '%{$query}%' OR cid LIKE '%{$query}%' OR service LIKE '%{$query}%' OR subscription LIKE '%{$query}%'";
			//if(isset($_REQUEST['orderBy']))
				//$search .= " ORDER BY ".$_REQUEST['orderBy'] . " " . (($_REQUEST['ascending']=='1') ? "ASC" : "DESC");
			$q = $this->getQuery(array('t1.id', 't1.name', 't1.cid', 't1.service', 't1.subscription'));			
			//$sql = "SELECT t1.*,t2.phone, t2.email, t2.address, (t2.name || ' ' || t2.surname)  as aname FROM agents as t1 INNER JOIN contacts as t2 ON t1.id=t2.agentId ".$where." ".$search;			
			//$result = $this->db->_query("SELECT id, name, cid, service, subscription FROM agents ".$q);
			$result = $this->db->_query("SELECT t1.id, t1.name, t1.cid, t1.service, t1.subscription, t3.name as contract, t2.phone, t2.mail as 'email', t2.address, (t2.name || ' ' || t2.surname)  as aname FROM agents as t1 LEFT JOIN contacts as t2 ON t1.id=t2.agentId LEFT JOIN documents as t3 ON t1.contract_id=t3.id ".$q);
			//$this->db->_exec("COMMIT");
			$results = $this->db->getResultArray($result);
			$err = $this->db->getLastErrMsg();
			//while($row = $result->fetchArray(SQLITE3_NUM)){
				//$results[] = $row;			
			//}
			//$this->results = array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"");
			//$this->results['data'] = array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"");
			//$this->results['count'] = 5;
			$this->results = array(
				'data' => $results,
				'errormsg'=>$err,
				'sql'=>$sql,
            //'data'  => [array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"")],
            'count' => count($results),
        );	
		}
		
		protected function remove_agent(){
			$id = $_POST['id'];
			$this->db->_query("DELETE FROM agents WHERE id='$id'");
			$this->db->_query("DELETE FROM contacts WHERE agentId='$id'");	
		}		
		
		protected function edit_agent(){
			$v = $_POST['data'];
			//$this->db->_query("UPDATE agents SET name='{$v['name']}', cid='{$v['cid']}', service='{$v['service']}', subscription='{$v['subscription']}' WHERE id='{$v['id']}'");		
			$this->db->_query("UPDATE contacts SET phone='{$v['phone']}', mail='{$v['email']}', address='{$v['address']}', name='{$v['aname']}', surname='{$v['surname']}' WHERE agentId={$v['id']}");			
			//$this->db->_query("UPDATE contacts SET phone='{$v['phone']}' WHERE agentId={$v['id']}");			
			$sql = "UPDATE contacts SET phone='{$v['phone']}', mail='{$v['email']}', address='{$v['address']}', name='{$v['aname']}', surname='{$v['surname']}' WHERE agentId='{$v['id']}'";			
			$err = $this->db->getLastErrMsg();
			$this->results = $sql."   ".$err;
		}
		
		protected function edit_org(){
			$v = json_decode($_POST['data']);
			$this->db->_query("UPDATE organizations SET name='{$v->name}', inn='{$v->inn}' WHERE id='{$v->id}'");		
		}
		
		protected function add_agent(){
			$data = $_POST["data"];
			//$this->results = $data;
			//$this->db->_exec("BEGIN"); 
			$this->db->_query("INSERT INTO agents(name, cid, service, subscription) VALUES('{$data['acompanyname']}', '{$data['anum']}', '{$data['aservice']}', '{$data['subscription']}')");
			$agentId = $this->db->lastId();			
			$this->db->_query("INSERT INTO contacts(agentId, phone, mail, address, name, surname) VALUES({$agentId}, '{$data['aphone']}', '{$data['email']}', '{$data['address']}', '{$data['aname']}', '{$data['surname']}' )");			
			$sql = "INSERT INTO contacts(agentId, phone, email, address, name, surname) VALUES({$agentId}, '{$data['aphone']}', '{$data['email']}', '{$data['address']}', '{$data['aname']}', '{$data['asurname']}' )";			
			//$this->db->_exec("COMMIT");				
			$this->results = $sql;
		}
		
		protected function add_organization(){
			$data = $_POST["data"];
			$this->db->_query("INSERT INTO organizations(name,inn) VALUES('{$data['oname']}', '{$data['inn']}')");
		}
		
		protected function get_organizations(){
			$q = $this->getQuery(array('id', 'name', 'inn'));
			$sql = "SELECT id, name, inn FROM organizations ".$q;
			$result = $this->db->_query("SELECT id, name, inn FROM organizations ".$q);
			$err = $this->db->getLastErrMsg();
			$results = $this->db->getResultArray($result);
			$this->results = array(
				'data' => $results,
				'errormsg'=>$err,
				'sql'=>$sql,
            //'data'  => [array("id"=>2,"name"=>"Home","cid"=>"123","service"=>"internet","subscription"=>"")],
            'count' => count($results),
        );	
		}
		
		public function output(){
			return json_encode($this->results);		
		}	
	}
?>