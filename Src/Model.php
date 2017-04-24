<?php
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
require_once 'Logger.php';
require_once 'CstError.php';
require_once 'Type.php';
require_once "Handler.php";
define('M_P_EVAL', "M_P_EVAL");
define('M_P_EVALP', "M_P_EVALP");

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
    /**
    /**
     * @var array The list of predefined attributes.
     */
    protected $attrPredef;
    /**
     * @var array The list of attributes.
     */
    protected $attrLst;
    /**
     * @var array The list of types associated to attributes.
     */
    protected $attrTyp;
    /**
     * @var array The list of values associated to attributes.
     */
    protected $attrVal;
    /**
     * @var array The list of path associated to reference attributes.
     */
    protected $refParm ;
    /**
     * @var array The list of types business key attributes.
     */
    protected $attrBkey;
    /**
     * @var array The list of mandatory attributes.
     */
    protected $attrMdtr;
    /**
     * @var array The list of attribute default values.
     */
    protected $attrDflt;
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
    protected $attrCkey;
    protected $attrProtected;
    protected $asCriteria;
    protected $attrEval;
    protected $attrEvalP;
    protected $obj; // custom class object
    protected $abstrct;
    protected $inhObj;
    protected $inhNme;

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
        $this->setValNoCheck("ctstp", date(M_FORMAT_T));
        $this->setValNoCheck("vnum", 1);
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
        if (! checkType($id, M_INTP)) {
            throw new Exception(E_ERC011.':'.$id.':'.M_INTP);
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
                throw new exception(E_ERC007.':'.$name.':'.$id);
            };
        }
        $this->modChgd=false;
        $this->initObj($name);
    }

    protected function initObj($name)
    {
        if (! class_exists($name)) {
            $this->obj= null;
            return null;
        }
        $this->custom=true;
        $this->obj = new $name($this);
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
        if (! checkIdentifier($name)) {
            throw new Exception(E_ERC010.':'.$name.':'.M_ALPHA);
        }
        $this->initattr();
        $this->id=$id;
        $this->name=$name;
        $logname = $name.'errLog';
        $this->errLog= new Logger($logname);
        $this->obj=null;
        $this->stateHdlr=getStateHandler($name);
    }
    /**
     * Re/Initialise the attributes and their properties
     *
     * @return void
     */
    protected function initattr()
    {
        $this->modChgd=false;
        $this->attrPredef = array('id','vnum','ctstp','utstp');
        $this->attrLst = array('id','vnum','ctstp','utstp');
        $this->attrTyp = array(
            "id"=>M_ID,
            "vnum"=>M_INT,
            "ctstp"=>M_TMSTP,
            "utstp"=>M_TMSTP,
        );
        $this->attrVal = array('vnum' => 0);
        $this->attrDflt = [];
        $this->refParm = [];
        $this->attrBkey = [];
        $this->attrMdtr = [];
        $this->attrCkey = [];
        $this->attrProtected = [];
        $this->asCriteria = [];
        $this->attrEval=[];
        $this->attrEvalP=[];
        $this->abstrct= false;
        $this->inhNme=false;
    }
    /**
     * Returns the errorlogger.
     *
     * @return Logger
     */
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
    /**
     * Returns the Model Name.
     *
     * @return string
     */
    public function getModName()
    {
        return $this->name;
    }

    public function getAbstrNme()
    {
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->getModName();
        }
        return null;
    }


    /**
     * Returns the Object Id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
    /**
     * Returns the list of attributes of a Model.
     *
     * @return array
     */
    public function getAllAttr()
    {
        return $this->attrLst;
    }

    public function getAttrList()
    {
        $list = $this->getAllAttr();
        $abstr = $this->getInhObj();
        if (is_null($abstr)) {
            return $list;
        }
        $ilist = $abstr->getAllAttr();
        $ilist = array_diff($ilist, $this->getAllPredef());
        $res = ['id'];
        $res = array_merge($res, $ilist);
        $key = array_search('id', $list);
        if ($key!==false) {
            unset($list[$key]);
        }
        $res = array_merge($res, $list);
        return $res ;
    }

    /**
     * Returns the list of attribute types of a Model.
     *
     * @return array
     */
    public function getAllTyp()
    {
        return $this->attrTyp;
    }
   /**
     * Returns the list of default values of a Model.
     *
     * @return array
     */
    public function getAllDflt()
    {
        return $this->attrDflt;
    }
    /**
     * Returns the list of values BUT the id value of a Model.
     *
     * @return array
     */
    public function getAllVal()
    {
        return $this->attrVal;
    }
    /**
     * Returns the list of all path of a Model.
     *
     * @return array
     */
    public function getAllRefParm()
    {
        return $this->refParm;
    }
    /**
     * Returns the list of Business Key attributes of a Model.
     *
     * @return array
     */
    public function getAllCkey()
    {
        return $this->attrCkey;
    }
    public function getAllBkey()
    {
        return $this->attrBkey;
    }
    /**
     * Returns the list of mandatory attributes of a Model.
     *
     * @return array
     */
    public function getAllMdtr()
    {
        return $this->attrMdtr;
    }
    /**
     * Returns the list of Predefined attributes of a Model.
     *
     * @return array
     */
    public function getAllPredef()
    {
        return $this->attrPredef;
    }

    /**
     * Returns the type of an attribute.
     *
     * @param string $attr the attribute.
     *
     * @return string its type.
     */
    public function getTyp($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if (isset($this->attrTyp[$attr])) {
            return $this->attrTyp[$attr];
        }
        $abstr = $this->getInhObj();
        return $abstr->getTyp($attr);
    }


    /**
     * Returns the 'path' of an attribute.
     *
     * @param string $attr the attribute.
     *
     * @return string its 'path'.
     */

    protected function getParm($attr)
    {
        if (isset($this->refParm[$attr])) {
            return $this->refParm[$attr] ;
        }
        $abstr = $this->getInhObj();
        return $abstr->getParm($attr);
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
                throw new Exception(E_ERC033.':'.$attr.':'.$modA.':'.$modN);
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
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($this->getTyp($attr)!= M_CREF) {
            $this->errLog->logLine(E_ERC027.':'.$attr);
            return false;
        }
        $path=$this->getParm($attr);
        $patha=explode('/', $path);
        return ($patha);
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
            throw new Exception(E_ERC032.':'.$attr.':'.$id);
        }
        $res->protect($patha[2]);
        return ($res);
    }

    public function getCode($attr, $id)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $typ = $this->getTyp($attr);
        if ($typ != M_CODE and $typ != M_REF) {
            $this->errLog->logLine(E_ERC028.':'.$attr);
            return false;
        }
        $path=$this->getParm($attr);
        $patha=explode('/', $path);
        if ($typ == M_CODE) {
            if (count($patha) == 4) {
                $m = new Model($patha[1], (int) $patha[2]);
                $res=$m->getCref($patha[3], $id);
                return ($res);
            }
            return new Model($patha[1], $id);
        }
        return new Model($this->getModRef($attr), $id);
    }

    /**
     * Returns the Logger.
     *
     * @return Logger
     */
    public function isErr()
    {
        $c=$this->errLog->logSize();
        if ($c) {
            return true;
        }
        return false;
    }
    /**
     * Returns true if the attribute is Protected.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function isProtected($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $res = in_array($attr, $this->attrProtected);
        return ($res);
    }
    /**
     * Returns true if the attribute is a Business key.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function isBkey($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $res = in_array($attr, $this->attrBkey);
        if ($res) {
            return $res;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->isBkey($attr);
        }
        return ($res);
    }
    /**
     * Returns true if the attribute is a Mandatory.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function isMdtr($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $res =in_array($attr, $this->attrMdtr);
        if ($res) {
            return $res;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->isMdtr($attr);
        }
        return ($res);
    }
    /**
     * Returns true if the attribute is a Pedefined attribute.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function isPredef($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        return (in_array($attr, $this->attrPredef));
    }
    /**
     * Returns true if the attribute is an optional attribute.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function isOptl($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($this->isPredef($attr)) {
            return false;
        }
        if ($this->isEval($attr)) {
            return false;
        }
        if ($this->isEvalP($attr)) {
            return false;
        }
        if ($this->isMdtr($attr)) {
            return false;
        }
        $typ = $this->getTyp($attr);
        if ($typ == M_CREF) {
            return false;
        }
        return true;
    }

    public function isModif($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $res = false;
        if ($this->isMdtr($attr) or $this->isOptl($attr)) {
            $res= true;
        }
        if ($this->isProtected($attr)) {
            $res = false;
        }
        return $res;
    }

    public function isSelect($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($attr == 'id') {
            return true;
        }
        if ($this->isEvalP($attr)) {
            return true;
        }
        return $this->isModif($attr);
    }


    public function isEvalP($attr)
    {
        $res = in_array($attr, $this->attrEvalP);
        if ($res) {
            return $res;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->isEvalP($attr);
        }
        return ($res);
    }

    public function isEval($attr)
    {
        $res=in_array($attr, $this->attrEval);
        if ($res) {
            return $res;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            return $abstr->isEval($attr);
        }
        return ($res);
    }

    /**
     * Returns true if the attribute exists.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function existsAttr($attr)
    {
        if (in_array($attr, $this->attrLst)) {
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
        if (in_array($attr, $this->attrLst)) {
            return true;
        } ;
        return false;
    }


    /**
     * Set the default value of an attribute.
     *
     * @param string $attr the attribute.
     * @param string $val  the default value.
     *
     * @return boolean
     */
    public function setDflt($attr, $val)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        $this->attrDflt[$attr]=$val;
        $this->modChgd=true;
        return true;
    }
    /**
     * Set an attribute as protected.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function protect($attr)
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if (!in_array($attr, $this->attrProtected)) {
            $this->attrProtected[]=$attr;
        }
        return true;
    }
    /**
     * Set an attribute value of a business key .
     *
     * @param string $attr the attribute.
     * @param string $val  the value.
     *
     * @return boolean
     */
    public function setBkey($attr, $val)
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if (!$this->stateHdlr) {
            $this->errLog->logLine(E_ERC017.':'.$attr);
            return false;
        }
        if ($val) {
            if (!in_array($attr, $this->attrBkey)) {
                $this->attrBkey[]=$attr;
            }
        }
        if (! $val) {
            $key = array_search($attr, $this->attrBkey);
            if ($key!==false) {
                unset($this->attrBkey[$key]);
            }
        }
        $this->modChgd=true;
        return true;
    }
    public function setCkey($attrLst, $val)
    {
        if (! is_array($attrLst)) {
            $this->errLog->logLine(E_ERC029);
            return false;
        }
        foreach ($attrLst as $attr) {
            if (! $x= $this->existsAttr($attr)) {
                $this->errLog->logLine(E_ERC002.':'.$attr);
                return false;
            };
        }
        if (!$this->stateHdlr) {
            $this->errLog->logLine(E_ERC017);
            return false;
        }
        if ($val) {
            if (!in_array($attrLst, $this->attrCkey)) {
                $this->attrCkey[]=$attrLst;
            }
        }
        if (! $val) {
            $key = array_search($attrLst, $this->attrCkey);
            if ($key!==false) {
                unset($this->attrCkey[$key]);
            }
        }
        $this->modChgd=true;
        return true;
    }
    /**
     * Set an attribute value of a mandatory attribute .
     *
     * @param string $attr the attribute.
     * @param string $val  the value.
     *
     * @return boolean
     */
    public function setMdtr($attr, $val)
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if ($val) {
            if (!in_array($attr, $this->attrMdtr)) {
                $this->attrMdtr[]=$attr;
            }
        }
        if (! $val) {
            $key = array_search($attr, $this->attrMdtr);
            if ($key!==false) {
                unset($this->attrMdtr[$key]);
            }
        }
        $this->modChgd=true;
        return true;
    }

    /**
     * Set the critera to select objects .
     *
     * @param string $attr attribute list.
     * @param string $val  value lits .
     *
     * @return boolean
     */
    public function setCriteria($attrL, $opL, $valL)
    {
        foreach ($attrL as $attr) {
            if (! $x= $this->existsAttr($attr)) {
                $this->errLog->logLine(E_ERC002.':'.$attr);
                return false;
            };
        }
        $this->asCriteria=[$attrL,$opL,$valL];
        return true;
    }
    /**
     * Add an attribute.
     *
     * @param string $attr the attribute.
     * @param string $typ  the type.
     * @param string $path the path.
     *
     * @return boolean
     */
    public function addAttr($attr, $typ, $path = 0)
    {
        $x= $this->existsAttr($attr);
        if ($x) {
            $this->errLog->logLine(E_ERC003.':'.$attr);
            return false;
        }
        if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE) {
            if (!$path) {
                $this->errLog->logLine(E_ERC008.':'.$attr.':'.$typ);
                return false;
            }
            if (!$this->stateHdlr) {
                $this->errLog->logLine(E_ERC014.':'.$attr.':'.$typ);
                return false;
            }
        }
        if (!isMtype($typ)) {
            $this->errLog->logLine(E_ERC004.':'.$typ);
            return false;
        }
        if (! $this->checkParm($attr, $typ, $path)) {
            return false;
        }
        if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE) {
            $this->refParm[$attr]=$path;
        }
        if ($path === M_P_EVAL) {
            $this->refParm[$attr]=$path;
            $this->attrEval[]=$attr;
        }
        if ($path === M_P_EVALP) {
            $this->refParm[$attr]=$path;
            $this->attrEvalP[]=$attr;
        }
        $this->attrLst[]=$attr;
        $this->attrTyp[$attr]=$typ;

        $this->modChgd=true;
        return true;
    }
    /**
     * Delete an attribute.
     *
     * @param string $attr the attribute.
     *
     * @return boolean
     */
    public function delAttr($attr)
    {
        $x= $this->existsAttrLocal($attr);
        if (!$x) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }

        if (in_array($attr, $this->attrPredef)) {
            $this->errLog->logLine(E_ERC001.':'.$attr);
            return false;
        }
        foreach ($this->attrCkey as $attrLst) {
            if (in_array($attr, $attrLst)) {
                $this->errLog->logLine(E_ERC030.':'.$attr);
                return false;
            }
        }
        $key = array_search($attr, $this->attrLst);
        if ($key!==false) {
            unset($this->attrLst[$key]);
        }
        if (isset($this->attrTyp[$attr])) {
            unset($this->attrTyp[$attr]);
        }
        if (isset($this->refParm[$attr])) {
            unset($this->refParm[$attr]);
        }
        if (isset($this->attrDflt[$attr])) {
            unset($this->attrDflt[$attr]);
        }
        $key = array_search($attr, $this->attrBkey);
        if ($key!==false) {
            unset($this->attrBkey[$key]);
        }
        $key = array_search($attr, $this->attrMdtr);
        if ($key!==false) {
            unset($this->attrMdtr[$key]);
        }
        $key = array_search($attr, $this->attrProtected);
        if ($key!==false) {
            unset($this->attrProtected[$key]);
        }
        $key = array_search($attr, $this->attrEval);
        if ($key!==false) {
            unset($this->attrEval[$key]);
        }
        $key = array_search($attr, $this->attrEvalP);
        if ($key!==false) {
            unset($this->attrEvalP[$key]);
        }
        $this->modChgd=true;
        return true;
    }
     /**
     * Get the default value of an attribute.
     *
     * @param string $attr the attribute.
     *
     * @return string  the value
     */
    public function getDflt($attr)
    {
        $x= $this->existsAttr($attr);
        if (!$x) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        foreach ($this->attrDflt as $x => $val) {
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
        $result=$this->stateHdlr->findObjWheOp(
            $mod,
            $res[0],
            $res[1],
            $res[2]
        );
        return $result;
    }

    /**
     * Get the value of an attribute.
     *
     * @param string $attr the attribute.
     *
     * @return void  the value
     */
    public function getVal($attr)
    {
        if ($attr == 'id') {
            return $this->getId();
        }
        $x= $this->existsAttr($attr);
        if (!$x) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($this->isEval($attr)) {
            return $this->obj->getVal($attr);
        }
        $type=$this->getTyp($attr);
        if ($type == M_CREF) { //will not work if on different Base !!
            $path = $this->getParm($attr);
            $patha=explode('/', $path);
            $hdlr=$this->stateHdlr;
            $res=$hdlr->findObj($patha[1], $patha[2], $this->getId());
            return $res;
        }
        foreach ($this->attrVal as $x => $val) {
            if ($x==$attr) {
                return $val;
            }
        }
        return null;
    }
    /**
     * Set the value of an attribute without any check.
     *
     * @param string $attr the attribute.
     * @param void   $val  the value.
     *
     * @return boolean
     */
    protected function setValNoCheck($attr, $val)
    {
        $this->attrVal[$attr]=$val;
        return true;
    }
    /**
     * Set the value of an attribute.
     *
     * @param string  $Attr  the attribute.
     * @param void    $Val   the value.
     * @param boolean $check check if predef or not.
     *
     * @return boolean
     */
    public function setVal($attr, $val)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($this->isAbstr()) {
            $this->errLog->logLine(E_ERC045);
            return false;
        }
        $check=!($this->trusted);
        if (in_array($attr, $this->attrPredef) and $check) {
            $this->errLog->logLine(E_ERC001.':'.$attr);
            return false;
        };
        $check= ($check or $this->checkTrusted);
        // Eval
        if ($this->isEval($attr)) {
            $this->errLog->logLine(E_ERC039.':'.$attr);
            return false;
        }
        // EvalP
        if ($this->isEvalP($attr)
            and (! $this->custom)
            and !($this->trusted)) {
            $this->errLog->logLine(E_ERC042.':'.$attr);
            return false;
        }
        // protected
        if ($this->isProtected($attr)) {
            $this->errLog->logLine(E_ERC034.':'.$attr);
            return false;
        }
        // type checking
        $type=$this->getTyp($attr);
        if ($type ==M_CREF) {
            $this->errLog->logLine(E_ERC013.':'.$attr);
            return false;
        }
        $btype=baseType($type);
        $res =checkType($val, $btype);
        if (! $res) {
            $this->errLog->logLine(E_ERC005.':'.$attr.':'.$val.':'.$btype);
            return false;
        }
        // ref checking
        if ($type == M_REF and $check) {
            $res = $this-> checkRef($attr, $val);
            if (! $res) {
                return false;
            }
        }
        // code values
        if ($type == M_CODE and $check) {
            $res = $this-> checkCode($attr, $val);
            if (! $res) {
                $this->errLog->logLine(E_ERC016.':'.$attr.':'.$val);
                return false;
            }
        }
        // BKey
        if ($this->isBkey($attr) and $check) {
            $res = $this->checkBkey($attr, $val);
            if (! $res) {
                $this->errLog->logLine(E_ERC018.':'.$attr.':'.$val);
                return false;
            }
        }
        return ($this->setValNoCheck($attr, $val));
    }
    /**
     * Check the value of an business key attribute.
     *
     * @param string $Attr the attribute.
     * @param void   $Val  the value.
     *
     * @return boolean
     */
    protected function checkBkey($attr, $val)
    {
        if (is_null($val)) {
            return true;
        }
        $res=$this->stateHdlr->findObj($this->getModName(), $attr, $val);
        if ($res == []) {
            return true;
        }
        $id=array_pop($res);
        if ($id == $this->getId()) {
            return true;
        }
        return false;
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
        $res=$this->stateHdlr->findObjWheOp(
            $this->getModName(),
            $attrLst,
            [],
            $valLst
        );
        if ($res == []) {
            return true;
        }
        $id=array_pop($res);
        if ($id == $this->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Check the value of a code attribute.
     *
     * @param string $Attr the attribute.
     * @param void   $Val  the value.
     *
     * @return boolean
     */
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

    protected function checkParm($attr, $typ, $parm)
    {
        if ($parm === M_P_EVAL or $parm === M_P_EVALP) {
            if (! class_exists($this->getModName())) {
                $this->errLog->logLine(E_ERC040.':'.$attr.':'.$typ);
                return false;
            }
            return true;
        }
        if ($typ != M_REF and $typ != M_CREF and $typ != M_CODE) {
            if ($parm) {
                $this->errLog->logLine(E_ERC041.':'.$attr.':'.$typ.':'.$parm);
                return false;
            }
            return true;
        }
        $path=explode('/', $parm);
        $root = $path[0];
        if (($root != "" )) {
            $this->errLog->logLine(E_ERC020.':'.$attr.':'.$parm);
            return false;
        }
        $c = count($path)-1;

        switch ($typ) {
            case M_REF:
                if ($c==1) {
                    return true;
                }
                break;
            case M_CREF:
                if ($c==2) {
                    return true;
                }
                break;
            case M_CODE:
                if ($c==3 or $c==1) {
                    return true;
                }
                break;
        }
        $this->errLog->logLine(E_ERC020.':'.$attr.':'.$parm);
        return false;
    }
 
    public function getModRef($attr)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if ($this->getTyp($attr)!= M_REF) {
             $this->errLog->logLine(E_ERC026.':'.$attr);
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
        $r=$this->getTyp($attr);
        if (!$r) {
            return false;
        }
        if ($r != M_CODE and $r != M_REF) {
            $this->errLog->logLine(E_ERC015.':'.$attr.':'.$r);
            return false;
        }
        if ($r == M_CODE) {
            $r=$this->getParm($attr);
            $apath = explode('/', $r);
            if (count($apath) > 2) {
                $mod = new Model($apath[1], (int) $apath[2]);
                $val = $mod->getVal($apath[3]);
            } else {
                $obj= new Model($apath[1]);
                $val=$obj->stateHdlr->findObjWheOp($apath[1], [], [], []);
            }
        }
        if ($r == M_REF) {
            $mod = $this->getModRef($attr);
            $obj= new Model($mod);
            $val=$obj->stateHdlr->findObjWheOp($mod, [], [], []);
        }
        return $val;
    }

     /**
     * Check the value of a ref attribute.
     *
     * @param string $Attr the attribute.
     * @param void   $id   the value.
     *
     * @return boolean
     */
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
    /**
     * Check if a mandatory attribute is set.
     *
     * @param string $Attr the attribute.
     *
     * @return boolean
     */
    protected function checkMdtr($attrList)
    {
        foreach ($attrList as $attr) {
            if (array_key_exists($attr, $this->attrVal)) {
                $val = $this->getVal($attr);
                if (is_null($val)) {
                    $this->errLog->logLine(E_ERC019.':'.$attr);
                    return false;
                }
            } else {
                $this->errLog->logLine(E_ERC019.':'.$attr);
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
                $this->errLog->logLine(E_ERC031.':'.$l);
                return false;
            }
        }
        return true;
    }


    /**
     * Save an object.
     *
     * @return int the id.
     */
    public function save()
    {
        if ($this->isAbstr()) {
            $this->errLog->logLine(E_ERC044);
            return false;
        }
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        if ($this->modChgd) {
            $this->errLog->logLine(E_ERC024);
            return false;
        }

        $attrL= $this->getAllMdtr();
        if (!$this->checkMdtr($attrL)) {
            return false;
        }
        $abstr = $this->getInhObj();
        if (!is_null($abstr)) {
            $attrL= $abstr->getAllMdtr();
            if (!$this->checkMdtr($attrL)) {
                return false;
            }
        }

        $ckeyList=$this->getAllCkey();
        if (!$this->checkCkeyList($ckeyList)) {
            return false;
        }
        if (!is_null($abstr)) {
            $ckeyList= $abstr->getAllCkey();
            if (!$this->checkCkeyList($ckeyList)) {
                return false;
            }
        }

        $n=$this->getVal('vnum');
        $n++;
        $this->setValNoCheck('vnum', $n);
        $this->setValNoCheck('utstp', date(M_FORMAT_T));
        if (! is_null($this->obj)) {
            $this->custom=true;
            $res = $this->obj->save();
            $this->custom=false;
            if (!$res) {
                return false;
            }
        }
        $res=$this->stateHdlr->saveObj($this);
        $this->id=$res;
        if (! is_null($this->obj)) {
            $this->custom=true;
            $res=$this->obj->afterSave();
            $this->custom=false;
            if (!$res) {
                return false;
            }
        }
        return $this->id;
    }

    public function isDel()
    {
        if ($this->isAbstr()) {
            return false;
        }
        foreach ($this->getAllAttr() as $attr) {
            $typ = $this->getTyp($attr);
            if ($typ == M_CREF) {
                $res=$this->getVal($attr);
                if (count($res)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Delete an object.
     *
     * @return boolean
     */
    public function delet()
    {
        if ($this->isAbstr()) {
            $this->errLog->logLine(E_ERC044);
            return false;
        }
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        if (!$this->isDel()) {
            $this->errLog->logLine(E_ERC052);
            return false;
        }
        if (! is_null($this->obj)) {
            $this->custom=true;
            $res = $this->obj->delet();
            $this->custom=false;
            if (!$res) {
                return false;
            }
        }

        try {
            $res=$this->stateHdlr->eraseObj($this);
        } catch (Exception $e) {
            $this->errLog->logLine(E_ERC052.':'.$e->getMessage());
            return false;
        }

        if ($res) {
            $this->id=0;
        }

        if (! is_null($this->obj)) {
            $this->custom=true;
            $res = $this->obj->afterDelet();
            $this->custom=false;
            if (!$res) {
                return false;
            }
        }
        return $res;
    }
    /**
     * Delete the Model of the object.
     *
     * @return boolean
     */
    public function deleteMod()
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        $res=$this->stateHdlr->eraseMod($this);
        $this->initattr();
        return $res;
    }
    /**
     * Save the Model of the object.
     *
     * @return boolean
     */
    public function saveMod()
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        $res=$this->stateHdlr->saveMod($this);
        if ($res) {
            $this->modChgd=false;
        }
        return $res;
    }
}
