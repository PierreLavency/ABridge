<?php
//phpinfo();
//require_once("Unittest\UnitTest_test.php"); // not ported
//require_once("Unittest\Logger_test.php"); // done decommisioned !!
require_once("Unittest\Model_test.php");    // done
require_once("Unittest\GenHTML_test.php");  // KEPT
require_once("Unittest\FileBase_test.php"); //done
require_once("Unittest\Handler_test.php");  //done
//require_once("Unittest\Type_test.php");     //Not maintained
require_once("Unittest\View_test.php");     // KEPT
require_once("Unittest\Model_test_1.php"); // done
//require_once("Unittest\Model_test_2.php"); //Not maintained
require_once("Unittest\FileBase_test_1.php"); //done
require_once("Unittest\ModBase_test.php"); //done
require_once("Unittest\Model_test_3.php"); //done
require_once("Unittest\Type_test_1.php");    //done
require_once("Unittest\FileBase_test_2.php");//done
require_once("Unittest\Model_test_4.php"); //done

require_once("Unittest\Class_code_test.php");

// require_once("Unittest\Model_test_5.php");  //Not maintained ! (inject)
//require_once("Unittest\Model_test_6.php");  // Not maintained ! (inject)

// require_once("Unittest\Model_test_5_2.php");//Not maintained ! (inject)

$application= 'genealogy';

//require_once("Example\\".$application.'_META.php');
//require_once("Example\\".$application.'_META_1.php');
//require_once("Example\\" .$application.'_LOAD.php');

require_once("Example\\" .$application.'_SETUP.php');

require_once('controler.php');


