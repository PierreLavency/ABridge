<?php




class SessionMgr
{
    protected $cleanUp = false;
    protected $newSess=false;
    protected $prevSess=false;
    protected $id=0;
    protected $pid=0;
    protected $typ;
    protected $cookieName;
    protected $prevCookieName;
    protected $timer= 7200; // 2 heure
    protected $prevtimer=86400; // 1 jour
    protected $sessHdl = null;
    protected $init=false;

    protected $sessionClass;
    protected $classKey;
    
    public function __construct($Sess)
    {
        if ($Sess == []) {
            return;
        }
        foreach ($Sess as $sessionClass => $classKey) {
            $this->cookieName= $sessionClass;
            $this->prevCookieName=$this->pvs($sessionClass);
            if ($this->cleanUp) {
                if (isset($_COOKIE[$this->cookieName])) {
                    unset($_COOKIE[$this->cookieName]);
                }
                if (isset($_COOKIE[$this->prevCookieName])) {
                    unset($_COOKIE[$this->prevCookieName]);
                }
            }
            $this->sessionClass=$sessionClass;
            $this->classKey=$classKey;
        }
        $this->initCookies();
    }
    
    private function pvs($name)
    {
        return 'p'.$name;
    }
    
    public function initCookies()
    {

        $this->init=true;

        $name  = $this->cookieName;
        $pname = $this->prevCookieName;
        
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        } else {
            $id= uniqid($name);
            setcookie($name, $id, time() + $this->timer, "/");
            $this->newSess = true;
        }
        $this->id=$id;
        if (isset($_COOKIE[$pname])) {
            $pid = $_COOKIE[$pname];
            if ($pid != $id and !$this->newSess) {
                $this->prevSess=true;
                setcookie($pname, $id, time() + $this->prevtimer, "/");
            }
        } else {
            setcookie($pname, $id, time() + $this->prevtimer, "/");
            $pid = $id;
        }
        $this->pid=$pid;
        return $id;
    }
    
    public function startSessions()
    {
        if (! $this->init) {
            return true;
        }
        if ($this->prevSess) {
            $pobj=$this->getObj($this->pid);
            if (!is_null($pobj)) {
                $pobj->delet();
                echo 'Delete previous Session :'.$this->prevCookieName."<br>";
            }
        }
        if ($this->newSess) {
            $this->newObj($this->id);
            echo 'New Session:'. $this->cookieName."<br>";
        }
        $obj = $this->getObj($this->id);
        if (is_null($obj)) {
            echo 'ERROR !!! ';
            echo 'new : '.$this->newSess;
            echo 'id : '.$this->id;
            echo 'pnew : '.$this->prevSess;
            echo 'pid : '.$this->pid;
            $obj=$this->newObj($this->id);
            $this->newSess=true;
        }
        $this->sessHdl = $obj;
    }

    public function isChanged()
    {
        return ($this->newSess or $this->prevSess);
    }
    
    public function getHandle()
    {
        if (! is_null($this->sessHdl)) {
            $res= new SessionHdl($this->sessHdl);
        } else {
            $res= new SessionHdl();
        }
        return $res;
    }
    
    protected function newObj($Bkey)
    {
        $obj = new Model($this->sessionClass);
        $obj->setVal($this->classKey, $Bkey);
        $obj->save();
        return $obj;
    }
    
    
    protected function getObj($Bkey)
    {
        $obj = new Model($this->sessionClass);
        $obj->setCriteria([$this->classKey], ['='], [$Bkey]);
        $res = $obj->select();
        if ($res==[]) {
            return null;
        }
        $id= array_pop($res);
        $obj= new Model($this->sessionClass, $id);
        return $obj;
    }
}
