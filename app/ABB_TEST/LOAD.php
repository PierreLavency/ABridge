<?php

use ABridge\ABridge\Mod\Model; 
use ABridge\ABridge\Apps\UsrApp;

UsrApp::loadData();

require_once 'SETUP.php';

	// Ctype
	
	$obj=new Model(Config::CType);
	$obj->setVal('Value','Message');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::CType);
	$obj->setVal('Value','Batch');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::CType);
	$obj->setVal('Value','GUI');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// SLevel
	
	$obj=new Model(Config::SLevel);
	$obj->setVal('Value','Critical');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::SLevel);
	$obj->setVal('Value','Major');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::SLevel);
	$obj->setVal('Value','Minor');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::SLevel);
	$obj->setVal('Value','None');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// A style

	$obj=new Model(Config::AStyle);
	$obj->setVal('Value','Recoverable Transactions');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::AStyle);
	$obj->setVal('Value','Low Latency, High Capacity');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::AStyle);
	$obj->setVal('Value','Data Warehousing, Reporting & Analytics');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::AStyle);
	$obj->setVal('Value','Data Broadcast & Streaming');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::AStyle);
	$obj->setVal('Value','GUI Applications');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// Source control

	$obj=new Model(Config::SControl);
	$obj->setVal('Value','Internal');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::SControl);
	$obj->setVal('Value','External');
	$obj->save();	
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// Interface type
	
	$obj=new Model(Config::IType);
	$obj->setVal('Value','Message');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::IType);
	$obj->setVal('Value','Noticiation');
	$obj->save();	
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::IType);
	$obj->setVal('Value','Batch');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::IType);
	$obj->setVal('Value','DataBase');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	// interface usage
	
	$obj=new Model(Config::IUse);
	$obj->setVal('Value','Internal');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
	$obj=new Model(Config::IUse);
	$obj->setVal('Value','External');
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";	
	
	// Roles 	
	
	$RSpec = 
'[
[["Select"],        ["|Application","|Component","|Interface","|Exchange"], "true"],
[["Read"],          "true",         "true"],
[["Read","Update","Delete"],  "|Session",    {"Session":":id"}],
[["Read","Update"],  "|User",       {"User":":id<==>:User"}]
]';

	$obj=new Model(Config::Role);
	$obj->setVal('Name','Public');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";
	
		$RSpec = 
'[
 [["Select"],                      ["|Application","|Component","|Interface","|Exchange"], "true"],
 [["Read"],                        "true",                              "true"],
 [["Update"],                      "|Application",                      {"Application":":Owner<==>:ActiveGroup"}],
 [["Create"],                      "|Application|BuiltFrom",            {"Application":":Owner<==>:ActiveGroup","BuiltFrom":":Owner<>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|In",                   {"Application":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|Out",                  {"Application":":Owner<==>:ActiveGroup"}],
 [["Update","Delete"],             "|Application|BuiltFrom",            {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|In",         {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Create","Update","Delete"],    "|Application|BuiltFrom|Out",        {"BuiltFrom":":Owner<==>:ActiveGroup"}],
 [["Read","Update","Delete"],      "|Session",                          {"Session":":id"}],
 [["Read","Update"],               "|User",                             {"User":":id<==>:User"}]
]';

		
	$obj=new Model(Config::Role);
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
 [["Read","Update","Delete"],      "|Session",                          {"Session":":id"}],
 [["Read","Update"],               "|User",                             {"User":":id<==>:User"}]
]';

	
	$obj=new Model(Config::Role);
	$obj->setVal('Name','ArchOwner');
	$obj->setVal('JSpec',$RSpec);
	$obj->save();
	echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();echo "<br>";

