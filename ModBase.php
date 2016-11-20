<?php
require_once("Handler.php"); 
require_once("Model.php"); 

class ModBase
{
    private $_base;
    
    function __construct($base) 
    {
        $this->_base=$base;
    }
    
    public function getBase() 
    {
        return $this->_base;
    }
    
    public function eraseMod($mod) 
    {
        $name = $mod->getModName();
        return ($this->_base->delMod($name));

    }
    
    public function saveMod($mod) 
    {
        $name = $mod->getModName();
        $meta['attr_lst'] = $mod->getAllAttr();
        $meta['attr_typ'] = $mod->getAllTyp();
        $meta['attr_dflt'] = $mod->getAllDflt();
        $meta['attr_path'] = $mod->getAllPath();
        $meta['attr_bkey'] = $mod->getAllBkey();
        $meta['attr_mdtr'] = $mod->getAllMdtr();
        if ( ! $this->_base->existsMod($name)) {
            return ($this->_base->newMod($name, $meta));
        }
        $values = $this->_base->getMod($name);
        $x = array_diff($meta['attr_lst'], $values['attr_lst']);
        $addList['attr_lst'] = $x;
        $addList['attr_typ'] = $meta['attr_typ'];
        $x = array_diff($values['attr_lst'], $meta['attr_lst']);
        $delList['attr_lst'] = $x;
        $delList['attr_typ'] = $values['attr_typ'];
        return ($this->_base->putMod($name, $meta, $addList, $delList)); 
    }
    
    public function restoreMod($mod) 
    {
        $name = $mod->getModName();
        $values = $this->_base->getMod($name); 
        if (!$values) {
            return false;
        }
        $attrlist=$values['attr_lst'];
        $attrtype=[];
        $attrdflt=[];
        $attrpath=[];
        $attrbkey=[];
        $attrmdtr=[];
        $attrlist=$values['attr_lst'];
        $attrtype=$values['attr_typ'];
        $attrpath=$values['attr_path'];
        $attrbkey=$values['attr_bkey'];
        $attrmdtr=$values['attr_mdtr'];
        if (isset($values['attr_dflt'])) {
            $attrdflt=$values['attr_dflt'];
        }
        $predef = $mod->getAllPredef();
        foreach ($attrlist as $attr) {
            if (! in_array($attr, $predef)) {
                $typ= $attrtype[$attr];
                $path=0;
                if (array_key_exists($attr, $attrpath)) {
                    $path=$attrpath[$attr];
                }
                $mod->addAttr($attr, $typ, $path);
                if (array_key_exists($attr, $attrdflt)) {
                    $mod->setDflt($attr, $attrdflt[$attr]);
                }
                if (in_array($attr, $attrbkey)) {
                    $mod->setBkey($attr, true);
                }
                if (in_array($attr, $attrmdtr)) {
                    $mod->setMdtr($attr, true);
                }
            }
        } 
        return true;    
    }

    public function saveObj($mod) 
    {
        $name = $mod->getModName();
        $values =$mod->getAllVal();
        $id = $mod->getId();
        if ($id == 0) {
            return ($this->_base->newObj($name, $values));               
        }
        return ($this->_base->putObj($name, $id, $values)); 
    }

    public function restoreObj($mod) 
    {
        $name = $mod->getModName();
        $id = $mod->getId();
        if ($id==0) {
            return 0;
        }
        $values = $this->_base->getObj($name, $id); 
        if (!$values) {
            return 0;
        }
        foreach ($values as $attr=>$val) {
            if ($mod->existsAttr($attr)) {
                $typ=$mod->getTyp($attr);
                $valn=convertString($val, $typ);
                $mod->setVal($attr, $valn, false);
            }
        };
        return $id;
    }

    public function eraseObj($mod) 
    {
        $name = $mod->getModName();
        $id = $mod->getId();
        if ($id==0) {
            return true;
        }
        return ($this->_base->delObj($name, $id));
    }
    
    
    public function findObj($modN,$attr,$val) 
    {
        return ($this->_base->findObj($modN, $attr, $val));
    }
    
    
    
}
