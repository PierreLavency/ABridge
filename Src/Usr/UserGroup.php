<?php
namespace ABridge\ABridge\Usr;

use ABridge\ABridge\Mod\CModel;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

class UserGroup extends CModel
{
    public function initMod($bindings)
    {
        $obj = $this->mod;
        
        $res = $obj->addAttr('Name', Mtype::M_STRING);
        $res = $obj->setBkey('Name', true);
        
        $res = $obj->addAttr('MetaData', Mtype::M_TXT);
        $res = $obj->setProp('MetaData', Model::P_EVL);
        $res = $obj->setProp('MetaData', Model::P_TMP);
 
        if (isset($bindings['GroupUser'])) {
            $groupuser =$bindings['GroupUser'];
            $res = $obj->addAttr('Users', Mtype::M_CREF, '/'.$groupuser.'/UserGroup');
        }

        
        return $obj->isErr();
    }
    
    public function getVal($attr)
    {
        if ($attr == 'MetaData') {
            return json_encode($this->mod->getMeta(), JSON_PRETTY_PRINT);
        }
        return $this->mod->getValN($attr);
    }
}
