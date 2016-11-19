
<?php

require_once("Base.php");

class FileBase extends Base
{

    function  __construct($id) 
    {
        parent::__construct('fileBase\\'.$id);
    }

    public function putMod($model,$meta,$addList,$delList) 
    {
        if (! $this->existsMod($model)) {
            return 0;
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
        $r = parent::putMod($model, $meta, $addList, $delList);
        return $r;
    }
    
    public function newObj($model, $values) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        $meta=$this->_objects[$model][0];
        $id = $meta["lastId"];
        $this->_objects[$model][$id] = $values;
        $meta["lastId"]=$id+1;
        $this->_objects[$model][0]=$meta;
        return $id;
    }

    public function getObj($model, $id) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        if ($id == 0) {
            return 0;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return 0;
        }; 
        return $this->_objects[$model][$id] ; 
    }

    public function putObj($model, $id , $values) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        if ($id == 0) {
            return 0;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return 0;
        }; 
        $this->_objects[$model][$id] = $values; 
        return $id; // check -> true
    }

    public function delObj($model, $id) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        if ($id == 0) {
            return 0;
        }; 
        if (! array_key_exists($id, $this->_objects[$model])) {
            return 0;
        }; 
        unset($this->_objects[$model][$id]); 
        return true;
    }


    public function findObj($model, $attr, $val) 
    {
        $result = [];
        if (! $this->existsMod($model)) {
            return 0;
        }; 
        foreach ($this->_objects[$model] as $id => $list) {
            if ($id) {
                foreach ($list as $a => $v) {
                    if ($attr == $a and $val == $v) {
                        $result[]=$id;
                    }
                }
            }; 
        };
        return $result;
    }   

};

