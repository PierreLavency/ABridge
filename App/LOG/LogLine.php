<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

class LogLine extends CModel 
{

	public function initMod($bindings)
	{

		$obj = $this->mod;
		
		$res = $obj->addAttr('LogMgr',Mtype::M_REF, "/".$bindings['LogMgr']);
		$res = $obj->addAttr('info',Mtype::M_STRING);	
		$res = $obj->addAttr('Content',Mtype::M_HTML);
	}
	

}

