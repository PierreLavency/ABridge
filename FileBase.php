
<?php

require_once ("Base.php");

class FileBase extends Base {

	function  __construct($id) {
		parent::__construct('fileBase_'.$id);
	}

	public function newObj($Model, $Values) {
		if (! $this->existsMod ($Model)) {return 0;}; 
		$meta=$this->objects[$Model][0];
		$id = $meta["lastId"];
		$this->objects[$Model][$id] = $Values;
		$meta["lastId"]=$id+1;
 		$this->objects[$Model][0]=$meta;
		return $id;
	}

	public function getObj($Model, $id) {
		if (! $this->existsMod ($Model)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		return $this->objects[$Model][$id] ; 
	}

	public function putObj($Model, $id , $Values) {
		if (! $this->existsMod ($Model)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		$this->objects[$Model][$id] = $Values; 
		return $id; // check -> true
	}

	public function delObj($Model, $id) {
		if (! $this->existsMod ($Model)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		unset($this->objects[$Model][$id]); 
		return true;
	}


	public function findObj($Model, $Attr, $Val) {
		$result1 = [];
		if (! $this->existsMod ($Model)) {return 0;}; 
		foreach ($this->objects[$Model] as $id => $List) {
			if ($id) {
				foreach ($List as $A => $V) {
				if ($Attr == $A and $Val == $V) {$result1[]=$id;}}
			}; 
		};
		return $result1;
	}	

};



?>
