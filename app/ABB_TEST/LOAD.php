<?php
		
	require_once 'CLASSDEC.php';	
				
	// Ctype
	
	$obj=new Model($CType);
	$obj->setVal('Value','Message');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($CType);
	$obj->setVal('Value','Batch');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($CType);
	$obj->setVal('Value','GUI');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// SLevel
	
	$obj=new Model($SLevel);
	$obj->setVal('Value','Critical');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($SLevel);
	$obj->setVal('Value','Major');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($SLevel);
	$obj->setVal('Value','Minor');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($SLevel);
	$obj->setVal('Value','None');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// A style

	$obj=new Model($AStyle);
	$obj->setVal('Value','Recoverable Transactions');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($AStyle);
	$obj->setVal('Value','Low Latency, High Capacity');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($AStyle);
	$obj->setVal('Value','Data Warehousing, Reporting & Analytics');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($AStyle);
	$obj->setVal('Value','Data Broadcast & Streaming');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($AStyle);
	$obj->setVal('Value','GUI Applications');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// Source control

	$obj=new Model($SControl);
	$obj->setVal('Value','Internal');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($SControl);
	$obj->setVal('Value','External');
	$obj->save();	
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// Interface type
	
	$obj=new Model($IType);
	$obj->setVal('Value','Message');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($IType);
	$obj->setVal('Value','Noticiation');
	$obj->save();	
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($IType);
	$obj->setVal('Value','Batch');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($IType);
	$obj->setVal('Value','DataBase');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// interface usage
	
	$obj=new Model($IUse);
	$obj->setVal('Value','Internal');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model($IUse);
	$obj->setVal('Value','External');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";	
	
	// Roles 	
	
	$RSpec = 
'[
["true","true","true"]
]';

	$obj=new Model($Role);
	$obj->setVal('Name','Root');
	$obj->setVal('JSpec',$RSpec);
	$RootRole=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	$RSpec = 
'[
[["Select"],        ["|Application","|Component","|Interface","|Exchange"], "true"],
[["Read"],          "true",         "true"],
[["Read","Update","Delete"],  "|Session",    {"Session":"id"}],
[["Read","Update"],  "|User",       {"User":"id<>User"}]
]';

	$obj=new Model($Role);
	$obj->setVal('Name','Default');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
		$RSpec = 
'[
 [["Select"],                      ["|Application","|Component","|Interface","|Exchange"], "true"],
 [["Read"],                        "true",                              "true"],
 [["Update"],                      "|Application",                      {"Application":"Owner<>User:UserGroup"}],
 [["Create"],                      "|Application|BuiltFrom",            {"Application":"Owner<>User:UserGroup","BuiltFrom":"Owner<>User:UserGroup"}],
 [["Create","Update","Delete"],    "|Application|In",                   {"Application":"Owner<>User:UserGroup"}],
 [["Create","Update","Delete"],    "|Application|Out",                  {"Application":"Owner<>User:UserGroup"}],
 [["Update","Delete"],             "|Application|BuiltFrom",            {"BuiltFrom":"Owner<>User:UserGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|In",         {"BuiltFrom":"Owner<>User:UserGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|Out",        {"BuiltFrom":"Owner<>User:UserGroup"}],
 [["Read","Update","Delete"],      "|Session",                          {"Session":"id"}],
 [["Read","Update"],               "|User",                             {"User":"id<>User"}]
]';

		
	$obj=new Model($Role);
	$obj->setVal('Name','AppOwner');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	$RSpec = 
'[
 [["Select"],                      ["|Application","|Component","|Interface","|Exchange"], "true"],
 [["Read"],                        "true",                              "true"],
 [["Create","Update","Delete"],    "|Application",                      "true"],
 [["Create","Update","Delete"],    "|Component",                        "true"],
 [["Create","Update","Delete"],    "|Interface",                        "true"],
 [["Create","Update","Delete"],    "|Exchange",                         "true"],
 [["Read","Update","Delete"],      "|Session",                          {"Session":"id"}],
 [["Read","Update"],               "|User",                             {"User":"id<>User"}]
]';

	
	$obj=new Model($Role);
	$obj->setVal('Name','ArchOwner');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	
	// User
	
	$obj=new Model($User);
	$obj->setVal('UserId','Root');
	$RootUser=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

	// Distribution
	
	$obj=new Model($Distribution);
	$obj->setVal('ofRole',$RootRole);
	$obj->setVal('toUser',$RootUser);		
	$res=$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	