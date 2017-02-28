<?php



class SessionMgr
{
    protected $_new=false;
    protected $_id=0;
    protected $_typ;
    protected $_name;
    protected $_time=3600; // 1 heure
    protected $_ptime=86400; // 1 jour
    protected $_session = null;
    
    function __construct() 
    {
        $cookie = 'RELTESTn';
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
        $typ = $handle[0];
        $this->_typ=$typ;
        $this->_name=$handle[1];
        $name = $handle[1];
        $pname = $this->pvs($name);
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        } else {
            $id= uniqid($name);
            setcookie($name, $id, time() + $this->_time, "/");
            $this->_new = true;
        }
        $this->_id=$id;
        if (isset($_COOKIE[$pname])) {
            $pid = $_COOKIE[$pname];
            if ($pid != $id) {
                $x=Handler::get()->getBaseNm($typ, $pname, $pid);
                $x->remove();
                setcookie($pname, $id, time() + $this->_ptime, "/");
            }
        } else {
            setcookie($pname, $id, time() + $this->_ptime, "/");
            $pid = $id;
        }       
        echo 'Session :'. $id.' previous: '.$pid.' ';       
        return $id;
    }
    
    public function startSessions() 
    {
        if ($this->_new) {
            if (! class_exists('SessionHdl')) {
                throw new exception(E_ERC051);
            }
        }
        if (class_exists('SessionHdl')) {
            $hdl = new SessionHdl();
            if ($this->_new) {
                $hdl->initMod($this->_id);
            }
            $obj = $hdl->getObj($this->_id);
            $this->_session = $obj;
            $age = $obj->getVal('ctstp');
            $d = new DateTime($age);
            $tst= time()-$d->getTimestamp();
            echo 'Age :'.$tst;
        }
    }
    
    public function getHome() 
    {
        $home = '/'; 
        if (! is_null($this->_session)) {
            $usrn= $this->_session->getVal('User');
            if (! is_null($usrn)) {
                $home = '/User/'.$usrn;
            }
            $this->_session->setVal('Comment', $home);
            $this->_session->save();
        }
        return $home;
    }
}
