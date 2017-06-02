<?php
	
// when running this data will be lost !!

	require_once 'CLASSDEC.php';
	
	// Architecture building block 
			
	$obj = new Model($ABB);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			M_STRING);
	$res = $obj->addAttr('CodeNm',			M_STRING);
	$res = $obj->addAttr('Alias',			M_STRING);
	$res = $obj->addAttr('ShortDesc',		M_STRING);	
	$res = $obj->addAttr('LongDesc',		M_TXT);
	$res = $obj->addAttr('Owner',			M_REF,	"/$Group");
	$res = $obj->addAttr('In',				M_CREF,	"/$Interface/Of");
	$res = $obj->addAttr('Out',				M_CREF,	"/$Exchange/OutOf");	
	$res = $obj->setAbstr();
 	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Application

	$obj = new Model($Application);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ABB);	
	$res = $obj->addAttr('Style',			M_REF,	"/$AStyle");
	$res = $obj->addAttr('Authenticity',	M_REF,	"/$SLevel");
	$res = $obj->addAttr('Availability',	M_REF,	"/$SLevel");
	$res = $obj->addAttr('Confidentiality',	M_REF,	"/$SLevel");	
	$res = $obj->addAttr('Integrity',		M_REF,	"/$SLevel");	
	$res = $obj->addAttr('BuiltFrom',		M_CREF,	"/$Component/Of"); 
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Component 
	
	$obj = new Model($Component);	
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme($ABB);
	$res = $obj->addAttr($CType,			M_REF,	"/$CType"); 	
	$res = $obj->addAttr('Of',				M_REF,	"/$Application"); 
	$res = $obj->addAttr('SourceControl',	M_REF,	"/$SControl");
	$res = $obj->addAttr('Url',				M_STRING);
	$res = $obj->addAttr('Queue',			M_STRING);
	$res = $obj->addAttr('OutQueue',		M_STRING);
	$res = $obj->addAttr('BatchNme',		M_STRING);
	$res = $obj->addAttr('Frequency',		M_STRING);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// $Interface

	$obj = new Model($Interface);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',			M_STRING);	
	$res = $obj->addAttr('Of',				M_REF,	"/$ABB");
	$res = $obj->addAttr($IType,			M_REF,	"/$IType");
	$res = $obj->addAttr($IUse,				M_REF,	"/$IUse");	
	$res = $obj->addAttr('Streaming',		M_STRING);
	$res = $obj->addAttr('LongDesc',		M_TXT);
	$res = $obj->addAttr('Content',			M_STRING);	
	$res = $obj->addAttr('UsedBy',			M_CREF,"/$Exchange/Through");
		
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	

	// Exchange
	
	$obj = new Model($Exchange);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('CodeNm',			M_STRING);
	$res = $obj->addAttr('Through',			M_REF,"/$Interface");
	$res = $obj->addAttr('OutOf',			M_REF,"/$ABB");
	$obj->setCkey(['OutOf','Through'],true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


/*******************************  Codes  ************************/
	
	// Abstract 
	
	$obj = new Model($ACode);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Value',M_STRING);
	$res = $obj->setMdtr('Value',true);
    $res = $obj->setBkey('Value',true);	
	$res = $obj->setAbstr();	

	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Ctype
	
	$obj = new Model($CType);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// SLevel
	
	$obj = new Model($SLevel);
	$res= $obj->deleteMod();

	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// A style

	$obj = new Model($AStyle);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


	// Source control

	$obj = new Model($SControl);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Interface type
		
	$obj = new Model($IType);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// interface usage
	
	$obj = new Model($IUse);
	$res= $obj->deleteMod();
	
	$res = $obj->setInhNme($ACode);	

	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	
/*******************************  User  ************************/

	// User
		
	$obj = new Model($User);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('UserId',		M_STRING);
	$res = $obj->addAttr('Password',	M_STRING);	
	$res = $obj->addAttr('NewPassword1',M_STRING,M_P_TEMP);
	$res = $obj->addAttr('NewPassword2',M_STRING,M_P_TEMP);	
	$res = $obj->addAttr('DefaultRole',	M_CODE,'/'.$Role);
	$res = $obj->addAttr($Group,		M_REF,'/'.$Group);		
	$res = $obj->addAttr('Play',		M_CREF,'/'.$Distribution.'/toUser');
	
    $res = $obj->setBkey('UserId',true);
	
	$res = $obj->saveMod();	
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";


	// Session
	
	$obj = new Model($Session);
	$res= $obj->deleteMod();

	$res = $obj->addAttr($User,			M_REF,		'/'.$User);
	$res = $obj->addAttr($Role,			M_REF,		'/'.$Role);
	$res = $obj->addAttr('UserId',		M_STRING);	
	$res = $obj->addAttr('Password',	M_STRING, 	M_P_TEMP);	
	$res = $obj->addAttr('BKey',		M_STRING, 	M_P_EVALP);
	$res = $obj->addAttr('ValidStart',	M_INT, 		M_P_EVALP);
	$res = $obj->addAttr('Prev',		M_REF,		"/$Session");
	
	$res = $obj->setBkey('BKey',true);
	$res = $obj->setMdtr('BKey',true);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";

	// Role
		
	$obj = new Model($Role);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('Name',		M_STRING);
 	$res = $obj->addAttr('JSpec',		M_JSON);	
	$res = $obj->addAttr('PlayedBy',	M_CREF,'/'.$Distribution.'/ofRole');
    $res = $obj->setBkey('Name',true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
	
	// Distribution

	$obj = new Model($Distribution);
	$res= $obj->deleteMod();

	$res = $obj->addAttr('ofRole',		M_REF,'/'.$Role);
	$res = $obj->addAttr('toUser',		M_REF,'/'.$User);
	
	$res = $obj->setMdtr('ofRole',true); // Mdtr
	$res = $obj->setMdtr('toUser',true); // Mdtr

	$obj->setCkey(['ofRole','toUser'],true);
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";
 
	// Group
	
	$obj = new Model($Group);
	$res= $obj->deleteMod();

 	$res = $obj->addAttr('Name',		M_STRING);
	$res = $obj->addAttr('Users',		M_CREF,'/'.$User.'/'.$Group);
    $res = $obj->setBkey('Name',true);	
	
	$res = $obj->saveMod();			
	echo $obj->getModName()."<br>";$obj->getErrLog()->show();echo "<br>";	
