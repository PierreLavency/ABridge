
<?php

$build = "C:\Users\pierr\ABridge\Bld";
$alias = "ABridge.phar";
$name = $build."\ABridge.phar";
$buildSrc = $build."\Tmp";

$phar = new Phar($name, 0, $alias);
$phar->buildFromIterator(
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($buildSrc, FilesystemIterator::SKIP_DOTS)
    ),
    $buildSrc
);
$phar->setStub($phar->createDefaultStub("\Src\index.php"));

echo $phar->count();


