<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Adm\Admin;
use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\Handler;

class Adm
{
    protected static $isNew=false;
    
    public static function init($app, $prm)
    {
        handler::get()->setCmod('Admin', 'ABridge\ABridge\Adm\Admin');
        $res = Find::AllId('Admin');
        
        if (count($res)==0) {
            $x=new Model('Admin');
            $x->setVal('Application', $app);
            $x->setVal('Init', true);
            $x->save();
            self::$isNew=true;
        }
    }
    
    public static function isNew()
    {
        return self::$isNew;
    }
}
