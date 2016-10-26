
<?php

function evalPathString ($Apath,$Model=0){
	$path=explode('/',$Apath);
	$root = $path[0];
	if ((!$Model) and ($root != "" )) {return 0;}
	return(evalPath($path,$Model));	
}

function evalPath($path,$Model){
	$c=count($path);
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