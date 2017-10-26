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
		'Step' 			=> ['dataBase',],
		'UniteMesure'	=> ['dataBase',],
		'UniteTemps'	=> ['dataBase',],
		'TypeRecette'	=> ['dataBase',],
		'NiveauDifficulte' =>['dataBase',],
		'AbstractCode'	=> ['dataBase','CSN'],	
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
							CstMode::V_S_READ=> ['Nom','Steps','Ingredients','Description',],
							CstMode::V_S_UPDT=> ['Nom','Description',],							
						],
						'attrProp' => [
								CstMode::V_S_READ =>[CstView::V_P_VAL],
						],		
						'attrHtml' => [
								CstMode::V_S_READ => [
										'Ingredients'=>[CstView::V_SLICE=>2,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN],
										'Steps'=>[CstView::V_SLICE=>2,CstView::V_COUNTF=>false,CstView::V_CTYP=>CstView::V_C_TYPN],
								]
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
						CstMode::V_S_SLCT => [
								CstMode::V_S_SLCT=>[
										CstView::V_SLICE=>3,
										CstView::V_CREFLBL=>true,
								]
						],
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
		$Step='Step';
		
	
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
		$res = $obj->addAttr($Step.'s',Mtype::M_CREF,'/'.$Step.'/'.'De');
		$res = $obj->addAttr($Ingredient.'s',Mtype::M_CREF,'/'.$Ingredient.'/'.'De');
		$res = $obj->addAttr('Photo',Mtype::M_STRING);
		
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

		
		// Steps
		$obj = new Model($Step);
		$res= $obj->deleteMod();
		$res = $obj->addAttr('Nom',Mtype::M_STRING);
		$res = $obj->addAttr('De',Mtype::M_REF,'/'.$Recette);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		echo "<br>".$Ingredient."<br>";
	
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