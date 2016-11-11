
<?php

function rootPath()
{
	return ('/ABridge.php');
}

function checkPath($Apath) {
	$path=explode('/',$Apath);
	$root = $path[0];
	if (($root != "" )) {return false;}
	if (count($path) < 2) {return false;}
	return true;
}

function refPath($ref,$id) 
{
	$path=rootPath().'/'.$ref.'/'.$id;
	return $path;
}

function modPath($mod) 
{
	$path='/'.$mod;
	return $path;
}

function objAbsPath($Model) 
{
	$RootPath= rootPath();
	$path = objPath($Model);
	$path = $RootPath.$path;
	return $path;
}

function objPath ($Model)
{
	$path = '/'.$Model->getModName();
	if ($Model->getId()) {$path=$path.'/'.$Model->getId();}
	return $path;
}

function pathObj($path)
{
	$apath=explode('/',$path);
	return (apathObj($apath));
}

function apathObj($apath)
{
	$c = count($apath);
	if ($c > 3) {return false;}
	if ($c < 2 ){return false;}
	if ($apath[0] != "" )  {return false;}
	if ($apath[1] == "" )  {return false;}
	if ($c == 3) {
		$id = (int) $apath[2];
		$mod = new Model($apath[1],$id);
	}
	if ($c == 2) {
		$mod = new Model($apath[1]);
	}
	return $mod;
}

function pathVal($path)
{
	$apath=explode('/',$path);
	if (count($apath) > 4) {return false;}
	if (count($apath) < 4 ){return false;}
	$attr=array_pop($apath);
	$mod = apathObj($apath);
	if (!$mod) {return false;}
	$val = $mod->getVal($attr);
	return $val;
}


?>