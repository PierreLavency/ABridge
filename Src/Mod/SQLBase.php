<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Mod\Base;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\CstError;

use Exception;
use Mysqli;
use function False\tRUE;

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
            return true;
        }
        $this->mysqli->query('SET foreign_key_checks = 0');
        return true;
    }
    
    
    public function remove()
    {
        $sql = "DROP DATABASE $this->dbname";
        $this->mysqli->query($sql);
        parent::removeBase();
        return $this->close();
    }

    public function allTables()
    {
        $sql= "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$this->dbname'";
        $res=[];
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= $row['TABLE_NAME'];
            }
        }
        return $res;
    }
    
    public function allAttributes($table)
    {
        $sql= "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$this->dbname' and TABLE_NAME='$table'";
        $res=[];
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[$row['COLUMN_NAME']]= $row['DATA_TYPE'];
            }
        }
        return $res;
    }
    
    protected function foreignKeys($table)
    {
        $sql= "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE ";
        $sql=$sql."WHERE TABLE_SCHEMA='$this->dbname' and TABLE_NAME='$table'";
        $res=[];
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[$row['COLUMN_NAME']]= 'XX';
            }
        }
        return $res;
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
 
    public function newModId($model, $meta, $idF, $newList)
    {
        if ($this->existsMod($model)) {
            return false;
        };
        $attrFrg=[];
        if (isset($newList['attr_frg'])) {
            $attrFrg = $newList['attr_frg'];
        }
        $sql = "\n CREATE TABLE $model ( " ;
        if ($idF) {
            $sql=$sql. "\n id INT(11) UNSIGNED NOT NULL";
            $sql=$sql." AUTO_INCREMENT PRIMARY KEY ";
        } else {
            $sql = $sql. "\n id INT(11) UNSIGNED NOT NULL PRIMARY KEY ";
            if (isset($attrFrg['id'])) {
                $cName= $model.'_id';
                $ref = $attrFrg['id'];
                $sql=$sql.", \n CONSTRAINT $cName ";
                $sql=$sql. "FOREIGN KEY (id) REFERENCES $ref(id)";
            }
        }
        $attrLst=[];
        if (isset($newList['attr_typ'])) {
            $attrLst= $newList['attr_typ'];
        }
        foreach ($attrLst as $attr => $typ) {
            if ($attr != 'id') {
                $typ = Mtype::convertSqlType($typ);
                $sql = $sql.", \n $attr $typ NULL";
                if (isset($attrFrg[$attr])) {
                    $cName= $model.'_'.$attr;
                    $sql=$sql.", \n CONSTRAINT $cName ";
                    $sql=$sql." FOREIGN KEY ($attr) REFERENCES $attrFrg[$attr](id)";
                }
            }
        }
        $sql=$sql. " ) \n";
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        if (! $this->mysqli->query($sql)) {
            throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
        };
        $res = parent::newModelId($model, $meta, $idF);
        parent::commit(); //DML always autocommited!!
        return $res;
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
            $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
            $this->logger->logLine($sqlDrop, $linfo);
            if (! $this->mysqli->query($sqlDrop)) {
                throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
            }
        }
        $sql = "\n ALTER TABLE $model ";
        $sqlAdd = $this->addAttr($model, $addList);
        if ($sqlAdd) {
            $sqlAdd=$sql.$sqlAdd;
            $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
            $this->logger->logLine($sqlAdd, $linfo);
            if (! $this->mysqli->query($sqlAdd)) {
                throw new Exception(CstError::E_ERC021. ':' . $this->mysqli->error);
            }
        }
        $res = parent::putModel($model, $meta);
        parent::commit();
        return $res;
    }
    
    protected function dropAttr($model, $delList)
    {
        $sql = "";
        $attrLst=[];
        if (isset($delList['attr_typ'])) {
            $attrLst = $delList['attr_typ'];
        }
        $listSize = count($attrLst);
        if (!$listSize) {
            return false;
        }
        $attrFrg=$this->foreignKeys($model);
        $i=0;
        foreach ($attrLst as $attr => $typ) {
            if (isset($attrFrg[$attr])) {
                $cName= $model.'_'.$attr;
                $sql=$sql."\n DROP FOREIGN KEY $cName ,";
            }
            
            $sql = $sql."\n DROP IF EXISTS $attr  " ;
            if ($i+1<$listSize) {
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
        $listSize = count($attrLst);
        if (!$listSize) {
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
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        if (! $this->mysqli->query($sql)) {
 //           echo E_ERC021.":$sql" . ":".$this->mysqli->error."<br>";
        };// if does not exist ok !!
        $res = parent::delMod($model);
        parent::commit();
        return $res;
    }
    
    public function getObj($model, $id)
    {
        $sql = "SELECT * FROM $model where id= $id";
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        $result = $this->mysqli->query($sql);
        if (!$result) {
            throw new Exception(CstError::E_ERC022.':'.$model);
        }
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
        }
        return false;
    }
    
    public function putObj($model, $id, $vnum, $values)
    {
        if ($id == 0) {
            return false;
        }
        $listVal = '';
        $i = 0;
        $listSize = count($values);
        foreach ($values as $key => $val) {
            $i++;
            $sqlVal="'". $val."'";
            if (is_null($val)) {
                $sqlVal="NULL";
            }
            $listVal = $listVal . $key. '=' . $sqlVal;
            if ($i<$listSize) {
                $listVal = $listVal . ',';
            }
        }
        $sql = "\n UPDATE $model SET $listVal WHERE id= $id and vnum= $vnum \n" ;
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
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
        $sql = "\n DELETE FROM $model WHERE id=$id \n";
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
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

        $attrString = '(';
        $valueString = '(';
        ;
        $c = count($values);
        if ($id) {
            $attrString='(id';
            $valueString="($id";
            if ($c) {
                $attrString=$attrString.',';
                $valueString=$valueString.',';
            }
        }
        $i = 0;
        foreach ($values as $attr => $val) {
            $i++;
            $attrString = $attrString . $attr;
            $sqlVal="'". $val."'";
            if (is_null($val)) {
                $sqlVal="NULL";
            }
            $valueString = $valueString .$sqlVal;
            if ($i<$c) {
                $attrString = $attrString.',';
                $valueString = $valueString.',';
            }
        }
        $attrString = $attrString. ')';
        $valueString = $valueString. ')';
        $sql = "\n INSERT INTO $model \n $attrString \n VALUES \n $valueString \n";
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
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
            throw new Exception(CstError::E_ERC022.':'.$model);
        }
        $res = [];
        $sqlWhere= " where $attr= '$val' ";
        $sql = "SELECT id FROM $model". $sqlWhere;
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
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
        $opr = '=';
        if (isset($opLst[$attr])) {
            $opr = $opLst[$attr];
        }
        $res = $this->buildWheOp($attrLst, $opLst, $valLst);
        if ($opr == '::') {
            return " $attr LIKE '%$val%' and  " . $res;
        }
        return " $attr $opr '$val' and  " . $res;
    }
    
    private function buildOrd($model, $ordList)
    {
        $sqlOrder = '';
        if (is_array($model) and $ordList==[]) {
            $ordList=[['id',false]];
        }
        if ($ordList==[]) {
            return $sqlOrder;
        }
        $sqlOrder=' ORDER BY ';
        $first=true;
        foreach ($ordList as $attrSpec) {
            if (! $first) {
                $sqlOrder= $sqlOrder.' , ';
            }
            $sqlOrder=$sqlOrder.$attrSpec[0];
            if ($attrSpec[1]) {
                $sqlOrder=$sqlOrder.' DESC ';
            }
        }
        return $sqlOrder;
    }
    
    protected function buildUnion($mods, $sqlWhere, $ordList)
    {
        $sql='';
        $first=true;
        foreach ($mods as $mod) {
            if (! $first) {
                $sql=$sql.'UNION ( '.$this->buildSelect($mod, $sqlWhere, $ordList).' )';
            }
            if ($first) {
                $sql='('.$this->buildSelect($mod, $sqlWhere, $ordList, $ordList).')';
                $first=false;
            }
        }
        return $sql;
    }
    
    protected function buildSelect($model, $sqlWhere, $ordList)
    {
        if (is_array($model)) {
            return $this->buildUnion($model, $sqlWhere, $ordList);
        }
        if (! $this->existsMod($model)) {
            throw new Exception(CstError::E_ERC022.':'.$model);
        }
        $attrList='id';
        if ($ordList != [] and $ordList[0][0]!='id') {
            $attrList=$attrList.','.$ordList[0][0];
        }
        return "SELECT $attrList FROM $model where ". $sqlWhere;
    }
    
    protected function execFind($sql)
    {
        $res=[];
        $linfo=[Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__];
        $this->logger->logLine($sql, $linfo);
        $result = $this->mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res[]= (int) $row["id"]; // not sure for int
            }
        }
        return $res;
    }
    
    public function findObjWheOp($model, $attrList, $opList, $valList, $ordList)
    {
        $sqlWhere= $this->buildWheOp($attrList, $opList, $valList);
        $sqlSelect=$this->buildSelect($model, $sqlWhere, $ordList);
        $sqlOrder= $this->buildOrd($model, $ordList);
        $sql = $sqlSelect. $sqlOrder;
        return $this->execFind($sql);
    }
}
