<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;
use ABridge\ABridge\CstError;

class Distribution extends CModel
{
    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        if (! isset($bindings['User']) or ! isset($bindings['Role'])) {
            return false;
        }

        $role = $bindings['Role'];
        $user = $bindings['User'];
        $res = $obj->addAttr('Role', Mtype::M_REF, '/'.$role);
        $res = $obj->addAttr('User', Mtype::M_REF, '/'.$user);
        
        $res = $obj->setMdtr('Role', true); // Mdtr
        $res = $obj->setMdtr('User', true); // Mdtr

        $obj->setCkey(['Role','User'], true);
        
        return $obj->isErr();
    }
    
    public function delet()
    {
        $user = $this->mod->getRef('User');
        if ($user->getVal('Role')==$this->mod->getVal('Role')) {
            $this->mod->getErrLog()->logLine(CstError::E_ERC052.':UserRole');
            return false;
        }
        return $this->mod->deletN();
    }
}
