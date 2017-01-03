
<?php

require_once("Base.php");

class FileBase extends Base
{

    function  __construct($id,$usr,$psw) 
    {
        parent::__construct('fileBase\\'.$id, $usr, $psw);
    } 

    public function putMod($model,$meta,$addList,$delList) 
    {
        if (! $this->existsMod($model)) {
            return false;
        }
        $attrLst=[];
        if (isset($delList['attr_lst'])) {
            $attrLst = $delList['attr_lst'];
        }
        foreach ($this->_objects[$model] as $id => $list) {
            if ($id) {
                foreach ($attrLst as $attr) {
                    if (isset($list[$attr])) {
                        unset($list[$attr]);
                    }
                }
            }; 
        };
        $r = parent::putModel($model, $meta);
        return $r;
    }
    
    public function newObj($model, $values) 
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        if (! $this->existsMod($model)) {
            return false;
        }; 
        $meta=$this->_objects[$model][0];
        $id = $meta["lastId"];
        $this->_objects[$model][$id] = $values;
        $meta["lastId"]=$id+1;
        $this->_objects[$model][0]=$meta;
        $this->logLine(1, "newObj $model $id \n");
        return $id;
    }

    public function getObj($model, $id) 
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        if (! $this->existsMod($model)) {
            return false;
        }; 
        if ($id == 0) {
            return false;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return false;
        }; 
        $this->logLine(1, "getObj $model $id \n");
        return $this->_objects[$model][$id] ; 
    }

    public function putObj($model, $id , $values) 
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        if (! $this->existsMod($model)) {
            return false;
        }; 
        if ($id == 0) {
            return false;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return false;
        }; 
        $this->_objects[$model][$id] = $values;
        $this->logLine(1, "putObj $model $id \n");
        return $id; // check -> true
    }

    public function delObj($model, $id) 
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        if (! $this->existsMod($model)) {
            return false;
        }; 
        if ($id == 0) {
            return true;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return true;
        }; 
        unset($this->_objects[$model][$id]); 
        $this->logLine(1, "delObj $model $id \n");
        return true;
    }


    public function findObj($model, $attr, $val) 
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        if (! $this->existsMod($model)) {
            return false;
        }; 
        $result = [];
        foreach ($this->_objects[$model] as $id => $list) {
            if ($id) {
                foreach ($list as $a => $v) {
                    if ($attr == $a and $val == $v) {
                        $result[]=$id;
                    }
                }
                if ($attr == 'id' and $id==$val) {
                    $result[]=$id;          
                }
            }; 
        };
        $this->logLine(1, "findObj $model $attr $val  \n");
        return $result;
    }
    
    public function findObjWhere($model,$attrList, $valList) 
    {
        if (! $this->existsMod($model)) {
            return false;
        }; 
        $res= $this->evalWhere($model, $attrList, $valList);
        return $res;
    }

    public function evalWhere($model,$attrList,$valList) 
    {
        if ($attrList== []) {
            $result = [];
            foreach ($this->_objects[$model] as $id => $list) {
                if ($id) {
                    $result[]=$id;
                };
            }
            return $result;
        }   
        $attr= array_pop($attrList);
        $val = array_pop($valList);
        $res = $this->findObj($model, $attr, $val);
        $result = $this->evalWhere($model, $attrList, $valList);
        $result = array_intersect($result, $res);
        return $result;
    }
    

    
    
    
}

