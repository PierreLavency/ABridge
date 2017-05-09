<?php
	
// when running this data will be lost !!

	$ACode = 'AbstractCode';

	$Album ='Album';
	$Photo='Photo';

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
	
	
	// Album
	$obj = new Model($Album);
	$res= $obj->deleteMod();
	
	$res = $obj->addAttr('Nom',M_STRING);
	$res = $obj->addAttr('Description',M_TXT);
	$res = $obj->addAttr($Photo.'s',M_CREF,'/'.$Photo.'/'.'De');
	$res = $obj->addAttr($User,M_REF,'/'.$User);	
	
	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Album."<br>";
	
	// Photos
	
	$obj = new Model($Photo);
	$res= $obj->deleteMod();
	$res = $obj->addAttr('Nom',M_STRING);
	$res = $obj->addAttr('Description',M_TXT);	
	$res = $obj->addAttr('Photo',M_STRING);	
	$res = $obj->addAttr('Rowp',M_INT);	
	$res = $obj->addAttr('Colp',M_INT);	
		
	$res = $obj->addAttr('De',M_REF,'/'.$Album);
	
	$res = $obj->saveMod();			
	$r = $obj->getErrLog ();
	$r->show();
	echo "<br>".$Photo."<br>";
	
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