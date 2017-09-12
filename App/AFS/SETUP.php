<?php

use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Apps\AdmApp;

use ABridge\ABridge\App;

class Config extends App
{

	static $config = [
	'Apps'	=>
			[
					'AdmApp',
					
			],
	'Handlers' =>
		[
		'Afs'  => ['dataBase',],
		'Dir'  => ['dataBase',],
		'Fle'  => ['dataBase',],
		],
	'View'=> [
		'Home' =>
			['/Dir','/Fle'],
		]
	];

	public static function loadMeta($prm=null)
	{
		AdmApp::loadMeta();
		
		$x = new Model('Afs');
		$x->deleteMod();
		$x->setAbstr();
		$x->addAttr('Name',Mtype::M_STRING);
		$x->saveMod();
		$r = $x-> getErrLog ();
		$r->show();
		
		$x=new Model('Dir');
		$x->deleteMod();
		$x->setInhNme('Afs');
		$x->addAttr('Father',Mtype::M_REF,'/Dir');
		$x->addAttr('FatherOfD',Mtype::M_CREF,'/Dir/Father');
		$x->addAttr('FatherOfF',Mtype::M_CREF,'/Fle/Father');
		$x->saveMod();
		$r = $x-> getErrLog ();
		$r->show();
		
		$x=new Model('Fle');
		$x->deleteMod();
		$x->setInhNme('Afs');
		$x->addAttr('Father',Mtype::M_REF,'/Dir');
		$x->saveMod();
		$r = $x-> getErrLog ();
		$r->show();
	}
	
	public static function loadData($prm=null)
	{
		AdmApp::loadData();
		
		$x=new Model('Dir');
		$x->setVal('Name','/');
		$id = $x->save();
		self::createData($id,3,2);
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