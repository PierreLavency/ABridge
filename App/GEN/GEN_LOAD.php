
<?php
	
	
	$sex = new Model($Code);
	$res = $sex->setVal('Name','Sexe');
	$sex_id = $sex->save();	
	
	$country = new Model($Code);
	$res = $country->setVal('Name','Country');
	$country_id = $country->save();	
	
	$r = $code-> getErrLog ();
	$r->show();
	
	$sextype1 = new Model($CodeVal);
	$res = $sextype1->setVal('Name','Male');
	$res = $sextype1->setVal('ValueOf',1);
	$s1 = $sextype1->save();

	$r = $sextype1-> getErrLog ();
	$r->show();
	
	$sextype1 = new Model($CodeVal);
	$res = $sextype1->setVal('Name','Female');
	$res = $sextype1->setVal('ValueOf',1);
	$s2 = $sextype1->save();
	
	$r = $sextype1-> getErrLog ();
	$r->show();	
	
	