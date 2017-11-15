<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\AppComp;

class Cdv extends AppComp
{
    const CODE='Code';
    const CODEVAL='CodeValue';
    const CODELIST='CodeList';
    const CODEDATA='CodeData';
    
    protected $code;
    protected $codeval;
    
        
    public function __construct($prm, $bindings)
    {
        $this->bindings=$bindings;
        $this->prm = $prm;
        $code = self::CODE;
        if (isset($bindings[self::CODE])) {
            $code = $bindings[self::CODE];
        }
        
        $codeval = self::CODEVAL;
        if (isset($bindings[self::CODEVAL])) {
            $codeval = $bindings[self::CODEVAL];
        }
              
        $this->config= [
                
                'Handlers' => [
                        $code => [],
                        $codeval => [],
                ],
                    
                'View' => [
                        $code=> [
                                'attrList' => [
                                        CstView::V_S_REF        => ['Name'],
                                ]
                                
                        ],
                        $codeval=>[
                                'attrList' => [
                                        CstView::V_S_REF        => ['Name'],
                                ]
                                    
                        ],
                            
                ],
        ];
    }
    
    
    public function initOwnMeta($prm)
    {
        $bindings=[];
        $code = self::CODE;
        if (isset($this->bindings[self::CODE])) {
            $code = $this->bindings[self::CODE];
        }
        $bindings[self::CODE]=$code;
        $codeval = self::CODEVAL;
        if (isset($this->bindings[self::CODEVAL])) {
            $codeval = $this->bindings[self::CODEVAL];
        }
        $bindings[self::CODEVAL]=$codeval;
        
        $codelist = [];
        if (isset($this->bindings[self::CODELIST])) {
            $codelist= $this->bindings[self::CODELIST];
        }
        
        // CodeVal
        
        $obj = new Model($codeval);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->addAttr('ValueOf', Mtype::M_REF, '/'.$code);
        $res = $obj->setProp('ValueOf', Model::P_MDT);
               
        $res = $obj->saveMod();
        $obj->getErrLog()->show();
        
        // Code
        
        $obj = new Model($code);
        $res= $obj->deleteMod();
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->setProp('Name', Model::P_BKY);// Unique
        $res = $obj->addAttr('Values', Mtype::M_CREF, '/'.$codeval.'/ValueOf');
        
        $res = $obj->saveMod();
        $obj->getErrLog()->show();
               
        foreach ($codelist as $codeName) {
            $codeMobj = new Model($code);
            $codeMobj->setVal('Name', $codeName);
            $codeId=$codeMobj->save();
            $bindings[$codeName]=$code.'/'.$codeId;
            $codeMobj->getErrLog()->show();
        }
        return $bindings;
    }
    
    public function initOwnData()
    {
        $prm=$this->bindings;
        $code = self::CODE;
        if (isset($prm[self::CODE])) {
            $code = $prm[self::CODE];
        }
        $codeval = self::CODEVAL;
        if (isset($prm[self::CODEVAL])) {
            $codeval = $prm[self::CODEVAL];
        }
        $codeData=$prm[self::CODEDATA];
        $i=0;
        foreach ($codeData as $codeName => $values) {
            $codeMobj = Find::byKey($code, 'Name', $codeName);
            $codeId= $codeMobj->getId();
            foreach ($values as $value) {
                $valMobj= new Model($codeval);
                $valMobj->setVal('Name', $value);
                $valMobj->setVal('ValueOf', $codeId);
                $valMobj->save();
                $i++;
            }
        }
        return $i;
    }
}
