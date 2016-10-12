
<?php

class fileBase{
	public  $filePath ='C:\Users\pierr\ABridge\Datastore\\';
	public  $objects;
	
	function  __construct($id= "defaultFileName.txt" ) {
		$this->fileName = $this->filePath . $id;
	}
	public function load() {
        $file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
		$this->objects = unserialize($file);
	}

	public function save() {
		$file = serialize($this->objects);
        $r=file_put_contents($this->fileName,$file,FILE_USE_INCLUDE_PATH);
		return $r;
	}

	public function newMod($Model) {
		if (array_key_exists($Model,$this->objects)) {return 0;}; 
		$this->objects[$Model][0] = ["lastId"=>1];
		return $Model;
	}	

	public function newObj($Model, $Values) {
		if (! array_key_exists($Model,$this->objects)) {return 0;}; 
		$id = $this->objects[$Model][0]["lastId"];
		$this->objects[$Model][$id] = $Values;
 		$this->objects[$Model][0]=["lastId"=>($id+1)];
		return $id;
	}

	public function getObj($Model, $id) {
		if (! array_key_exists($Model,$this->objects)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		return $this->objects[$Model][$id] ; 
	}

	public function putObj($Model, $id , $Values) {
		if (! array_key_exists($Model,$this->objects)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		$this->objects[$Model][$id] = $Values; 
		return $id;
	}

	public function delObj($Model, $id) {
		if (! array_key_exists($Model,$this->objects)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		unset($this->objects[$Model][$id]); 
		return 1;
	}

	public function allAttrVal($Model, $Attr) {
		$result = [];
		if (! array_key_exists($Model,$this->objects)) {return 0;}; 
		$i=0;
		foreach ($this->objects[$Model] as $value) {
			foreach ($value as $a => $v) {
				if ($a == $Attr) {$result[]=$v;}
			}
		}
		return $result;
		
	}
	public function findObj($Model, $Attr, $Val) {
		$result = [];
		if (array_key_exists($Model,$this->objects)) {return 0;}; 
		foreach ($this->objects[$Model] as $id => $List) {
			foreach ($List as $A => $V) {
				if ($Attr == $A and $Val == $V) {$result[]=$id;}
			}; 
		};
		return $result;
	}	
};




?>
