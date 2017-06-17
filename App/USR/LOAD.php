<?php
		
	require_once 'CLASSDEC.php';	
				

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
	