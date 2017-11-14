<?php

use ABridge\ABridge\Adm\Adm;
use ABridge\ABridge\App;
use ABridge\ABridge\Apps\AdmApp;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\View\CstView;

class Config extends App
{
	public static function  init($prm, $config)
	{
		return self::$config;
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
							CstMode::V_S_READ   => ['id','Name','Of','Dir','Fle','Elements'],
					],
					'attrHtml' => [
							CstMode::V_S_READ => [
									'Elements'=>[
											CstView::V_B_NEW=>false,
											CstView::V_B_SLC=>true,
									],
									'Dir'=>[
											CstView::V_SLICE=>0,
									],
									'Fle'=>[
											CstView::V_SLICE=>0,
									],
							],
					],
			],
			'Fle' => [
					'attrList' =>
					[
							CstView::V_S_REF    => ['Name'],
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
		$x->addAttr('Of', Mtype::M_REF, '/Elm');		
		
		$x->saveMod();
		$x->getErrLog()->show();
			
		
		$x=new Model('Dir');
		$x->deleteMod();		
		$x->setInhNme('Elm');	
		$x->addAttr('Elements', 	Mtype::M_CREF,	'/Elm/Of');
		$x->addAttr('Dir', 		Mtype::M_CREF,	'/Dir/Of');
		$x->addAttr('Fle', 		Mtype::M_CREF,	'/Fle/Of');
		
		$x->saveMod();
		$x->getErrLog()->show();

		
		$x=new Model('Fle');
		$x->deleteMod();		
		$x->setInhNme('Elm');
		
		$x->addAttr('Content', Mtype::M_TXT);
		
		$x->saveMod();
		$x->getErrLog()->show();	
		
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