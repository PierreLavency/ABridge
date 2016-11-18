
<?php

abstract class Base
{
    protected $filePath ='C:\Users\pierr\ABridge\Datastore\\';
    protected $objects=[];
    protected $fileName;
    
    function  __construct($id) 
    {
        $this->fileName = $this->filePath . $id .'.txt';
        $this->load();
    }

    private function load() 
    {
        if (file_exists($this->fileName)) {
            $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
            $this->objects = unserialize($file);
            return true;
        }
        $this->objects = [];
        return true;
    }

    function beginTrans() 
    {
        return true;
    }

    function commit()
    {
        $file = serialize($this->objects);
        $r=file_put_contents($this->fileName, $file, FILE_USE_INCLUDE_PATH);
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
            $this->filePath.$id.'.txt', 
            FILE_USE_INCLUDE_PATH
        );
        $objects = unserialize($file);
        foreach ($objects as $mod=>$val) {
            $this->objects[$mod]=$val;
        }
    }

    function existsMod ($model) 
    {
        return(array_key_exists($model, $this->objects));
    }
    
    function newMod($model,$meta) 
    {
        if ($this->existsMod($model)) {
            return 0;
        }; 
        $meta['lastId']=1;
        $this->objects[$model][0] = $meta;
        return true;
    }   

    function getMod($model) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $meta = $this->objects[$model][0] ;
        unset($meta['lastId']);
        return $meta;
    }
    
    function putMod($model,$meta) 
    {
        if (! $this->existsMod($model)) {
            return 0;
        };
        $id = $this->objects[$model][0]['lastId'] ;
        $meta['lastId']=$id;
        $this->objects[$model][0] = $meta;
        return true;
    }
    
    function delMod($model) 
    {
        if (! $this->existsMod($model)) {
            return true;
        };
        unset($this->objects[$model]);
        return true;    
    }
    
    abstract protected function newObj($model, $values) ;

    abstract protected function getObj($model, $id) ;

    abstract protected function putObj($model, $id , $values) ;

    abstract protected function delObj($model, $id) ;
    
    abstract protected function findObj($model, $attr, $val) ;

};
