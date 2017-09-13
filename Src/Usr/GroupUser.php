<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Mtype;

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
            return $this->mod->getMeta();
        }
        return $this->mod->getValN($attr);
    }
}
