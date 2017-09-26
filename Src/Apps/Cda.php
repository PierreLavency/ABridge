<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\App;

class Cda extends App
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
    
    public static function loadMeta($prm)
    {
        // CodeVal
        
        $obj = new Model(self::CODEVAL);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->addAttr('ValueOf', Mtype::M_REF, '/'.self::CODE);
        $res=$obj->setProp('ValueOf', Model::P_MDT);
        
        $res = $obj->saveMod();
        echo $obj->getModName()."<br>";
        $obj->getErrLog()->show();
        echo "<br>";
        
        // Code
        
        $obj = new Model(self::CODE);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res=$obj->setProp('Name', Model::P_BKY);// Unique
        $res = $obj->addAttr('Values', Mtype::M_CREF, '/'.self::CODEVAL.'/ValueOf');
        
        $res = $obj->saveMod();
        echo $obj->getModName()."<br>";
        $obj->getErrLog()->show();
        echo "<br>";

        foreach ($prm as $codeName) {
            $codeMobj = new Model(self::CODE);
            $codeMobj->setVal('Name', $codeName);
            $codeMobj->save();
            echo $codeMobj->getVal('Name')."<br>";
            $codeMobj->getErrLog()->show();
            echo "<br>";
        }
    }
    
    public static function loadData($prm)
    {
        foreach ($prm as $code => $values) {
            $codeMobj = Find::byKey(self::CODE, 'Name', $code);
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
