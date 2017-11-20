
<?php

// to include in rootdoc prod version 

$home = "phar://ABridge.phar";
$conf = parse_ini_file("config.ini");
$phase = 'prod';
require "ABridge.phar";
