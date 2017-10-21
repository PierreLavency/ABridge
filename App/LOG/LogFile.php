<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\Log\Log;

class LogFile extends CModel 
{

	public function initMod($bindings)
	{

		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);	

		$res = $obj->addAttr('Load', Mtype::M_BOOL);
		$res = $obj->setProp('Load', Model::P_TMP);

		$res = $obj->addAttr('Path', Mtype::M_STRING);
		$res = $obj->setProp('Path', Model::P_EVL);		
		$res = $obj->setProp('Path', Model::P_TMP);
		
		$res = $obj->addAttr('LoadedLines', Mtype::M_INT);
		$res = $obj->setProp('LoadedLines', Model::P_EVL);		
		$res = $obj->setProp('LoadedLines', Model::P_TMP);
		
		$res = $obj->addAttr('Lines', Mtype::M_CREF,"/".$bindings['LogLine']."/".'LogFile');
	}
	
	public function getVal($attr)
	{
		if ($attr == 'Path') {
			return Log::get()->getPath();
		}
		
		if ($attr == 'LoadedLines') {
			return count($this->mod->getVal('Lines'));
		}
		
		return $this->mod->getValN($attr);
	}
	
	public function save()
	{
		$name = $this->mod->getVal('Name');
		
		if ($this->mod->getVal('Load')) {
			$lineList = $this->mod->getVal('Lines'); 
			foreach ($lineList as $LineId) {
				$LineObj = new Model('LogLine',(int) $LineId);
				$LineObj->deletN();
			}
			$path = Log::get()->getPath();
			$logger = new Logger();
			$logger->load($path, $name);
			$logsize = $logger->logSize();
			for ($i = 0; $i < $logsize; $i++) {
				$LineObj=new Model('LogLine');
				$val=$logger->getLine($i);
				$LineObj->setVal('Content',$val);
				$LineObj->setVal('LogFile', $this->mod->getId());
				$LineObj->save();
			}
		}
		
		return $this->mod->saveN();
	}
	
	public function delet()
	{
		$lineList = $this->mod->getVal('Lines');
		foreach ($lineList as $LineId) {
			$LineObj = new Model('LogLine',(int) $LineId);
			$LineObj->deletN();
		}
		return $this->mod->deletN();
	}
	
	
}

