<?php
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Mtype;

use ABridge\ABridge\Usr\Session;
use ABridge\ABridge\Usr\User;
use ABridge\ABridge\Usr\Role;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\CstError;

class Handle_Usr_Test_dataBase_User extends User
{
}
class Handle_Usr_Test_fileBase_User extends User
{
}

class Handle_Usr_Test_dataBase_Role extends Role
{
}
class Handle_Usr_Test_fileBase_Role extends Role
{
}

class Handle_Usr_Test_dataBase_Session extends Session
{
}
class Handle_Usr_Test_fileBase_Session extends Session
{
}


class Handle_Usr_Test extends PHPUnit_Framework_TestCase
{

    
   
    public function testInit()
    {
        $classes = [Usr::SESSION,Usr::USER,Usr::ROLE];
        
        $prm=UtilsC::genPrm($classes, get_called_class());
                
        Mod::get()->reset();
        Usr::get()->reset();
        
        $mod= Mod::get();
        $usr= Usr::get();
        
        $prm['application']['base']='fileBase';
        $usr->init($prm['application'], $prm['fileBase']);
        $prm['application']['base']='dataBase';
        $usr->init($prm['application'], $prm['dataBase']);
        
        $mod->begin();
        
        $res = usr::initMeta($prm['application'], $prm['dataBase']);
        $res = $res and usr::initMeta($prm['application'], $prm['fileBase']);
                
        $mod->end();
        
        $classes=['Name',Usr::SESSION,Usr::USER,Usr::ROLE];
        $prm=UtilsC::genPrm($classes, get_called_class());
        
        $mod->init($prm['application'], $prm['handlers']);

        $this->assertTrue($res);
        
        return $prm;
    }
    /**
     * @depends testInit
     */
    public function testSave($prm)
    {
        Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            Mod::get()->begin();
            
            $ho = null;

    
    // User
            $hu1 = new Handle('/'.$bd['User'], CstMode::V_S_CREA, $ho);
            $hu1->setVal('UserId', 'U1');
            $hu1->save();
            $this->assertEquals(1, $hu1->getId());
            
            $hu2 = new Handle('/'.$bd['User'], CstMode::V_S_CREA, $ho);
            $hu2->setVal('UserId', 'U2');
            $hu2->save();
            $this->assertEquals(2, $hu2->getId());

    // Role
            $path='|'.$bd['Name'];
            $rolespec =[
                    [CstMode::V_S_SLCT,                         $path,                    'true'],
                    [CstMode::V_S_READ,                         $path,                    'true'],
                    [CstMode::V_S_CREA,                         $path,                [$bd['Name']=>"User"]],
                    [CstMode::V_S_UPDT,                         $path,                [$bd['Name']=>"User"]],

            ];
            $rlspec = json_encode($rolespec);
            
            $hu1 = new Handle('/'.$bd['Role'], CstMode::V_S_CREA, $ho);
            $hu1->setVal('JSpec', $rlspec);
            $hu1->setVal('Name', 'Default');
            $hu1->save();
            $this->assertEquals(1, $hu1->getId());

    //  Sessions
    
            $x = new Handle('/'.$bd['Session'], CstMode::V_S_CREA, $ho);
            $x->setVal('UserId', 'U1');
            $x->setVal('RoleName', 'Default');
            $res=$x->save();
            $this->assertEquals(1, $x->getId());
            $res=$x->save();
            $this->assertfalse($x->isErr());

            
            $x = new Handle('/'.$bd['Session'], CstMode::V_S_CREA, $ho);
            $x->setVal('UserId', 'U2');
            $x->setVal('RoleName', 'Default');
            $res=$x->save();
            $this->assertEquals(2, $x->getId());
            $res=$x->save();
            $x->getErrLog()->show();

            
            $mod = new Model($bd['Name']);
            $res= $mod->deleteMod();
            $res = $mod->addAttr('Ref', Mtype::M_REF, '/'.$bd['Name']);
            $res = $mod->addAttr('CRef', Mtype::M_CREF, '/'.$bd['Name'].'/Ref');
            $res = $mod->addAttr('User', Mtype::M_REF, '/'.$bd['User']);
            $res = $mod->saveMod();
            $this->assertfalse($mod->isErr());
            
            Mod::get()->end();
        }
        return $prm;
    }

    /**
     * @depends testSave
     */
    public function testInitHandle($prm)
    {
        Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            Mod::get()->begin();

            $y = new Model($bd['Session'], 1);
            $s1 = $y->getCobj();
            $y = new Model($bd['Session'], 2);
            $s2 = $y->getCobj();
            
            $ho1 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $s1);
            $id1 = $ho1->save();
            $this->assertEquals(1, $ho1->getId());
            $this->assertEquals(1, $ho1->getVal('User'));
            
            $ho2 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $s2);
            $res=$ho2->setVal('Ref', 1);
            $id2 = $ho2->save();
            $this->assertEquals(2, $ho2->getId());
            $this->assertEquals(2, $ho2->getVal('User'));
            
            $ho3 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $s1);
            $res=$ho3->setVal('Ref', 2);
            $id3 = $ho3->save();
            $this->assertEquals(3, $ho3->getId());
            
            $ho4 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $s1);
            $res=$ho4->setVal('Ref', 3);
            $id4 = $ho4->save();
            $this->assertEquals(4, $ho4->getId());
            
            Mod::get()->end();
        }
        return $prm;
    }
    
    /**
     * @depends testInitHandle
     */
    public function testUpd($prm)
    {
        Mod::get();
        
        foreach ($prm['bindL'] as $bd) {
            Mod::get()->begin();
            $y = new Model($bd['Session'], 1);
            $s1 = $y->getCobj();
            $y = new Model($bd['Session'], 2);
            $s2 = $y->getCobj();

            
            $ho1 = new Handle('/'.$bd['Name'].'/1', CstMode::V_S_READ, $s2);
            $this->assertEquals(1, $ho1->getId());

            $this->assertNull($ho1->getActionUrl(CstMode::V_S_UPDT, null));
            $this->assertNull($ho1->getCrefUrl('CRef', CstMode::V_S_CREA, null));
            $this->assertNull($ho1->getCref('CRef', 2));
            $this->assertEquals(['/'.$bd['Name']], $ho1->getSelPath());
            
            $res="";
            try {
                $ho1 = new Handle('/'.$bd['Name'].'/1', CstMode::V_S_UPDT, $s2);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals(CstError::E_ERC053.':"/ABridge.php/'.$bd['Name'].'/1?Action='.CstMode::V_S_UPDT.'"', $res);
            
            
            $res="";
            try {
                $ho1 = new Handle('/'.$bd['Name'].'x/1', CstMode::V_S_UPDT, $s2);
            } catch (Exception $e) {
                $res= $e->getMessage();
            }
            $this->assertEquals(CstError::E_ERC049.':"/ABridge.php/'.$bd['Name'].'x/1?Action='.CstMode::V_S_UPDT.'"', $res);
            
            Mod::get()->end();
        }
        return $prm;
    }
}
