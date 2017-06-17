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
        $sessionHdl = $className::getSession($id);
        if ($sessionHdl->isNew()) {
            $id = $sessionHdl->getKey();
            $end = 0;
            if ($this->timer) {
                $end = time() + $this->timer;
            }
            if (php_sapi_name()==='cli') {
                $_COOKIE[$name]=$id;
            } else {
                setcookie($name, $id, $end, "/");
            }
        }
        $this->sessHdl = $sessionHdl;
    }
    
    public function getSession()
    {
        return $this->sessHdl;
    }
}
