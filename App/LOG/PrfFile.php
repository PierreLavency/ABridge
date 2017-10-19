<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Log\Logger;
use ABridge\ABridge\Log\Log;
use ABridge\ABridge\Hdl\CstMode;

class PrfFile extends CModel 
{
	
	static $attrList = ['Breath','Depth','Code','Number','Time','Avg'];
	static $attrListCode = ['Base','Operation','Access'];
	static $decList= [
			CstMode::V_S_READ=>1,
			CstMode::V_S_UPDT=>2,
			CstMode::V_S_CREA=>3,
			CstMode::V_S_DELT=>4,
			'dataBase'=>5,
			'fileBase'=>6,
			'memBase'=>7,
			'NA'=>8,
			'Root'=>9,
			'Usr'=>10,
	];

	public function initMod($bindings)
	{

		$obj = $this->mod;
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		$res = $obj->addAttr('Scenario',Mtype::M_STRING);	
		$res = $obj->setProp('Scenario', Model::P_EVL);	
		
		$res = $obj->addAttr('ExecTime',Mtype::M_TMSTP);
		$res = $obj->setProp('ExecTime', Model::P_EVL);		
		
		
		$res = $obj->addAttr('Load', Mtype::M_BOOL);
		$res = $obj->setProp('Load', Model::P_TMP);

		$res = $obj->addAttr('Path', Mtype::M_STRING);
		$res = $obj->setProp('Path', Model::P_EVL);		
		$res = $obj->setProp('Path', Model::P_TMP);
		
		$res = $obj->addAttr('LoadedLines', Mtype::M_INT);
		$res = $obj->setProp('LoadedLines', Model::P_EVL);		
		$res = $obj->setProp('LoadedLines', Model::P_TMP);
		
		$res = $obj->addAttr('Lines', Mtype::M_CREF,"/".$bindings['PrfLine']."/".'PrfFile');
	}
	
	public function getVal($attr)
	{
		if ($attr == 'Path') {
			return Log::get()->getPath().'/PerfRun/';
		}
		
		if ($attr == 'LoadedLines') {
			return count($this->mod->getVal('Lines'));
		}
		
		return $this->mod->getValN($attr);
	}
	
	public function save()
	{
		$name = $this->mod->getVal('Name');
		$time = (int) $name;
		$execTime= date(Mtype::M_FORMAT_T,$time);
		$this->mod->setValN('ExecTime',$execTime);
		
		if ($this->mod->getVal('Load')) {
			$lineList = $this->mod->getVal('Lines'); 
			foreach ($lineList as $LineId) {
				$LineObj = new Model('PrfLine',(int) $LineId);
				$LineObj->deletN();
			}
			$path = Log::get()->getPath();
			$logger = new Logger();
			$logger->load($path, '/PerfRun/'.$name);
			$logsize = $logger->logSize();
			$scenario= '';
			if ($logsize) {
				$attributes=$logger->getAttributes(0);
				$this->mod->setValN('Scenario', $attributes['Scenario']);
			}
			for ($i = 0; $i < $logsize; $i++) {
				$LineObj=new Model('PrfLine');
				$val=$logger->getLine($i);
				$LineObj->setVal('Content',$val);
				$LineObj->setVal('PrfFile', $this->mod->getId());
				$attributes=$logger->getAttributes($i);
				foreach(self::$attrListCode as $attr) {
					$LineObj->setVal($attr,self::$decList[$attributes[$attr]]);
				}
				foreach(self::$attrList as $attr) {
					$LineObj->setVal($attr, $attributes[$attr]);
				}
				$LineObj->save();
			}
		}
		
		return $this->mod->saveN();
	}
	
	public function delet()
	{	
	  $lineList = $this->mod->getVal('Lines');
	  foreach ($lineList as $LineId) {
				$LineObj = new Model('PrfLine',(int) $LineId);
				$LineObj->deletN();
	  }
	  return $this->mod->deletN();
	}
	
	
}

