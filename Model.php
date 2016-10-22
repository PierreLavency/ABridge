<?php

/* Format date to be checked */

define ('TSTP_F', "d-m-Y H:i:s");
define ('DATE_F', "d-m-Y");

require_once("Logger.php");
require_once("TypeConstant.php");
require_once("ErrorConstant.php");
require_once("Type.php");
require_once("Handler.php");

class Model {
	// property
 
	public $id;
	public $name ;
	public $attrPredefList = array("id","vnum","ctstp","utstp");
	public $attr_lst = array("id","vnum","ctstp","utstp");
	public $attr_typ = array("id"=>M_ID,"vnum"=>M_INT,"ctstp"=>M_TMSTP,"utstp"=>M_TMSTP);
	public $attr_val = array("vnum"=>0);
	public $errLog;
	public $stateHdlr=0;
	
	// constructors
	
	function __construct($name='Model',$id=0) {

		if (! ctype_alnum($name)) {
			throw new Exception("invalid model name : $name");
		}
		if (ctype_digit($name)) {
			throw new Exception("invalid model name : $name");
		}
	
		$this->id=$id; 
		$this->name=$name;
		$this->setValNoCheck ("id",$id);
		if ($id==0) {
			$this->setValNoCheck ("ctstp",date(TSTP_F));
			$this->setValNoCheck ("vnum",1);
		};
		$this->setValNoCheck ("utstp",date(TSTP_F));
		$logname = $name.'_ErrLog';
		$this->errLog= new Logger($logname);
		$x=0;
		$idr=0;
		$x=getStateHandler ($name);
		if ($x) {
			$this->stateHdlr=$x; 
			$x->restoreMod($this);
			$idr =$x-> restoreObj($this);
		}
		if ($id != $idr){
			$this->errLog->logLine(E_ERC007.':'.$id);
		}
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


	public function getAllAttrTyp () {
		return $this->attr_typ;        	
   	}

	public function getAllVal () {
		return $this->attr_val;        	
   	}

	public function getPreDefAttr () {
		return $this->attrPredefList;
	}

	public function addAttr ($attr,$typ=M_STRING) {
		$x= $this->existsAttr ($attr);
		if ($x) {$this->errLog->logLine(E_ERC003.':'.$attr);return 0;}
		$this->attr_lst[]=$attr;
		$r=$this->setTyp ($attr,$typ);
		if ($r){return $attr;}
		return $r;
		}

	public function delAttr ($attr) {
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return 0;}
		if (in_array($attr,$this->attrPredefList)) 
												{$this->errLog->logLine(E_ERC001.':'.$attr);return 0;}
		unset($this->attr_lst[$attr]);
		unset($this->attr_typ[$attr]);
		return $attr;
	}

	public function getVal ($attr) {
		$x= $this->existsAttr ($attr);
		if (!$x) 								{$this->errLog->logLine( E_ERC002.':'.$attr);return 0;}
		foreach($this->attr_val as $x => $val) {
			if ($x==$attr) {return $val;}
		}    	
		return NULL;        	
   	}

	public function getTyp ($attr) {
		foreach($this->attr_typ as $x => $typ) {
			if ($x==$attr) {return $typ;}
		}        
		$this->errLog->logLine(E_ERC002.':'.$attr);     			
		return NULL;
    }

	public function existsAttr ($attr) {
		if (in_array($attr, $this->attr_lst)) {return $attr;} ;
        return 0;
    }

	private function setValNoCheck ($attr,$val) {
		if (! $this->existsAttr ($attr)) 		{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;}
		$this->attr_val[$attr]=$val;
		return $val;
	}
	
	public function setTyp ($attr,$typ) {
		if (! $x= $this->existsAttr ($attr)) 	{$this->errLog->logLine(E_ERC002.':'.$attr);return 0;};
		if (!isMtype($typ)) 					{$this->errLog->logLine(E_ERC004.':'.$typ) ;return 0;}
		$this->attr_typ[$attr]=$typ;
	    return $typ;
	}

	public function setVal ($Attr,$Val,$check=true) {
		if (in_array($Attr,$this->attrPredefList) 
			and $check){						$this->errLog->logLine(E_ERC001.':'.$Attr);return 0;};
		$type=$this->getTyp($Attr);
		if (! checkType($Val,$type))			{$this->errLog->logLine(E_ERC005.':'.$Val.':'.$type) ;return 0;}
		return ($this->setValNoCheck ($Attr,$Val));
    }

	public function save (){
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return 0;}
		$res=$this->stateHdlr->saveObj($this);
		return $res;
	}

	public function saveMod (){
		if (! $this->stateHdlr) 				{$this->errLog->logLine(E_ERC006);return 0;}
		$res=$this->stateHdlr->saveMod($this);
		return $res;
	}
}

?>