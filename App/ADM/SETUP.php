<?php
require_once 'CstMode.php';
require_once '/View/Src/CstView.php';
require_once 'CModel.php';

	
	require_once 'CLASSDEC.php';

	$config = [
	'Handlers' => [
		$Adm => ['dataBase',$DBDEC,false],
	],
	'Home' => [
		'/','/Admin/1',
	],
	'Adm' => [
			
	],
	'Views' => [

		$Adm =>[		
			'attrList' => [
				V_S_REF		=> ['id'],
			],
			'lblList'  => [
				V_S_UPDT => 'Load',
			],
			'navList' => [
				V_S_READ => [V_S_UPDT],
			],
		],		

		],
	];		
	

	