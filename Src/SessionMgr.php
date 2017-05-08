<?php




class SessionMgr
{
    protected $newSess=false;
    protected $prevSess=false;
    protected $id=0;
    protected $pid=0;
    protected $typ;
    protected $nameSess;
    protected $prevnameSess;
    protected $timer= 3600; // 1 heure
    protected $prevtimer=86400; // 1 jour
    protected $sessHdl = null;
    protected $init=false;
    
    public function __construct()
    {
        $cookie = 'sidpp';
        $pcookie=$this->pvs($cookie);
        if (isset($_COOKIE[$cookie])) {
            unset($_COOKIE[$cookie]);
        }
        if (isset($_COOKIE[$pcookie])) {
            unset($_COOKIE[$pcookie]);
        }
        if (class_exists('SessionMeta')) {
            $this->initSession();
        }
    }
    
    private function pvs($name)
    {
        return 'p'.$name;
    }
    
    public function initSession()
    {
        if (! class_exists('SessionMeta')) {
            throw new exception(E_ERC051);
        }
        $this->init=true;

        // hard code 1
        $this->nameSess='sid';
        $name = 'sid';
        $pname = $this->pvs($name);
        $this->prevnameSess=$pname;
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
        $hdl = new SessionMeta();
        if ($this->prevSess) {
            $pobj=$hdl->getObj($this->pid);
            $pobj->delet();
            echo "Delete previous Session <br>";
        }
        if ($this->newSess) {
            $hdl->newObj($this->id);
            echo "New Session <br>";
        }
        $obj = $hdl->getObj($this->id);
        if (is_null($obj)) {
            echo 'ERROR !!! ';
            echo 'new : '.$this->newSess;
            echo 'id : '.$this->id;
            echo 'pnew : '.$this->prevSess;
            echo 'pid : '.$this->pid;
            $obj=$hdl->newObj($this->id);
            $this->newSess=true;
        }
        $this->sessHdl = $obj;
        $age = $obj->getVal('ctstp');
        $d = new DateTime($age);
        $tst= time()-$d->getTimestamp();
 //           echo 'Age :'.$tst;
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
}
