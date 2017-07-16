<?php
require_once 'CstMode.php';
require_once 'View/CstView.php';

class Config
{

	static $config = [
	'Handlers' =>
		[
		'Afs'  => ['dataBase'],
		'Dir'  => ['dataBase'],
		'Fle'  => ['dataBase'],
		],
	'Home' =>
		['/Dir','/Fle'],
	];
	
}