<?php
	
// when running this data will be lost !!

	$ACode = 'AbstractCode';

	$Unit  = 'UniteMesure';
	$TUnit  = 'UniteTemps';
	$RType = 'TypeRecette';
	$Diff = 'NiveauDifficulte';

	$Recette ='Recette';
	$Ingredient='Ingredient';

	$User ='User';
	$Role = 'Role';
	$Session ='Session';
	$Distribution = 'Distribution';

	
	// Unite Mesure
	
	$obj = new Model($Unit);

	$Value= 'Gramme';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Unit.':'.$Value."<br>";

	$obj = new Model($Unit);
	$Value= 'CentiLitre';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Unit.':'.$Value."<br>";

	$obj = new Model($Unit);
	$Value= 'DeciLitre';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Unit.':'.$Value."<br>";

	
	// Unite Temps

	
	// Type de recette
	
	$obj = new Model($RType);

	$Value= 'Entree';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType.':'.$Value."<br>";
	
		$obj = new Model($RType);

	$Value= 'Plat';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType.':'.$Value."<br>";

	$obj = new Model($RType);

	$Value= 'Dessert';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType.':'.$Value."<br>";

	$obj = new Model($RType);

	$Value= 'Zakousky';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType.':'.$Value."<br>";

	$obj = new Model($RType);

	$Value= 'Coktail';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType.':'.$Value."<br>";
	
	// Niveau de difficulte
	
	$obj = new Model($Diff);

	$Value= 'TresFacile';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Diff.':'.$Value."<br>";
	
	$obj = new Model($Diff);

	$Value= 'Facile';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Diff.':'.$Value."<br>";
	
	$obj = new Model($Diff);

	$Value= 'Difficile';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Diff.':'.$Value."<br>";
	
	$obj = new Model($Diff);

	$Value= 'TresDifficile';
	
	$obj->setVal('Value',$Value);	
	$res = $obj->save();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Diff.':'.$Value."<br>";
	