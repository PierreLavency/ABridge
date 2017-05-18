<?php

class CookieReq
{
    protected $cleanUp = false;
    protected $newSess=false;
    protected $prevSess=false;
    protected $id=0;
    protected $pid=0;
    protected $cookieName;
    protected $prevCookieName;
    protected $timer= 7200; // 2 heure
    protected $prevtimer=86400; // 1 jour
    
    public function __construct($name)
    {
        $this->cookieName= $name;
        $this->prevCookieName=$this->pvs($name);

        if ($this->cleanUp) {
            if (isset($_COOKIE[$this->cookieName])) {
                unset($_COOKIE[$this->cookieName]);
            }
            if (isset($_COOKIE[$this->prevCookieName])) {
                unset($_COOKIE[$this->prevCookieName]);
            }
        }

        if (isset($_COOKIE[$this->cookieName])) {
            $id=$_COOKIE[$this->cookieName];
        } else {
            $id= uniqid($this->cookieName);
            setcookie($this->cookieName, $id, time() + $this->timer, "/");
            $this->newSess = true;
        }
        $this->id=$id;
        if (isset($_COOKIE[$this->prevCookieName])) {
            $pid = $_COOKIE[$this->prevCookieName];
            if ($pid != $id and !$this->newSess) {
                $this->prevSess=true;
                setcookie($this->prevCookieName, $id, time() + $this->prevtimer, "/");
            }
        } else {
            setcookie($this->prevCookieName, $id, time() + $this->prevtimer, "/");
            $pid = $id;
        }
        $this->pid=$pid;
    }
    
    public function isNew()
    {
        return ($this->newSess) ;
    }
    
    public function getId()
    {
        return ($this->id) ;
    }

    public function isPrev()
    {
        return ($this->prevSess);
    }

    public function getPrevId()
    {
        return ($this->pid) ;
    }
    
    public function isChanged()
    {
        return ($this->newSess or $this->prevSess);
    }
 
    private function pvs($name)
    {
        return 'p'.$name;
    }
}
