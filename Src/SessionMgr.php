<?php



class SessionMgr
{
    protected $_newSession=[];
    protected $_oldSession=[];
    protected $_sessionId=[];
    protected $_pTyp=[];
    protected $_pId=[];
    protected $_time=3600; // 1 heure
    protected $_ptime=86400; // 1 jour 
    
    function __construct() 
    {
        $cookie = 'sidc';
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
        $name = $handle[1];
        $pname = $this->pvs($name);
        if (isset($_COOKIE[$name])) {
            $id=$_COOKIE[$name];
        } else {
            $id= uniqid($name);
            setcookie($name, $id, time() + $this->_time, "/");
            $this->_newSession[] = $name;
        }
        if (isset($_COOKIE[$pname])) {
            $pid = $_COOKIE[$pname];
            if ($pid != $id) {
                $this->_oldSession[]=$pname;
                $this->_pId[$pname]=$pid;
                $this->_pTyp[$pname]=$handle[0];
                setcookie($pname, $id, time() + $this->_ptime, "/");
            }
        } else {
            setcookie($pname, $id, time() + $this->_ptime, "/");
            $pid = $id;
        }       
        echo 'Session :'. $id.' previous: '.$pid.' ';       
        $this->_sessionId[$name]=$id;
        return $id;
    }
    
    public function startSessions() 
    {
        if (count($this->_newSession)) {
            if (! class_exists('SessionHdl')) {
                throw new exception(E_ERC051);
            }
        }
        if (class_exists('SessionHdl')) {
            $hdl = new SessionHdl();
            foreach ($this->_newSession as $name) {
                $hdl->initMod($name);
            }
            foreach ($this->_oldSession as $pname) {
                $pid = $this->_pId[$pname];
                $ptyp= $this->_pTyp[$pname];
                $x=Handler::get()->getBaseNm($ptyp, $pname, $pid);
                $x->remove();
            }           
            foreach ($this->_sessionId as $name => $id) {
                $age = $hdl->getCtstp($name);
                $d = new DateTime($age);
                $tst= time()-$d->getTimestamp();
                echo 'Age :'.$tst;
            }
        } else {
            if (count($this->_newSession)) {     
                throw new exception(E_ERC051);
            }
        }
    }
    
    
}
