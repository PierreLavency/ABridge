<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Mod\Base;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\CstError;

use Exception;
use Mysqli;

class SQLBase extends Base
{

    protected $server;
    protected $usr;
    protected $psw;
    protected $dbname;
    protected $mysqli;

    public function __construct($path, $server, $usr, $psw, $dbname)
    {
        $this->dbname =$dbname;
        $this->server= $server;
        $this->usr=$usr;
        $this->psw=$psw;
        $this->connect();
        parent::__construct($path, 'sqlBase/'.$dbname);
    }

    public function getBaseType()
    {
        return 'dataBase';
    }
    
    public function connect()
    {
        try {
            $this->mysqli = new mysqli(
                $this->server,
                $this->usr,
                $this->psw
            );
        } catch (Exception $e) {
            throw
            new Exception(CstError::E_ERC021. ':' . $e->getMessage());
        }
        $this->mysqli->autocommit(false);
        if (! $this->mysqli->select_db($this->dbname)) {
            $sql = "CREATE DATABASE $this->dbname";
            if (! $this->mysqli->query($sql)) {
                throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
            };
            $this->mysqli->select_db($this->dbname);
        }
        $this->mysqli->query('SET foreign_key_checks = 0');
        return (parent::connect());
    }

    
    public function checkFKey($flag)
    {
        if ($flag) {
            $this->mysqli->query('SET foreign_key_checks = 1');
        } else {
            $this->mysqli->query('SET foreign_key_checks = 0');
        }
        return true;
    }
    
    
    public function remove()
    {
        $sql = "DROP DATABASE $this->dbname";
        $this->mysqli->query($sql);
        parent::removeBase();
        return $this->close();
    }

    public static function exists($path, $id)
    {
        return parent::existsBase($path, 'sqlBase\\'.$id);
    }
    
    public function beginTrans()
    {
        try {
            $this->mysqli->begin_transaction();
        } catch (Exception $e) {
            throw
            new Exception(CstError::E_ERC021. ':' . $e->getMessage());
        }
        return (parent::beginTrans());
    }
    
    public function commit()
    {
        try {
            $this->mysqli->commit();
        } catch (Exception $e) {
            throw
            new Exception(CstError::E_ERC021. ':' . $e->getMessage());
        }
        return (parent::commit());
    }
    
    public function rollback()
    {
        try {
            $this->mysqli->rollback();
        } catch (Exception $e) {
            throw
            new Exception(CstError::E_ERC021. ':' . $e->getMessage());
        }
        return (parent::rollback());
    }
    
    public function close()
    {
        try {
            $this->mysqli->close();
        } catch (Exception $e) {
            throw
            new Exception(CstError::E_ERC021. ':' . $e->getMessage());
        }
        return (parent::close());
    }
 
    public function newMod($model, $meta, $newList)
    {
        return $this->newModId($model, $meta, true, $newList);
    }
 
    public function newModId($model, $meta, $idF, $newList)
    {
        if ($this->existsMod($model)) {
            return false;
        };
        $attrFrg=[];
        if (isset($newList['attr_frg'])) {
            $attrFrg = $newList['attr_frg'];
        }
        $s = "\n CREATE TABLE $model ( " ;
        if ($idF) {
            $s=$s. "\n id INT(11) UNSIGNED NOT NULL";
            $s=$s." AUTO_INCREMENT PRIMARY KEY ";
        } else {
            $s = $s. "\n id INT(11) UNSIGNED NOT NULL PRIMARY KEY ";
            if (isset($attrFrg['id'])) {
                $cName= $model.'_id';
                $ref = $attrFrg['id'];
                $s=$s.", \n CONSTRAINT $cName ";
                $s=$s. "FOREIGN KEY (id) REFERENCES $ref(id)";
            }
        }
        $attrLst=[];
        if (isset($newList['attr_typ'])) {
            $attrLst= $newList['attr_typ'];
        }

        foreach ($attrLst as $attr => $typ) {
            if ($attr != 'id') {
                $typ = Mtype::convertSqlType($typ);
                $s = $s.", \n $attr $typ NULL";
                if (isset($attrFrg[$attr])) {
                    $cName= $model.'_'.$attr;
                    $s=$s.", \n CONSTRAINT $cName ";
                    $s=$s." FOREIGN KEY ($attr) REFERENCES $attrFrg[$attr](id)";
                }
            }
        }
        $sql=$s. " ) \n";
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        if (! $this->mysqli->query($sql)) {
            throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
        };
        $r = parent::newModelId($model, $meta, $idF);
        parent::commit(); //DML always autocommited!!
        return $r;
    }

