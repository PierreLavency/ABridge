
<?php

require_once ("TypeSql.php");

class SQLBase{

	public $servername;
	public $username;
	public $password;
	public $dbname;
	public $conn ;
	
	function  __construct() {
		$this->servername = "localhost";
		$this->username = "cl822";
		$this->password = "cl822";
		$this->dbname = "atest";
		// Create connection
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		} 
	}
	
	

	public function newMod($Model,$Meta=[]) {
		$s = "\n CREATE TABLE $Model ( \n `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		$attr_lst = $Meta['attr_lst'];
		$attr_typ = $Meta['attr_typ'];
		$c = count($attr_lst);
		for ($i=0;$i<$c; $i++) {
			$attr = $attr_lst[$i];
			$typ = convertSqlType($attr_typ[$attr]);
			$s = $s.", \n '$attr' $typ ";
		}
		$s=$s. " ) \n";
		echo $s;
		return true;
	}	
	
	public function delMod($Model) {
		$s = "\n DROP TABLE $Model \n";
		echo $s ;
		return true;	
	}
	
	public function getObj($Model, $id) {
		$sql = "SELECT * FROM $Model where id= $id";
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
				// output data of each row
			while($row = $result->fetch_assoc()) {
				echo "id: " . $row["id"]. " - Name: " . $row["Name"]. " " . $row["SurName"]. "<br>";
			}
		}
		else {
			echo "0 results";
		}
		$this->conn->close();
	}
	
	public function putObj($Model, $id , $Values) {
		$L1 = '';
		$i = 0;
		$c = count($Values);
		foreach ($Values as $key=>$val) {
			$i++;
			$L1 = $L1 . $key. '=' . $val ;
			if ($i<$c) {
				$L1 = $L1 . ',';
				$L2 = $L1 . ',';				
			}
		}
		$sql = "\n UPDATE $Model SET $L1 WHERE id= $id \n" ;
		echo $sql;
/*		if ($this->conn->query($sql) === TRUE) {echo "New record  updated successfully";} 
		else {echo "Error: " . $sql . "<br>" . $this->conn->error;}
		$this->conn->close();
*/	}
	
	public function delObj($Model, $id) {
		$sql = "\n DELETE FROM $Model WHERE id=$id \n";
		echo $sql;
/*		if ($this->conn->query($sql) === TRUE) {echo "New record  deleted successfully";} 
		else {echo "Error: " . $sql . "<br>" . $this->conn->error;}
		$this->conn->close();
*/	}
	
	public function newObj($Model, $Values) {
		$L1 = '(';
		$L2 = $L1;
		$i = 0;
		$c = count($Values);
		foreach ($Values as $key=>$val) {
			$i++;
			$L1 = $L1 . $key;
			$L2 = $L2 . $val;
			if ($i<$c) {
				$L1 = $L1 . ',';
				$L2 = $L2 . ',';
			}
		}
		$L1 = $L1. ')';
		$L2 = $L2. ')';
		$sql = "\n INSERT INTO $Model \n $L1 \n VALUES \n $L2 \n";
		echo $sql;
/*		if ($this->conn->query($sql) === TRUE) {echo "New record created successfully";} 
		else {echo "Error: " . $sql . "<br>" . $this->conn->error;}
		$this->conn->close();
*/	}
	
}

$db= new SQLBase();
$db->getObj('person',2);
$meta= [];
$meta['attr_lst']=['vnum','ctstp','utstp','Name','SurName','BirthDay'];
$meta['attr_typ']=["vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP,'Name'=>M_STRING,'SurName'=>M_STRING,'BirthDay'=>M_DATE];
$values = ['Name'=>'Lavency','vnum'=>0,'BirthDay'=>'26-05-1959','SurName'=>'Pierre','ctstp'=>'04-11-2016 15:09:09'];
$id=1;
$Model='Student';
$db->newMod($Model,$meta);
$db->delMod($Model);

$db->newObj($Model,$values);
$db->delObj($Model,$id);
$db->putObj($Model, $id , $values);

?>