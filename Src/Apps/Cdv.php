<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

class Cdv
{
    const CODE='Code';
    const CODEVAL='CodeValue';
    
    public static $config = [
            'Handlers' => [
                    self::CODE => [],
                    self::CODEVAL => [],
            ],
            
            'View' => [
                    self::CODE=> [
                            'attrList' => [
                                    CstView::V_S_REF        => ['Name'],
                            ]
                            
                    ],
                    self::CODEVAL=>[
                            'attrList' => [
                                    CstView::V_S_REF        => ['Name'],
                            ]
                            
                    ],
                    
            ],
    ];
    
    public static function loadMeta()
    {
        // CodeVal
        
        $obj = new Model(self::CODEVAL);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->addAttr('ValueOf', Mtype::M_REF, '/'.self::CODE);
        $res=$obj->setMdtr('ValueOf', true); // Mdtr
        
        
        $res = $obj->saveMod();
        echo "<br>".self::CODEVAL."<br>";
        $r = $obj-> getErrLog();
        $r->show();
        
        // Code
        
        $obj = new Model(self::CODE);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res=$obj->setBkey('Name', true);// Unique
        $res = $obj->addAttr('Values', Mtype::M_CREF, '/'.self::CODEVAL.'/ValueOf');
        
        echo self::CODE."<br>";
        $res = $obj->saveMod();
        $r->show();
    }
    
    public static function loadData($prm)
    {
        foreach ($prm as $code => $values) {
            $codeMobj = new Model(self::CODE);
            $codeMobj->setVal('Name', $code);
            $codeMobj->save();
            $codeId= $codeMobj->getId();
            foreach ($values as $value) {
                $valMobj= new Model(self::CODEVAL);
                $valMobj->setVal('Name', $value);
                $valMobj->setVal('ValueOf', $codeId);
                $valMobj->save();
            }
        }
    }
}
