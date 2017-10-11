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
		'Recette'		=> ['dataBase',],
		'Ingredient' 	=> ['dataBase',],
		'UniteMesure'	=> ['dataBase',],
		'UniteTemps'	=> ['dataBase',],
		'TypeRecette'	=> ['dataBase',],
		'NiveauDifficulte' =>['dataBase',],
		'AbstractCode'	=> ['dataBase','CSN'],	
		'User'	 	 	=> ['dataBase',],
		'Role'	 	 	=> ['dataBase',],
		'Distribution'	=> ['dataBase',],
		'Session'		=> ['dataBase',],		
		],

	'View' => [
		'Home' => ['/',],
		'MenuExcl' =>["/AbstractCode"],
		'Recette'=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Nom'],
					CstView::V_S_CREF	=> ['id','TypeRecette','NiveauDifficulte','Minutes'],
				],
				'attrHtml' => [

				],
				'lblList'  => [
					'NiveauDifficulte'=>'Niveau de difficulte:',
					'TypeRecette' => 'Type de recette:',
					'Nom'=>'Nom de la recette:',
					'New'=>'Ajouter',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_UPDT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_CREA=> ['Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],
							CstMode::V_S_DELT=> ['id','Nom','TypeRecette','NiveauDifficulte','Minutes','Resume','Photo'],							
						],
						'attrHtml' => [
							CstMode::V_S_READ => ['Photo'=>[CstHTML::H_TYPE=>CstHTML::H_T_IMG,CstHTML::H_ROWP=> 80],0],
						],	
					],
					'Description'  => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Ingredients','Description',],
							CstMode::V_S_UPDT=> ['Nom','Description',],							
						],
						'attrProp' => [
								CstMode::V_S_READ =>[CstView::V_P_VAL],
						],		
						'attrHtml' => [
								CstMode::V_S_READ => ['Ingredients'=>[CstView::V_SLICE=>20,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN]]
						],	
						'navList' => [CstMode::V_S_READ => [CstMode::V_S_UPDT],
						],
					],
					'Ingredients' => [
						'attrList' => [
							CstMode::V_S_READ=> ['Nom','Ingredients'],							
						],
						'navList' => [CstMode::V_S_READ => []
						],
					],
				]
				
		],
		
		'Ingredient'=> [
		
				'attrList' => [
//					V_S_REF		=> ['Nom'],
				],
				'attrHtml' => [
				],
				'lblList'  => [
				],	
		],	

		'UniteMesure'=> [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Value'],
				]
				
		],
		'TypeRecette'=> [
		
				'attrList' => [
						CstView::V_S_REF		=> ['Value'],
				]
				
		],
		'NiveauDifficulte'=> [
		
				'attrList' => [
						CstView::V_S_REF		=> ['Value'],
				]
				
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
	
public static	function initMeta($config)
	{
		$ACode = 'AbstractCode';
		
		$Unit  = 'UniteMesure';
		$TUnit  = 'UniteTemps';
		$RType = 'TypeRecette';
		$Diff = 'NiveauDifficulte';
		
		$Recette ='Recette';
		$Ingredient='Ingredient';
		
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
		
		// Unite Mesure
		
		$obj = new Model($Unit);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme($ACode);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Unit."<br>";
		
		// Unite Temps
		
		$obj = new Model($TUnit);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme($ACode);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Unit."<br>";
		
		// Type de recette
		
		$obj = new Model($RType);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme($ACode);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType."<br>";
		
		// Niveau de difficulte
		
		$obj = new Model($Diff);
		$res= $obj->deleteMod();
		
		$res = $obj->setInhNme($ACode);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Diff."<br>";
		
		// Recette
		$obj = new Model($Recette);
		$res= $obj->deleteMod();
		
		$res = $obj->addAttr('Nom',Mtype::M_STRING);
		$res = $obj->addAttr($RType,Mtype::M_CODE,'/'.$RType);
		$res = $obj->addAttr($Diff,Mtype::M_CODE,'/'.$Diff);
		$res = $obj->addAttr('Minutes',Mtype::M_INT);
		$res = $obj->addAttr('Resume',Mtype::M_HTML);
		$res = $obj->addAttr('Description',Mtype::M_HTML);
		$res = $obj->addAttr($Ingredient.'s',Mtype::M_CREF,'/'.$Ingredient.'/'.'De');
		$res = $obj->addAttr('Photo',Mtype::M_STRING);
		$res = $obj->addAttr($User,Mtype::M_REF,'/'.$User);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Recette."<br>";
		
		// Ingrediens
		$obj = new Model($Ingredient);
		$res= $obj->deleteMod();
		$res = $obj->addAttr('Nom',Mtype::M_STRING);
		$res = $obj->addAttr('Quantite',Mtype::M_INT);
		$res = $obj->addAttr($Unit,Mtype::M_CODE,'/'.$Unit);
		$res = $obj->addAttr('De',Mtype::M_REF,'/'.$Recette);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Ingredient."<br>";
		
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
		$res=$obj->setProp('ofRole', Model::P_MDT); 
		
		$path='/'.$User;
		$res = $obj->addAttr('toUser',Mtype::M_REF,$path);
		$res=$obj->setProp('toUser', Model::P_MDT); 
		
		$obj->setCkey(['ofRole','toUser'],true);
		
		echo "<br>Distribution<br>";
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
	}
	
public static 	function initData($prm=null)
	{
		
		$ACode = 'AbstractCode';
		
		$Unit  = 'UniteMesure';
		$TUnit  = 'UniteTemps';
		$RType = 'TypeRecette';
		$Diff = 'NiveauDifficulte';
		
		$Recette ='Recette';
		$Ingredient='Ingredient';
		
		$User ='User';
		$Role = 'Role';
		$Session ='Session';
		$Distribution = 'Distribution';
		
		
		// Unite Mesure
		
		$obj = new Model($Unit);
		
		$Value= 'Gramme';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Unit.':'.$Value."<br>";
		
		$obj = new Model($Unit);
		$Value= 'CentiLitre';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Unit.':'.$Value."<br>";
		
		$obj = new Model($Unit);
		$Value= 'DeciLitre';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Unit.':'.$Value."<br>";
				
		// Unite Temps
				
		// Type de recette
		
		$obj = new Model($RType);
		
		$Value= 'Entree';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType.':'.$Value."<br>";
		
		$obj = new Model($RType);
		
		$Value= 'Plat';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType.':'.$Value."<br>";
		
		$obj = new Model($RType);
		
		$Value= 'Dessert';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType.':'.$Value."<br>";
		
		$obj = new Model($RType);
		
		$Value= 'Zakousky';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType.':'.$Value."<br>";
		
		$obj = new Model($RType);
		
		$Value= 'Coktail';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$RType.':'.$Value."<br>";
		
		// Niveau de difficulte
		
		$obj = new Model($Diff);
		
		$Value= 'TresFacile';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Diff.':'.$Value."<br>";
		
		$obj = new Model($Diff);
		
		$Value= 'Facile';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Diff.':'.$Value."<br>";
		
		$obj = new Model($Diff);
		
		$Value= 'Difficile';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Diff.':'.$Value."<br>";
		
		$obj = new Model($Diff);
		
		$Value= 'TresDifficile';
		
		$obj->setVal('Value',$Value);
		$res = $obj->save();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Diff.':'.$Value."<br>";
	}
}