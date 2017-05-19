<?php

require_once 'Cookies.php';

class SessionMgr
{
    protected $sessHdl = null;
    protected $init=false;

    protected $cookies=[];
    protected $sessions=[];
    protected $changed = false;
    
    public function __construct($sessions)
    {
        if ($sessions == []) {
            return ;
        }
        $this->init = true;
        $this->cookies = [];
        $this->sessions = $sessions;
        foreach ($sessions as $sessionClass => $classKey) {
            $this->cookies[$sessionClass]= new CookieReq($sessionClass);
        }
    }
    
    public function startSessions()
    {
        if (! $this->init) {
            return null;
        }
        $obj=null;
        foreach ($this->sessions as $sessionClass => $classKey) {
            $obj= $this->startSession($sessionClass, $classKey, $this->cookies[$sessionClass]);
        }
        return $obj; // return last one !!
    }

    public function startSession($sessionClass, $classKey, $cookie)
    {
        if ($cookie->isPrev()) {
            $pobj=$this->getObj($sessionClass, $classKey, $cookie->getPrevId());
            if (!is_null($pobj)) {
                $pobj->delet();
                echo 'Delete previous Session :'.$sessionClass."<br>";
            }
        }
        if ($cookie->isNew()) {
            $this->newObj($sessionClass, $classKey, $cookie->getId());
            echo 'New Session:'. $sessionClass."<br>";
        }
        $obj = $this->getObj($sessionClass, $classKey, $cookie->getId());
        if (is_null($obj)) {
            echo 'ERROR !!! ';
            echo 'new : '.$cookie->isNew();
            echo 'id : '.$cookie->getId();
            echo 'pnew : '.$cookie->isPrev();
            echo 'pid : '.$cookie->getPrevId();
            $obj=$this->newObj($sessionClass, $classKey, $cookie->getId());
            $this->changed=true;
        }
        $this->sessHdl = $obj;
        return $obj;
    }

    public function isChanged()
    {
        $changed = $this->changed;
        foreach ($this->cookies as $sessionClass => $cookie) {
            $changed = $changed or $cookie->isNew() or $cookie->isPrev();
        }
        return $changed;
    }
    
    
    protected function newObj($sessionClass, $classKey, $Bkey)
    {
        $obj = new Model($sessionClass);
        $obj->setVal($classKey, $Bkey);
        $obj->save();
        return $obj;
    }
    
    protected function getObj($sessionClass, $classKey, $Bkey)
    {
        $obj = new Model($sessionClass);
        $obj->setCriteria([$classKey], ['='], [$Bkey]);
        $res = $obj->select();
        if ($res==[]) {
            return null;
        }
        $id= array_pop($res);
        $obj= new Model($sessionClass, $id);
        return $obj;
    }
}
