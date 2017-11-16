<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\AppComp;

class Config extends AppComp
{
	
	protected  $config = [
	'Handlers' =>[
		'Album'		=> ['dataBase',],
		'Photo' 	=> ['dataBase',],
		],
	'Apps'	=>[
					'AdmApp'=>[],
			
	],
			
	'View' => [
		'Home' => ['/',],

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
												CstView::V_CVAL=>[
														CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,
														CstHTML::H_TABLEN=>4]
												
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
				'listHtmlClassElem' => [
					CstView::V_S_CREF =>[CstHTML::H_DIV,'test'],
				],
				'attrHtml' => [
					CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 600,CstHTML::H_COLP=> 400]],
					CstView::V_S_CREF => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 100,CstHTML::H_COLP=> 100]],

				],	
				'lblList'  => [
				
				],	
		],	

		],
	];
	
	public function initOwnMeta($config)
	{
		$ACode = 'AbstractCode';
		
		$Album ='Album';
		$Photo='Photo';
		
		$User ='User';
		$Role = 'Role';
		$Session ='Session';
		$Distribution = 'Distribution';
		
		
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
		

	}

	
}