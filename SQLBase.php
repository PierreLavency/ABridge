
<?php

require_once ("Type.php");
require_once ("ErrorConstant.php");
require_once ("Base.php");

class SQLBase extends Base {

	protected $servername;
	protected $username;
	protected $password;
	protected $dbname;
	protected $mysqli =0;
	
	function  __construct($dbname) 
	{//bof
		$this->servername = "localhost";
		$this->username = "cl822";
		$this->password = "cl822";
		$this->dbname =$dbname;
		$this->begintrans();
		parent::__construct($dbname);
	}

	public function beginTrans()
	{
		if (!$this->mysqli) {
			$this->mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
			if ($this->mysqli->connect_error)       {throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
			if (! $this->mysqli->autocommit(false)) {throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		}
		if (! $this->mysqli->begin_transaction())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function commit()
	{
		if (! $this->mysqli->commit())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function rollback() 
	{
		if (! $this->mysqli->rollback())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function close() 
	{
		if (! $this->mysqli->close())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		$this->mysqli =0;
		return true;
	}
	
	public function newMod($Model,$Meta) 
	{
		if ($this->existsMod ($Model)) {return 0;}; 
		$s = "\n CREATE TABLE $Model ( \n id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		$attr_lst=[];
		$attr_typ =[];
		if (isset($Meta['attr_lst'])) {$attr_lst = $Meta['attr_lst'];}
		if (isset($Meta['attr_typ'])) {$attr_typ = $Meta['attr_typ'];}
		$c = count($attr_lst);
		for ($i=0;$i<$c; $i++) {
			if ($attr_lst[$i] != 'id') {
				$attr = $attr_lst[$i];
				$typ=$attr_typ[$attr];
				if ($typ!= M_CREF) {//bof
					$typ = convertSqlType($typ);
					$s = $s.", \n $attr $typ NULL";					
				}
			}
		}
		$sql=$s. " ) \n";
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		$r = parent::newMod($Model,$Meta);
		parent::commit(); //tocheck !!
		return $r;
	}	

	public function putMod($Model,$Meta) 
	{// to review
		if (! $this->existsMod ($Model)) {return 0;};
		$r=$this->delMod($Model,$Meta);
		if (!$r) {return $r;}
		$r=$this->newMod($Model,$Meta);
		return $r;
	}
	
	public function delMod($Model) 
	{//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$sql = "\n DROP TABLE $Model \n";
		if (! $this->mysqli->query($sql)) {/*echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";*/}; // if does not exist ok !!
		$r = parent::delMod($Model);
		parent::commit();
		return $r;
	}
	
	public function getObj($Model, $id) 
	{//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$sql = "SELECT * FROM $Model where id= $id";
		$result = $this->mysqli->query($sql);
		if ($result->num_rows ==1) {
			// output data of each row
			$row = $result->fetch_assoc();
			$res=[];
			foreach($row as $attr=>$val){
				if (($attr != 'id') and (!is_null($val))) {$res[$attr]=$val;}
			}
			return $res;
		}
		else {return 0;}
	}
	
	public function putObj($Model, $id , $Values) 
	{//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$L1 = '';
		$i = 0;
		$c = count($Values);
		foreach ($Values as $key=>$val) {
			$i++;
			$L1 = $L1 . $key. '=' . "'".$val."'" ;
			if ($i<$c) {
				$L1 = $L1 . ',';
				$L2 = $L1 . ',';				
			}
		}
		$sql = "\n UPDATE $Model SET $L1 WHERE id= $id \n" ;
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		if ($this->mysqli->affected_rows == 1) {return $id; /* -> true*/}
		return 0;
	}
	
	public function delObj($Model, $id) 
	{//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$sql = "\n DELETE FROM $Model WHERE id=$id \n";
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		if ($this->mysqli->affected_rows == 1) {return true;}
		return 0;
	}
	
	public function newObj($Model, $Values)
	{//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$L1 = '(';
		$L2 = $L1;
		$i = 0;
		$c = count($Values);
		foreach ($Values as $key=>$val) {
			$i++;
			$L1 = $L1 . $key;
			$L2 = $L2 ."'". $val."'";
			if ($i<$c) {
				$L1 = $L1 . ',';
				$L2 = $L2.',';
			}
		}
		$L1 = $L1. ')';
		$L2 = $L2. ')';
		$sql = "\n INSERT INTO $Model \n $L1 \n VALUES \n $L2 \n";
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		return $this->mysqli->insert_id;
	}
	
	public function findObj($Model, $Attr, $Val) 
	{//ok
		if (! $this->existsMod ($Model)) {return 0;}; 
		$result1 = [];
		$sql = "SELECT id FROM $Model where $Attr= '$Val'";
		$result = $this->mysqli->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$result1[]= (int) $row["id"]; // not sure for int
			}; 
		}
		return $result1;
	}	
}

?>