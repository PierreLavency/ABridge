
<?php

function rootPath()
{
    return ('/ABridge.php');
}

function checkPath($apath) 
{
    $path=explode('/', $apath);
    $root = $path[0];
    if (($root != "" )) {
        return false;
    }
    if (count($path) < 2) {
        return false;
    }
    return true;
}

function refPath($ref,$id) 
{
    if ($id) {
    $path=rootPath().'/'.$ref.'/'.$id;
    return $path;       
    }
    $path=rootPath().'/'.$ref;
    return $path;
}

function modPath($mod) 
{
    $path='/'.$mod;
    return $path;
}

function objAbsPath($model) 
{
    $rootPath= rootPath();
    $path = objPath($model);
    $path = $rootPath.$path;
    return $path;
}

function objPath ($model)
{
    $path = '/'.$model->getModName();
    if ($model->getId()) {
        $path=$path.'/'.$model->getId();
    }
    return $path;
}

function pathObj($path)
{
    $apath=explode('/', $path);
    return (apathObj($apath));
}

function apathObj($apath)
{
    $c = count($apath);
    if ($c > 3) {
        return false;
    }
    if ($c < 2 ) {
        return false;
    }
    if ($apath[0] != "" ) {
        return false;
    }
    if ($apath[1] == "" ) {
        return false;
    }
    if ($c == 3) {
        $id = (int) $apath[2];
        $mod = new Model($apath[1], $id);
    }
    if ($c == 2) {
        $mod = new Model($apath[1]);
    }
    return $mod;
}

function pathVal($path)
{
    $apath=explode('/', $path);
    if (count($apath) > 4) {
        return false;
    }
    if (count($apath) < 4 ) {
        return false;
    }
    $attr=array_pop($apath);
    $mod = apathObj($apath);
    if (!$mod) {
        return false;
    }
    $val = $mod->getVal($attr);
    return $val;
}

