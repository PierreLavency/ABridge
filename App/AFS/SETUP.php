<?php

use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;
use ABridge\ABridge\View\CstView;

class Config extends App
{
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [
	'Apps'	=>
			[
					'AdmApp'=> [],
					
			],
	'Handlers' =>
		[
		'Elm'  => ['dataBase',],
		'Dir'  => ['dataBase',],
		'Fle'  => ['dataBase',],
		'DirElm'  => ['dataBase',],
		],
	'View'=> [
		'Home' =>
			['/',"/".ADM::ADMIN."/1",'/Dir','/Fle'],
		'MenuExcl' =>["/".ADM::ADMIN],
			'Elm' => [
					'attrList' =>
					[
							CstView::V_S_REF    => ['Name'],
					],
			],
			'Dir' => [
					'attrList' =>
					[
							CstView::V_S_REF    => ['Name'],
					],
			],
			'Fle' => [
					'attrList' =>
					[
							CstView::V_S_REF    => ['Name'],
					],
			],
			'DirElm' => [
					'attrList' =>
					[
							CstView::V_S_REF    => ['Dir'],
							CstView::V_S_CREF   => ['Elm'],
					],
			],
		]
	];

	
	public static function initMeta($config)
	{
		AdmApp::initMeta(self::$config['Apps']['AdmApp']);
		
		$x = new Model('Elm');
		$x->deleteMod();

		$x->setAbstr();
		$x->addAttr('Name',Mtype::M_STRING);
		
		$x->saveMod();
		$x->getErrLog()->show();
			
		
		$x=new Model('Dir');
		$x->deleteMod();		
		$x->setInhNme('Elm');
		
		$x->addAttr('Elments', 	Mtype::M_CREF,	'/DirElm/Dir');
		$x->addAttr('DotDot',	Mtype::M_CREF,	'/DirElm/Elm');
		
		$x->saveMod();
		$x->getErrLog()->show();

		
		$x=new Model('Fle');
		$x->deleteMod();		
		$x->setInhNme('Elm');
		
		$x->addAttr('DotDot',Mtype::M_CREF,'/DirElm/Elm');
		$x->addAttr('Content', Mtype::M_TXT);
		
		$x->saveMod();
		$x->getErrLog()->show();
		
		
		$obj=new Model('DirElm');
		$obj->deleteMod();
		
		$res = $obj->addAttr('Dir', Mtype::M_REF, '/Dir');
		$res = $obj->addAttr('Elm', Mtype::M_REF, '/Elm');		
		$res=$obj->setProp('Dir', Model::P_MDT);
		$res=$obj->setProp('Elm', Model::P_MDT);	
		$res=$obj->setProp('Elm', Model::P_BKY);	
		$obj->setCkey(['Dir','Elm'], true);
		
		$obj->saveMod();
		
		
	}
	
	public static function initData($prm=null)
	{
		AdmApp::initData(self::$config['Apps']['AdmApp']);
	}
	
	private static function  createData($id,$B,$D)
	{
		for ($i=1;$i<$B;$i++) {
			$x = new Model('Dir');
			$name = 'D_'.$id.'.'.$i;
			$x->setVal('Name',$name);
			$x->setVal('Father',$id);
			$id2= $x->save();
			$x = new Model('Fle');
			$name = 'F_'.$id.'.'.$i;
			$x->setVal('Name',$name);
			$x->setVal('Father',$id);
			$x->save();
			if($D > 0) {
				self::createData($id2,$B,$D-1);
			}
		}
	}
}