<?php

class SessionMgr
{
    protected $sessHdl = null;
    protected $cleanUp = false;
    protected $timer= 0; // 0 when connected
    protected $Keep = false;
    
    protected $cookies=[];
    protected $sessions=[];
    protected $changed = false;
    protected $cookieName;
    protected $session;
    
    public function __construct($name, $className)
    {
        $id=0;
        if ($this->cleanUp) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }
        }
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        }
        $mod= new Model($className);
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
            if (php_sapi_name()==='cli') {
                $_COOKIE[$name]=$id;
            } else {
                setcookie($name, $id, $end, "/");
            }
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
        $this->session = $session;
    }

    public function isChanged()
    {
        return $this->changed;
    }
    
    public function getSession()
    {
        return $this->session;
    }
}
