<?php
namespace ABridge\ABridge\Mod;

/**
 * Model class file
 *
 * PHP Version 5.6
 *
 * @category PHP
 * @package  ABridge
 * @author   Pierre Lavency <pierrelavency@hotmail.com>
 * @link     No
 */
use ABridge\ABridge\Log\Logger;

use ABridge\ABridge\CstError;

use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\Mod\Mod;

use Exception;

/**
 * Model class
 *
 * Models are the business entities of the applications
 *
 * @category PHP
 * @package  ABridge
 * @author   Pierre Lavency <pierrelavency@hotmail.com>
 * @link     No
 */
class Model
{
    /**
     * @var integer The object's id.
     */
    protected $id;
    /**
     * @var string  The Model name.
     */
    protected $name ;
    /**
     * @var indicates if model has changed or not.
     */
    protected $modChgd;

    protected $attributeValues;
    protected $attributeTypes;

    /**
     * @var array The error logger.
     */
    protected $errLog;
    /**
     * @var array The state handler.
     */
    protected $stateHdlr=0;
    protected $trusted=false;
    protected $checkTrusted=false; // could be usefull !!
    protected $custom=false;
    protected $obj; // custom class object
    protected $abstrct;
    protected $inhObj;
    protected $inhNme;
    protected $vnum;
    
    protected $meta = [];


