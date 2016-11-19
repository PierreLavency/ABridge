
<?php

abstract class Base
{
    protected $_filePath ='C:\Users\pierr\ABridge\Datastore\\';
    protected $_objects=[];
    protected $_fileName;
    
    function  __construct($id) 
    {
        $this->_fileName = $this->_filePath . $id .'.txt';
        $this->load();
    }

    private function load() 
    {
        if (file_exists($this->_fileName)) {
            $file = file_get_contents($this->_fileName, FILE_USE_INCLUDE_PATH);
            $this->_objects = unserialize($file);
            return true;
        }
        $this->_objects = [];
        return true;
    }

    function beginTrans() 
    {
        return true;
    }

    function commit()
    {
        $file = serialize($this->_objects);
        $r=file_put_contents($this->_fileName, $file, FILE_USE_INCLUDE_PATH);
        return $r;
    }

    function rollback() 
    {
        $this->load();
        return true;
    }
    
    function close() 
    {
        return true;
    }
    
    function inject($id) 
    {
        $file = file_get_contents(
            $this->_filePath.$id.'.txt', 
            FILE_USE_INCLUDE_PATH
        );
        $objects = unserialize($file);
        foreach ($objects as $mod=>$val) {
            $this->_objects[$mod]=$val;
        }
    }

    function existsMod ($model) 
    {
        return(array_key_exists($model, $this->_objects));
    }
    
    function newMod($model,$meta) 
    {
        if ($this->existsMod($model)) {
            return 0;
        }; 
        $meta['lastId']=1;
        $this->_objects[$model][0] = $meta;
        return true;
    }   

    function getMod($model) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $meta = $this->_objects[$model][0] ;
        unset($meta['lastId']);
        return $meta;
    }
    
    function putMod($model,$meta,$addList,$delList) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $id = $this->_objects[$model][0]['lastId'] ;
        $meta['lastId']=$id;
        $this->_objects[$model][0] = $meta;
        return true;
    }
    
    function delMod($model) 
    {
        if (! $this->existsMod($model)) {
            return true;
        };
        unset($this->_objects[$model]);
        return true;    
    }
    
    abstract protected function newObj($model, $values) ;

    abstract protected function getObj($model, $id) ;

    abstract protected function putObj($model, $id , $values) ;

    abstract protected function delObj($model, $id) ;
    
    abstract protected function findObj($model, $attr, $val) ;

};
