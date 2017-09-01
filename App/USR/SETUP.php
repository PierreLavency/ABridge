<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Apps\Usr;
use ABridge\ABridge\Apps\Adm;

class Config 
{
		
	const DBDEC = 'USR';
	const PDATA = 'ProfileData';
		
	static $config = [
	'Apps'	=>
			[
					'Usr',
					'Adm',				
			],
	'Handlers' =>
			[
					'ProfileData' => [],				
			],
			
	'View' => [
		'Home' =>
			['/',"/".Usr::SESSION."/~","/".Usr::USER."/~","/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			["/".Adm::ADMIN,"/".Usr::USER,"/".Usr::SESSION],
			
		Usr::USER =>[
			'viewList' => [
					'Profile' => [
							'attrList' => [
									CstMode::V_S_READ=> [
											'id',
											'UserId',
											'Role',
											'UserGroup',
											'Of',
									],
							],
							'navList' => [
									CstMode::V_S_READ => [
											CstMode::V_S_SLCT,CstMode::V_S_CREA,CstMode::V_S_DELT,
									],
							],
					],
			],
			
		],
		self::PDATA => [
				'attrList' => [
						CstView::V_S_REF		=> ['SurName','Name'],
				],
		],
		],
	];
	
	public static function loadMeta()
	{
		Usr::loadMeta();
		Adm::loadMeta();
		
		$obj = new Model(Usr::USER);		
		$res = $obj->addAttr('Of',Mtype::M_CREF,'/'.self::PDATA.'/'.Usr::USER);		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
		
		
		$obj = new Model(self::PDATA);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		
		$res = $obj->addAttr('SurName',Mtype::M_STRING);
		
		$res = $obj->addAttr('BirthDay',Mtype::M_DATE);
		
		$res = $obj->addAttr('Image',Mtype::M_STRING);
		
		$res =$obj->addAttr(Usr::USER,Mtype::M_REF,'/'.Usr::USER);
		
		$res=$obj->setBkey(Usr::USER,true);
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	}
	
	public static function loadData()
	{
		Usr::loadData();
		Adm::loadData();
		
		$RSpec ='[
[["Read"],"true", "true"],
[["Read","Update","Delete"],"|Session",{"Session":"id"}],
[["Read","Update"],"|User",{"User":"id<>User"}],
[["Read","Create","Update","Delete"],"|User|Of",{"User":"id<>User"}]
]';
		
		$obj=new Model(usr::ROLE);
		$obj->setVal('Name', 'Default');
		$obj->setVal('JSpec', $RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
		echo "<br>";
		
	}
	
}
	