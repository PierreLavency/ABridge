<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\CstError;

class GroupUser extends CModel
{
    public function initMod($bindings)
    {
        $obj = $this->mod;

        if (! isset($bindings['User']) or ! isset($bindings['UserGroup'])) {
            return false;
        }
             
        $user = $bindings['User'];
        $group = $bindings['UserGroup'];
        
        $res = $obj->addAttr('User', Mtype::M_REF, '/'.$user);
        $res = $obj->addAttr('UserGroup', Mtype::M_REF, '/'.$group);
        $res = $obj->addAttr('MetaData', Mtype::M_TXT, M_P_EVAL);
        
        $res = $obj->setMdtr('User', true); // Mdtr
        $res = $obj->setMdtr('UserGroup', true); // Mdtr
        
        $obj->setCkey(['User','UserGroup'], true);
        

        return $obj->isErr();
    }
    
    public function getVal($attr)
    {
        if ($attr == 'MetaData') {
            return json_encode($this->mod->getMeta(), JSON_PRETTY_PRINT);
        }
        return $this->mod->getValN($attr);
    }
    
    public function delet()
    {
        $user = $this->mod->getRef('User');
        if ($user->getVal('UserGroup')==$this->mod->getVal('UserGroup')) {
            $this->mod->getErrLog()->logLine(CstError::E_ERC052.':UserGroup');
            return false;
        }
        return $this->mod->deletN();
    }
}
