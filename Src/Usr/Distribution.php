<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

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
}
