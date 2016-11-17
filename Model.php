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
 
require_once "Logger.php";
require_once "TypeConstant.php";
require_once "ErrorConstant.php";
require_once "Type.php";
require_once "Handler.php";
require_once "Path.php";

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
     * @var array The list of predefined attributes. 
     */
    protected $attr_predef;
    /**
     * @var array The list of attributes. 
     */
    protected $attr_lst;
    /**
     * @var array The list of types associated to attributes. 
     */   
    protected $attr_typ;
    /**
     * @var array The list of values associated to attributes. 
     */ 
    protected $attr_val;
    /**
     * @var array The list of path associated to reference attributes.  
     */
    protected $attr_path ;
    /**
     * @var array The list of types business key attributes. 
     */
    protected $attr_bkey;
    /**
     * @var array The list of mandatory attributes. 
     */
    protected $attr_mdtr;
	/**
     * @var array The list of attribute default values. 
     */
    protected $attr_dflt;
    /**
     * @var array The error logger. 
     */
    protected $errLog;
    /**
     * @var array The state handler.
     */
    protected $stateHdlr=0;
    /**
    * Constructor
    */
    function __construct() 
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
    function construct1($name) 
    {
        $this->init($name, 0);
        $this->setValNoCheck("ctstp", date(M_FORMAT_T));
        $this->setValNoCheck("vnum", 1);
        $x = $this->stateHdlr;
        if ($x) {
            $x->restoreMod($this);
        }
    }  
    /**
     * Constructor of an existing object (id must be different from 0).
     *
     * @param string $name The model name. 
     * @param int    $id   The object id. 
     *
     * @return void
     */   
    function construct2($name,$id) 
    {
        $this->init($name, $id);
        if (! $id) {
            throw new Exception(E_ERC012.':'.$name.':'.$id);
        }
        $idr=0;
        $x = $this->stateHdlr;
        if ($x) {
            if (! $x->restoreMod($this)) {
                throw new exception(E_ERC022.':'.$name);
            }
            $idr =$x->restoreObj($this);
            if ($id != $idr) {
                throw new exception(E_ERC007.':'.$name.':'.$id);
            };
        }
    }
     /**
     * Initialise the attributes and the errologger
     *
     * @param string $name The model name. 
     * @param int    $id   The object id. 
     *
     * @return void
     */     
    protected function init($name,$id) 
    {
        if (! checkType($name, M_ALPHA)) {
            throw new Exception(E_ERC010.':'.$name.':'.M_ALPHA);
        }
        if (! checkType($id, M_INTP)) {
            throw new Exception(E_ERC011.':'.$id.':'.M_INTP);
        }
        $this->initattr();
        $this->id=$id; 
        $this->name=$name;
        $logname = $name.'_ErrLog';
        $this->errLog= new Logger($logname);
        $this->stateHdlr=getStateHandler($name);
    }
    /**
     * Re/Initialise the attributes and their properties
     *
     * @return void
     */ 
    protected function initattr() 
    {
        $this->attr_predef = array('id','vnum','ctstp','utstp');
        $this->attr_lst = array('id','vnum','ctstp','utstp');
        $this->attr_typ = array(
            "id"=>M_ID,
            "vnum"=>M_INT,
            "ctstp"=>M_TMSTP,
            "utstp"=>M_TMSTP,
        );
        $this->attr_val = array('vnum' => 0);
        $this->attr_dflt = [];
        $this->attr_path = [];
        $this->attr_bkey = [];
        $this->attr_mdtr = [];
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
    /**
     * Returns the Model Name.
     *
     * @return string
     */
    public function getModName() 
    {
        return $this->name;
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
    
    /**
     * Returns the list of attributes of a Model.
     *
     * @return array
     */
    public function getAllAttr() 
    {
        return $this->attr_lst;
    }  
    /**
     * Returns the list of attribute types of a Model. 
     *
     * @return array
     */    
    public function getAllTyp()
    {
        return $this->attr_typ;         
    }
   /**
     * Returns the list of default values of a Model.
     *
     * @return array
     */
    public function getAllDflt() 
    {
        return $this->attr_dflt;         
    }
    /**
     * Returns the list of values BUT the id value of a Model.
     *
     * @return array
     */
    public function getAllVal() 
    {
        return $this->attr_val;         
    }
    /**
     * Returns the list of all path of a Model.
     *
     * @return array
     */ 
    public function getAllPath() 
    { 
        return $this->attr_path;            
    }   
    /**
     * Returns the list of Business Key attributes of a Model.
     *
     * @return array
     */  
    public function getAllBkey() 
    { 
        return $this->attr_bkey;            
    } 
    /**
     * Returns the list of mandatory attributes of a Model.
     *
     * @return array
     */ 
    public function getAllMdtr()
    { 
        return $this->attr_mdtr;            
    }  
    /**
     * Returns the list of Predefined attributes of a Model.
     *
     * @return array
     */
    public function getAllPredef() 
    {
        return $this->attr_predef;
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
        foreach ($this->attr_typ as $x => $typ) {
            if ($x==$attr) {
                return $typ;
            }
        }                       
        return null;
    }
    /**
     * Returns the 'path' of an attribute.
     *
     * @param string $attr the attribute. 
     *
     * @return string its 'path'.
     */ 
    protected function getPath($attr) 
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        foreach ($this->attr_path as $x => $path) {
            if ($x==$attr) {
                return $path;
            }
        }                       
        return null;
    }
    /**
     * Returns the Model Name of a reference attribute.
     *
     * @param string $attr the attribute. 
     *
     * @return string its Model Name.
     */
    public function getRefMod($attr) 
    {
        $path = $this->getPath($attr);
        if (! $path) {
            return false;
        }
        $patha=explode('/', $path);
        return ($patha[1]);
    }
    /**
     * Returns the Logger.
     *
     * @return Logger
     */    
    public function isErr()
    {
        $c=$this->errLog->logSize();
        return ($c);
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
            $this->errLog->logLine(E_ERC002.':'.$attr);return false;
        }
        return (in_array($attr, $this->attr_bkey));
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
        return (in_array($attr, $this->attr_mdtr));
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
        return (in_array($attr, $this->attr_predef));
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
        if ($this->isPredef($attr) ) {
            return false;
        }
        if ($this->isMdtr($attr) ) {
            return false;
        }
        $typ = $this->getTyp($attr);
        if ($typ == M_CREF) {
            return false;
        }
        return true;
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
        if (in_array($attr, $this->attr_lst)) {
            return true;
        } ;
        return false;
    }
    /**
     * Set the type of an attribute.
     *
     * @param string $attr the attribute. 
     * @param string $typ  the type.
     *
     * @return boolean
     */      
    public function setTyp($attr,$typ) 
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if (!isMtype($typ)) {
            $this->errLog->logLine(E_ERC004.':'.$typ);
            return false;
        }
        $this->attr_typ[$attr]=$typ;
        return true;
    }
	/**
     * Set the default value of an attribute.
     *
     * @param string $attr the attribute. 
     * @param string $val  the default value.
     *
     * @return boolean
     */      
    public function setDflt($attr,$val) 
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        $this->attr_dflt[$attr]=$val;
        return true;
    }
    /**
     * Set the path of an attribute.
     *
     * @param string $attr the attribute. 
     * @param string $path the path.
     *
     * @return boolean
     */          
    public function setPath($attr,$path) 
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if (! checkPath($path) ) {
            $this->errLog->logLine(E_ERC020.':'.$attr.':'.$path);
            return false;
        };
        $this->attr_path[$attr]=$path;
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
    public function setBkey($attr,$val) 
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if (!$this->stateHdlr) {
            $this->errLog->logLine(E_ERC017.':'.$attr.':'.$typ);
            return false;
        }     
        if ($val) {
            if (!in_array($attr, $this->attr_bkey)) {
                $this->attr_bkey[]=$attr;
            }      
        }
        if (! $val) {
            if (in_array($attr, $this->attr_bkey)) {
                unset($this->attr_bkey[$attr]);
            }         
        }
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
    public function setMdtr($attr,$val) 
    {
        if (! $x= $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        };
        if ($val) {
            if (!in_array($attr, $this->attr_mdtr)) {
                $this->attr_mdtr[]=$attr;
            }      
        }
        if (! $val) {
            if (in_array($attr, $this->attr_mdtr)) {
                unset($this->attr_mdtr[$attr]);
            }         
        }
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
    public function addAttr($attr,$typ=M_STRING,$path=0) 
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
        $this->attr_lst[]=$attr;
        $r=$this->setTyp($attr, $typ);
        $r2=true;
        if ($typ == M_REF or $typ == M_CREF or $typ == M_CODE) {
            $r2=$this->setPath($attr, $path); 
        }
        if ($r and $r2) {
            return true;
        }
        $this->delAttr($attr);
        return false;
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
        $x= $this->existsAttr($attr);
        if (!$x) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        if (in_array($attr, $this->attr_predef)) {
            $this->errLog->logLine(E_ERC001.':'.$attr);
            return false;
        }
        $key = array_search($attr, $this->attr_lst);
        if ($key!==false) {
            unset($this->attr_lst[$key]);
        }                             
        if (isset($this->attr_typ[$attr])) {
            unset($this->attr_typ[$attr]);
        }
        if (isset($this->attr_path[$attr])) {
            unset($this->attr_path[$attr]);
        }
        if (isset($this->attr_dflt[$attr])) {
            unset($this->attr_dflt[$attr]);
        }
        $key = array_search($attr, $this->attr_bkey);
        if ($key!==false) {
            unset($this->attr_bkey[$key]);
        }    
        $key = array_search($attr, $this->attr_mdtr);
        if ($key!==false) {
            unset($this->attr_mdtr[$key]);
        }    
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
        foreach ($this->attr_dflt as $x => $val) {
            if ($x==$attr) {
                return $val;
            }
        }       
        return null;            
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
        $type=$this->getTyp($attr);
        if ($type == M_CREF) { //will not work if on different Base !!
            $path = $this->getPath($attr);
            $patha=explode('/', $path);
            $res=$this->stateHdlr->findObj($patha[1], $patha[2], $this->getId());
            return $res;
        }
        foreach ($this->attr_val as $x => $val) {
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
    protected function setValNoCheck($attr,$val)
    {
        if (! $this->existsAttr($attr)) {
            $this->errLog->logLine(E_ERC002.':'.$attr);
            return false;
        }
        $this->attr_val[$attr]=$val;
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
    public function setVal($Attr,$Val,$check=true)
    {
        if (! $this->existsAttr($Attr)) {
            $this->errLog->logLine(E_ERC002.':'.$Attr);
            return false;
        }
        if (in_array($Attr, $this->attr_predef) and $check) {
            $this->errLog->logLine(E_ERC001.':'.$Attr);
            return false;
        };
        // type checking
        $type=$this->getTyp($Attr);
        if ($type ==M_CREF ) {
            $this->errLog->logLine(E_ERC013.':'.$Attr);
            return false;
        }
        $btype=baseType($type);
        $res =checkType($Val, $btype);
        if (! $res) {
            $this->errLog->logLine(E_ERC005.':'.$Attr.':'.$Val.':'.$btype);
            return false;
        }
        // ref checking
        if ($type == M_REF) {
            $res = $this-> checkRef($Attr, $Val);
            if (! $res) {
                return false;
            }
        }
        // code values
        if ($type == M_CODE) {
            $res = $this-> checkCode($Attr, $Val);
            if (! $res) {
                $this->errLog->logLine(E_ERC016.':'.$Attr.':'.$Val);
                return false;
            }
        }
        // BKey
        if ($this->isBkey($Attr)) {
            if (! $this->checkBkey($Attr, $Val)) {
                $this->errLog->logLine(E_ERC018.':'.$Attr.':'.$Val);
                return false;
            } 
        }
        return ($this->setValNoCheck($Attr, $Val));
    }
    /**
     * Check the value of an business key attribute.
     *
     * @param string $Attr the attribute. 
     * @param void   $Val  the value.
     *
     * @return boolean
     */    
    public function checkBkey($Attr,$Val)
    {		
	    if (is_null($Val)) {
			return true;
		}
        $res=$this->stateHdlr->findObj($this->getModName(), $Attr, $Val);
        if ($res == []) {
            return true;
        }
        if ($res == [$this->getId()]) {
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
    public function checkCode($Attr,$Val)
    {
		if (is_null($Val)) {
			return true;
		}
        $Vals = $this->getValues($Attr);
        if (!$Vals) {
            return false;
        }
        $res = in_array($Val, $Vals);
        return $res;        
    }
    /**
     * Get the possible values of a code attribute.
     *
     * @param string $Attr the attribute. 
     *
     * @return array
     */
    public function getValues($Attr) 
    {
        $r=$this->getTyp($Attr);
        if (!$r) {
            return false;
        }
        if ($r != M_CODE) {
            $this->errLog->logLine(E_ERC015.':'.$Attr.':'.$r); 
            return false;
        }
        $r=$this->getPath($Attr);
        if ($r) {
            $res=pathVal($r);
            return $res;
        }
        return $r;
    }  
     /**
     * Check the value of a ref attribute.
     *
     * @param string $Attr the attribute. 
     * @param void   $id   the value.
     *
     * @return boolean
     */   
    public function checkRef($Attr,$id)
    {
        if (is_null($id)) {
            return true;
        }
        $path = $this->getPath($Attr);
        if (!$path) {
            $this-$this->errLog->logLine(E_ERC008.':'.$Attr);
            return false;
        };
        $path=$path.'/'.$id;
        try {
            $res = pathObj($path);
        } 
        catch (Exception $e) {
            $this->errLog->logLine($e->getMessage());
            return false;
        }
        if (!$res) {
            throw new Exception(E_ERC023.':'.$Attr.':'.$path);
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
    public function checkMdtr($Attr)
    {
        $typ= $this->getTyp($Attr);
        if (!$typ) {
            return false;
        }
        if (array_key_exists($Attr, $this->attr_val)) {
            $val = $this->getVal($Attr);
			if (! is_null($val)) {
				return true;
			}
        }
        return false;
    }   
    /**
     * Save an object.
     *
     * @return int the id.
     */      
    public function save()
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        foreach ($this->getAllMdtr() as $Attr) {
            $res=$this->checkMdtr($Attr);
            if (!$res) {
                $this->errLog->logLine(E_ERC019.':'.$Attr);
                return false;
            }
        }
        $n=$this->getVal('vnum');
        $n++;
        $this->setValNoCheck('vnum', $n);
        $this->setValNoCheck('utstp', date(M_FORMAT_T));
        $res=$this->stateHdlr->saveObj($this);
        $this->id=$res;
        return $res;
    }
    /**
     * Delete an object.
     *
     * @return boolean
     */         
    public function delet() 
    {
        if (! $this->stateHdlr) {
            $this->errLog->logLine(E_ERC006);
            return false;
        }
        $res=$this->stateHdlr->eraseObj($this);
        if ($res) {
            $this->id=0;
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
        return $res;
    }
}

?>
