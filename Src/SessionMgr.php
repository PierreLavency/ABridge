<?php

class SessionMgr
{
    protected $sessHdl = null;
    protected $cleanUp = false;
    protected $timer= 0; // 0 when connected 2 heure
    protected $Keep = false;
    
    protected $cookies=[];
    protected $sessions=[];
    protected $changed = false;
    
    public function __construct($sessions)
    {
        if ($sessions == []) {
            return ;
        }
        $this->sessions = $sessions;
    }
    
    public function startSessions()
    {
        $obj=null;
        foreach ($this->sessions as $sessionClass => $classKey) {
            $obj= $this->startSession($sessionClass);
        }
        return $obj; // return last one !!
    }

    public function startSession($sessionClass)
    {
        $id=0;
        if ($this->cleanUp) {
            if (isset($_COOKIE[$sessionClass])) {
                unset($_COOKIE[$sessionClass]);
            }
        }
        if (isset($_COOKIE[$sessionClass])) {
            $id=$_COOKIE[$sessionClass];
        }
        $mod= new Model($sessionClass);
        $obj=$mod->getCobj();
        $session = null;
        $pobj=null;
        if ($id) {
            $res = $obj->findValidSession($id);
            $session = $res[0];
            $pobj = $res[1];
        }
        if (is_null($session)) {
            $id = $obj->getKey();
            $end = 0;
            if ($this->timer) {
                $end = time() + $this->timer;
            }
            setcookie($sessionClass, $id, $end, "/");
            $mod->save();
            if ($pobj) {
                $obj->initPrev($pobj);
                if (!$this->Keep) {
                    $pobj->delet();
                }
            }
            $session=$mod;
            $this->changed=true;
        }
        return $session;
    }

    public function isChanged()
    {
        return $this->changed;
    }
}
