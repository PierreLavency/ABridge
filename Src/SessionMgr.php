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
    protected $timer=3600; // 1 heure
    protected $prevtimer=86400; // 1 jour
    protected $sessHdl = null;
    protected $init=false;
    
    public function __construct()
    {
        $cookie = 'sidn';
        $pcookie=$this->pvs($cookie);
        if (isset($_COOKIE[$cookie])) {
            unset($_COOKIE[$cookie]);
        }
        if (isset($_COOKIE[$pcookie])) {
            unset($_COOKIE[$pcookie]);
        }
    }
    
    private function pvs($name)
    {
        return 'p'.$name;
    }
    
    public function initSession($handle)
    {
        if (! class_exists('SessionMeta')) {
            throw new exception(E_ERC051);
        }
        $this->init=true;
        $typ = $handle[0];
        $this->typ=$typ;
        $this->nameSess=$handle[1];
        $name = $handle[1];
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
            if ($pid != $id) {
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
            $x=Handler::get()->getBaseNm(
                $this->typ,
                $this->prevnameSess,
                $this->pid
            );
            $x->remove();
        }
        if ($this->newSess) {
            $hdl->initMod($this->id);
            echo "New Session <br>";
        }
        if (! $hdl->existObj()) {
            echo 'ERROR !!! ';
            echo 'new : '.$this->newSess;
            echo 'id : '.$this->id;
            echo 'pnew : '.$this->prevSess;
            echo 'pid : '.$this->pid;
            $hdl->initMod($this->id);
            $this->newSess=true;
        }
        $obj = $hdl->getObj($this->id);
        $this->sessHdl = $obj;
        $age = $obj->getVal('ctstp');
        $d = new DateTime($age);
        $tst= time()-$d->getTimestamp();
 //           echo 'Age :'.$tst;
    }
    
    public function isNew()
    {
        return $this->newSess;
    }
    
    public function getHome()
    {
        $home = '/';
        if (! is_null($this->sessHdl)) {
            $usrn= $this->sessHdl->getVal('User');
            if (! is_null($usrn)) {
                $home = '/User/'.$usrn;
            }
            $this->sessHdl->setVal('Comment', $home);
            $this->sessHdl->save();
        }
        return $home;
    }
    
    public function getHandle()
    {
        return $this->sessHdl;
    }
}
