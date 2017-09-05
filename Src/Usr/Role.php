<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

class Role extends CModel
{
    public function initMod($bindings)
    {
        $obj = $this->mod;
        $distribution = null;
        $role = null;

        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->addAttr('JSpec', Mtype::M_JSON);

        $res = $obj->setBkey('Name', true);
        
        if (isset($bindings['Distribution'])) {
            $distribution=$bindings['Distribution'];
            $res = $obj->addAttr('Users', Mtype::M_CREF, '/'.$distribution.'/Role');
        }
        
        return $obj->isErr();
    }
    
    public function getSpec()
    {
        $res = $this->mod->getValN('JSpec');
        if (! $res) {
            return $res;
        }
        $val = json_decode($res, true);
        return $val;
    }
}
