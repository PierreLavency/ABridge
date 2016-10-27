<?php

/* Format date to be checked */


require_once("Logger.php");
require_once("TypeConstant.php");
require_once("ErrorConstant.php");
require_once("Type.php");
require_once("Handler.php");
require_once("Path.php");

class Model {
	// property
 
	public $id;
	public $name ;
	public $attr_predef = array('id','vnum','ctstp','utstp');
	public $attr_lst = array('id','vnum','ctstp','utstp');
	public $attr_typ = array("id"=>M_ID,"vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP);
	public $attr_val = array('vnum'=>0);
	public $attr_path = [];
	public $attr_bkey = [];
	public $attr_mdtr = [];
	public $errLog;
	public $stateHdlr=0;

	// constructors
	function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    } 
	
	function __construct1($name) {
		$this->init($name,0);
		$this->setValNoCheck ("ctstp",date(M_FORMAT_T));
		$this->setValNoCheck ("vnum",1);
		$x = $this->stateHdlr;
		if ($x) {
			$x->restoreMod($this);
		}
	}

	function __construct2($name,$id) {
		$this->init($name,$id);
		if ( ! $id) {throw new Exception(E_ERC012.':'.$name.':'.$id);}
		$idr=0;
		$x = $this->stateHdlr;
		if ($x) {
			$x->restoreMod($this);
			$idr =$x->restoreObj($this);
			if ($id !== $idr){throw new exception(E_ERC007.':'.$name.':'.$id);};
		}
	}

	public function init($name,$id) {
		if (! checkType($name,M_ALPHA)) {throw new Exception(E_ERC010.':'.$name.':'.M_ALPHA);}
		if (! checkType($id,M_INTP))    {throw new Exception(E_ERC011.':'.$id.':'.M_INTP);}
		$this->id=$id; 
		$this->name=$name;
		$logname = $name.'_ErrLog';
		$this->errLog= new Logger($logname);
		$this->stateHdlr=getStateHandler ($name);
	}
	
	public function getErrLog () {
		return $this->errLog;
	}
	
	public function getModName () {
		return $this->name;
	}
	
	public function getId () {
		return $this->id;
	}
	
	public function getAllAttr () {
        return $this->attr_lst;}

	public function getAllTyp () {
		return $this->attr_typ;        	
   	}

	public function getAllVal () { // all BUT id 
		return $this->attr_val;        	
   	}
	
	public function getAllPath () { 
		return $this->attr_path;        	
   	}
	
	public function getAllBkey () { 
		return $this->attr_bkey;        	
   	}
	
	public function getAllMdtr () { 
		return $this->attr_mdtr;        	
   	}
	
	public function getAllPredef () {
		return $this->attr_predef;
	}

