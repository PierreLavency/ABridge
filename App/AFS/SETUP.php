<?php
require_once 'CstMode.php';
require_once '/View/Src/CstView.php';

	$config = [
	'Handlers' =>
		[
		'Afs'  => ['dataBase'],
		'Dir'  => ['dataBase'],
		'Fle'  => ['dataBase'],
		],
	'Home' =>
		['/Dir','/Fle'],
	];
	
