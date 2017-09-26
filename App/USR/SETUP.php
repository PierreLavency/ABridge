<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Apps\UsrApp;

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\Usr\Usr;

use ABridge\ABridge\App;

class Config extends App
{
		
	const DBDEC = 'USR';
	const PDATA = 'ProfileData';
		
	static $config = [
	'Apps'	=>
			[
					'UsrApp',
					'AdmApp',				
			],
	'Handlers' =>
			[
					'ProfileData' => [],				
			],
			
	'View' => [
		'Home' =>
			['/',"/".Usr::SESSION."/~","/".Usr::USER."/~","/".Adm::ADMIN."/1"],
		'MenuExcl' =>
			[
					"/".Adm::ADMIN,
					"/".Usr::USER,"/".Usr::SESSION,
					"/".Usr::DISTRIBUTION,"/".Usr::GROUPUSER,
					"/".self::PDATA,
			],
			
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
	
	public static function loadMeta($prm=null)
	{
		UsrApp::loadMeta();
		AdmApp::loadMeta();
		
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
		
		$res=$obj->setProp(Usr::USER,Model::P_BKY);
		
		$res = $obj->saveMod();
		echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	}
	
	public static function loadData($prm=null)
	{
		UsrApp::loadData();
		AdmApp::loadData();
		
		$RSpec ='[
[["Read"],"true", "true"],
[["Read","Update","Delete"],"|Session",{"Session":":id"}],
[["Read","Update"],"|User",{"User":":id<>:User"}],
[["Read","Create","Update","Delete"],"|User|Of",{"User":":id<>:User"}]
]';
		
		$obj=new Model(usr::ROLE);
		$obj->setVal('Name', 'Default');
		$obj->setVal('JSpec', $RSpec);
		$obj->save();
		echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
		echo "<br>";
		
	}
	
}
	