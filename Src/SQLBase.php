
<?php

require_once 'Type.php';
require_once 'CstError.php';
require_once 'Base.php';

class SQLBase extends Base
{

    protected static $_servername;
    protected static $_username;
    protected static $_password;
    protected $_dbname;
    protected $_mysqli;
    
    function  __construct($dbname)
    {
        $this->_dbname =$dbname;
        $this->connect();
        parent::__construct('sqlBase/'.$dbname);
    }

    public static function setDB($server,$usr,$psw)
    {
        self::$_servername=$server;
        self::$_username=$usr;
        self::$_password=$psw;
        return true;
    }
    
    public static function getDB()
    {
        $res=[];
        $res[]=self::$_servername;
        $res[]=self::$_username;
        $res[]=self::$_password;
        return $res;
    }
    
    
    public function connect()
    {
        try {       
            $this->_mysqli = new mysqli(
                self::$_servername, 
                self::$_username, 
                self::$_password
            );
        }
        catch (Exception $e) {
            throw 
            new Exception(E_ERC021. ':' . $e->getMessage());
        }
        $this->_mysqli->autocommit(false);
        if (! $this->_mysqli->select_db($this->_dbname)) {
            $sql = "CREATE DATABASE $this->_dbname";
            if (! $this->_mysqli->query($sql)) {
                throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
            };
            $this->_mysqli->select_db($this->_dbname);
        }
        return (parent::connect());
    }

    public function remove() 
    {
        $sql = "DROP DATABASE $this->_dbname";
        $this->_mysqli->query($sql);
        parent::erase();
        return $this->close();
    }

    public static function exists($id)
    {
        return parent::_exists('sqlBase\\'.$id);
    }
    
    public function beginTrans()
    {
        try {
            $this->_mysqli->begin_transaction();
        }
        catch (Exception $e) {
            throw 
            new Exception(E_ERC021. ':' . $e->getMessage());
        }
        return (parent::beginTrans());
    }
    
    public function commit()
    {
        try {
            $this->_mysqli->commit();
        }
        catch (Exception $e) {
            throw 
            new Exception(E_ERC021. ':' . $e->getMessage());
        } 
        return (parent::commit());
    }
    
    public function rollback() 
    {
        try {
            $this->_mysqli->rollback();
        }
        catch (Exception $e) {
            throw 
            new Exception(E_ERC021. ':' . $e->getMessage());
        }
        return (parent::rollback());
    }
    
    public function close() 
    {
        try {
            $this->_mysqli->close();
        }
        catch (Exception $e) {
            throw 
            new Exception(E_ERC021. ':' . $e->getMessage());
        }
        return (parent::close());
    }
 
    public function newMod($model,$meta) 
    {
        return $this->newModId($model, $meta, true);
    }
 
