
<?php



function checkAbsPathString($Apath) {
	$path=explode('/',$Apath);
	$root = $path[0];
	if (($root != "" )) {return 0;}
	if (count($path) < 2) {return 0;}
	return true;
}

function getModPathString ($Apath) {
	if (! checkAbsPathString($Apath)) {return 0;}
	$path=explode('/',$Apath);
	return ($path[1]);
}

function getPathStringMod($Mod) {
	$path = '/'.$Mod;
	return $path;
}

function getRootPathString($Mod,$id) {
	$RootPath= '/ABridge.php';
	$path = $RootPath.'/'.$Mod.'/'.$id;
	return $path;
}


function evalPathString ($Apath,$Model=0){
	$path=explode('/',$Apath);
	$root = $path[0];
	if ((!$Model) and ($root != "" )) {return 0;}
	return(evalPath($path,$Model));	
}

function evalPath($path,$Model){
	$c=count($path);
	if ($c == 0) {return $Model;}
	$elm=array_shift($path);
	if ($elm == "") {
		$ModN = array_shift($path);
		$id = (int) array_shift($path);
		$Model = new Model ($ModN,$id);
		return (evalPath($path,$Model));
	}
	$Val=$Model->getVal($elm);
	if ($c>1) {
		$typ = $Model->getTyp($elm);
		if ($typ == M_REF) {
			$ModN = $Model->getPath($elm);
			$Model = new Model ($ModN,$Val);
			return (evalPath($path,$Model));
		}
		return 0;
	}
	return $Val;
}





?>