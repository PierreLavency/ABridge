<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\CModel;

class Role extends CModel
{
    public function initMod($bindings)
    {
        $obj = $this->mod;
        $distribution = null;
        $role = null;

        $res = $obj->addAttr('Name', M_STRING);
        $res = $obj->addAttr('JSpec', M_JSON);

        $res = $obj->setBkey('Name', true);
        
        if (isset($bindings['Distribution'])) {
            $distribution=$bindings['Distribution'];
            $res = $obj->addAttr('PlayedBy', M_CREF, '/'.$distribution.'/ofRole');
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