    public function putMod($model, $meta, $addList, $delList)
    {
        if (! $this->existsMod($model)) {
            return false;
        };
        $sql = "\n ALTER TABLE $model ";

        $sqlDrop = $this->dropAttr($model, $delList);
        if ($sqlDrop) {
            $sqlDrop=$sql.$sqlDrop;
            $this->logger->logLine($sqlDrop, ['class'=>__CLASS__,'line'=>__LINE__]);
            if (! $this->mysqli->query($sqlDrop)) {
                throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
            }
        }
        $sql = "\n ALTER TABLE $model ";
        $sqlAdd = $this->addAttr($model, $addList);
        if ($sqlAdd) {
            $sqlAdd=$sql.$sqlAdd;
            $this->logger->logLine($sqlAdd, ['class'=>__CLASS__,'line'=>__LINE__]);
            if (! $this->mysqli->query($sqlAdd)) {
                throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
            }
        }
        $r = parent::putModel($model, $meta);
        parent::commit();
        return $r;
    }
    
    protected function dropAttr($model, $delList)
    {
        $sql = "";
        $attrLst=[];
        if (isset($delList['attr_typ'])) {
            $attrLst = $delList['attr_typ'];
        }
        $attrFrg=[];
        if (isset($delList['attr_frg'])) {
            $attrFrg = $delList['attr_frg'];
        }
        $c = count($attrLst);
        if (!$c) {
            return false;
        }
        $i=0;
        foreach ($attrLst as $attr => $typ) {
            if (isset($attrFrg[$attr])) {
                $cName= $model.'_'.$attr;
                $sql=$sql."\n DROP FOREIGN KEY $cName ,";
            }
            $sql = $sql."\n DROP $attr " ;
            if ($i+1<$c) {
                $sql=$sql.",";
            }
            $i++;
        }
        return $sql;
    }
    
    protected function addAttr($model, $addList)
    {
        $sql = "";
        $attrLst=[];
        if (isset($addList['attr_typ'])) {
            $attrLst= $addList['attr_typ'];
        }
        $attrFrg=[];
        if (isset($addList['attr_frg'])) {
            $attrFrg = $addList['attr_frg'];
        }
        $c = count($attrLst);
        if (!$c) {
            return false;
        }
        $i=0;
        foreach ($attrLst as $attr => $typ) {
            if ($i > 0) {
                $sql = $sql . ",";
            }
            $typ = Mtype::convertSqlType($typ);
            $sql = $sql."\n ADD $attr $typ NULL" ;
            if (isset($attrFrg[$attr])) {
                $cName= $model.'_'.$attr;
                $sql=$sql.", \n ADD CONSTRAINT $cName ";
                $sql=$sql." FOREIGN KEY ($attr) REFERENCES $attrFrg[$attr](id)";
            }
            $i++;
        }
        return $sql;
    }
    
    public function delMod($model)
    {
        $sql = "\n DROP TABLE $model \n";
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        if (! $this->mysqli->query($sql)) {
 //           echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";
        };// if does not exist ok !!
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
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows ==1) {
            // output data of each row
            $row = $result->fetch_assoc();
            $res=[];
            foreach ($row as $attr => $val) {
                if (($attr != 'id') and (!is_null($val))) {
                    $res[$attr]=$val;
                }
            }
            return $res;
        } else {
            return false;
        }
    }
    
    public function putObj($model, $id, $vnum, $values)
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
        foreach ($values as $key => $val) {
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
        $sql = "\n UPDATE $model SET $lv WHERE id= $id and vnum= $vnum \n" ;
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        if (! $this->mysqli->query($sql)) {
            throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
        };
        if ($this->mysqli->affected_rows == 1) {
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
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        if (! $this->mysqli->query($sql)) {
            throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
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
        foreach ($values as $key => $val) {
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
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        if (! $this->mysqli->query($sql)) {
            throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
        };
        if ($id) {
            return $id;
        }
        $id = $this->mysqli->insert_id;
        if (!$id) {
            throw new Exception(CstError::E_ERC043.':'.$id);
        }
        return $id;
    }
    
    public function findObj($model, $attr, $val)
    {
        if (! $this->existsMod($model)) {
            return false;
        }
        $res = [];
        $sql = "SELECT id FROM $model where $attr= '$val'";
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }
        }
        return $res;
    }

    private function buildWheOp($attrLst, $opLst, $valLst)
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
        
    public function findObjWheOp($model, $attrList, $opList, $valList)
    {
        if (! $this->existsMod($model)) {
            return false;
        }
        $res = [];
        $w= $this->buildWheOp($attrList, $opList, $valList);
        $sql = "SELECT id FROM $model where ". $w;
        $this->logger->logLine($sql, ['class'=>__CLASS__,'line'=>__LINE__]);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }
        }
        return $res;
    }
}
