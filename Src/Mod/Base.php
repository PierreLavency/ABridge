<?php
namespace ABridge\ABridge\Mod;

use Exception;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\CstError;

abstract class Base
{
    
    protected $objects=[];
    protected $preObjects=[];
    private $fileN;
    private $fileName;
    protected $logger;
    protected $connected;
    protected $memBase;

    // memBase = FileBase with null file
    
    protected function __construct($path, $id)
    {
        $this->logger=Log::Get();
        $this->fileN = $path . $id;
        $this->memBase= true;
        if (! is_null($id)) {
            $this->fileName = $path . $id .'.txt';
            $this->memBase=false;
        }
        $this->connected=true;
        $this->load();
    }
    
    protected function removeBase()
    {
        if ($this->memBase) {
            $this->objects = [];
            $this->preObjects=[];
            return true;
        }
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
        $this->objects = [];
        return true;
    }

    protected static function existBase($path, $id)
    {
        $fileName = $path . $id.'.txt';
        return file_exists($fileName);
    }
      
    
    public function connect()
    {
        $this->connected = true;
        return true;
    }
    
    public function close()
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        $this->connected = false;
        return true;
    }
    
    protected function isConnected()
    {
        return $this->connected;
    }
    
    private function load()
    {
        if ($this->memBase) {
            $this->objects = [];
            $this->preObjects=[];
            return true;
        }
        if (file_exists($this->fileName)) {
            $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
            $this->objects = unserialize($file);
            return true;
        }
        $this->objects = [];
        return true;
    }
    
    public function beginTrans()
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        $this->_transOpen=true;
        return true;
    }

    public function commit()
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        if ($this->memBase) {
            $this->preObjects=$this->objects;
            return true;
        }
        $file = serialize($this->objects);
        $res=file_put_contents($this->fileName, $file, FILE_USE_INCLUDE_PATH);
        return $res;
    }

    public function rollback()
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        if ($this->memBase) {
            $this->objects=$this->preObjects;
            return true;
        }
        $this->load();
        return true;
    }
 
    public function existsMod($model)
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        return(array_key_exists($model, $this->objects));
    }

    public function newModelId($model, $meta, $idF)
    {
        if ($this->existsMod($model)) {
            return false;
        }
        $meta['lastId']=1;
        if (!$idF) {
            $meta['lastId']=0;
        }
        $this->objects[$model][0] = $meta;
        return true;
    }

    public function getAllMod()
    {
        if (! $this->isConnected()) {
            throw new Exception(CstError::E_ERC025);
        }
        return array_keys($this->objects);
    }
    
    
    public function getMod($model)
    {
        if (! $this->existsMod($model)) {
            return false;
        };
        $meta = $this->objects[$model][0] ;
        unset($meta['lastId']);
        return $meta;
    }
    
    protected function putModel($model, $meta)
    {
        $id = $this->objects[$model][0]['lastId'] ;
        $meta['lastId']=$id;
        $this->objects[$model][0] = $meta;
        return true;
    }
    
    public function delMod($model)
    {
        if (! $this->existsMod($model)) {
            return true;
        };
        unset($this->objects[$model]);
        return true;
    }
    

    abstract public function getBaseType();
    
    abstract protected function checkFKey($flag);
 
    abstract protected function remove();

    abstract protected static function existsBase($path, $name);

    abstract protected function newModId($model, $meta, $id, $newist, $foreignKeyList);
    
    abstract protected function putMod($model, $meta, $addList, $delList, $foreignKeyList);
    
    abstract protected function newObj($model, $values);
    
    abstract protected function newObjId($model, $values, $id);

    abstract protected function getObj($model, $id);

    abstract protected function putObj($model, $id, $vnum, $values);

    abstract protected function delObj($model, $id);
    
    abstract protected function findObj($model, $attr, $val);

    abstract protected function findObjWheOp($model, $attrList, $opList, $valList, $ordList);
}
