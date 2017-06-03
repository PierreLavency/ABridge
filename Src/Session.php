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

    public function initMod()
    {
        $obj = $this->mod;
        $user='User';
        $role='Role';
        
        $res = $obj->addAttr($user, M_REF, '/'.$user);
        $res = $obj->addAttr($role, M_REF, '/'.$role);
        $res = $obj->addAttr('UserId', M_STRING);
        $res = $obj->addAttr('Password', M_STRING, M_P_TEMP);
        $res = $obj->addAttr('BKey', M_STRING);
        $res = $obj->addAttr('ValidStart', M_INT, M_P_EVALP);
        $res = $obj->addAttr('ValidFlag', M_INT, M_P_EVALP);
        
        $res = $obj->setBkey('BKey', true);
        $res = $obj->setMdtr('BKey', true);
        return $obj->isErr();
    }

    public function initPrev($pobj)
    {
        $val = $pobj->getValN('UserId');
        $this->mod->setValN('UserId', $val);
        $val = $pobj->getValN('Role');
        $this->mod->setValN('Role', $val);
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
        $usrn = $this->mod->getValN('UserId');
        $role = $this->mod->getValN('Role');
        $id = $this->mod->getId();
        $roleobj= Find::byKey('Role', 'Name', 'Default');
        $this->mod->setValN('User', null);
        if (! is_null($usrn)) {
            $usrobj= Find::byKey('User', 'UserId', $usrn);
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
            if ($role) {
                if (! $usrcobj->checkRole($role)) {
                    $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                    return false;
                }
            } else {
                $role = $usrobj->getVal('DefaultRole');
                if (! $role) {
                    $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                    return false;
                }
                $this->mod->setValN('Role', $role);
            }
            $this->mod->setValN('User', $usrobj->getId());
        } else {
            if (!is_null($role) and (is_null($roleobj) or ($role != $roleobj->getId()))) {
                $this->mod->getErrLog()->logLine(E_ERC060.":$role");
                return false;
            }
            if (is_null($role) and (! is_null($roleobj))) {
                $role = $roleobj->getId();
                $this->mod->setValN('Role', $role);
            }
        }
        if (! $id) {
            $this->mod->setVal('ValidStart', time());
            $this->mod->setVal('ValidFlag', 1);
            $this->mod->setVal('BKey', $this->Bkey);
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
