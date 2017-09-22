<?php
namespace ABridge\ABridge\Mod;

use ABridge\ABridge\Mod\Model;

class ModBase
{
    private $base;
    private $abstr = [
        'attr_lst' => ['CName'],
        'attr_plst'=> ['CName'],
        'attr_typ' => ['CName'=>Mtype::M_STRING,],
        'abstract' => true,
    ];
    
    public function __construct($base)
    {
        $this->base=$base;
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
        $modName = $mod->getModName();
        $abstractMod = $mod->isAbstr();
        $inhertFromMod  = $mod->getInhNme();
        $typ  = $mod->getAllTyp();
        $plst = $mod->getAllPeristAttr();

        if ($abstractMod) { // Why ?
            $predef = $mod->getAllPredef();
            $plst   = array_diff($plst, $predef);
        }
        $frg=[];
        
        $modelMetaData=$mod->getMeta();

        if ($inhertFromMod) {
            $metaInh=$this->base->getMod($inhertFromMod);
            $iplst=$metaInh['attr_aplst'];
            $ityp =$metaInh['attr_atyp'];
            $plst = array_merge($plst, $iplst);
            $typ= $typ+$ityp;
            $frg['id']=$inhertFromMod;
        }
        
        $newBaseMod['attr_plst'] = $plst;
        $newBaseMod['attr_typ'] =  $typ;
        if ($abstractMod) {
            $newBaseMod=$this->abstr;
        }
        $newBaseMod['meta']=$modelMetaData;
        $newBaseMod['attr_aplst']= $plst;
        $newBaseMod['attr_atyp']= $typ;
        
        foreach ($plst as $persistAttribute) {
            if ($mod->getTyp($persistAttribute) == Mtype::M_REF) {
                $frg[$persistAttribute] = $mod->getModRef($persistAttribute);
            }
        }
        
        $newBaseMod['attr_frg']=$frg;
        
        if (! $this->base->existsMod($modName)) {
            if ($inhertFromMod) {
                return ($this->base->newModId($modName, $newBaseMod, false));
            }
            return ($this->base->newMod($modName, $newBaseMod));
        }
        
        $oldBaseMod = $this->base->getMod($modName);
        $iplst = $oldBaseMod['attr_aplst'];
        $ityp  = $oldBaseMod['attr_atyp'];
       
        $x = array_diff($plst, $iplst);
        $addList['attr_plst'] = $x;
        $addList['attr_typ'] = $typ;
        $x = array_diff($iplst, $plst);
        $delList['attr_plst'] = $x;
        $delList['attr_typ'] = $ityp;

        foreach ($delList['attr_plst'] as $persistAttribute) {
            if ($delList['attr_typ'][$persistAttribute] == Mtype::M_REF) {
                $frg[$persistAttribute] = 'XX';
            }
        }
        
        $newBaseMod['attr_frg']=$frg;
        if ($abstractMod) {
            $res= $this->base->putMod($modName, $newBaseMod, [], []);
            foreach ($this->base->getAllMod() as $smod) {
                $svals = $this->base->getMod($smod);
                if (isset($svals['meta'])) {
                    $sval = $svals['meta'];
                    if (isset($sval['inhnme'])) {
                        if ($sval['inhnme']==$modName) {
                            $svals['attr_frg']=$frg;
                            $this->base->putMod(
                                $smod,
                                $svals,
                                $addList,
                                $delList
                            );
                        }
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
        if (isset($values['meta'])) {
            $values = $values['meta'];
        } else {
            throw new Exception(CstError::E_ERC047.':'.$mod);
        }
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
            } else {
                return ($this->base->newObj($name, $values));
            }
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
            if ($mod->existsAttr($attr)) {
                $typ=$mod->getTyp($attr);
                $valn=Mtype::convertString($val, $typ);
                $mod->setVal($attr, $valn);
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

    public function findObjWheOp($model, $attrList, $opList, $valList)
    {
        return (
            $this->base->findObjWheOp($model, $attrList, $opList, $valList)
        );
    }
 
 /*
    public function copyMod($mod,$base) {
        $meta = $this->base->getMod($mod);
        $inh = false;
        if (isset($meta['inhnme'])) {
            $inh = $meta['inhnme']; 
        }
        if ($inh) {
            $base->newModId($mod, $meta, false);
        } else {
            $base->newMod($mod, $meta);
        }
        
    }
    
    */
}
