<?php
namespace ABridge\ABridge;

use ABridge\ABridge\Handler;
use ABridge\ABridge\Model;

class ModBase
{
    private $base;
    private $abstr = [
        'attr_lst' => ['CName'],
        'attr_plst'=> ['CName'],
        'attr_typ' => ['CName'=>M_STRING,],
        'abstract' => true,
    ];
    
    public function __construct($base)
    {
        $this->base=$base;
    }

    public function eraseMod($mod)
    {
        $name = $mod->getModName();
        return ($this->base->delMod($name));
    }
    
    protected function getPeristAttr($mod)
    {
        $attrLst = $mod->getAllAttr();
        $res= [];
        foreach ($attrLst as $attr) {
            if (($mod->getTyp($attr) !=  M_CREF)
                and (! $mod->isEval($attr))
                and (! $mod->isTemp($attr))
                ) {
                $res[]=$attr;
            }
        }
        return $res;
    }
    
    public function saveMod($mod)
    {
        $name = $mod->getModName();
        $abst = $mod->isAbstr();
        $inh  = $mod->getInhNme();
        $typ  = $mod->getAllTyp();
        $plst = $this->getPeristAttr($mod);
        if ($abst) {
            $predef = $mod->getAllPredef();
            $plst   = array_diff($plst, $predef);
        }
        $meta=[];
        $frg=[];
        $meta['attr_lst']  = $mod->getAllAttr();
        $meta['attr_typ']  = $typ;
        $meta['attr_plst'] = $plst;
        $meta['attr_dflt'] = $mod->getAllDflt();
        $meta['attr_path'] = $mod->getAllRefParm();
        $meta['attr_bkey'] = $mod->getAllBkey();
        $meta['attr_mdtr'] = $mod->getAllMdtr();
        $meta['attr_ckey'] = $mod->getAllCkey();
        $meta['inhnme']    = $inh;
        $meta['isabstr']   = $abst;
        if ($inh) {
            $metaInh=$this->base->getMod($inh);
            $metaInh=$metaInh['meta'];
            $iplst=$metaInh['attr_plst'];
            $ityp =$metaInh['attr_typ'];
            $plst = array_merge($plst, $iplst);
            $typ= $typ+$ityp;
            $frg['id']=$inh;
        }
        $ameta['attr_plst'] = $plst;
        $ameta['attr_typ'] = $typ;
        $ameta['meta']=$meta;

        if ($abst) {
            $ameta=$this->abstr;
            $ameta['meta']=$meta;
        }
        
        foreach ($plst as $pattr) {
            if ($mod->getTyp($pattr) == M_REF) {
                $frg[$pattr] = $mod->getModRef($pattr);
            }
        }
        $ameta['attr_frg']=$frg;
        
        if (! $this->base->existsMod($name)) {
            if ($inh) {
                return ($this->base->newModId($name, $ameta, false));
            }
            return ($this->base->newMod($name, $ameta));
        }
        
        $values = $this->base->getMod($name);
        if ($abst) {
            $values=$values['meta'];
        }
        $iplst = [];
        if (isset($values['attr_plst'])) {
            $iplst = $values['attr_plst'];
        }
        $x = array_diff($plst, $iplst);
        $addList['attr_plst'] = $x;
        $addList['attr_typ'] = $typ;
        $x = array_diff($iplst, $plst);
        $delList['attr_plst'] = $x;
        $delList['attr_typ'] = $values['attr_typ'];

        foreach ($delList['attr_plst'] as $pattr) {
            if ($delList['attr_typ'][$pattr] == M_REF) {
                $frg[$pattr] = 'XX';
            }
        }
        $ameta['attr_frg']=$frg;
        if ($abst) {
            $res= $this->base->putMod($name, $ameta, [], []);
            foreach ($this->base->getAllMod() as $smod) {
                $svals = $this->base->getMod($smod);
                if (isset($svals['meta'])) {
                    $sval = $svals['meta'];
                    if (isset($sval['inhnme'])) {
                        if ($sval['inhnme']==$name) {
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
        return ($this->base->putMod($name, $ameta, $addList, $delList));
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
 //     } else {
 //        echo ' !!!! '.$name.' !!!! ';
        }
        $attrlist=[];
        $attrtype=[];
        $attrdflt=[];
        $attrpath=[];
        $attrbkey=[];
        $attrckey=[];
        $attrmdtr=[];
        $inherit = false;
        $abst = false;
        
        if (isset($values['isabstr'])) {
            $abst=$values['isabstr'];
            if ($abst) {
                $mod-> setAbstr();
            }
        }
        if (isset($values['inhnme'])) {
            $inherit=$values['inhnme'];
            if ($inherit) {
                $mod->setInhNme($inherit);
            }
        }
        if (isset($values['attr_lst'])) {
            $attrlist=$values['attr_lst'];
        }
        if (isset($values['attr_typ'])) {
            $attrtype=$values['attr_typ'];
        }
        if (isset($values['attr_path'])) {
            $attrpath=$values['attr_path'];
        }
        if (isset($values['attr_ckey'])) {
            $attrckey=$values['attr_ckey'];
        }
        if (isset($values['attr_bkey'])) {
            $attrbkey=$values['attr_bkey'];
        }
        if (isset($values['attr_mdtr'])) {
            $attrmdtr=$values['attr_mdtr'];
        }
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
        foreach ($attrckey as $ckey) {
            $mod->setCkey($ckey, true);
        }
        return true;
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
                $valn=convertString($val, $typ);
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