    const P_TMP = 'tmp';
    const P_EVL = 'eval';
    const P_DFT = 'dflt';
    const P_MDT = 'mdtr';
    const P_BKY = 'bkey';
    
    
    public static $propList= [self::P_TMP,self::P_EVL,Self::P_MDT, self::P_BKY];
    
    
    /**
    * Constructor
    */
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = 'construct'.$i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }
    /**
     * Constructor of a new object that does not exists (id is equal to 0).
     *
     * @param string $name The model name.
     *
     * @return void
     */
    public function construct1($name)
    {
        $this->init($name, 0);
        $this->vnum =0;
        $this->setValNoCheck("vnum", 0);
        $x = $this->stateHdlr;
        if ($x) {
            $x->restoreMod($this);
        }
        $this->modChgd=false;
        $this->initObj($name);
    }
    /**
     * Constructor of an existing object (id must be different from 0).
     *
     * @param string $name The model name.
     * @param int    $id   The object id.
     *
     * @return void
     */
    public function construct2($name, $id)
    {
        if (! Mtype::checkType($id, Mtype::M_INTP)) {
            throw new Exception(CstError::E_ERC011.':'.$id.':'.Mtype::M_INTP);
        }
        $this->init($name, $id);
        $idr=0;
        $x = $this->stateHdlr;
        if ($x) {
            $x->restoreMod($this);
            $this->trusted = true;
            $idr =$x->restoreObj($this);
            $this->trusted = false;
            if ($id != $idr) {
                throw new exception(CstError::E_ERC007.':'.$name.':'.$id);
            };
        }
        $this->modChgd=false;
        $this->vnum=$this->getVal('vnum');
        $this->initObj($name);
    }

    protected function initObj($name)
    {
        $cmodName = Mod::get()->getClassMod($name);
        if (is_null($cmodName)) {
            $this->obj= null;
            return null;
        }
        $this->custom=true;
        $this->obj = new $cmodName($this);
        $this->custom=false;
        return $this->obj;
    }
     /**
     * Initialise the attributes and the errologger
     *
     * @param string $name The model name.
     * @param int    $id   The object id.
     *
     * @return void
     */
    protected function init($name, $id)
    {
        if (! Mtype::checkIdentifier($name)) {
            throw new Exception(CstError::E_ERC010.':'.$name.':'.Mtype::M_ALPHA);
        }
        $this->name=$name;
        $this->initattr();
        $this->id=$id;
        $this->isNew = false;
        $logname = $name.'errLog';
        $this->errLog= new Logger($logname);
        $this->obj=null;
        $this->stateHdlr=Mod::get()->getStateHandler($name);
    }
    /**
     * Re/Initialise the attributes and their properties
     *
     * @return void
     */
    protected function initattr()
    {
        $this->meta['modname'] = $this->getModName();
        $this->meta['predef'] = array('id','vnum','ctstp','utstp');
        $this->attributeTypes = array(
            "id"    =>Mtype::M_ID,
            "vnum"  =>Mtype::M_INT,
            "ctstp"     =>Mtype::M_TMSTP,
            "utstp"     =>Mtype::M_TMSTP,
        );
        $this->attributeValues = array('vnum' => 0);
        $this->meta['dflt'] = [];
        $this->meta['param'] = [];
        $this->meta['ckey'] = [];
        $this->meta['protected'] = [];
        foreach (self::$propList as $prop) {
            $this->meta[$prop]=[];
        }

        $this->abstrct= false;
        $this->inhNme =false;
        
        $this->asCriteria = [];
        $this->modChgd=false;
    }
    
    public function initMod($bindings)
    {
        if ($this->obj) {
            $this->custom=true;
            $res = $this->obj->initMod($bindings);
            $this->custom=false;
            return $res;
        }
        throw new Exception(CstError::E_ERC061.':'.$this->getModName());
    }
    
    public function getMeta()
    {
        $modelMetaData=[];
        
        $modelMetaData['attr_typ']  = $this->getAllTyp();
        $modelMetaData['attr_dflt'] = $this->getAllDflt();
        $modelMetaData['attr_path'] = $this->getAllParm();
        $modelMetaData['attr_ckey'] = $this->getAllCkey();
        $modelMetaData['inhnme']    = $this->getInhNme();
        $modelMetaData['isabstr']   = $this->isAbstr();
        foreach (self::$propList as $prop) {
            $modelMetaData[$prop]= $this->getallProp($prop);
        }

        return $modelMetaData;
    }
    
    public function setMeta($modelMetaData)
    {
        $tagList=[
                'attr_typ','inhnme','isabstr',
                'attr_dflt','attr_path','attr_ckey',
                 self::P_TMP,self::P_EVL,self::P_BKY,self::P_MDT
                
        ];
        foreach ($tagList as $tag) {
            if (!isset($modelMetaData[$tag])) {
                throw new Exception(CstError::E_ERC047.':'.$this->getModName().':'.$tag);
            }
        }

        $abst=$modelMetaData['isabstr'];
        if ($abst) {
            $this-> setAbstr();
        }
        $inherit=$modelMetaData['inhnme'];
        if ($inherit) {
            $this->setInhNme($inherit);
        }
        $attrTypeList=$modelMetaData['attr_typ'];
        $attrpath=$modelMetaData['attr_path'];
        $attrckey=$modelMetaData['attr_ckey'];
        $attrdflt=$modelMetaData['attr_dflt'];
        $predef = $this->getAllPredef();
        foreach ($attrTypeList as $attr => $typ) {
            if (! in_array($attr, $predef)) {
                $path=0;
                if (array_key_exists($attr, $attrpath)) {
                    $path=$attrpath[$attr];
                }
                $this->addAttr($attr, $typ, $path);
            }
        }
        foreach ($attrdflt as $attr => $val) {
            $this->setDflt($attr, $val);
        }
        foreach ($attrckey as $ckey) {
            $this->setCkey($ckey, true);
        }
        foreach (self::$propList as $prop) {
            $attrList  = $modelMetaData[$prop];
            foreach ($attrList as $attr) {
                $this->setProp($attr, $prop);
            }
        }
        return true;
    }

    public function addAttr($attr, $typ, $path = 0)
    {
        $x= $this->existsAttr($attr);
        if ($x) {
            $this->errLog->logLine(CstError::E_ERC003.':'.$attr);
            return false;
        }
        if (!Mtype::isMtype($typ)) {
            $this->errLog->logLine(CstError::E_ERC004.':'.$typ);
            return false;
        }
        if ($typ == Mtype::M_REF or $typ == Mtype::M_CREF or $typ == Mtype::M_CODE) {
            if (!$path) {
                $this->errLog->logLine(CstError::E_ERC008.':'.$attr.':'.$typ);
                return false;
            }
            if (! $this->setParm($attr, $typ, $path)) {
                return false;
            }
        }
        
        $this->attributeTypes[$attr]=$typ;
        $this->modChgd=true;
        return true;
    }
    
    public function delAttr($attr)
    {
        $x= $this->existsAttrLocal($attr);
        if (!$x) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        if (in_array($attr, $this->meta['predef'])) {
            $this->errLog->logLine(CstError::E_ERC001.':'.$attr);
            return false;
        }
        foreach ($this->meta['ckey'] as $attrLst) {
            if (in_array($attr, $attrLst)) {
                $this->errLog->logLine(CstError::E_ERC030.':'.$attr);
                return false;
            }
        }
        if (isset($this->attributeTypes[$attr])) {
            unset($this->attributeTypes[$attr]);
        }
        if (isset($this->meta['param'][$attr])) {
            unset($this->meta['param'][$attr]);
        }
        if (isset($this->meta['dflt'][$attr])) {
            unset($this->meta['dflt'][$attr]);
        }
        $key = array_search($attr, $this->meta['protected']);
        if ($key!==false) {
            unset($this->meta['protected'][$key]);
        }
        foreach (self::$propList as $prop) {
            $key = array_search($attr, $this->meta[$prop]);
            if ($key!==false) {
                unset($this->meta[$prop][$key]);
            }
        }
        $this->modChgd=true;
        return true;
    }

    protected function setParm($attr, $typ, $parm)
    {
        $path=explode('/', $parm);
        $root = $path[0];
        if (($root != "" )) {
            $this->errLog->logLine(CstError::E_ERC020.':'.$attr.':'.$parm);
            return false;
        }
        $c = count($path)-1;
        
        switch ($typ) {
            case Mtype::M_REF:
                if ($c !=1) {
                    $this->errLog->logLine(CstError::E_ERC020.':'.$attr.':'.$parm);
                    return false;
                }
                break;
            case Mtype::M_CREF:
                if ($c !=2) {
                    $this->errLog->logLine(CstError::E_ERC020.':'.$attr.':'.$parm);
                    return false;
                }
                break;
            case Mtype::M_CODE:
                if ($c >3 or $c<1) {
                    $this->errLog->logLine(CstError::E_ERC020.':'.$attr.':'.$parm);
                    return false;
                }
                break;
        }
        $this->meta['param'][$attr]=$parm;
        return true;
    }
        
    public function getParm($attr)
    {
        if (isset($this->meta['param'][$attr])) {
            return $this->meta['param'][$attr] ;
        }
        $abstr = $this->getInhObj();
        if (! is_null($abstr)) {
            return $abstr->getParm($attr);
        }
        return 0;
    }
       
    private function getAllParm()
    {
        return $this->meta['param'];
    }
    
    public function isPredef($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        return (in_array($attr, $this->meta['predef']));
    }
    
    public function getAllPredef()
    {
        return $this->meta['predef'];
    }
    
    public function setDflt($attr, $val)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        };
        $this->meta['dflt'][$attr]=$val;
        $this->modChgd=true;
        return true;
    }
    
    public function getDflt($attr)
    {
        foreach ($this->meta['dflt'] as $x => $val) {
            if ($x==$attr) {
                return $val;
            }
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->getDflt($attr);
        }
        return null;
    }
    
    private function getAllDflt()
    {
        return $this->meta['dflt'];
    }
    
    public function setCkey($attrLst, $val)
    {
        if (! is_array($attrLst)) {
            $this->errLog->logLine(CstError::E_ERC029);
            return false;
        }
        foreach ($attrLst as $attr) {
            if (! $x= $this->existsAttr($attr)) {
                $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
                return false;
            };
        }
        if (!$this->stateHdlr) {
            $this->errLog->logLine(CstError::E_ERC017);
            return false;
        }
        if ($val) {
            if (!in_array($attrLst, $this->meta['ckey'])) {
                $this->meta['ckey'][]=$attrLst;
            }
        }
        if (! $val) {
            $key = array_search($attrLst, $this->meta['ckey']);
            if ($key!==false) {
                unset($this->meta['ckey'][$key]);
            }
        }
        $this->modChgd=true;
        return true;
    }
       
    private function getAllCkey()
    {
        return $this->meta['ckey'];
    }
    
    public function setProp($attr, $prop)
    {
        if (in_array($attr, $this->meta[$prop])) {
            return true;
        }
        $this->meta[$prop][]=$attr;
        $this->modChgd=true;
        return true;
    }
    
    public function unsetProp($attr, $prop)
    {
        $key = array_search($attr, $this->meta[$prop]);
        if ($key!==false) {
            unset($this->meta[$prop][$key]);
        }
        $this->modChgd=true;
        return true;
    }
    
    public function isProp($attr, $prop)
    {
        $res = in_array($attr, $this->meta[$prop]);
        if ($res) {
            return $res;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->isProp($attr, $prop);
        }
        return ($res);
    }
    
    protected function getallProp($prop)
    {
        return $this->meta[$prop];
    }
    
    protected function getListProp($prop)
    {
        $listProp= $this->meta[$prop];
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            $inheritedList=  $abstr->getallProp($prop);
            $listProp=array_unique(array_merge($listProp, $inheritedList));
        }
        return $listProp;
    }
    

    public function getModName()
    {
        return $this->name;
    }
    
    public function getId()
    {
        return $this->id;
    }
           
    public function getVnum()
    {
        return $this->vnum;
    }
    
    public function getCobj()
    {
        return $this->obj;
    }

    public function getStateHandler()
    {
        return $this->stateHdlr;
    }
    
    public function getAbstrNme()
    {
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->getModName();
        }
        return null;
    }
    
    public function isAbstr()
    {
        return $this->abstrct;
    }
    
    public function setAbstr()
    {
        $this->abstrct=true;
        return true;
    }

    public function getInhNme()
    {
        return $this->inhNme;
    }

    public function getInhObj()
    {
        return $this->inhObj;
    }

    public function setInhNme($name)
    {
        $this->inhNme=$name;
        $obj = new Model($name);
        if ($obj->isAbstr()) {
            $this->inhObj=$obj;
            return true;
        }
        return false;
    }

    public function getErrLog()
    {
        return $this->errLog;
    }
    
    public function getErrLine()
    {
        $c = $this->errLog->logSize();
        if ($c) {
            $l = $this->errLog->getLine($c-1);
            return $l;
        }
        return false;
    }
    
    public function isErr()
    {
        $c=$this->errLog->logSize();
        if ($c) {
            return true;
        }
        return false;
    }

    public function getAllAttr()
    {
        return array_keys($this->attributeTypes);
    }
    
    private function isStateType($attr)
    {
        if (($this->getTyp($attr) !=  Mtype::M_CREF)
                and (! $this->isProp($attr, self::P_TMP))) {
                    return true;
        }
    }
    
    public function getAttrList()
    {
        return array_keys($this->getAttrTypList());
    }
        
    public function getAllAttrStateTyp()
    {
        $allStateTyp= [];
        foreach ($this->getAllTyp() as $attribute => $type) {
            if ($this->isStateType($attribute)) {
                $allStateTyp[$attribute]=$type;
            }
        }
        return $allStateTyp;
    }
        
    public function getAttrTypList()
    {
        $attributes=[];
        $ownAttributes = $this->attributeTypes;
        $abstractClass = $this->getInhObj();
        if (is_null($abstractClass)) {
            return $ownAttributes;
        }
        $inhertAttributes = $abstractClass->getAllTyp();
        $attributes = array_merge($inhertAttributes, $ownAttributes);
        return $attributes;
    }

    public function getAllTyp()
    {
        return $this->attributeTypes;
    }

    public function getAllVal()
    {
        $res=[];
        foreach ($this->attributeValues as $attr => $val) {
            if (! $this->isProp($attr, self::P_TMP)) {
                $res[$attr]=$val;
            }
        }
        return $res;
    }

    public function getTyp($attr)
    {
        if (isset($this->attributeTypes[$attr])) {
            return $this->attributeTypes[$attr];
        };
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            $typ= $abstr->getTyp($attr);
            if ($typ) {
                return $typ;
            }
        }
        $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
        return false;
    }

    public function existsAttr($attr)
    {
        if (isset($this->attributeTypes[$attr])) {
            return true;
        } ;
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->existsAttr($attr);
        }
        return false;
    }
    
    protected function existsAttrLocal($attr)
    {
        return isset($this->attributeTypes[$attr]);
    }

    public function getRef($attr)
    {
        $mod = $this->getModRef($attr);
        if (! $mod) {
            throw new Exception($this->getErrLine());
        }
        $id = $this->getVal($attr);
        if (is_null($id)) {
            return null;
        }
        $res=new Model($mod, $id);
        return ($res);
    }

    protected function setRef($attr, $mod)
    {
        $modA = $this->getModRef($attr);
        $modN = $mod->getModName();
        if ($modA != $modN) {
            $abstr = $mod->getInhObj();
            if (is_null($abstr) or $modA != $abstr->getModName()) {
                throw new Exception(CstError::E_ERC033.':'.$attr.':'.$modA.':'.$modN);
            }
        }
        $id = $mod->getId();
        if ($id) {
            $this->setValNoCheck($attr, $id);
        }
    }

    public function getModCref($attr)
    {
        $res = $this->getCrefMod($attr);
        if (!$res) {
            return false;
        }
        return $res[1];
    }

    private function getCrefMod($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        if ($this->getTyp($attr)!= Mtype::M_CREF) {
            $this->errLog->logLine(CstError::E_ERC027.':'.$attr);
            return false;
        }
        $path=$this->getParm($attr);
        $patha=explode('/', $path);
        return ($patha);
    }
  
    public function isOneCref($attr)
    {
        $patha=$this->getCrefMod($attr);
        if (!$patha) {
            throw new Exception($this->getErrLine());
        }
        $m=new Model($patha[1]);
        return $m->isProp($patha[2], self::P_BKY);
    }
    
    public function newCref($attr)
    {
        $patha=$this->getCrefMod($attr);
        if (!$patha) {
            throw new Exception($this->getErrLine());
        }
        $m=new Model($patha[1]);
        $m->setRef($patha[2], $this);
        $m->protect($patha[2]);
        return $m;
    }

    public function getCref($attr, $id)
    {
        $patha=$this->getCrefMod($attr);
        if (!$patha) {
            throw new Exception($this->getErrLine());
        }
        $res=new Model($patha[1], $id);
        $rid=$res->getVal($patha[2]);
        if ($rid != $this->getId()) {
            throw new Exception(CstError::E_ERC032.':'.$attr.':'.$id);
        }
        $res->protect($patha[2]);
        return ($res);
    }

    public function getCode($attr, $id)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        $typ = $this->getTyp($attr);
        if ($typ != Mtype::M_CODE and $typ != Mtype::M_REF) {
            $this->errLog->logLine(CstError::E_ERC028.':'.$attr);
            return false;
        }
        $path=$this->getParm($attr);
        $patha=explode('/', $path);
        if ($typ == Mtype::M_CODE) {
            $c = count($patha);
            if ($c == 4) {
                $m = new Model($patha[1], (int) $patha[2]);
                $res=$m->getCref($patha[3], $id);
                return ($res);
            } elseif ($c == 3) {
                return $this->getCref($patha[2], $id) ;
            }
            return new Model($patha[1], $id);
        }
        return new Model($this->getModRef($attr), $id);
    }

    public function protect($attr)
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        };
        if (!in_array($attr, $this->meta['protected'])) {
            $this->meta['protected'][]=$attr;
        }
        return true;
    }
    
    public function isProtected($attr)
    {
        $res = in_array($attr, $this->meta['protected']);
        return ($res);
    }
    
    public function isOptl($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        if ($this->isPredef($attr)) {
            return false;
        }
        if ($this->isProp($attr, self::P_EVL)) {
            return false;
        }
        if ($this->isProp($attr, self::P_MDT)) {
            return false;
        }
        $typ = $this->getTyp($attr);
        if ($typ == Mtype::M_CREF) {
            return false;
        }
        return true;
    }

    public function isModif($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        $res = false;
        if ($this->isProp($attr, self::P_MDT) or $this->isOptl($attr)) {
            $res= true;
        }
        if ($this->isProtected($attr)) { // shoul be first ?
            $res = false;
        }
        return $res;
    }

    public function isSelect($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        if ($attr == 'id') {
            return true;
        }
        if ($this->isProp($attr, self::P_TMP)) {
            return false;
        }
        if ($this->isProp($attr, self::P_EVL)) {
            return true;
        }
        return $this->isModif($attr);
    }
    
    public function setCriteria($attrL, $opL, $valL)
    {
        foreach ($attrL as $attr) {
            if (! $x= $this->existsAttr($attr)) {
                $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
                return false;
            };
        }
        $this->asCriteria=[$attrL,$opL,$valL];
        return true;
    }
     
    public function select()
    {
        $result = [];
        $res = $this->asCriteria;
        if ($res == []) {
            return $result;
        }

        foreach ($this->getAllAttr() as $attr) {
            if ($this->isProtected($attr)) {
                $val = $this->getVal($attr);
                $key = array_search($attr, $res[0]);
                if ($key!==false) {
                    $res[2][$key]=$val;
                } else {
                    $res[2][]=$val;
                    $res[0][]=$attr;
                }
                $res[1][$attr]='=';
            }
        }

        $mod=$this->getModName();
        $result=$this->findObjWhe($mod, $res[0], $res[1], $res[2]);
        return $result;
    }


    public function getVal($attr)
    {
        if ((! is_null($this->obj)) and (! $this->custom)) {
            $this->custom=true;
            $res = $this->obj->getVal($attr);
            $this->custom=false;
            return $res;
        } else {
            return $this->getValN($attr);
        }
    }
     
    public function getValN($attr)
    {
        if ($attr == 'id') {
            return $this->getId();
        }
        $type=$this->getTyp($attr);
        if (! $type) {
            return false;
        }
        if ($type == Mtype::M_CREF) {
            $path = $this->getParm($attr);
            $patha=explode('/', $path);
            $res=$this->findObjAttr($patha[1], $patha[2], $this->getId());
            return $res;
        }
        foreach ($this->attributeValues as $x => $val) {
            if ($x==$attr) {
                return $val;
            }
        }
        return null;
    }

    protected function setValNoCheck($attr, $val)
    {
        $this->attributeValues[$attr]=$val;
        return true;
    }

    public function setVal($attr, $val)
    {
        if ((! is_null($this->obj))) {
            $res = $this->obj->setVal($attr, $val);
            return $res;
        } else {
            return $this->setValN($attr, $val);
        }
    }
    
    public function setValN($attr, $val)
    {
        $type=$this->getTyp($attr);
        if (! $type) {
            return false;
        }
        if ($this->isAbstr()) {
            $this->errLog->logLine(CstError::E_ERC045);
            return false;
        }
        $check=!($this->trusted);
        if (in_array($attr, $this->meta['predef']) and $check) {
            $this->errLog->logLine(CstError::E_ERC001.':'.$attr);
            return false;
        };
        $check= ($check or $this->checkTrusted);

        if ($this->isProp($attr, self::P_EVL)
            and (! $this->custom)
            and !($this->trusted)) {
            $this->errLog->logLine(CstError::E_ERC042.':'.$attr);
            return false;
        }
        // protected
        if ($this->isProtected($attr)) {
            $this->errLog->logLine(CstError::E_ERC034.':'.$attr);
            return false;
        }
        // type checking
        if ($type ==Mtype::M_CREF) {
            $this->errLog->logLine(CstError::E_ERC013.':'.$attr);
            return false;
        }
        $btype=Mtype::baseType($type);
        $res=Mtype::checkType($val, $btype);
        if (! $res) {
            $this->errLog->logLine(CstError::E_ERC005.':'.$attr.':'.$val.':'.$btype);
            return false;
        }
        // ref checking
        if ($check and $type == Mtype::M_REF) {
            $res = $this-> checkRef($attr, $val);
            if (! $res) {
                return false;
            }
        }
        // code values
        if ($check and $type == Mtype::M_CODE) {
            $res = $this-> checkCode($attr, $val);
            if (! $res) {
                $this->errLog->logLine(CstError::E_ERC016.':'.$attr.':'.$val);
                return false;
            }
        }
        return ($this->setValNoCheck($attr, $val));
    }

    protected function checkBkey($attr, $val)
    {
        if (is_null($val)) {
            return true;
        }
        $res=$this->findObjAttr($this->getModName(), $attr, $val);
        if ($res == []) {
            return true;
        }
        $id=array_pop($res);
        if ($id == $this->getId()) {
            return true;
        }
        return false;
    }

    public function getBkey($attr, $val)
    {
        if (!$this->isProp($attr, self::P_BKY)) {
            throw new Exception(CstError::E_ERC056.':'.$attr);
        };
        $res=$this->findObjAttr($this->getModName(), $attr, $val);
        if ($res == []) {
            return null;
        }
        $id=array_pop($res);
        $res = new Model($this->getModName(), $id);
        return $res;
    }
    
    protected function findObjAttr($mod, $attr, $val)
    {
        if ($mod == $this->getModName()) {
            $hdl = $this->stateHdlr;
        } else {
            $obj = new Model($mod);
            $hdl = $obj->stateHdlr;
        }
        if (!$hdl) {
            throw new exception(CstError::E_ERC017.':'.$mod);
        }
        $res=$hdl->findObj($mod, $attr, $val);
        return $res;
    }


    protected function findObjWhe($mod, $attrLst, $opLst, $valLst)
    {
        if ($mod == $this->getModName()) {
            $hdl = $this->stateHdlr;
        } else {
            $obj = new Model($mod);
            $hdl = $obj->stateHdlr;
        }
        if (!$hdl) {
            throw new exception(CstError::E_ERC017.':'.$mod);
        }
        $res=$hdl->findObjWheOp($mod, $attrLst, $opLst, $valLst);
        return $res;
    }
    
    protected function checkCkey($attrLst)
    {
        $valLst=[];
        $res=[];
        foreach ($attrLst as $attr) {
            $val = $this->getVal($attr);
            if (is_null($val)) {
                return true;
            }
            $valLst[]=$val;
        }
        $res=$this->findObjWhe($this->getModName(), $attrLst, [], $valLst);
        if ($res == []) {
            return true;
        }
        $id=array_pop($res);
        if ($id == $this->getId()) {
            return true;
        }
        return false;
    }

    protected function checkCode($attr, $val)
    {
        if (is_null($val)) {
            return true;
        }
        $vals = $this->getValues($attr);
        if (!$vals) {
            return false;
        }
        $res = in_array($val, $vals);
        return $res;
    }

 
    public function getModRef($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(CstError::E_ERC002.':'.$attr);
            return false;
        }
        if ($this->getTyp($attr)!= Mtype::M_REF) {
             $this->errLog->logLine(CstError::E_ERC026.':'.$attr);
            return false;
        }
        $path=$this->getParm($attr);
        $patha=explode('/', $path);
        return ($patha[1]);
    }
       /**
     * Get the possible values of a code attribute.
     *
     * @param string $Attr the attribute.
     *
     * @return array
     */
    public function getValues($attr)
    {
        if ((! is_null($this->obj)) and (! $this->custom)) {
            $this->custom=true;
            $res = $this->obj->getValues($attr);
            $this->custom=false;
            return $res;
        } else {
            return $this->getValuesN($attr);
        }
    }
     
    public function getValuesN($attr)
    {
        $r=$this->getTyp($attr);
        if (!$r) {
            return false;
        }
        if ($r != Mtype::M_CODE and $r != Mtype::M_REF) {
            $this->errLog->logLine(CstError::E_ERC015.':'.$attr.':'.$r);
            return false;
        }
        if ($r == Mtype::M_CODE) {
            $r=$this->getParm($attr);
            $apath = explode('/', $r);
            $c = count($apath);
            if ($c > 3) {
                $mod = new Model($apath[1], (int) $apath[2]);
                $val = $mod->getVal($apath[3]);
            } elseif ($c==2) {
                $val=$this->findObjWhe($apath[1], [], [], []);
            } elseif ($c==3) {
                $val  = [];
                if ($this->getid()) {
                    $val = $this->getVal($apath[2]);
                }
            }
        }
        if ($r == Mtype::M_REF) {
            $mod = $this->getModRef($attr);
            $val=$this->findObjWhe($mod, [], [], []);
        }
        return $val;
    }

    protected function checkRef($attr, $id)
    {
        if (is_null($id)) {
            return true;
        }

        $mod = $this->getModRef($attr);
        try {
            $res = new Model($mod, $id);
        } catch (Exception $e) {
            $this->errLog->logLine($e->getMessage());
            return false;
        }
        return true;
    }

    protected function checkMdtr($attrList)
    {
        foreach ($attrList as $attr) {
            if (array_key_exists($attr, $this->attributeValues)) {
                $val = $this->getVal($attr);
                if (is_null($val)) {
                    $this->errLog->logLine(CstError::E_ERC019.':'.$attr);
                    return false;
                }
            } else {
                $this->errLog->logLine(CstError::E_ERC019.':'.$attr);
                return false;
            }
        }
        return true;
    }

    protected function checkBkeyList($keyList)
    {
        foreach ($keyList as $attr) {
            $val =$this->getVal($attr);
            $res=$this->checkBkey($attr, $val);
            if (! $res) {
                $this->errLog->logLine(CstError::E_ERC018.':'.$attr.':'.$val);
                return false;
            }
        }
        return true;
    }
    
    protected function checkCkeyList($ckeyList)
    {
        foreach ($ckeyList as $attrLst) {
            $res=$this->checkCkey($attrLst);
            if (!$res) {
                $l=implode(':', $attrLst);
                $this->errLog->logLine(CstError::E_ERC031.':'.$l);
                return false;
            }
        }
        return true;
    }
     
    public function save()
    {
        if ((! is_null($this->obj)) and (! $this->custom)) {
            $this->custom=true;
            $res = $this->obj->save();
            $this->custom=false;
        } else {
            $res= $this->saveN();
        }
        if ($res) {
            if (! $this->getId()) {
                $this->isNew=true;
            }
            $this->id=$res;
            $this->vnum++;
        }
        return $res;
    }
    
    public function checkVers($vnum)
    {
        $n=$this->vnum;
        $res=($vnum === $n);
        if (!$res) {
            $this->errLog->logLine(CstError::E_ERC062.":$n");
        }
        return $res;
    }
 
    public function saveN()
    {
        if ($this->isAbstr()) {
            $this->errLog->logLine(CstError::E_ERC044);
            return false;
        }
        if (! $this->stateHdlr) {
            $this->errLog->logLine(CstError::E_ERC006.':'.$this->getModName());
            return false;
        }
        if ($this->modChgd) {
            $this->errLog->logLine(CstError::E_ERC024);
            return false;
        }

        $attrL= $this->getListProp(self::P_MDT);
        if (!$this->checkMdtr($attrL)) {
            return false;
        }
        $keyList=$this->getListProp(self::P_BKY);
        if (!$this->checkBkeyList($keyList)) {
            return false;
        }
        $abstr = $this->getInhObj();
        
        $ckeyList=$this->getAllCkey();
        if (!$this->checkCkeyList($ckeyList)) {
            return false;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            $ckeyList= $abstr->getAllCkey();
            if (!$this->checkCkeyList($ckeyList)) {
                return false;
            }
        }

        $n=$this->vnum;
        $n++;
        $this->setValNoCheck('vnum', $n);
        if (! $this->getId()) {
            $this->setValNoCheck('ctstp', date(Mtype::M_FORMAT_T));
        }
        $this->setValNoCheck('utstp', date(Mtype::M_FORMAT_T));

        $res=$this->stateHdlr->saveObj($this);
        
        if (!$res and $this->getId()) {
            $this->errLog->logLine(CstError::E_ERC062);
        }
        
        return $res;
    }

    public function isDel()
    {
        if ($this->isAbstr()) {
            return false;
        }
        foreach ($this->getAllAttr() as $attr) {
            $typ = $this->getTyp($attr);
            if ($typ == Mtype::M_CREF) {
                $res=$this->getVal($attr);
                if (count($res)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function delet()
    {
        if ((! is_null($this->obj)) and (! $this->custom)) {
            $this->custom=true;
            $res = $this->obj->delet();
            $this->custom=false;
            return $res;
        } else {
            return $this->deletN();
        }
    }
        
    public function deletN()
    {
        if ($this->isAbstr()) {
            $this->errLog->logLine(CstError::E_ERC044);
            return false;
        }
        if (! $this->stateHdlr) {
            $this->errLog->logLine(CstError::E_ERC006.':'.$this->getModName());
            return false;
        }
        if (!$this->isDel()) {
            $this->errLog->logLine(CstError::E_ERC052);
            return false;
        }
        try {
            $res=$this->stateHdlr->eraseObj($this);
        } catch (Exception $e) {
            $this->errLog->logLine(CstError::E_ERC052.':'.$e->getMessage());
            return false;
        }
        if ($res) {
            $this->id=0;
            $this->vnum=0;
            $this->isNew=false;
        }
        return $res;
    }
 
    public function deleteMod()
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(CstError::E_ERC006.':'.$this->getModName());
            return false;
        }
        $res=$this->stateHdlr->eraseMod($this);
        $this->initattr();
        $this->id=0;
        $this->vnum=0;
        $this->isNew=false;
        return $res;
    }
 
    public function saveMod()
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(CstError::E_ERC006.':'.$this->getModName());
            return false;
        }
        $res=$this->stateHdlr->saveMod($this);
        if ($res) {
            $this->modChgd=false;
        }
        return $res;
    }
}