    public function newModId($model,$meta,$idF) 
    {
        if ($this->existsMod($model)) {
            return false;
        }; 
        $s = "\n CREATE TABLE $model ( " ;
        if ($idF) {
            $s=$s. "\n id INT(11) UNSIGNED NOT NULL";
            $s=$s." AUTO_INCREMENT PRIMARY KEY ";
        } else {
            $s = $s. "\n id INT(11) UNSIGNED NOT NULL PRIMARY KEY ";
        }
        $attrLst=[];
        $attrTyp =[];
        if (isset($meta['attr_plst'])) {
            $attrLst = $meta['attr_plst'];
        }
        if (isset($meta['attr_typ'])) {
            $attrTyp = $meta['attr_typ'];
        }
        $c = count($attrLst);
        for ($i=0;$i<$c; $i++) {
            if ($attrLst[$i] != 'id') {
                $attr = $attrLst[$i];
                $typ=$attrTyp[$attr];
                $typ = convertSqlType($typ);
                $s = $s.", \n $attr $typ NULL";                 
            }
        }
        $sql=$s. " ) \n";
        $this->logLine(1, $sql);
        if (! $this->_mysqli->query($sql)) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
        };
        $r = parent::newModId($model, $meta, $idF);
        parent::commit(); //DML always autocommited!!
        return $r;
    }   

    public function putMod($model,$meta,$addList,$delList) 
    {
        if (! $this->existsMod($model)) {
            return false;
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
            $this->logLine(1, $sql);
            if (! $this->_mysqli->query($sql)) {
                throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
            }
        }
        $r = parent::putModel($model, $meta);
        parent::commit(); 
        return $r;
    }
    
    public function dropAttr($model,$delList)
    {
        $sql = "";
        $attrLst=[];
        if (isset($delList['attr_plst'])) {
            $attrLst = $delList['attr_plst'];
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
        if (isset($addList['attr_plst'])) {
            $attrLst = $addList['attr_plst'];
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
            if ($i > 0) {
                $sql = $sql . ",";
            }
            $typ = convertSqlType($typ);
            $sql = $sql."\n ADD $attr $typ NULL" ;
            $i++;
        }
        return $sql;
    }
    
    public function delMod($model) 
    {
        $sql = "\n DROP TABLE $model \n";
        $this->logLine(1, $sql);
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
            return false;
        };
        $sql = "SELECT * FROM $model where id= $id";
        $this->logLine(1, $sql);
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
            return false;
        }
    }
    
    public function putObj($model, $id , $values) 
    {
        if (! $this->existsMod($model)) {
            return false;
        };
        if ($id == 0) {
            return false;
        }
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
        $this->logLine(1, $sql);
        if (! $this->_mysqli->query($sql)) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
        };
        if ($this->_mysqli->affected_rows == 1) {
            return $id; /* -> true*/
        }
        return false;
    }
       
    public function delObj($model, $id) 
    {
        if (! $this->existsMod($model)) {
            return false;
        };
        $sql = "\n DELETE FROM $model WHERE id=$id \n";
        $this->logLine(1, $sql);
        if (! $this->_mysqli->query($sql)) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
        };

        return true;
    }
    
    
    public function newObj($model, $values)
    {
        return $this->newObjId($model, $values, 0);
    }   
    
    public function newObjId($model, $values, $id)
    {
        if (! $this->existsMod($model)) {
            return false;
        };
        $la = '(';
        $lv = $la;
        $c = count($values);        
        if ($id) {
            $la='(id';
            $lv="($id";
            if ($c) {
                $la=$la.',';
                $lv=$lv.',';
            }
        }
        $i = 0;
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
        $this->logLine(1, $sql);
        if (! $this->_mysqli->query($sql)) {
            throw new Exception(E_ERC021. ':' . $this->_mysqli->error);
        };
        if ($id) {
            return $id;
        }
        $id = $this->_mysqli->insert_id;
        if (!$id) {
            throw new Exception(E_ERC043.':'.$id);
        }
        return $id;
    }
    
    public function findObj($model, $attr, $val) 
    {
        if (! $this->existsMod($model)) {
            return false;
        }; 
        $res = [];
        $sql = "SELECT id FROM $model where $attr= '$val'";
        $this->logLine(1, $sql);
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }; 
        }
        return $res;
    }   

    private function buildWheOp($attrLst,$opLst,$valLst) 
    {
        if ($attrLst == []) {
            return ' true ';
        }
        $attr= array_pop($attrLst);
        $val = array_pop($valLst);
        $op = '=';
        if (isset($opLst[$attr])) {
            $op = $opLst[$attr];
        }
        $res = $this->buildWheOp($attrLst, $opLst, $valLst);
        if ($op == '::') {
            $res = " $attr LIKE '%$val%' and  " . $res;
        } else {
            $res = " $attr $op '$val' and  " . $res;
        }
        return $res;
    }
        
    public function findObjWheOp($model,$attrList,$opList,$valList)
    {
        if (! $this->existsMod($model)) {
            return false;
        }; 
        $res = [];
        $w= $this->buildWheOp($attrList, $opList, $valList);
        $sql = "SELECT id FROM $model where ". $w;
        $this->logLine(1, $sql);
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }; 
        }
        return $res;
    }
}
