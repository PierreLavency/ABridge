<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");
$z=new unitTest($logName);
$show = 0;

require_once("PersistFile.php"); 

$test1=['CODE'=> '001', 'SEVERITY'=> 1];
$test2=['CODE'=> '002', 'SEVERITY'=> 2];
$test3=['CODE'=> '001', 'SEVERITY'=> 0];

// create save and get

$x = new fileBase();

$res=$x->newMod('ERC');
	$line = "$res=x->newMod('ERC')"; $z->logLine($line);

$id1=$x->newObj('ERC',$test1);
	$line = "$id1=x->newObj('ERC',test1);"; $z->logLine($line);

$id2=$x->newObj('ERC',$test2);
	$line = "$id2=x->newObj('ERC',test2);"; $z->logLine($line);

	
$res=$x->save();
	$line = "$res=x->save();"; $z->logLine($line);

$y= new fileBase();

$y->load();

$res1=$y->getObj('ERC',$id1);
	$line = "res1=y->getObj('ERC',$id1);"; $z->logLine($line);
	
$t = ($res1 == $test1);
	$line = "$t = (res1 == test1);"; $z->logLine($line);
	
$res2=$y->getObj('ERC',$id2);
	$line = "res2=y->getObj('ERC',$id2);"; $z->logLine($line);

$t = ($res2 == $test2);
	$line = "$t = (res2 == test2);"; $z->logLine($line);

// update save and get

$res= $y->putObj('ERC',$id1,$test3);
	$line = "$res= y->putObj('ERC',$id1,test3);"; $z->logLine($line);

$res=$y->save();
	$line = "$res=y->save();"; $z->logLine($line);

$y = new fileBase();

$y->load();

$res3=$y->getObj('ERC',$id1);
	$line = "res3=y->getObj('ERC',$id1);"; $z->logLine($line);
	
$t = ($res3 == $test3);
	$line = "$t = (res3 == test3);"; $z->logLine($line);

// delete save and get


$res=$y->delObj('ERC',$id2);
	$line = "$res=y->delObj('ERC',$id2);"; $z->logLine($line);

$res=$y->save();
	$line = "$res=y->save();"; $z->logLine($line);
	
$y = new fileBase();

$y->load();

$res=$y->getObj('ERC',$id2);
	$line = "$res=y->getObj('ERC',$id2);"; $z->logLine($line);

/* shoudl return false */

/* META DATA */

$testm1= ['attr_lst'=>['CODE','SEVERITY'],'typ_lst'=>['CODE'=>'m_string','SEVERITY'=>'m_id']];

// create save and get

$x = new fileBase();

$res=$x->newMod('ERC2',$testm1);
	$line = "$res=x->newMod('ERC2',testm1);"; $z->logLine($line);
	
$res=$x->save();
	$line = "$res=x->save();"; $z->logLine($line);
	
$y = new fileBase();

$y->load();

$res1=$y->getMod('ERC2');
	$line = "res1=y->getMod('ERC2');"; $z->logLine($line);
	
$t=($testm1 == $res1); 
	$line = "$t=(testm1 == res1);"; $z->logLine($line);

// delete save and get

$res=$y->delMod('ERC2');
	$line = "$res=y->delMod('ERC2');"; $z->logLine($line);
	
$res=$y->save();
	$line = "$res=y->save();"; $z->logLine($line);

$y = new fileBase();

$y->load();

$res1=$y->getMod('ERC2');
	$line = "res1=y->getMod('ERC2');"; $z->logLine($line);

// update save and get
$res=$y->newMod('ERC');
	$line = "$res=y->newMod('ERC');"; $z->logLine($line);
	
$res=$y->putMod('ERC',$testm1);
	$line = "res=y->putMod('ERC',testm1);"; $z->logLine($line);

$res=$y->save();
	$line = "$res=y->save();"; $z->logLine($line);
	
$y = new fileBase();

$y->load();

$res1=$y->getMod('ERC');
	$line = "res1=y->getMod('ERC');"; $z->logLine($line);
	
$t=($res1 == $testm1); 
	$line = "$t=(res1 == testm1); "; $z->logLine($line);
	

$z->save();

?>
