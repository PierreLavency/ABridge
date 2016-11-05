
<?php

require_once ("TypeSql.php");

class SQLBase{

	public $servername;
	public $username;
	public $password;
	public $dbname;
	public $conn ;
	
	
	function  __construct($dbname) {
		$this->servername = "localhost";
		$this->username = "cl822";
		$this->password = "cl822";
		$this->dbname =$dbname;
		// Create connection
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		} 
	}
	
	public function execsql ($sql,$mes) {
		echo "<br>".$sql . "<br>";
		if ($this->conn->query($sql) === TRUE) {echo $mes;} 
		else {echo "Error: " . $sql . "<br>" . $this->conn->error."<br>";}
	}
	

	public function newMod($Model,$Meta=[]) {
		$s = "\n CREATE TABLE $Model ( \n id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		$attr_lst = $Meta['attr_lst'];
		$attr_typ = $Meta['attr_typ'];
		$c = count($attr_lst);
		for ($i=0;$i<$c; $i++) {
			$attr = $attr_lst[$i];
			$typ = convertSqlType($attr_typ[$attr]);
			$s = $s.", \n $attr $typ NULL";
		}
		$sql=$s. " ) \n";
		$this->execsql ($sql,"New table created successfully");
		return true;
	}	
	
	public function delMod($Model) {
		$sql = "\n DROP TABLE $Model \n";
		$this->execsql ($sql,"table dropped successfully");
		return true;	
	}
	
	public function getObj($Model, $id) {
		$sql = "SELECT * FROM $Model where id= $id";
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
				// output data of each row
			while($row = $result->fetch_assoc()) {
				echo "id: " . $row["id"]. " - Name: " . $row["Name"]. " " 
				. $row["SurName"]. " " . $row["BirthDay"]. " " . $row["vnum"]. "<br>";
			}
		}
		else {
			echo "0 results";
		}
	}
	
	public function putObj($Model, $id , $Values) {
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
		$this->execsql ($sql,"New record  updated successfully");
	}
	
	public function delObj($Model, $id) {
		$sql = "\n DELETE FROM $Model WHERE id=$id \n";
		$this->execsql ($sql,"New record  deleted successfully");
	}
	
	public function newObj($Model, $Values) {
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
		$this->execsql ($sql,"New record created successfully");
	}
	
}

$db= new SQLBase('atest');

$meta= [];
$meta['attr_lst']=['vnum','ctstp','utstp','Name','SurName','BirthDay'];
$meta['attr_typ']=["vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP,'Name'=>M_STRING,'SurName'=>M_STRING,'BirthDay'=>M_DATE];
$values = ['Name'=>'Lavency','vnum'=>5,'BirthDay'=>'1959-05-26','SurName'=>'Pierre','ctstp'=>'2016-04-11 15:09:09'];
$id=1;
$Model='Student';

// $db->delMod($Model);
// $db->newMod($Model,$meta);

$db->newObj($Model,$values);
//$db->delObj($Model,2);
$db->putObj($Model, $id , $values);
$db->getObj($Model,2);
?>