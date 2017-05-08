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

	// Abstract 
	
	$obj = new Model($ACode);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',M_STRING);
	$res = $obj->setMdtr('Value',true);
    $res = $obj->setBkey('Value',true);	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$ACode."<br>";
	
	// Unite Mesure
	
	$obj = new Model($Unit);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Unit."<br>";
	
	// Unite Temps
	
	$obj = new Model($TUnit);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Unit."<br>";
	
	// Type de recette
	
	$obj = new Model($RType);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$RType."<br>";
	
	// Niveau de difficulte
	
	$obj = new Model($Diff);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Diff."<br>";
	
	// Recette
	$obj = new Model($Recette);
	$res= $obj->deleteMod();
	
	$res = $obj->addAttr('Nom',M_STRING);
	$res = $obj->addAttr($RType,M_CODE,'/'.$RType);
	$res = $obj->addAttr($Diff,M_CODE,'/'.$Diff);
	$res = $obj->addAttr('Minutes',M_INT);	
	$res = $obj->addAttr('Resume',M_HTML);
	$res = $obj->addAttr('Description',M_HTML);
	$res = $obj->addAttr($Ingredient.'s',M_CREF,'/'.$Ingredient.'/'.'De');
	$res = $obj->addAttr('Photo',M_STRING);	
	$res = $obj->addAttr($User,M_REF,'/'.$User);	
	
	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Recette."<br>";
	
	// Ingrediens
	$obj = new Model($Ingredient);
	$res= $obj->deleteMod();
	$res = $obj->addAttr('Nom',M_STRING);
	$res = $obj->addAttr('Quantite',M_INT);
	$res = $obj->addAttr($Unit,M_CODE,'/'.$Unit);
	$res = $obj->addAttr('De',M_REF,'/'.$Recette);
	
	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Ingredient."<br>";
	
		// User
		
	$obj = new Model($User);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('SurName',M_STRING);
	$res = $obj->addAttr('Play',M_CREF,'/'.$Distribution.'/toUser');
	
	
	echo "<br>User<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
		// Role
		
	$obj = new Model($Role);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',M_STRING);
 	$res = $obj->addAttr('JSpec',M_JSON);	
	$res = $obj->addAttr('PlayedBy',M_CREF,'/'.$Distribution.'/ofRole');
	
	echo "<br>$Role<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	
	
	
		// Session
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();

	$res = $obj->addAttr($User,M_REF,'/'.$User);
	$res = $obj->addAttr($Role,M_REF,'/'.$Role);
	$res = $obj->addAttr('Comment',M_STRING);
	$res = $obj->addAttr('BKey',M_STRING);
	$res = $obj->setBkey('BKey',true);
		
	
	echo "<br>Session<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();
	
	// Distribution

	$obj = new Model($Distribution);
	$res= $obj->deleteMod();

	$path='/'.$Role;
	$res = $obj->addAttr('ofRole',M_REF,$path);
	$res = $obj->setMdtr('ofRole',true); // Mdtr

	$path='/'.$User;
	$res = $obj->addAttr('toUser',M_REF,$path);
	$res = $obj->setMdtr('toUser',true); // Mdtr

	$obj->setCkey(['ofRole','toUser'],true);
	
	echo "<br>Distribution<br>";		
	$res = $obj->saveMod();	
	$r = $obj->getErrLog ();
	$r->show();	