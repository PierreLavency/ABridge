<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mod\Mtype;

class User extends CModel
{

    private $psw=null;
   
    public function __construct($mod)
    {
        $this->mod=$mod;
        if ($mod->existsAttr('Password')) {
            $this->psw = $this->mod->getValN('Password');
        }
    }

    public function initMod($bindings)
    {
        $obj = $this->mod;
        $distribution = null;
        $role = null;
        
        $res = $obj->addAttr('UserId', Mtype::M_STRING);
        $res = $obj->addAttr('Password', Mtype::M_STRING);
        
        $res = $obj->addAttr('NewPassword1', Mtype::M_STRING);
        $res = $obj->setProp('NewPassword1', Model::P_TMP);
 
        $res = $obj->addAttr('NewPassword2', Mtype::M_STRING);
        $res = $obj->setProp('NewPassword2', Model::P_TMP);
        
        $res = $obj->addAttr('MetaData', Mtype::M_TXT);
        $res = $obj->setProp('MetaData', Model::P_EVL);
        $res = $obj->setProp('MetaData', Model::P_TMP);
        
        $res = $obj->setBkey('UserId', true);
 
        if (isset($bindings['UserGroup'])) {
            $usergroup = $bindings['UserGroup'];
            $res = $obj->addAttr('UserGroup', Mtype::M_REF, '/'.$usergroup);
        }

        if (isset($bindings['GroupUser'])) {
            $groupuser=$bindings['GroupUser'];
            $res = $obj->addAttr('UserGroups', Mtype::M_CREF, '/'.$groupuser.'/User');
        }
        
        if (isset($bindings['Role'])) {
            $role = $bindings['Role'];
            $res = $obj->addAttr('Role', Mtype::M_REF, '/'.$role);
        }
        if (isset($bindings['Distribution'])) {
            $distribution=$bindings['Distribution'];
            $res = $obj->addAttr('Roles', Mtype::M_CREF, '/'.$distribution.'/User');
        }
       
        return $obj->isErr();
    }

    public function getVal($attr)
    {
        if ($attr == 'Password') {
            return null;
        }
        if ($attr == 'MetaData') {
            return json_encode($this->mod->getMeta(), JSON_PRETTY_PRINT);
        }
        return $this->mod->getValN($attr);
    }
    
    public function setVal($attr, $val)
    {
        $res=$this->checkAttr($attr, $val);
        if (!$res) {
            $this->mod->getErrLog()->logLine(CstError::E_ERC016.':'.$attr.':'.$val);
            return false;
        }
        return $this->mod->setValN($attr, $val);
    }
    
    public function getValues($attr)
    {
        if ($attr == 'Role' and $this->mod->existsAttr('Roles')) {
            $res = [];
            $dist=$this->mod->getValN('Roles');
            foreach ($dist as $id) {
                $obj = $this->mod->getCref('Roles', $id);
                $res[]=$obj->getVal('Role');
            }
            return $res;
        }
        if ($attr=='UserGroup' and $this->mod->existsAttr('UserGroups')) {
            $res = [];
            $groups = $this->mod->getVal('UserGroups');
            foreach ($groups as $id) {
                $obj = $this->mod->getCref('UserGroups', $id);
                $res[]=$obj->getVal('UserGroup');
            }
            return $res;
        }
        return $this->mod->getValuesN($attr);
    }

    public function checkAttr($attr, $val)
    {
        if ($attr == 'Role' and !is_null($val)) {
            $vals= $this->getValues('Role');
            $res = in_array($val, $vals);
            return $res;
        }
        if ($attr == 'UserGroup' and !is_null($val)) {
            $vals= $this->getValues('UserGroup');
            $res = in_array($val, $vals);
            return $res;
        }
        return true;
    }
    
    public function save()
    {
        $psw = $this->mod->getValN('Password');
        if (! is_null($this->psw)) {
            $res= password_verify($psw, $this->psw);
            if (!$res) {
                $this->mod->getErrLog()->logLine(CstError::E_ERC057);
                return false;
            }
        }
        $psw1=$this->mod->getValN('NewPassword1');
        $psw2=$this->mod->getValN('NewPassword2');
        if ($psw1 != $psw2) {
            $this->mod->getErrLog()->logLine(CstError::E_ERC058);
                return false;
        }
        if (! is_null($psw1)) {
            $this->psw = password_hash($psw1, PASSWORD_DEFAULT);
        }
        $this->mod->setValN('Password', $this->psw);
        return $this->mod->saveN();
    }

    public function authenticate($userN, $psw)
    {
        $nme= $this->mod->getValN('UserId');
        if ($nme!= $userN) {
            return false;
        }
        $pswU = $this->mod->getValN('Password');
        if (is_null($psw) and is_null($pswU)) {
            return true;
        }
        return  password_verify($psw, $pswU);
    }
}
