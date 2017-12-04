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
		
		$res= $obj->addAttr('ExecPath', Mtype::M_STRING);

		$res = $obj->addAttr('Exec', Mtype::M_BOOL);
		$res = $obj->setProp('Exec', Model::P_TMP);
		
		$res = $obj->addAttr('Load', Mtype::M_BOOL);
		$res = $obj->setProp('Load', Model::P_TMP);
		
		$res = $obj->addAttr('Promote', Mtype::M_BOOL);
		$res = $obj->setProp('Promote', Model::P_TMP);

		$res = $obj->addAttr('Compare', Mtype::M_BOOL);
		$res = $obj->setProp('Compare', Model::P_TMP);

		$res = $obj->addAttr('Diff',Mtype::M_HTML);
		$res = $obj->setProp('Diff', Model::P_EVL);
		
		$res = $obj->addAttr('Result',Mtype::M_STRING);
		$res = $obj->setProp('Result', Model::P_EVL);
		
		$res = $obj->addAttr('Path', Mtype::M_STRING);
		$res = $obj->setProp('Path', Model::P_EVL);		
		$res = $obj->setProp('Path', Model::P_TMP);
		
		$res = $obj->addAttr('LoadedLines', Mtype::M_INT);
		$res = $obj->setProp('LoadedLines', Model::P_EVL);		
		$res = $obj->setProp('LoadedLines', Model::P_TMP);
		
		$res = $obj->addAttr('LoadedExpected', Mtype::M_INT);
		$res = $obj->setProp('LoadedExpected', Model::P_EVL);
		$res = $obj->setProp('LoadedExpected', Model::P_TMP);
		
		$res = $obj->addAttr('Lines', Mtype::M_CREF,"/".$bindings['LogLine']."/".'LogFile');
		$res = $obj->addAttr('ExpectedLines', Mtype::M_CREF,"/".$bindings['ExpectedLine']."/".'LogFile');
	}
	
	public function getVal($attr)
	{
		if ($attr == 'Path') {
			return Log::get()->getPath();
		}
		
		if ($attr == 'LoadedLines') {
			return count($this->mod->getVal('Lines'));
		}
		
		if ($attr == 'LoadedExpected') {
			return count($this->mod->getVal('ExpectedLines'));
		}
		
		return $this->mod->getValN($attr);
	}
	
	public function getValues($attr)
	{
		if ($attr == 'Load' or $attr=='Exec' or $attr=='Promote' or $attr=='Compare') {
			return ['true','false'];
		}
		return $this->mod->getValuesN($attr);
		
	}
	
	public function save()
	{
		$name = $this->mod->getVal('Name');
		$path = Log::get()->getPath();
		$nameActual=$name.'_testRun';
		$nameExpected=$name;
		$fileActual=$path.'Logstore/'.$nameActual.'.txt';
		$fileExpected=$path.'Logstore/'.$nameExpected.'.txt';

		$id = $this->mod->saveN();
		$lines='Lines';
		$logLine='LogLine';
		
		if ($this->mod->getVal('Exec')) {
			$file=$this->mod->getValN('ExecPath').$name.'.php';
			exec('php '.$file);
		}
		
		if ($this->mod->getVal('Promote')) {
			copy($fileActual,$fileExpected);
			$lines='ExpectedLines';
			$logLine='ExpectedLine';			
		}
		
		if ($this->mod->getVal('Load')) {
			$this->deleteLines($lines,$logLine);
			$logger = new Logger();
			$logger->load($path, $nameActual);
			$logsize = $logger->logSize();
			for ($i = 0; $i < $logsize; $i++) {
				$LineObj=new Model($logLine);
				$val=$logger->getLine($i);
				$LineObj->setVal('Content',$val);
				$LineObj->setVal('LogFile', $id);
				$LineObj->save();
			}
		}
		
		if ($this->mod->getVal('Compare')) {
			$logger = new Logger();
			$logger->load($path, $nameExpected);
			$loggerA= new Logger();
			$loggerA->load($path, $nameActual);
			$diff='Actual and expected results are the same';
			$result='Ok';
			$res = $logger->diff($loggerA);
			if ($res) {
				$result='Ko';
				$diff = "Differences in line: $res";
				if($res<0) {
					$diff = "Expected number of lines: $logger->logSize()  Actual:$logger->logSize()";
				}
			}
			$this->mod->setValN('Diff',$diff);
			$this->mod->setValN('Result',$result);
			$this->mod->saveN();
		}
		
		return $id;
	}
	
	public function delet()
	{
		$this->deleteLines('Lines','LogLine');
		return $this->mod->deletN();
	}
		
	protected function deleteLines($lines,$logLine)
	{
		$lineList = $this->mod->getVal($lines);
		foreach ($lineList as $LineId) {
			$LineObj = new Model($logLine,(int) $LineId);
			$LineObj->deletN();
		}
	}
	
}

