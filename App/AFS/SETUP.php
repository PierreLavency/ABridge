<?php
use ABridge\ABridge\View\CstHTML;

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