	public function getTyp ($attr) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		foreach($this->attr_typ as $x => $typ) {
			if ($x==$attr) {return $typ;}
		}           			
		return NULL;
    }
	
	public function getPath ($attr) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		foreach($this->attr_path as $x => $path) {
			if ($x==$attr) {return $path;}
		}           			
		return NULL;
    }
	
	public function isBkey ($attr) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		return (in_array($attr,$this->attr_bkey));
    }
	
	public function isMdtr ($attr) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		return (in_array($attr,$this->attr_mdtr));
    }
	
	public function existsAttr ($attr) {
		if (in_array($attr, $this->attr_lst)) {return true;} ;
        return 0;
    }
	
	public function setTyp ($attr,$typ) {
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;};
		if (!isMtype($typ)) 					{$this->errLog->logLine(E_ERC004.':'.$typ) ;return 0;}
		$this->attr_typ[$attr]=$typ;
	    return true;
	}
	
	public function setPath ($attr,$path) {
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;};
		$this->attr_path[$attr]=$path;
	    return true;
	}

	public function setBkey ($attr,$val) {
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;};
		if(!$this->stateHdlr)					{$this->errLog->logLine(E_ERC017.':'.$attr.':'.$typ);return 0;}		
		if ($val) {
			if (!in_array($attr,$this->attr_bkey)) {$this->attr_bkey[]=$attr;}		
		}
		if ( ! $val) {
			if (in_array($attr,$this->attr_bkey)) {unset($this->attr_bkey[$attr]);}			
		}
	    return true;
	}
	
	public function setMdtr ($attr,$val) {
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;};
		if ($val) {
			if (!in_array($attr,$this->attr_mdtr)) {$this->attr_mdtr[]=$attr;}		
		}
		if ( ! $val) {
			if (in_array($attr,$this->attr_mdtr)) {unset($this->attr_mdtr[$attr]);}			
		}
	    return true;
	}
	
	public function addAttr ($attr,$typ=M_STRING,$path=0) {
		$x= $this->existsAttr ($attr);
		if ($x) 								{$this->errLog->logLine(E_ERC003.':'.$attr);return 0;}
		if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE){ 
			if (!$path) 						{$this->errLog->logLine(E_ERC008.':'.$attr);return 0;}
			if(!$this->stateHdlr)				{$this->errLog->logLine(E_ERC014.':'.$attr.':'.$typ);return 0;}
		}
		$this->attr_lst[]=$attr;
		$r=$this->setTyp ($attr,$typ);
		$r2=true;
		if ($typ == M_REF or $typ == M_CREF or M_CODE) {
			$r2=$this->setPath($attr,$path); // should check path ?
		}
		if ($r and $r2){return true;}
		return 0;
	}

	public function delAttr ($attr) {
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return 0;}
		if (in_array($attr,$this->attr_predef)) 
												{$this->errLog->logLine(E_ERC001.':'.$attr);return 0;}
		unset($this->attr_lst[$attr]);
		unset($this->attr_typ[$attr]);
		unset($this->attr_path[$attr]);
		return true;
	}

	public function getVal ($attr) {
		if ($attr == 'id') {return $this->getId();}
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return 0;}
		$type=$this->getTyp($attr);
		if ($type == M_CREF) {
			$path = $this->getPath($attr);
			$patha=explode('/',$path);
			$res=$this->stateHdlr->findObj($patha[1],$patha[2],$this->getId());
			return $res;
		}
		foreach($this->attr_val as $x => $val) {
			if ($x==$attr) {return $val;}
		}    	
		return NULL;        	
   	}

	private function setValNoCheck ($attr,$val) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		$this->attr_val[$attr]=$val;
		return true;
	}

	public function setVal ($Attr,$Val,$check=true) {
		if (in_array($Attr,$this->attr_predef) 
			and $check)							{$this->errLog->logLine(E_ERC001.':'.$Attr);return 0;};
		// type checking
		$type=$this->getTyp($Attr);
		if ($type ==M_CREF ) 					{$this->errLog->logLine(E_ERC013.':'.$Attr);return 0;}
		$btype=$type;
		if ($type==M_ID or $type == M_REF or $type == M_CREF or $type==M_CODE) {$btype = M_INTP;}
		$res =checkType($Val,$btype);
		if (! $res)								{$this->errLog->logLine(E_ERC005.':'.$Val.':'.$btype) ;return 0;}
		// ref checking
		if ($type == M_REF) {
			$res = $this-> checkRef($Attr,$Val);
			if (! $res) {return 0;}
		}
		// code values
		if ($type == M_CODE) {
			$res = $this-> checkCode($Attr,$Val);
			if (! $res) {$this->errLog->logLine(E_ERC016.':'.$Attr.':'.$Val);return 0;}
		}
		// BKey
		if ($this->isBkey($Attr)) {
			if (! $this->checkBkey($Attr,$Val)) {$this->errLog->logLine(E_ERC018.':'.$Attr.':'.$Val) ;return 0;} 
		}
		return ($this->setValNoCheck ($Attr,$Val));
    }

	public function checkBkey ($Attr,$Val) {
		$res=$this->stateHdlr->findObj($this->getModName(),$Attr,$Val);
		if ($res==[]) {return true;}
		return false;		
	}
	
	public function checkCode ($Attr,$Val) {
		$Vals = $this->getValues($Attr);
		if (!$Vals) {return 0;}
		$res = in_array($Val,$Vals);
		return $res;		
	}

	public function getValues($Attr) {
		$r=$this->getTyp($Attr);
		if(!$r) {return 0;}
		if ($r != M_CODE) {$this->errLog->logLine(E_ERC015.':'.$Attr.':'.$r); return 0;}
		$r=$this->getPath($Attr);
		if ($r) {
			$res=evalPathString($r);
			return $res;
		}
		return $r;
	}
	
	public function checkRef($Attr,$id) {
		$mod = $this->getPath($Attr) ;
		if ($id == 0) {return true;}
		if (!$mod)								 {$this->errLog->logLine(E_ERC008.':'.$Attr);return 0;}
		try {$res = new Model($mod,$id);} catch (Exception $e) {$this->errLog->logLine($e->getMessage());return 0;}
		return true;
	}
	
	public function save (){
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return 0;}
		$n=$this->getVal('vnum');
		$n++;
		$this->setValNoCheck('vnum',$n);
		$this->setValNoCheck ('utstp',date(M_FORMAT_T));
		$res=$this->stateHdlr->saveObj($this);
		$this->id=$res;
		return $res;
	}

	public function delet() {
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return 0;}
		$res=$this->stateHdlr->eraseObj($this);
		if ($res) {$this->id=0;}
		return $res;
	}
	
	public function saveMod (){
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return 0;}
		$res=$this->stateHdlr->saveMod($this);
		return $res;
	}
}

?>