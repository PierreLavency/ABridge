<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Find;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

class Session extends CModel
{
    protected static $timer= 6000;
    protected $bkey = 0;
    protected $user;
    protected $role;
    protected $isNew=false;
    protected $Keep = false;
    protected $hdl = null;
    protected $roleSpecLoaded=false;
    protected $roleSpec=null;
    
    public function __construct($mod)
    {
        $this->mod=$mod;
        $this->roleSpecLoaded=false;
        $this->roleSpec=null;
        if (!$mod->getId()) {
            $this->bkey=uniqid();
            $this->isNew = true;
        }
    }

    public function initMod($bindings)
    {
        $obj = $this->mod;

        $res = $obj->addAttr('BKey', Mtype::M_STRING);
        $res = $obj->setBkey('BKey', true);
        $res = $obj->setMdtr('BKey', true);
        
        $res = $obj->addAttr('Checked', Mtype::M_INT);
        $res = $obj->setProp('Checked', Model::P_EVL);
        
        $res = $obj->addAttr('ValidStart', Mtype::M_INT);
        $res = $obj->setProp('ValidStart', Model::P_EVL);
        
        $res = $obj->addAttr('ValidFlag', Mtype::M_INT);
        $res = $obj->setProp('ValidFlag', Model::P_EVL);
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        
        if (isset($bindings['User'])) {
            $user = $bindings['User'];
            $res = $obj->addAttr('User', Mtype::M_REF, '/'.$user);
            $res = $obj->addAttr('UserId', Mtype::M_STRING);
            $res = $obj->addAttr('Password', Mtype::M_STRING);
            $res = $obj->setProp('Password', Model::P_TMP);
        }
        
        if (isset($bindings['Role'])) {
            $role = $bindings['Role'];
            $res = $obj->addAttr('RoleName', Mtype::M_STRING);
            $res = $obj->addAttr('ActiveRole', Mtype::M_REF, '/'.$role);
        }

        if (isset($bindings['UserGroup'])) {
            $usergroup= $bindings['UserGroup'];
            $res = $obj->addAttr('GroupName', Mtype::M_STRING);
            $res = $obj->addAttr('ActiveGroup', Mtype::M_REF, '/'.$usergroup);
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
        if ($this->roleSpecLoaded) {
            return $this->roleSpec;
        }
        $this->roleSpecLoaded=true;
        $role = $this->mod->getRef('ActiveRole');
        if (! $role) {
            return null;
        }
        $res = $role->getVal('JSpec');
        if (! $res) {
            return null;
        }
        $val = json_decode($res, true);
        $this->roleSpec=$val;
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
            $this->mod->setVal('Checked', 0);
            return $this->mod->saveN();
        }

        $usrName =null;
        $usrMobj =null;
        $usrCobj =null;
        $usrClassName =null;
              
        if ($this->mod->existsAttr('UserId')) {
            $usrName = $this->mod->getValN('UserId');
            $this->mod->setValN('User', null);
            $usrClassName = $this->mod->getModRef('User');
            if (! is_null($usrName)) {
                $usrMobj= Find::byKey($usrClassName, 'UserId', $usrName);
                if (is_null($usrMobj)) {
                    $this->mod->getErrLog()->logLine(CstError::E_ERC059.":$usrClassName:$usrName");
                    return false;
                }
                $usrCobj = $usrMobj->getCobj();
                $sespsw = $this->mod->getValN('Password');
                if (!$usrCobj->authenticate($usrName, $sespsw)) {
                    $this->mod->getErrLog()->logLine(CstError::E_ERC057);
                    return false;
                }
                $this->mod->setValN('User', $usrMobj->getId());
            }
        }
               
        $roleName = null;
        $roleMobj = null;
        $roleClassName = null;
        
        if ($this->mod->existsAttr('RoleName')) {
            $roleName = $this->mod->getValN('RoleName');
            $this->mod->setValN('ActiveRole', null);
            $roleClassName = $this->mod->getModRef('ActiveRole');
            if (! is_null($roleName)) {
                $roleMobj= Find::byKey($roleClassName, 'Name', $roleName);
                if (is_null($roleMobj)) {
                    $this->mod->getErrLog()->logLine(CstError::E_ERC059.":$roleClassName:$roleName");
                    return false;
                }
            }
            
            if (! $this->mod->existsAttr('UserId')) {
                if (!$roleMobj) {
                    $roleMobj= Find::byKey($roleClassName, 'Name', 'Default');
                }
            }
            
            if ($this->mod->existsAttr('UserId')) {
                if (!$roleMobj and $usrMobj) {
                    $roleMobj = $usrMobj->getRef('Role');
                }
                if ($roleMobj and $usrCobj) {
                    if (! $usrCobj->checkAttr('Role', $roleMobj->getId())) {
                        $this->mod->getErrLog()->logLine(CstError::E_ERC060.":".$roleMobj->getVal('Name'));
                        return false;
                    }
                }
                if ($roleMobj and !$usrMobj) {
                    $roleDef= Find::byKey($roleClassName, 'Name', 'Default');
                    if (!$roleDef or $roleMobj->getId() != $roleDef->getId()) {
                        $this->mod->getErrLog()->logLine(CstError::E_ERC060.":".$roleMobj->getVal('Name'));
                        return false;
                    }
                }
                if (!$roleMobj and !$usrMobj) {
                    $roleMobj= Find::byKey($roleClassName, 'Name', 'Default');
                }
            }
            
            if (! $roleMobj) {
                $this->mod->getErrLog()->logLine(CstError::E_ERC064.":$roleClassName");
                return false;
            }
            $this->mod->setValN('ActiveRole', $roleMobj->getId());
            $this->mod->setValN('RoleName', $roleMobj->getVal('Name'));
        }

        $groupName = null;
        $groupMobj = null;
        $groupClassName = null;
        
        if ($this->mod->existsAttr('GroupName')) {
            $groupName = $this->mod->getValN('GroupName');
            $this->mod->setValN('ActiveGroup', null);
            $groupClassName = $this->mod->getModRef('ActiveGroup');
            if (! is_null($groupName)) {
                $groupMobj= Find::byKey($groupClassName, 'Name', $groupName);
                if (is_null($groupMobj)) {
                    $this->mod->getErrLog()->logLine(CstError::E_ERC059.":$groupClassName:$groupName");
                    return false;
                }
            }

            if (!$groupMobj and $usrMobj) {
                $groupMobj = $usrMobj->getRef('UserGroup');
            }
            
            if ($groupMobj and $usrMobj) {
                if (! $usrCobj->checkAttr('UserGroup', $groupMobj->getId())) {
                    $this->mod->getErrLog()->logLine(CstError::E_ERC060.":".$groupMobj->getVal('Name'));
                    return false;
                }
            }
            
            if ($groupMobj) {
                $this->mod->setValN('ActiveGroup', $groupMobj->getId());
                $this->mod->setValN('GroupName', $groupMobj->getVal('Name'));
            }
        }
        
        $this->roleSpecLoaded=false;
        $this->mod->setVal('Checked', 1);
        return $this->mod->saveN();
    }
    
