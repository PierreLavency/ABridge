
<?php

abstract class Base{
	protected $filePath ='C:\Users\pierr\ABridge\Datastore\\';
	protected $objects=[];
	protected $fileName;
	
	function  __construct($id) {
		$this->fileName = $this->filePath . $id .'.txt';
		$this->load();
	}

	private function load() {
		if (file_exists($this->fileName)) {
			$file = file_get_contents($this->fileName, FILE_USE_INCLUDE_PATH);
			$this->objects = unserialize($file);
			return true;
		}
		$this->objects = [];
		return true;
	}

	function beginTrans() {
		return true;
	}

	function commit() {
		$file = serialize($this->objects);
        $r=file_put_contents($this->fileName,$file,FILE_USE_INCLUDE_PATH);
		return $r;
	}

	function rollback() {
		$this->load();
		return true;
	}
	
	function close() {
		return true;
	}
	
	function inject($id) {
		$file = file_get_contents($this->filePath.$id.'.txt', FILE_USE_INCLUDE_PATH);
		$objects = unserialize($file);
		foreach ($objects as $mod=>$val) {$this->objects[$mod]=$val;}
	}

	function existsMod ($Model) {
		return(array_key_exists($Model,$this->objects));
	}
	
	function newMod($Model,$Meta) {
		if ($this->existsMod ($Model)) {return 0;}; 
		$Meta['lastId']=1;
		$this->objects[$Model][0] = $Meta;
		return true;
	}	

	function getMod($Model) {
		if (! $this->existsMod ($Model)) {return 0;};
		$Meta = $this->objects[$Model][0] ;
		unset($Meta['lastId']);
		return $Meta;
	}
	
	function putMod($Model,$Meta) {
		if (! $this->existsMod ($Model)) {return 0;};
		$id = $this->objects[$Model][0]['lastId'] ;
		$Meta['lastId']=$id;
		$this->objects[$Model][0] = $Meta;
		return true;
	}
	
	function delMod($Model) {
		if (! $this->existsMod ($Model)) {return true;};
        unset($this->objects[$Model]);
		return true;	
	}
	
	abstract protected function newObj($Model, $Values) ;

	abstract protected function getObj($Model, $id) ;

	abstract protected function putObj($Model, $id , $Values) ;

	abstract protected function delObj($Model, $id) ;
	
	abstract protected function findObj($Model, $Attr, $Val) ;

};



?>
