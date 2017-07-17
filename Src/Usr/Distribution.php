<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CModel;
use ABridge\ABridge\Mtype;

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
        $res = $obj->addAttr('ofRole', Mtype::M_REF, '/'.$role);
        $res = $obj->addAttr('toUser', Mtype::M_REF, '/'.$user);
        
        $res = $obj->setMdtr('ofRole', true); // Mdtr
        $res = $obj->setMdtr('toUser', true); // Mdtr

        $obj->setCkey(['ofRole','toUser'], true);
        
        return $obj->isErr();
    }
}
