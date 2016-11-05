
<?php


class FileBase{
	protected $filePath ='C:\Users\pierr\ABridge\Datastore\\';
	protected $objects=[];
	protected $fileName;
	
	function  __construct($id) {
		$this->fileName = $this->filePath . $id .'.txt';
		$this->load();
	}

	protected function load() {
		if (file_exists($this->fileName)) {
			$file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
			$this->objects = unserialize($file);
			return true;
		}
		$this->objects = [];
		return true;
	}

	public function commit() {
		$file = serialize($this->objects);
        $r=file_put_contents($this->fileName,$file,FILE_USE_INCLUDE_PATH);
		return $r;
	}

	public function rollback() {
		$this->load();
		return true;
	}
	
	public function inject($id) {
		$file = file_get_contents($this->filePath.$id.'.txt', FILE_USE_INCLUDE_PATH);
		$objects = unserialize($file);
		foreach ($objects as $mod=>$val) {$this->objects[$mod]=$val;}
	}

	public function existsMod ($Model) {
		return(array_key_exists($Model,$this->objects));
	}
	
	public function newMod($Model,$Meta=[]) {
		if ($this->existsMod ($Model)) {return 0;}; 
		$Meta['lastId']=1;
		$this->objects[$Model][0] = $Meta;
		return true;
	}	
	public function getMod($Model) {
		if (! $this->existsMod ($Model)) {return 0;};
		$Meta = $this->objects[$Model][0] ;
		unset($Meta['lastId']);
		return $Meta;
	}
	
	public function putMod($Model,$Meta) {
		if (! $this->existsMod ($Model)) {return 0;};
		$id = $this->objects[$Model][0]['lastId'] ;
		$Meta['lastId']=$id;
		$this->objects[$Model][0] = $Meta;
		return true;
	}
	
	public function delMod($Model) {
		if (! $this->existsMod ($Model)) {return 0;};
        unset($this->objects[$Model]);
		return true;	
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
		return $id;
	}

	public function delObj($Model, $id) {
		if (! $this->existsMod ($Model)) {return 0;}; 
		if ($id == 0) {return 0;}; 
		if (! array_key_exists($id,$this->objects[$Model])) {return 0;}; 
		unset($this->objects[$Model][$id]); 
		return true;
	}
	
	public function allAttrVal($Model, $Attr) {
		$result = [];
		if (! $this->existsMod ($Model)) {return 0;}; 
		$i=0;
		foreach ($this->objects[$Model] as $value) {
			foreach ($value as $a => $v) {
				if ($a == $Attr) {$result[]=$v;}
			}
		}
		return $result;
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
