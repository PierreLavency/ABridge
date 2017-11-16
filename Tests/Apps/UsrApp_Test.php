<?php

use ABridge\ABridge\Apps\UsrApp;

use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Distribution;
use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\UserGroup;
use ABridge\ABridge\Usr\GroupUser;

class UserApp_Test_fileBase_User extends User
{
}

class UserApp_Test_fileBase_Role extends Role
{
}

class UserApp_Test_fileBase_Session extends Session
{
}

class UserApp_Test_fileBase_Distribution extends Distribution
{
}

class UserApp_Test_fileBase_UserGroup extends UserGroup
{
}

class UserApp_Test_fileBase_GroupUser extends GroupUser
{
}

class UserApp_Test extends PHPUnit_Framework_TestCase
{
    
    public function testInit()
    {
        Mod::reset();
        Usr::reset();
        
        $classes = [
                Usr::USER          ,
                Usr::ROLE          ,
                Usr::DISTRIBUTION  ,
                Usr::SESSION       ,
                Usr::USERGROUP     ,
                Usr::GROUPUSER     ,
                
        ];
        
        $prm=UtilsC::genPrm($classes, get_called_class(), ['fileBase']);
        
        $usrName=$prm['fileBase'][Usr::USER];
        
        $usr = new UsrApp($prm['application'], $prm['fileBase']);
        $usr->init();
                       
        $mod= Mod::get();
        
        
        $mod->begin();
        
        $res= $usr->initMeta();
        $this->assertEquals($prm['fileBase'], $res);
  
        
        $res= $usr->initData();
        $this->assertEquals([1], $res);
        
        $mod->end();
    }
}
