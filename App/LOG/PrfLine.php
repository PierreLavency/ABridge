<?php
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

class PrfLine extends CModel 
{

	static $attrListInt = ['Breath','Depth','Code','Number'];
	static $attrListFloat = ['Time','Avg'];
	static $attrListString = [];
	static $attrListCode = ['Base','Operation','Access'];
	
	public function initMod($bindings)
	{

		$obj = $this->mod;
		
		$res = $obj->addAttr('PrfFile',Mtype::M_REF, "/".$bindings['PrfFile']);
		$res = $obj->addAttr('info',Mtype::M_STRING);	
		$res = $obj->addAttr('Content',Mtype::M_HTML);
		foreach(self::$attrListCode as $attr) {
			$res = $obj->addAttr($attr, Mtype::M_CODE,'/'.$bindings[$attr].'/Values');
		}
		foreach(self::$attrListString as $attr) {
			$res = $obj->addAttr($attr, Mtype::M_STRING);
		}
		foreach(self::$attrListInt as $attr) {
			$res = $obj->addAttr($attr, Mtype::M_INT);
		}
		foreach(self::$attrListFloat as $attr) {
			$res = $obj->addAttr($attr, Mtype::M_FLOAT);
		}
	}
	

}

