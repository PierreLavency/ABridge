<?php
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;

	require_once 'CLASSDEC.php';
	
	
	
	$bindings = [$Session=>$Session,$User=>$User,$Role=>$Role,$Distribution=>$Distribution];
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();
	
	$obj->initMod($bindings);
		
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";