
<?php

require_once("Type.php");
require_once("ErrorConstant.php");
require_once("Base.php");

class SQLBase extends Base
{

    protected $_servername;
    protected $_username;
    protected $_password;
    protected $_dbname;
    protected $_mysqli =0;
    
    function  __construct($dbname)
    {
        $this->_servername = "localhost"; //bof
        $this->_username = "cl822";
        $this->_password = "cl822";
        $this->_dbname =$dbname;
        $this->begintrans();
        parent::__construct('sqlBase\\'.$dbname);
    }

    public function beginTrans()
    {
        if (!$this->_mysqli) {
            $this->_mysqli = new mysqli(
                $this->_servername, 
                $this->_username, 
                $this->_password, 
                $this->_dbname
            );
            if ($this->_mysqli->connect_error) {
                throw 
                new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
            }
            if (! $this->_mysqli->autocommit(false)) {
                throw 
                new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
            }
        }
        if (! $this->_mysqli->begin_transaction()) {
            throw 
            new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
        }
        return true;
    }
    
    public function commit()
    {
        if (! $this->_mysqli->commit()) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
        }
        return true;
    }
    
    public function rollback() 
    {
        if (! $this->_mysqli->rollback()) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
        }
        return true;
    }
    
    public function close() 
    {
        if (! $this->_mysqli->close()) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->connect_error);
        }
        $this->_mysqli =0;
        return true;
    }
    
    public function newMod($model,$meta) 
    {
        if ($this->existsMod($model)) {
            return 0;
        }; 
        $s = "\n CREATE TABLE $model ( " ;
        $s = $s. "\n id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
        $attrLst=[];
        $attrTyp =[];
        if (isset($meta['attr_lst'])) {
            $attrLst = $meta['attr_lst'];
        }
        if (isset($meta['attr_typ'])) {
            $attrTyp = $meta['attr_typ'];
        }
        $c = count($attrLst);
        for ($i=0;$i<$c; $i++) {
            if ($attrLst[$i] != 'id') {
                $attr = $attrLst[$i];
                $typ=$attrTyp[$attr];
                if ($typ!= M_CREF) {//bof
                    $typ = convertSqlType($typ);
                    $s = $s.", \n $attr $typ NULL";                 
                }
            }
        }
        $sql=$s. " ) \n";
        if (! $this->_mysqli->query($sql)) {
            echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";
            return 0;
        };
        $r = parent::newMod($model, $meta);
        parent::commit(); //tocheck !!
        return $r;
    }   

    public function putMod($model,$meta,$addList,$delList) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $sql = "\n ALTER TABLE $model ";
        $sqlAdd = $this->addAttr($model, $addList);
        if ($sqlAdd) {
            $sql=$sql.$sqlAdd;
        }
        $sqlDrop = $this->dropAttr($model, $delList);
        if ($sqlDrop) {
            if ($sqlAdd) {
                $sql = $sql . ',';
            }
            $sql=$sql.$sqlDrop;
        }
        if ($sqlAdd or $sqlDrop) {
            if (! $this->_mysqli->query($sql)) {
                echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";
                return 0;
            }
        }
        $r = parent::putMod($model, $meta, $addList, $delList);
        parent::commit(); //tocheck !!
        return $r;
    }
    
    public function dropAttr($model,$delList)
    {
        $sql = "";
        $attrLst=[];
        if (isset($delList['attr_lst'])) {
            $attrLst = $delList['attr_lst'];
        }
        $c = count($attrLst);
        if (!$c) {
            return false;
        }
        $i=0;
        foreach ($attrLst as $attr) {
            $sql = $sql."\n DROP $attr " ;
            if ($i+1<$c) {
                $sql=$sql.",";
            }
            $i++;
        }
        return $sql;
    }
    
    public function addAttr($model,$addList)
    {
        $attrLst=[];
        $attrTyp =[];
        $sql = "";
        if (isset($addList['attr_lst'])) {
            $attrLst = $addList['attr_lst'];
        }
        if (isset($addList['attr_typ'])) {
            $attrTyp = $addList['attr_typ'];
        }
        $c = count($attrLst);
        if (!$c) {
            return false;
        }
        $i=0;
        foreach ($attrLst as $attr) {
            $typ=$attrTyp[$attr];
            if ($typ!= M_CREF) {//bof
                if ($i > 0) {
                    $sql = $sql . ",";
                }
                $typ = convertSqlType($typ);
                $sql = $sql."\n ADD $attr $typ NULL" ;
                $i++;
            }
        }
        return $sql;
    }
    
    
    
    public function delMod($model) 
    {
        $sql = "\n DROP TABLE $model \n";
        if (! $this->_mysqli->query($sql)) {
            /*echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";*/
        }; // if does not exist ok !!
        $r = parent::delMod($model);
        parent::commit();
        return $r;
    }
    
    public function getObj($model, $id) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $sql = "SELECT * FROM $model where id= $id";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows ==1) {
            // output data of each row
            $row = $result->fetch_assoc();
            $res=[];
            foreach ($row as $attr=>$val) {
                if (($attr != 'id') and (!is_null($val))) {
                    $res[$attr]=$val;
                }
            }
            return $res;
        } else {
            return 0;
        }
    }
    
    public function putObj($model, $id , $values) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $lv = '';
        $i = 0;
        $c = count($values);
        foreach ($values as $key=>$val) {
            $i++;
            if (is_null($val)) {
                $v="NULL";
            } else {
                $v="'". $val."'";
            }
            $lv = $lv . $key. '=' . $v;
            if ($i<$c) {
                $lv = $lv . ',';           
            }
        }
        $sql = "\n UPDATE $model SET $lv WHERE id= $id \n" ;
        if (! $this->_mysqli->query($sql)) {
            echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";
            return 0;
        };
        if ($this->_mysqli->affected_rows == 1) {
            return $id; /* -> true*/
        }
        return 0;
    }
       
    public function delObj($model, $id) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $sql = "\n DELETE FROM $model WHERE id=$id \n";
        if (! $this->_mysqli->query($sql)) {
            echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";
            return 0;
        };
        if ($this->_mysqli->affected_rows == 1) {
            return true;
        }
        return 0;
    }
    
    public function newObj($model, $values)
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $la = '(';
        $lv = $la;
        $i = 0;
        $c = count($values);
        foreach ($values as $key=>$val) {
            $i++;
            $la = $la . $key;
            if (is_null($val)) {
                $v="NULL";
            } else {
                $v="'". $val."'";
            }
            $lv = $lv .$v;
            if ($i<$c) {
                $la = $la.',';
                $lv = $lv.',';
            }
        }
        $la = $la. ')';
        $lv = $lv. ')';
        $sql = "\n INSERT INTO $model \n $la \n VALUES \n $lv \n";
        if (! $this->_mysqli->query($sql)) {
            echo E_ERC021.":$sql" . ":".$this->_mysqli->error."<br>";
            return 0;
        };
        return $this->_mysqli->insert_id;
    }
    
    public function findObj($model, $attr, $val) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        $res = [];
        $sql = "SELECT id FROM $model where $attr= '$val'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }; 
        }
        return $res;
    }   
}
