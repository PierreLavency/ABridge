<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Handler;

class Mod
{
    protected static $isNew=false;
    
    public static function init($prm, $config)
    {
        foreach ($config as $classN => $handler) {
            $c = count($handler);
            switch ($c) {
                case 1:
                    if ($handler[0]=='dataBase') {
                        $handler[]=$prm['dbnm'];
                    }
                    if ($handler[0]=='fileBase') {
                        $handler[]=$prm['flnm'];
                    }
                    // default
                case 2:
                    Handler::get()->setBase($handler[0], $handler[1], $prm);
                    Handler::get()->setStateHandler($classN, $handler[0], $handler[1]);
                    break;
                default:
                    throw new Exception(CstError::E_ERC063);
            }
        }
    }
}
