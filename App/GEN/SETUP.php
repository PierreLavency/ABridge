<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\App;

require_once 'Person.php';

class Config extends App
{
	public  static function loadMeta($prm=null)
	{
		return true;
	}
	
	public  static function loadData($prm=null)
	{
		return  true;
	}
	
	static $config = [
	'Handlers' =>
		[
		'Person'	 => ['dataBase'],
		'User'	 	 => ['dataBase'],
		'CodeValue'  => ['dataBase'],
		'Code' 		 => ['dataBase'],		
		],
	
	'View' => [
		'Home' => ['/',],
		'User' =>[
		
				'attrList' => [
					CstView::V_S_REF		=> ['SurName','Name'],
					]
					
			],
		'Person' => [
		
				'attrHtml' => [
					CstMode::V_S_CREA => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_UPDT => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_SLCT => ['Sexe'=>CstHTML::H_T_RADIO],
					CstMode::V_S_READ => ['text'=>[CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,CstHTML::H_COL=>90,CstHTML::H_ROW=> 10]],
				],
				'attrList' => [
					CstView::V_S_CREF 	=> ['id','Sexe','BirthDay','DeathDate','Age','DeathAge','Father','Mother'],
					CstView::V_S_REF		=> ['SurName','Name'],
				],
				'attrProp' => [
					CstMode::V_S_SLCT =>[CstView::V_P_LBL,CstView::V_P_OP,CstView::V_P_VAL],
				]
				
		],
		'Code' => [
		
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]
				
		],
		'CodeValue' =>[
		
				'attrList' => [
					CstView::V_S_REF		=> ['Name'],
				]
				
		],
	]
	];
}



