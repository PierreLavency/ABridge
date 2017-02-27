
<?php

//to boostrap phpunit  must be the same as ABridge

$home = 'C:/Users/pierr/ABridge';
$conf = parse_ini_file($home.'/Src/config.ini');
$conf['name'] = 'UnitTest';
require_once $home.'/Src/Index.php';





