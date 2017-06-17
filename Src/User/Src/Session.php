<?php
require_once 'User/Src/Access.php';

class Session extends CModel
{
    protected static $timer= 6000;
    protected $bkey = 0;
    protected $user;
    protected $role;
    protected $isNew=false;
    protected $Keep = false;
    protected $hdl = null;
    
    public function __construct($mod)
    {
        $this->mod=$mod;
        if (!$mod->getId()) {
            $this->bkey=uniqid();
            $this->isNew = true;
        }
    }

    public function initMod($bindings)
    {
        $obj = $this->mod;

        $res = $obj->addAttr('BKey', M_STRING);
        $res = $obj->addAttr('ValidStart', M_INT, M_P_EVALP);
        $res = $obj->addAttr('ValidFlag', M_INT, M_P_EVALP);
        $res = $obj->setBkey('BKey', true);
        $res = $obj->setMdtr('BKey', true);

        if (isset($bindings['User'])) {
            $user = $bindings['User'];
            $res = $obj->addAttr('User', M_REF, '/'.$user);
            $res = $obj->addAttr('UserId', M_STRING);
            $res = $obj->addAttr('Password', M_STRING, M_P_TEMP);
        }
        
        if (isset($bindings['Role'])) {
            $role = $bindings['Role'];
            $res = $obj->addAttr('Role', M_REF, '/'.$role);
        }

        return $obj->isErr();
    }
 
    public function getKey()
    {
        if (!$this->mod->getId()) {
            return $this->bkey;
        }
        return $this->mod->getValN('BKey');
    }
 
    public function getRSpec()
    {
        $role = $this->mod->getRef('Role');
        if (! $role) {
            return null;
        }
        $res = $role->getVal('JSpec');
        if (!$res) {
            return null;
        }
        $val = json_decode($res, true);
        return $val;
    }
    
    public function getVal($attr)
    {
        if ($attr == 'Password') {
            return null;
        }
        return $this->mod->getValN($attr);
    }

    public function save()
    {
        $id = $this->mod->getId();
        if (! $id) {
            $this->mod->setVal('ValidStart', time());
            $this->mod->setVal('ValidFlag', 1);
            $this->mod->setVal('BKey', $this->bkey);
        }

        $usrn = null;
        $usrobj=null;
        $usrcobj=null;
                
        if ($this->mod->existsAttr('UserId')) {
            $usrn = $this->mod->getValN('UserId');
            $this->mod->setValN('User', null);
            $usrCN = $this->mod->getModRef('User');
            if (! is_null($usrn)) {
                $usrobj= Find::byKey($usrCN, 'UserId', $usrn);
                if (is_null($usrobj)) {
                    $this->mod->getErrLog()->logLine(E_ERC059.":$usrn");
                    return false;
                }
                $usrcobj = $usrobj->getCobj();
                $sespsw = $this->mod->getValN('Password');
                if (!$usrcobj->authenticate($usrn, $sespsw)) {
                    $this->mod->getErrLog()->logLine(E_ERC057);
                    return false;
                }
                $this->mod->setValN('User', $usrobj->getId());
            }
        }

        if ($this->mod->existsAttr('Role') and ! $this->mod->existsAttr('UserId')) {
            $role = $this->mod->getValN('Role');
            $roleCN = $this->mod->getModRef('Role');
            if (!$role) {
                $roleobj= Find::byKey($roleCN, 'Name', 'Default');
                if ($roleobj) {
                    $this->mod->setValN('Role', $roleobj->getId());
                }
            }
        }
        
        if ($this->mod->existsAttr('Role') and $this->mod->existsAttr('UserId')) {
            $role = $this->mod->getValN('Role');
            $roleCN = $this->mod->getModRef('Role');
            if (!$role and $usrobj) {
                $role = $usrobj->getVal('DefaultRole');
                if (!$role) {
                    $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                    return false;
                }
                $this->mod->setValN('Role', $role);
            }
            if ($role and $usrcobj) {
                if (! $usrcobj->checkRole($role)) {
                    $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                    return false;
                }
            }
            if ($role and !$usrobj) {
                $roleobj= Find::byKey($roleCN, 'Name', 'Default');
                if (!$roleobj or $roleobj->getId() != $role) {
                    $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                    return false;
                }
            }
            if (!$role and !$usrobj) {
                $roleobj= Find::byKey($roleCN, 'Name', 'Default');
                if ($roleobj) {
                    $this->mod->setValN('Role', $roleobj->getId());
                }
            }
        }
        return $this->mod->saveN();
    }
    
    public function delet()
    {
        $flag = $this->mod->getValN('ValidFlag');
        if (!$flag and !$this->Keep) {
            return $this->mod->deletN();
        } else {
            $this->mod->setValN('ValidFlag', 0);
            return $this->mod->saveN();
        }
    }

    public static function getSession($id)
    {
        $mod = new Model(get_called_class());
        $sessionHdl=$mod->getCObj();
        $obj= $mod->getBkey('BKey', $id);
        if (is_null($obj)) {
            $mod->save();
            return $sessionHdl;
        }
        $flag = $obj->getValN('ValidFlag');
        $valid = $obj->getValN('ValidStart')+self::$timer;
        if ($flag and ($valid > time())) {
            $sessionHdl=$obj->getCObj();
            return $sessionHdl;
        }
        if ($flag) {
            $obj->delet();
        }
        $sessionHdl->initPrev($obj);
        $mod->save();
        $obj->delet();
        return $sessionHdl;
    }
    
    public function initPrev($pobj)
    {
        if ($this->mod->existsAttr('UserId')) {
            $val = $pobj->getValN('UserId');
            $this->mod->setValN('UserId', $val);
        }
        if ($this->mod->existsAttr('Role')) {
            $val = $pobj->getValN('Role');
            $this->mod->setValN('Role', $val);
        }
    }
    
    public function isNew()
    {
        return $this->isNew;
    }
    
    public function getObj($mod)
    {
        if ($mod == $this->mod->getModName()) {
            return $this->mod;
        }
        return $this->mod->getRef($mod);
    }
    
    public function checkReq($req)
    {
        return Access::checkReq($this->mod, $req);
    }
    
    public function getSelMenu($classList)
    {
        return Access::getSelMenu($this->mod, $classList);
    }
    
    public function checkARight($req, $attrObjs, $protect, $plast = true)
    {
        return Access::checkARight($this->mod, $req, $attrObjs, $protect, $plast);
    }
}
