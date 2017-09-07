<?php
namespace ABridge\ABridge\Adm;

use ABridge\ABridge\Comp;

use ABridge\ABridge\Mod\Model;

use ABridge\ABridge\Handler;

class Adm extends Comp
{
    protected $isNew = false;
    private static $instance = null;
    
    private function __construct()
    {
    }
    
    public static function get()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Adm();
        }
        return self::$instance;
    }
    
    public function reset()
    {
        $this->isNew=false;
        self::$instance =null;
        return true;
    }
    
    public function init($app, $prm)
    {
        $mod = 'Admin';
        handler::get()->setCmod($mod, 'ABridge\ABridge\Adm\Admin');
    }
    
    public function begin($app, $prm)
    {
        $mod = 'Admin';
        $obj = new Model($mod);
        $obj->setCriteria([], [], []);
        $res = $obj->select();
        if (count($res)==0) {
            $obj->setVal('Application', $app);
            $obj->setVal('Init', true);
            $obj->save();
            $this->isNew = true;
        } else {
            $obj = new Model($mod, $res[0]);
        }
        return $obj;
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
}
