<?php

class Session extends CModel
{

    protected $timer= 6000;
    protected $Bkey = 0;
    protected $user;
    protected $role;

    public function __construct($mod)
    {
        $this->mod=$mod;
        if (!$mod->getId()) {
            $this->Bkey=uniqid();
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
    
    public function getKey()
    {
        if (!$this->mod->getId()) {
            return $this->Bkey;
        }
        return $this->mod->getValN('BKey');
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
            $this->mod->setVal('BKey', $this->Bkey);
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
        if (!$flag) {
            return $this->mod->deletN();
        } else {
            $this->mod->setValN('ValidFlag', 0);
            return $this->mod->saveN();
        }
    }
    
    public function findValidSession($id)
    {
        $obj= $this->mod->getBkey('BKey', $id);
        if (is_null($obj)) {
            return [null,null];
        }
        $flag = $obj->getValN('ValidFlag');
        if (!$flag) {
            return [null,$obj]; // null ?
        }
        $valid = $obj->getValN('ValidStart')+$this->timer;
        if ($valid < time()) {
            $obj->setValN('ValidFlag', 0);
            return [null,$obj];
        }
        return  [$obj,$obj];
    }
}
