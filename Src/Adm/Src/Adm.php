<?php

require_once 'Admin.php';

class Adm
{
    protected static $isNew=false;
    
    public static function init($app)
    {
        $res = Find::AllId('Admin');
        
        if (count($res)==0) {
            $x=new Model('Admin');
            $x->setVal('Application', $app[0]);
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
