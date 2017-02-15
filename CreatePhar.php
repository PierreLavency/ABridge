<?php
$srcRoot = ".";
$buildRoot ='Build';
 
$phar = new Phar($buildRoot . "\ABridge.phar", 
	FilesystemIterator::CURRENT_AS_FILEINFO |     	FilesystemIterator::KEY_AS_FILENAME, "ABridge.phar");
$phar->buildFromDirectory(".",'/.php$/');
$phar->setStub($phar->createDefaultStub("index.php"));
echo $phar->count();
copy("config.ini", $buildRoot ."\config.ini");