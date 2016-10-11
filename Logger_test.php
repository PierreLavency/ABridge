<?php

require_once("Logger.php");

$x = new Logger();

$x->logLine ("this is my first logged line");

$r = $x->logLine ("this is my second logged line");

$x->show();

$x = new Logger();

$r++;

$x->logLine ("$r lines logged");

$x->show();

?>