<?php
namespace ABridge\ABridge\Hdl;

use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Usr\Usr;

class Hdl
{
    public static function init($app, $prm)
    {
        if (isset($prm['Usr'])) {
            Usr::init($app, $prm['Usr']);
        }
    }
    
    public static function begin($app, $prm)
    {
        $isNew=false;
        if (isset($prm['Usr'])) {
            $sessionHdl= Usr::begin($app, ['ABridge\ABridge\Usr\Session']);
            if ($sessionHdl->isNew()) {
                $handle = new Handle('/Session/~', CstMode::V_S_UPDT, $sessionHdl);
                $isNew=true;
            } else {
                $handle = new Handle($sessionHdl);
            }
        } else {
            $handle = new Handle(null);
        }
        return [$isNew,$handle];
    }
}
