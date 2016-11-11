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
	public $attr_predef;
	public $attr_lst;
	public $attr_typ;
	public $attr_val;
	public $attr_path ;
	public $attr_bkey;
	public $attr_mdtr;
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
	
	function __construct1($name) 
	{
		$this->init($name,0);
		$this->setValNoCheck ("ctstp",date(M_FORMAT_T));
		$this->setValNoCheck ("vnum",1);
		$x = $this->stateHdlr;
		if ($x) {
			$x->restoreMod($this);
		}
	}

	function __construct2($name,$id) 
	{
		$this->init($name,$id);
		if ( ! $id) {throw new Exception(E_ERC012.':'.$name.':'.$id);}
		$idr=0;
		$x = $this->stateHdlr;
		if ($x) {
			if (! $x->restoreMod($this)) {throw new exception(E_ERC022.':'.$name);}
			$idr =$x->restoreObj($this);
			if ($id != $idr){throw new exception(E_ERC007.':'.$name.':'.$id);};
		}
	}
	
	public function init($name,$id) 
	{
		if (! checkType($name,M_ALPHA)) {throw new Exception(E_ERC010.':'.$name.':'.M_ALPHA);}
		if (! checkType($id,M_INTP))    {throw new Exception(E_ERC011.':'.$id.':'.M_INTP);}
		$this->initattr();
		$this->id=$id; 
		$this->name=$name;
		$logname = $name.'_ErrLog';
		$this->errLog= new Logger($logname);
		$this->stateHdlr=getStateHandler ($name);
	}

	public function initattr() 
	{
		$this->attr_predef = array('id','vnum','ctstp','utstp');
		$this->attr_lst = array('id','vnum','ctstp','utstp');
		$this->attr_typ = array("id"=>M_ID,"vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP);
		$this->attr_val = array('vnum'=>0);
		$this->attr_path = [];
		$this->attr_bkey = [];
		$this->attr_mdtr = [];
	}
	
	public function getErrLog() 
	{
		return $this->errLog;
	}
	
	public function getModName() 
	{
		return $this->name;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getAllAttr() 
	{
        return $this->attr_lst;}

	public function getAllTyp()
	{
		return $this->attr_typ;        	
   	}

	public function getAllVal() 
	{ // all BUT Id and Mod
		return $this->attr_val;        	
   	}
	
	public function getAllPath() 
	{ 
		return $this->attr_path;        	
   	}
	
	public function getAllBkey() 
	{ 
		return $this->attr_bkey;        	
   	}
	
	public function getAllMdtr()
	{ 
		return $this->attr_mdtr;        	
   	}
	
	public function getAllPredef() 
	{
		return $this->attr_predef;
	}

	public function getTyp($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		foreach($this->attr_typ as $x => $typ) {
			if ($x==$attr) {return $typ;}
		}           			
		return NULL;
    }
	
	public function getPath($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		foreach($this->attr_path as $x => $path) {
			if ($x==$attr) {return $path;}
		}           			
		return NULL;
    }

	public function getRefMod($attr) 
	{
		$path = $this->getPath($attr);
		if (! $path) {return false;}
		$patha=explode('/',$path);
		return ($patha[1]);
	}
	
	
	public function isErr() 
	{
		$c=$this->errLog->logSize();
		return ($c);
    }
	
	public function isBkey($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		return (in_array($attr,$this->attr_bkey));
    }
	
	public function isMdtr($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		return (in_array($attr,$this->attr_mdtr));
    }
	
	public function isPredef($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		return (in_array($attr,$this->attr_predef));
    }
	
	public function isOptl($attr) 
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		if ($this->isPredef($attr) ) {return false;}
		if ($this->isMdtr($attr) ) {return false;}
		$typ = $this->getTyp($attr);
		if ($typ == M_CREF) {return false;}
		return true;
    }
	
	public function existsAttr($attr)
	{
		if (in_array($attr, $this->attr_lst)) {return true;} ;
        return false;
    }
	
	public function setTyp($attr,$typ) 
	{
		if (! $this->existsAttr ($attr)) 	    {$this->errLog->logLine(E_ERC002.':'.$attr);return false;};
		if (!isMtype($typ)) 					{$this->errLog->logLine(E_ERC004.':'.$typ) ;return false;}
		$this->attr_typ[$attr]=$typ;
	    return true;
	}
	
	public function setPath($attr,$path) 
	{
		if (! $this->existsAttr ($attr)) 	    {$this->errLog->logLine(E_ERC002.':'.$attr);return false;};
		if (! checkPath($path) )      			{$this->errLog->logLine(E_ERC020.':'.$attr.':'.$path);return false;};
		$this->attr_path[$attr]=$path;
	    return true;
	}

	public function setBkey($attr,$val) 
	{
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return false;};
		if(!$this->stateHdlr)					{$this->errLog->logLine(E_ERC017.':'.$attr.':'.$typ);return false;}		
		if ($val) {
			if (!in_array($attr,$this->attr_bkey)) {$this->attr_bkey[]=$attr;}		
		}
		if ( ! $val) {
			if (in_array($attr,$this->attr_bkey)) {unset($this->attr_bkey[$attr]);}			
		}
	    return true;
	}
	
	public function setMdtr($attr,$val) 
	{
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return false;};
		if ($val) {
			if (!in_array($attr,$this->attr_mdtr)) {$this->attr_mdtr[]=$attr;}		
		}
		if ( ! $val) {
			if (in_array($attr,$this->attr_mdtr)) {unset($this->attr_mdtr[$attr]);}			
		}
	    return true;
	}
	
	public function addAttr ($attr,$typ=M_STRING,$path=0) 
	{
		$x= $this->existsAttr ($attr);
		if ($x) 								{$this->errLog->logLine(E_ERC003.':'.$attr);return false;}
		if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE){ 
			if (!$path) 						{$this->errLog->logLine(E_ERC008.':'.$attr.':'.$typ);return false;}
			if(!$this->stateHdlr)				{$this->errLog->logLine(E_ERC014.':'.$attr.':'.$typ);return false;}
		}
		$this->attr_lst[]=$attr;
		$r=$this->setTyp ($attr,$typ);
		$r2=true;
		if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE) {
			$r2=$this->setPath($attr,$path); 
		}
		if ($r and $r2){return true;}
		$this->delAttr($attr);
		return false;
	}

	public function delAttr($attr) 
	{
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return false;}
		if (in_array($attr,$this->attr_predef)) 
												{$this->errLog->logLine(E_ERC001.':'.$attr);return false;}
			
		$key = array_search($attr,$this->attr_lst);
		if($key!==false){unset($this->attr_lst[$key]);}								
		if (isset($this->attr_typ[$attr]))  {unset($this->attr_typ[$attr]);}
		if (isset($this->attr_path[$attr])) {unset($this->attr_path[$attr]);}
		$key = array_search($attr,$this->attr_bkey);
		if($key!==false){unset($this->attr_bkey[$key]);}	
		$key = array_search($attr,$this->attr_mdtr);
		if($key!==false){unset($this->attr_mdtr[$key]);}	
		return true;
	}

	public function getVal($attr) 
	{
		if ($attr == 'id') {return $this->getId();}
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return false;}
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

	private function setValNoCheck($attr,$val)
	{
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return false;}
		$this->attr_val[$attr]=$val;
		return true;
	}

	public function setVal($Attr,$Val,$check=true)
	{
		if (! $this->existsAttr ($Attr)) 		{$this->errLog->logLine(E_ERC002.':'.$Attr);return false;}
		if (in_array($Attr,$this->attr_predef) 
			and $check)							{$this->errLog->logLine(E_ERC001.':'.$Attr);return false;};
		// type checking
		$type=$this->getTyp($Attr);
		if ($type ==M_CREF ) 					{$this->errLog->logLine(E_ERC013.':'.$Attr);return false;}
		$btype=baseType($type);
		$res =checkType($Val,$btype);
		if (! $res)								{$this->errLog->logLine(E_ERC005.':'.$Attr.':'.$Val.':'.$btype) ;return false;}
		// ref checking
		if ($type == M_REF) {
			$res = $this-> checkRef($Attr,$Val);
			if (! $res) {return false;}
		}
		// code values
		if ($type == M_CODE) {
			$res = $this-> checkCode($Attr,$Val);
			if (! $res) 						{$this->errLog->logLine(E_ERC016.':'.$Attr.':'.$Val);return false;}
		}
		// BKey
		if ($this->isBkey($Attr)) {
			if (! $this->checkBkey($Attr,$Val)) {$this->errLog->logLine(E_ERC018.':'.$Attr.':'.$Val) ;return false;} 
		}
		return ($this->setValNoCheck ($Attr,$Val));
    }

	public function checkBkey($Attr,$Val)
	{
		$res=$this->stateHdlr->findObj($this->getModName(),$Attr,$Val);
		if ($res==[]) {return true;}
		return false;		
	}
	
	public function checkCode($Attr,$Val)
	{
		$Vals = $this->getValues($Attr);
		if (!$Vals) {return false;}
		$res = in_array($Val,$Vals);
		return $res;		
	}

	public function getValues($Attr) 
	{
		$r=$this->getTyp($Attr);
		if(!$r) {return false;}
		if ($r != M_CODE) {$this->errLog->logLine(E_ERC015.':'.$Attr.':'.$r); return false;}
		$r=$this->getPath($Attr);
		if ($r) {
			$res=pathVal($r);
			return $res;
		}
		return $r;
	}
	
	public function checkRef($Attr,$id)
	{
		if ($id == 0) {return true;}
		$path = $this->getPath($Attr) ;
		if (!$path)	{$this->errLog->logLine(E_ERC008.':'.$Attr);return false;};
		$path=$path.'/'.$id;
		try {$res = pathObj($path);} catch (Exception $e) {$this->errLog->logLine($e->getMessage());return false;}
		if (!$res) {throw new Exception(E_ERC023.':'.$Attr.':'.$path);}
		return true;
	}
	
	public function checkMdtr($Attr)
	{
		$typ= $this->getTyp($Attr);
		if (!$typ) {return false;}
		if (array_key_exists($Attr,$this->attr_val)) {
			$val = $this->getVal($Attr);
			if ($typ == M_CODE or $typ == M_REF or $typ == M_ID) {if (!$val) { return false;}}
			if ($typ == M_STRING) {if ($val == "") {return false;}}
			return true;
		}
		return false;
	}	
	
	public function save()
	{
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return false;}
		foreach ($this->getAllMdtr() as $Attr) {
			$res=$this->checkMdtr($Attr);
			if (!$res){$this->errLog->logLine(E_ERC019.':'.$Attr);return false;}
		}
		$n=$this->getVal('vnum');
		$n++;
		$this->setValNoCheck('vnum',$n);
		$this->setValNoCheck ('utstp',date(M_FORMAT_T));
		$res=$this->stateHdlr->saveObj($this);
		$this->id=$res;
		return $res;
	}

	public function delet() 
	{
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return false;}
		$res=$this->stateHdlr->eraseObj($this);
		if ($res) {$this->id=0;}
		return $res;
	}
	
	public function deleteMod()
	{
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return false;}
		$res=$this->stateHdlr->eraseMod($this);
		$this->initattr();
		return $res;
	}
	
	public function saveMod()
	{
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return false;}
		$res=$this->stateHdlr->saveMod($this);
		return $res;
	}
}

?>