    public function delet()
    {
        $flag = $this->mod->getValN('ValidFlag');
        if (!$flag and ! $this->Keep) {
            return $this->mod->deletN();
        }
        $this->mod->setValN('ValidFlag', 0);
        return $this->mod->saveN();
    }

    public static function getSession($id, $attrValList = [])
    {
        $modName=  get_called_class();
        if ($name =substr(strrchr($modName, '\\'), 1)) {
            $modName = $name;
        }
        $mod = new Model($modName);
        $sessionHdl=$mod->getCObj();
        $obj= $mod->getBkey('BKey', $id);
        if (is_null($obj)) {
            foreach ($attrValList as $attr => $val) {
                $mod->setVal($attr, $val);
            }
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
        if ($this->mod->existsAttr('RoleName')) {
            $roleMobj=$pobj->getRef('ActiveRole');
            if ($roleMobj) {
                $this->mod->setValN('RoleName', $roleMobj->getVal('Name'));
            }
        }
        if ($this->mod->existsAttr('GroupName')) {
            $groupMobj=$pobj->getRef('ActiveGroup');
            if ($groupMobj) {
                $this->mod->setValN('GroupName', $groupMobj->getVal('Name'));
            }
        }
        $val = $pobj->getValN('Name');
        $this->mod->setValN('Name', $val);
    }
    
    public function isNew()
    {
        return ($this->isNew);
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
    
    public function getAttrPathVal($obj, $attrPath)
    {
        return Access::getAttrPathVal($obj, $attrPath);
    }
    
    public function checkARight($req, $attrObjs, $protect, $plast = true)
    {
        return Access::checkARight($this->mod, $req, $attrObjs, $protect, $plast);
    }
}
