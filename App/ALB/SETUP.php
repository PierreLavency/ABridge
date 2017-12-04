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
		'Album_Photo' 	=> ['dataBase',],
		],
	'Apps'	=>[
					'AdmApp'=>[],
			
	],
	'Default'=> [
		'cssName' => 'ALBstyle.css'
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
							CstMode::V_S_READ=> ['Nom','Photos',],
						],
						'navList' => [
							CstMode::V_S_READ => [],
						],

						'listHtmlClass' => [
							CstView::V_CVAL =>[CstHTML::H_DIV,'albimg'],
						],
						'attrHtml' => [
								CstMode::V_S_READ => [
										'Photos'=>[
												CstView::V_SLICE=>16,
												CstView::V_COUNTF=>true,
												CstView::V_CTYP=>CstView::V_C_TYPN,
												CstView::V_CVAL=>[
														CstHTML::H_TYPE=>CstHTML::H_T_NTABLE,
														CstHTML::H_TABLEN=>16]
												
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
		
		'Album_Photo' => [
				'attrHtml' => [
						CstMode::V_S_CREA => [
								'Album'=>CstHTML::H_T_SELECT,
								'Photo'=>CstHTML::H_T_SELECT,
						],
						CstMode::V_S_UPDT => [
								'Album'=>CstHTML::H_T_SELECT,
								'Photo'=>CstHTML::H_T_SELECT,
						],
						CstMode::V_S_SLCT => [
								'Album'=>CstHTML::H_T_SELECT,
								'Photo'=>CstHTML::H_T_SELECT,
						],
						CstView::V_S_CREF => ['Photo'=>'Reference'],
				],
			],
		'Photo'=> [		
				'attrList' => [
					CstView::V_S_REF	=> ['Nom'],			
					CstView::V_S_CREF	=> ['Photo','id'],					
				],
				'listHtml'=> [
					CstView::V_S_CREF=> [
							cstView::V_S_CREF=>[CstHTML::H_TYPE =>CstHTML::H_T_TABLE],
					],
				],
				'listHtmlClass' => [
					CstView::V_S_CREF =>[CstHTML::H_DIV,'albimg'],
				],
				'attrHtml' => [
					CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 200,CstHTML::H_COLP=> 200]],
					CstView::V_S_CREF => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 100,CstHTML::H_COLP=> 100]],
					CstMode::V_S_SLCT => [
								CstMode::V_S_SLCT =>[
										CstView::V_SLICE=>33,
										CstView::V_CVAL=>[CstHTML::H_TYPE =>CstHTML::H_T_LIST_BR]
								]
						]

				],	
				'lblList'  => [
				
				],
				'viewList' => [
						'Description' => [

						],
						'Albums' => [
								'attrList' => [
										CstMode::V_S_READ=> ['Albums'],
								],
								'navList' => [
										CstMode::V_S_READ => [],
								],
								
						],
						'Reference' => [
								'attrList' => [
										CstMode::V_S_READ=> ['Photo'],
								],
								'attrProp' => [
										CstMode::V_S_READ =>[CstView::V_P_VAL],
								],
								'navList' => [
										CstMode::V_S_READ => [],
								],
								
						]
				]
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
		$res = $obj->addAttr($Photo.'s', Mtype::M_CREF, '/'.$Album.'_'.$Photo.'/'.$Album);
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
		$res = $obj->addAttr($Album.'s', Mtype::M_CREF, '/'.$Album.'_'.$Photo.'/'.$Photo);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Photo."<br>";		

		
		$obj = new Model($Album.'_'.$Photo);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr($Album, Mtype::M_REF, '/'.$Album);
		$res = $obj->addAttr($Photo, Mtype::M_REF, '/'.$Photo);
		$res=$obj->setProp($Album, Model::P_MDT);
		$res=$obj->setProp($Photo, Model::P_MDT);
		
		$obj->setCkey([$Album,$Photo], true);
		$obj->saveMod();
		
		$r = $obj->getErrLog ();
		$r->show();
	}
	
	public function initDelta()
	{
		$Album ='Album';
		$Photo= 'Photo';
		
	}
	
	
	protected function loadFile()
	{
		$photos = new Model('Photo');
		$dir = 'C:\xampp\htdocs\Photos\jogging\\';
		$reldDir='/Photos/jogging/';
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$fileName = $dir.$file;
					if (is_dir($fileName)) {
						echo "directory name: ".$file."<br>" ;
					} else {
						echo "file name: $file : filetype: " . mime_content_type($dir . $file) ."<br>";
						$photoFile=$reldDir.$file;
						$photos->setCriteria(['Photo'], [], [$photoFile], []);
						$res=$photos->select();
						if ($res!=[]) {
							echo "found <br>";
						} else {
							$photo = new  Model('Photo');
							$photo->setVal('Photo',$photoFile);
							$photo->save();
						}
					}
				}
				closedir($dh);
			}
		}
	}
	
}