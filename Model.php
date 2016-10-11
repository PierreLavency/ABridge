<?php

/* Format date to be checked */

define ('TSTP_F', "d-m-Y H:i:s");
define ('DATE_F', "d-m-Y");

class Model {
	// property
 
	public $id;
	public $name ;
	public $attrPredefList = array("id","vnum","ctstp","utstp");
	public $attr_lst = array("id","vnum","ctstp","utstp");
	public $attr_typ = array("id"=>"ref","vnum"=>"int","ctstp"=>"tstamp","utstp"=>"tstamp");
	public $attr_val = array("vnum"=>0);
	
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
	}

	
	public function getAttrList () {
        return $this->attr_lst;
    }

	public function setAttrList ($attrList) {
		$this->attr_lst = array_merge($this->attrPredefList  , $attrList) ;
        return  $this->attr_lst;
    }

	public function getVal ($attr) {
		foreach($this->attr_val as $x => $val) {
			if ($x==$attr) {return $val;}
		}        	
		return NULL;        	
   	}

	public function getTyp ($attr) {
		foreach($this->attr_typ as $x => $typ) {
			if ($x==$attr) {return $typ;}
		}                	
		return NULL;
    }

	public function existsAttr ($attr) {
		if (in_array($attr, $this->attr_lst)) {return $attr;} ;
        return 0;
    }

	private function setValNoCheck ($attr,$val) {
		$x= $this->existsAttr ($attr);
		if ($x) {
			$this->attr_val[$attr]=$val;
			$x=$val;
		}
        return $x;
	}
	public function setTyp ($attr,$typ) {
		$x= $this->existsAttr ($attr);
		if ($x) {
			$this->attr_typ[$attr]=$typ;
			$x=$typ;
		}
        return $x;
	}
	
	public function setVal ($attr,$val) {
		if (in_array($attr,$this->attrPredefList)) {return 0;};
		return ($this->setValNoCheck ($attr,$val));
    }

}

?>