
<?php
require_once("UnitTest.php");
$logName = basename(__FILE__, ".php");

$log=new unitTest($logName);

$log->logLine('/* standalone test */');

$log->logLine('/* standalone test */');
	
/**************************************/

$show = 0;


require_once("FileBase.php"); 


$test = [
	"ERC" => [0=>["lastId"=>3],1=>["CODE"=> "001", "SEVERITY"=> 1], 2 => ["CODE"=> "002", "SEVERITY"=> 2] ], 
	];

$test1=['CODE'=> '001', 'SEVERITY'=> 1];
$test2=['CODE'=> '002', 'SEVERITY'=> 2];
$test3=['CODE'=> '001', 'SEVERITY'=> 0];
	
// saving 
$x = new FileBase($logName);

$res=$x->newMod('ERC');
	$line = "$res=x->newMod('ERC')"; $log->logLine($line);

$id1=$x->newObj('ERC',$test1);
	$line = "$id1=x->newObj('ERC',test1);"; $log->logLine($line);

$id2=$x->newObj('ERC',$test2);
	$line = "$id2=x->newObj('ERC',test2);"; $log->logLine($line);
	
$r = $x->commit();
									$line = "$r = x->save();";
									$log->logLine($line);
$y = new FileBase($logName);
									$line = "y = new FileBase($logName);";
									$log->logLine($line);
$y->load();
									$line = "y->load();";
									$log->logLine($line);




//select
if ($show) {
	echo "testing select";
	echo "<br/>";
}

$r = implode (' , ',$erc= $x->getObj('ERC',1));
									$line = "$r = implode (' , ',erc= x->getObj('ERC',1));";
									$log->logLine($line);

if ($show) {
print_r($erc);
echo "<br/>";
}

// updating
if ($show){
	echo "testing update";
	echo "<br/>";
}

$erc["SEVERITY"] = 0;
$erc = $y->putObj('ERC',1,$erc);
									$line = "$erc = y->putObj('ERC',1,erc);";
									$log->logLine($line);
$r=$y->commit();
									$line = "$r=y->save();";
									$log->logLine($line);

$y = new FileBase($logName);
									$line = "y = new FileBase($logName);";
									$log->logLine($line);
$y->load();
									$line = "y->load();";
									$log->logLine($line);

$r = implode (' , ',$erc= $y->getObj('ERC',1));
									$line = "$r = implode (' , ',erc= y->getObj('ERC',1))";
									$log->logLine($line);
if ($show) {
	print_r($erc);
	echo "<br/>";

// deleting 

	echo "testing delete";
	echo "<br/>";
}

$r=implode(' , ',$erc = $y->getObj('ERC',2));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',2));";
									$log->logLine($line);

if ($show) {	
	print_r($erc);
	echo "<br/>";
}

$r=$y->delObj('ERC',2);
									$line = "$r=y->delObj('ERC',2);";
									$log->logLine($line);


if ($show) {								


	// creating

	echo "testing create";
	echo "<br/>";
}

$id = $y->newObj('ERC',$erc);
									$line = "$id = y->newObj('ERC',erc);";
									$log->logLine($line);


									
$r=implode(' , ',$erc = $y->getObj('ERC',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',$id));";
									$log->logLine($line);

if ($show) {
	print_r($erc);
	echo "<br/>";
}

$r=$y->commit();
									$line = "$r=y->save();";
									$log->logLine($line);

$y = new FileBase($logName);
									$line = "y = new FileBase($logName);";
									$log->logLine($line);
$y->load();
									$line = "y->load();";
									$log->logLine($line);

$r=implode(' , ',$erc = $y->getObj('ERC',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERC',$id));";
									$log->logLine($line);

if ($show){

	print_r($erc);
	echo "<br/>";

// new model

	echo "testing new model";
	echo "<br/>";
}

$r=$y->newMod('ERCode');
									$line = "$r=y->newMod('ERCode');";
									$log->logLine($line);

									

$id = $y->newObj('ERCode',$erc);
									$line = "$id = y->newObj('ERCode',erc);";
									$log->logLine($line);

$r=implode(' , ',$erc = $y->getObj('ERCode',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERCode',$id));";
									$log->logLine($line);

if ($show){
	print_r($erc);
	echo "<br/>";
}

$r=$y->commit();
									$line = "$r=y->save();";
									$log->logLine($line);

$y = new FileBase($logName);									
									$line = "y = new FileBase($logName);";
									$log->logLine($line);

$y->load();
									$line = "y->load();";
									$log->logLine($line);
$r=implode(' , ',$erc = $y->getObj('ERCode',$id));
									$line = "$r=implode(' , ',erc = y->getObj('ERCode',$id));";
									$log->logLine($line);
if ($show){
	print_r($erc);
	echo "<br/>";

	echo "testing All values";
	echo "<br/>";


}

$r=implode (' , ',$a = $y->allAttrVal('ERC','CODE'));
									$line = "$r=implode (' , ',a = y->allAttrVal('ERC','CODE'));;";
									$log->logLine($line);
if ($show){
	print_r($a);
	echo "<br/>";
}

$log->saveTest();

?>
