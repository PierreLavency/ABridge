
<?php

require_once 'Logger.php';

abstract class Base
{
    protected static $filePath ="";
    protected $objects=[];
    protected $fileN;
    protected $fileName;
    protected $logLevl;
    protected $logger;
    protected $connected;
     
    protected function __construct($id)
    {
        $this->fileN = self::$filePath . $id;
        $this->fileName = $this->fileN.'.txt';
        $this->logLevl=0;
        $this->logger=null;
        $this->connected=true;
        $this->load();
    }

    protected function erase()
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
        $this->objects = [];
        return true;
    }
    
    public static function setPath($path)
    {
        self::$filePath =$path;
        return true;
    }
    
    
    public static function getPath()
    {
        return self::$filePath;
    }
    
    protected static function existsBase($id)
    {
        $f = self::$filePath . $id.'.txt';
        return file_exists($f);
    }
    
    public function connect()
    {
        $this->connected = true;
        return true;
    }
    
    public function close()
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        $this->connected = false;
        return true;
    }
    
    public function isConnected()
    {
        return $this->connected;
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
    
    public function beginTrans()
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        $this->_transOpen=true;
        return true;
    }

    public function commit()
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        $file = serialize($this->objects);
        $r=file_put_contents($this->fileName, $file, FILE_USE_INCLUDE_PATH);
        return $r;
    }

    public function rollback()
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        $this->load();
        return true;
    }
 
    public function existsMod($model)
    {
        if (! $this->isConnected()) {
            throw new Exception(E_ERC025);
        }
        return(array_key_exists($model, $this->objects));
    }
 
    public function newMod($model, $meta)
    {
        return $this->newModId($model, $meta, true);
    }
    
    public function newModId($model, $meta, $idF)
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
            throw new Exception(E_ERC025);
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
    
    public function setLogLevl($levl)
    {
        if (($levl > 0) and is_null($this->logger)) {
            $logname = $this->fileN.'_ErrLog';
            $this->logger = new Logger($logname);
        }
        $this->logLevl=$levl;
        return true;
    }
    
    protected function logLine($levl, $line)
    {
        if ($this->logLevl <= 0) {
            return true;
        }
        if ($levl <= $this->logLevl) {
            $this->logger->logLine($line);
        }
        return true;
    }
    
    public function getLog()
    {
        if (is_null($this->logger)) {
            return false;
        }
        return $this->logger;
    }
 
    abstract protected function checkFKey($flag);
 
    abstract protected function remove();

    abstract protected static function exists($name);

    abstract protected function putMod($model, $meta, $addList, $delList);
    
    abstract protected function newObj($model, $values);
    
    abstract protected function newObjId($model, $values, $id);

    abstract protected function getObj($model, $id);

    abstract protected function putObj($model, $id, $values);

    abstract protected function delObj($model, $id);
    
    abstract protected function findObj($model, $attr, $val);

    abstract protected function findObjWheOp($model, $attrList, $opList, $valList);
}
