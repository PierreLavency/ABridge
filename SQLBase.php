
<?php

require_once ("Type.php");
require_once ("ErrorConstant.php");
require_once ("Base.php");

class SQLBase extends Base {

	public $servername;
	public $username;
	public $password;
	public $dbname;
	public $mysqli =0;
	
	function  __construct($dbname) {//bof
		$this->servername = "localhost";
		$this->username = "cl822";
		$this->password = "cl822";
		$this->dbname =$dbname;
		// Create mysqliection
		$this->mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($this->mysqli->connect_error)      
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		if (! $this->mysqli->autocommit(false)) 
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		if (! $this->mysqli->begin_transaction())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		parent::__construct($dbname);
	}

	public function beginTrans(){
		if (! $this->mysqli->begin_transaction())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function commit(){
		if (! $this->mysqli->commit())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function rollback() {
		if (! $this->mysqli->rollback())
			{throw new Exception(E_ERC021. ':' . $this->mysqli->connect_error);}
		return true;
	}
	
	public function newMod($Model,$Meta) {
		if ($this->existsMod ($Model)) {return 0;}; 
		$s = "\n CREATE TABLE $Model ( \n id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		$attr_lst = $Meta['attr_lst'];
		$attr_typ = $Meta['attr_typ'];
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
		parent::commit();
		return $r;
	}	

	public function putMod($Model,$Meta) {// to review
		if (! $this->existsMod ($Model)) {return 0;};
		$r=$this->delMod($Model,$Meta);
		if (!$r) {return $r;}
		$r=$this->newMod($Model,$Meta);
		return $r;
	}
	
	public function delMod($Model) {//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$sql = "\n DROP TABLE $Model \n";
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		$r = parent::delMod($Model);
		parent::commit();
		return $r;
	}
	
	public function getObj($Model, $id) {//ok
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
	
	public function putObj($Model, $id , $Values) {//ok
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
	
	public function delObj($Model, $id) {//ok
		if (! $this->existsMod ($Model)) {return 0;};
		$sql = "\n DELETE FROM $Model WHERE id=$id \n";
		if (! $this->mysqli->query($sql)) {echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";return 0;};
		if ($this->mysqli->affected_rows == 1) {return true;}
		return 0;
	}
	
	public function newObj($Model, $Values) {//ok
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
	
	public function findObj($Model, $Attr, $Val) {//ok
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

/*
$db= new SQLBase('atest');

$meta= [];
$meta['attr_lst']=['vnum','ctstp','utstp','Name','SurName','BirthDay'];
$meta['attr_typ']=["vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP,'Name'=>M_STRING,'SurName'=>M_STRING,'BirthDay'=>M_DATE];
$values = ['Name'=>'Lavency','vnum'=>5,'BirthDay'=>'1959-05-26','SurName'=>'Pierr','ctstp'=>'2016-04-11 15:09:09'];
$valuesb = ['Surname' => 'Pierre'];

$Model='Student';

$r=$db->delMod($Model);
echo 'delMod returns :' .$r."<br>";;
$r=$db->newMod($Model,$meta);
echo 'newMod returns :' .$r."<br>";;



$id=$db->newObj($Model,$values);
echo 'NewObj :' .$id."<br>";

$r=$db->delObj($Model,2);
echo 'delObj returns :' .$r."<br>";;

$r=$db->putObj($Model, $id , $valuesb);
echo 'putObj returns :' .$r."<br>";;

$r=$db->getObj($Model,$id);
echo "getObj found ".count($r)."<br>";

$r=$db->findObj($Model,'Name','Lavency');
echo "FindObj found ".count($r)."<br>";

$r=$db->commit();


// $r=$db->rollback();


echo "rollback/commit ".$r."<br>";


*/
?>