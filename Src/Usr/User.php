<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CModel;
use ABridge\ABridge\CstError;
use ABridge\ABridge\Mtype;

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
        $res = $obj->addAttr('NewPassword1', Mtype::M_STRING, M_P_TEMP);
        $res = $obj->addAttr('NewPassword2', Mtype::M_STRING, M_P_TEMP);
        $res = $obj->addAttr('MetaData', Mtype::M_TXT, M_P_EVAL);
        
        $res = $obj->setBkey('UserId', true);
        
        if (isset($bindings['Distribution'])) {
            $distribution=$bindings['Distribution'];
            $res = $obj->addAttr('Play', Mtype::M_CREF, '/'.$distribution.'/toUser');
        }
        if (isset($bindings['Role'])) {
            $role = $bindings['Role'];
            $res = $obj->addAttr('DefaultRole', Mtype::M_REF, '/'.$role);
        }
       
        return $obj->isErr();
    }

    public function setVal($attr, $val)
    {
        if ($attr == 'DefaultRole' and !is_null($val)) {
            $vals= $this->getValues('DefaultRole');
            $res = in_array($val, $vals);
            if (!$res) {
                $this->mod->getErrLog()->logLine(CstError::E_ERC016.':'.$attr.':'.$val);
                return false;
            }
        }
        return $this->mod->setValN($attr, $val);
    }
    
    
    
    public function getVal($attr)
    {
        if ($attr == 'Password') {
            return null;
        }
        if ($attr == 'MetaData') {
            return $this->mod->getMeta();
        }
        return $this->mod->getValN($attr);
    }

    public function getValues($attr)
    {
        if ($attr == 'DefaultRole' and $this->mod->existsAttr('Play')) {
            $res = [];
            $dist=$this->mod->getValN('Play');
            foreach ($dist as $id) {
                $obj = $this->mod->getCref('Play', $id);
                $res[]=$obj->getValN('ofRole');
            }
            return $res;
        }
        return $this->mod->getValuesN($attr);
    }

    public function checkRole($id)
    {
        $res= $this->getValues('DefaultRole');
        return in_array($id, $res);
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
