<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\App;

class Config extends App
{
	public static function  init($prm, $config)
	{
		return $config;
	}
	
	static $config = [
	'Handlers' =>
		[
		'Album'		=> ['dataBase',],
		'Photo' 	=> ['dataBase',],
		'AbstractCode'	=> ['dataBase',],	
		'User'	 	 	=> ['dataBase',],
		'Role'	 	 	=> ['dataBase',],
		'Distribution'	=> ['dataBase',],
		'Session'		=> ['dataBase',],		
		],
	'View' => [
		'Home' =>
			['/',],
		'Album'=> [
		
				'attrList' => [
					CstView::V_S_REF	=> ['Nom'],
				],			
				'lblList'  => [
				],
				'viewList' => [
					'Photos'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Photos',],
						],
						'navList' => [
							CstMode::V_S_READ => [],
						],
						'attrHtml' => [
								CstMode::V_S_READ => [
										'Photos'=>[
												CstView::V_SLICE=>4,
												CstView::V_COUNTF=>true,
												CstView::V_CTYP=>CstView::V_C_TYPN,
												CstView::V_CVAL=>[CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,CstHTML::H_TABLEN=>2]
												
										]
								],
						],							
					],
					'Descritpion'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Description'],
							CstMode::V_S_UPDT=> ['id','Nom','Description'],
							CstMode::V_S_CREA=> ['id','Nom','Description'],
							CstMode::V_S_DELT=> ['id','Nom','Description'],							
						],
					],					
				]	
		],
		'Photo'=> [		
				'attrList' => [			
					CstView::V_S_CREF	=> ['id','Photo'],					
				],
				'attrHtml' => [
					CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 600,CstHTML::H_COLP=> 400]],
					CstView::V_S_CREF => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 300,CstHTML::H_COLP=> 100]],

				],	
				'lblList'  => [
				
				],	
		],	

		'User' =>[		
			'attrList' => [
				CstView::V_S_REF		=> ['SurName','Name'],
				],
		],
		'Role' =>[	
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]

		],
		'Distribution' =>[
			'attrHtml' => [
				CstMode::V_S_CREA => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_UPDT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
				CstMode::V_S_SLCT => ['ofRole'=>CstHTML::H_T_SELECT,'toUser'=>CstHTML::H_T_SELECT],
			],

		],
		],
	];
	
	public static function initMeta($config)
	{
		$ACode = 'AbstractCode';
		
		$Album ='Album';
		$Photo='Photo';
		
		$User ='User';
		$Role = 'Role';
		$Session ='Session';
		$Distribution = 'Distribution';
		
		// Abstract
		
		$obj = new Model($ACode);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Value',Mtype::M_STRING);
		$res=$obj->setProp('Value', Model::P_MDT); 
		$res = $obj->setProp('Value',Model::P_BKY);
		$res = $obj->setAbstr();
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$ACode."<br>";
		
		
		// Album
		$obj = new Model($Album);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Nom',Mtype::M_STRING);
		$res = $obj->addAttr('Description',Mtype::M_TXT);
		$res = $obj->addAttr($Photo.'s',Mtype::M_CREF,'/'.$Photo.'/'.'De');
		$res = $obj->addAttr($User,Mtype::M_REF,'/'.$User);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Album."<br>";
		
		// Photos
		
		$obj = new Model($Photo);
		$res= $obj->deleteMod();
		$res = $obj->addAttr('Nom',Mtype::M_STRING);
		$res = $obj->addAttr('Description',Mtype::M_TXT);
		$res = $obj->addAttr('Photo',Mtype::M_STRING);
		$res = $obj->addAttr('Rowp',Mtype::M_INT);
		$res = $obj->addAttr('Colp',Mtype::M_INT);
		$res = $obj->addAttr('De',Mtype::M_REF,'/'.$Album);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Photo."<br>";
		
		// User
		
		$obj = new Model($User);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		$res = $obj->addAttr('SurName',Mtype::M_STRING);
		$res = $obj->addAttr('Play',Mtype::M_CREF,'/'.$Distribution.'/toUser');
		
		
		echo "<br>User<br>";
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		
		// Role
		
		$obj = new Model($Role);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		$res = $obj->addAttr('JSpec',Mtype::M_JSON);
		$res = $obj->addAttr('PlayedBy',Mtype::M_CREF,'/'.$Distribution.'/ofRole');
		
		echo "<br>$Role<br>";
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		
		
		// Session
		
		$obj = new Model($Session);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr($User,Mtype::M_REF,'/'.$User);
		$res = $obj->addAttr($Role,Mtype::M_REF,'/'.$Role);
		$res = $obj->addAttr('Comment',Mtype::M_STRING);
		$res = $obj->addAttr('BKey',Mtype::M_STRING);
		$res = $obj->setProp('BKey',Model::P_BKY);
		
		
		echo "<br>Session<br>";
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		
		// Distribution
		
		$obj = new Model($Distribution);
		$res= $obj->deleteMod();
		
		$path='/'.$Role;
		$res = $obj->addAttr('ofRole',Mtype::M_REF,$path);
		$res=$obj->setProp('OfRole', Model::P_MDT); 
		
		$path='/'.$User;
		$res = $obj->addAttr('toUser',Mtype::M_REF,$path);
		$res=$obj->setProp('toUser', Model::P_MDT); 
		
		$obj->setCkey(['ofRole','toUser'],true);
		
		echo "<br>Distribution<br>";
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();	
	}

	public static function initData($prm=null)
	{
		
	}
	
}