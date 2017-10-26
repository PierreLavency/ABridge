<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Log\Log;

class ModBase
{
    private $base;
    private $logger;

    
    public function __construct($base)
    {
        $this->base=$base;
        $this->logger=Log::Get();
    }
    
    public function showState($modName = null)
    {
        $showState=[];
        $modNameList=[];
        if (is_null($modName)) {
            $modNameList=$this->base->getAllMod();
        } elseif ($this->base->existsMod($modName)) {
            $modNameList=[$modName];
        }
        foreach ($modNameList as $modName) {
            $showState[$modName] = $this->base->getMod($modName);
        }
        return $showState;
    }
    
    public function eraseMod($mod)
    {
        $name = $mod->getModName();
        return ($this->base->delMod($name));
    }
       
    public function saveMod($mod)
    {
        $newList=[];
        $addList=[];
        $delList=[];
        $newBaseMod=[];
        $foreignKeyList=[];
        
        $modName = $mod->getModName();
        $abstractMod = $mod->isAbstr();
        $inhertFromMod  = $mod->getInhNme();
        $attrTypList  = $mod->getAllAttrStateTyp();

        if ($inhertFromMod) { // could be done in Model but introduce test dependency on stateHandler
            $metaInh=$this->base->getMod($inhertFromMod);
            $ityp =$metaInh['attr_atyp'];
            $attrTypList= array_merge($ityp, $attrTypList);
            $foreignKeyList['id']=$inhertFromMod;
        }

        $newBaseMod['attr_atyp']= $attrTypList;
        $newBaseMod['attr_inhnme']=$inhertFromMod;
        $newBaseMod['meta']=$mod->getMeta();

        
        foreach ($attrTypList as $attr => $typ) {
            if ($typ == Mtype::M_REF) {
                $foreignKeyList[$attr] = $mod->getModRef($attr);
            }
        }
        
        if (! $this->base->existsMod($modName)) {
            $newList['attr_typ']=$attrTypList;
            if ($abstractMod) {
                $newList['attr_typ'] = ['CName'=>Mtype::M_STRING,];
            }
            $newList['attr_frg']=$foreignKeyList;
            if ($inhertFromMod) {
                return ($this->base->newModId($modName, $newBaseMod, false, $newList));
            }
            return ($this->base->newMod($modName, $newBaseMod, $newList));
        }
        
        $oldBaseMod = $this->base->getMod($modName);
        $ityp  = $oldBaseMod['attr_atyp'];

        $addList['attr_typ']= array_diff_assoc($attrTypList, $ityp);
        $addList['attr_frg']=$foreignKeyList;

        $delList['attr_typ']= array_diff_assoc($ityp, $attrTypList);
        $delList['attr_frg']=[];
        foreach ($delList['attr_typ'] as $attr => $typ) {
            if ($typ == Mtype::M_REF) {
                $delList['attr_frg'][$attr] = 'XX';
            }
        }
      
        $changed = false;
        $addChange=count($addList['attr_typ']);
        $delChange=count($delList['attr_typ']);
        if ($addChange or $delChange) {
            $changed = true;
            $this->logger->logLine(
                "$modName : Attribute Added:$addChange Attribute Deleted $delChange",
                [Log::TCLASS=>__CLASS__,LOG::TFUNCT=>__FUNCTION__,LOG::TLINE=>__LINE__]
            );
        }
        
        if ($abstractMod) {
            $res= $this->base->putMod($modName, $newBaseMod, [], []);
            if ($changed) {
                foreach ($this->base->getAllMod() as $smod) {
                    $baseMod = $this->base->getMod($smod);
                    if (isset($baseMod['attr_inhnme']) and $baseMod['attr_inhnme']==$modName) {
                        $baseMod['attr_frg']=$foreignKeyList;
                        $this->base->putMod($smod, $baseMod, $addList, $delList);
                    }
                }
            }
            return $res;
        }
        return ($this->base->putMod($modName, $newBaseMod, $addList, $delList));
    }
    

    
    public function restoreMod($mod)
    {
        $name = $mod->getModName();
        $values = $this->base->getMod($name);
        if (!$values) {
            return false;
        }
        $values = $values['meta'];
        return $mod->setMeta($values);
    }

    public function saveObj($mod)
    {
        $name = $mod->getModName();
        $values =$mod->getAllVal();
        $id = $mod->getId();
        $vnum=$mod->getVnum();
        if ($id == 0) {
            $abstr = $mod->getInhNme();
            if ($abstr) {
                $id = $this->base->newObj($abstr, ['CName'=>$name]);
                return ($this->base->newObjId($name, $values, $id));
            }
            return ($this->base->newObj($name, $values));
        }
        return ($this->base->putObj($name, $id, $vnum, $values));
    }

    public function restoreObj($mod)
    {
        $name = $mod->getModName();
        $id = $mod->getId();
        if ($id==0) {
            return false;
        }
        $values = $this->base->getObj($name, $id);
        if (!$values) {
            return false;
        }
        if ($mod->isAbstr()) {
            $name = $values['CName'];
            $mod->construct2($name, $id);
            return $id;
        }
        foreach ($values as $attr => $val) {
            $typ=$mod->getTyp($attr);
            $valn=Mtype::convertString($val, $typ);
            $mod->setVal($attr, $valn);
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
        $abstr = $mod->getInhNme();
        if ($abstr) {
            $this->base->delObj($abstr, $id);
        }
        return ($this->base->delObj($name, $id));
    }
    
    
    public function findObj($modN, $attr, $val)
    {
        return ($this->base->findObj($modN, $attr, $val));
    }

    public function findObjWheOp($model, $attrList, $opList, $valList, $ordList)
    {
        return (
            $this->base->findObjWheOp($model, $attrList, $opList, $valList, $ordList)
        );
    }
}
