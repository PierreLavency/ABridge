<?php

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\AppComp;

require_once 'Person.php';

class Config extends AppComp
{
	
	protected  $config = [
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
					CstView::V_S_REF	=> ['SurName','Name'],
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
	
	public function initOwnMeta($config)
	{
				
		// CodeVal
		$Code = 'Code';
		$CodeVal= 'CodeValue';
		
		$codeval = new Model($CodeVal);
		$res= $codeval->deleteMod();
		
		$res = $codeval->addAttr('Name',Mtype::M_STRING);
		
		$path='/'.$Code;
		$res = $codeval->addAttr('ValueOf',Mtype::M_REF,$path);
		$res=$codeval->setProp('ValueOf', Model::P_MDT);
		
		$res = $codeval->saveMod();
		
		$r = $codeval-> getErrLog ();
		$r->show();
		
		// Code
		
		$code = new Model($Code);
		$res= $code->deleteMod();
		
		$res = $code->addAttr('Name',Mtype::M_STRING);
		$res=$code->setProp('Name',Model::P_BKY);// Unique
		
		$path='/'.$CodeVal.'/ValueOf';
		$res = $code->addAttr('Values',Mtype::M_CREF,$path);
		
		$res = $code->saveMod();
		
		$r = $code-> getErrLog ();
		$r->show();
		
		
		// Person
		
		$person = new Model('Person');
		$res= $person->deleteMod();
		
		$res = $person->addAttr('Name',Mtype::M_STRING);
		$res = $person->setDflt('Name','Lavency'); // HERE
		$res = $person->addAttr('SurName',Mtype::M_STRING);
		$res = $person->addAttr('BirthDay',Mtype::M_DATE);
		
		$path='/Code/1/Values';
		$res = $person->addAttr('Sexe',Mtype::M_CODE,$path);
		
		$path='/Code/2/Values';
		$res = $person->addAttr('Country',Mtype::M_CODE,$path);
		
		
		$res = $person->addAttr('Father',Mtype::M_REF,'/Person');
		$res = $person->addAttr('Mother',Mtype::M_REF,'/Person');
		
		$path='/Person/Father';
		$res = $person->addAttr('FatherOf',Mtype::M_CREF,$path);
		
		$path='/Person/Mother';
		$res = $person->addAttr('MotherOf',Mtype::M_CREF,$path);
		
		$res = $person->addAttr('DeathDate',Mtype::M_DATE);
		$res = $person->addAttr('Age',Mtype::M_INT);
		$res = $person->setProp('Age', Model::P_EVL);
		$res = $person->setProp('Age', Model::P_TMP);
		
		$res = $person->addAttr('DeathAge',Mtype::M_INT);
		$res = $person->setProp('DeathAge',Model::P_EVL);
		
		$res = $person->addAttr('text',Mtype::M_TXT);
		
		$res= $person->addAttr('User',Mtype::M_REF,'/User');
		
		$res = $person->saveMod();
		$r = $person->getErrLog ();
		$r->show();
		
		
		// User
		
		$obj = new Model('User');
		$res= $obj->deleteMod();
		$res = $obj->addAttr('Code',Mtype::M_STRING);
		$res = $obj->addAttr('Name',Mtype::M_STRING);
		$res = $obj->addAttr('SurName',Mtype::M_STRING);
		
		$res = $obj->saveMod();
		$r = $obj->getErrLog ();
		$r->show();
		
		
	}
	
	public function initOwnData($prm)
	{
		
		$Code = 'Code';
		$CodeVal= 'CodeValue';
		
		$sex = new Model($Code);
		$res = $sex->setVal('Name','Sexe');
		$sex_id = $sex->save();
		
		$country = new Model($Code);
		$res = $country->setVal('Name','Country');
		$country_id = $country->save();
		
		$r = $code-> getErrLog ();
		$r->show();
		
		$sextype1 = new Model($CodeVal);
		$res = $sextype1->setVal('Name','Male');
		$res = $sextype1->setVal('ValueOf',1);
		$s1 = $sextype1->save();
		
		$r = $sextype1-> getErrLog ();
		$r->show();
		
		$sextype1 = new Model($CodeVal);
		$res = $sextype1->setVal('Name','Female');
		$res = $sextype1->setVal('ValueOf',1);
		$s2 = $sextype1->save();
		
		$r = $sextype1-> getErrLog ();
		$r->show();	
	}
	
}



