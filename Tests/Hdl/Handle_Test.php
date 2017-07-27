<?php
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;

use ABridge\ABridge\Handler;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mtype;

class Handle_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;

    protected $CName;
    protected $CUser;
    protected $CCode;
    
    protected $db;
    
    
    public static function setUpBeforeClass()
    {
        $prm=[
                'path'=>'C:/Users/pierr/ABridge/Datastore/',
                'host'=>'localhost',
                'user'=>'cl822',
                'pass'=>'cl822'
        ];
        Handler::get()->resetHandlers();
    
        $typ='dataBase';
        $CName=get_called_class().'_1';
        $CUser=get_called_class().'_2';
        $CCode=get_called_class().'_3';
        $name = 'test';
        self::$db1= Handler::get()->setBase($typ, $name, $prm);
        Handler::get()->setStateHandler($CName, $typ, $name);
        Handler::get()->setStateHandler($CUser, $typ, $name);
        Handler::get()->setStateHandler($CCode, $typ, $name);
        
        $typ='fileBase';
        $name=$name.'_f';
        $CName=get_called_class().'_f_1';
        $CUser=get_called_class().'_f_2';
        $CCode=get_called_class().'_f_3';
        
        self::$db2= Handler::get()->setBase($typ, $name, $prm);
        Handler::get()->setStateHandler($CName, $typ, $name);
        Handler::get()->setStateHandler($CUser, $typ, $name);
        Handler::get()->setStateHandler($CCode, $typ, $name);
    }
    
    public function setTyp($typ)
    {
        if ($typ== 'SQL') {
            $this->db=self::$db1;
            $this->CName=get_called_class().'_1';
            $this->CUser=get_called_class().'_2';
            $this->CCode=get_called_class().'_3';
        } else {
            $this->db=self::$db2;
            $this->CName=get_called_class().'_f_1';
            $this->CUser=get_called_class().'_f_2';
            $this->CCode=get_called_class().'_f_3';
        }
    }
    
    public function Provider1()
    {
        return [['SQL'],['FLE']];
    }
    /**
     * @dataProvider Provider1
     */

    public function testRoot($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();

        $ho = null;
        
// code
        $mod = new Model($this->CCode);
        $res = $mod->deleteMod();
        $res = $mod->saveMod();
        $r = $mod-> getErrLog();
        $r->show();
        $this->assertfalse($mod->isErr());

        $hc1 = new Handle('/'.$this->CCode, CstMode::V_S_CREA, $ho);
        $hc1->save();
        $this->assertfalse($hc1->isErr());
        
        $hc2 = new Handle('/'.$this->CCode, CstMode::V_S_CREA, $ho);
        $hc2->save();
        $this->assertfalse($hc2->isErr());


// User		
        $mod = new Model($this->CUser);
        $res = $mod->deleteMod();
        $res = $mod->saveMod();
        $this->assertfalse($mod->isErr());

        $hu1 = new Handle('/'.$this->CUser, CstMode::V_S_CREA, $ho);
        $hu1->save();
        $this->assertfalse($hu1->isErr());
        
        $hu2 = new Handle('/'.$this->CUser, CstMode::V_S_CREA, $ho);
        $hu2->save();
        $this->assertfalse($hu2->isErr());


// Class
        
        $mod = new Model($this->CName);
        $res= $mod->deleteMod();
        $res = $mod->addAttr('Ref', Mtype::M_REF, '/'.$this->CName);
        $res = $mod->addAttr('CRef', Mtype::M_CREF, '/'.$this->CName.'/Ref');
        $res = $mod->addAttr('Code', Mtype::M_CODE, '/'.$this->CCode);
        $res = $mod->addAttr($this->CUser, Mtype::M_REF, '/'.$this->CUser);
        $res = $mod->setDflt($this->CUser, 1);
        $res = $mod->saveMod();
        $this->assertfalse($mod->isErr());
        
        $ho1 = new Handle('/'.$this->CName, CstMode::V_S_CREA, $ho);
        $res=$ho1->setVal($this->CUser, $hu1->getId());
        $res=$ho1->setVal('Code', $hc2->getId());
        $id1 = $ho1->save();
        $obj1 = $ho1;
        $this->assertfalse($ho1->isErr());
        
        $ho2 = new Handle('/'.$this->CName, CstMode::V_S_CREA, $ho);
        $res=$ho2->setVal($this->CUser, $hu2->getId());
        $res=$ho2->setVal('Code', $hc2->getid());
        $res=$ho2->setVal('Ref', $ho1->getId());
        $id2 = $ho2->save();
        $this->assertfalse($ho2->isErr());

        $ho3 = new Handle('/'.$this->CName, CstMode::V_S_CREA, $ho);
        $res=$ho3->setVal($this->CUser, $hu1->getId());
        $res=$ho3->setVal('Code', $hc2->getId());
        $res=$ho3->setVal('Ref', $ho2->getId());
        $id3 = $ho3->save();
        $this->assertfalse($ho3->isErr());

        $ho4 = new Handle('/'.$this->CName, CstMode::V_S_CREA, $ho);
        $res=$ho4->setVal($this->CUser, $hu1->getId());
        $res=$ho4->setVal('Code', $hc2->getId());
        $res=$ho4->setVal('Ref', $ho3->getId());
        $id4 = $ho4->save();
        $this->assertfalse($ho4->isErr());
                
// 		
        $path0= '/'.$this->CName;
        $path1 = $path0.'/'.$ho1->getId();
        $rid = $ho1->getId();

    
        $h1 = new Handle($path1, CstMode::V_S_READ, $ho);
        $this->assertNotNull($h1);
        $this->assertTrue($h1->isMain());
        $this->assertEquals($path1, $h1->getRPath());
        $this->assertNull($h1->getRef('Ref'));
                
        $path2=$path1.'/CRef/'.$ho2->getId();
        $h2 = $h1->getCref('CRef', $ho2->getId());
        $this->assertNotNull($h2);
        $this->assertFalse($h2->isMain());
        $this->assertEquals($ho2->getId(), $h2->getId());
        $this->assertEquals($path2, $h2->getRpath());
        $this->assertTrue($h2->isMainRef('Ref'));
        
        $path3=$path2.'/CRef/'.$ho3->getId();
        $h3 = new Handle($path3, CstMode::V_S_READ, $ho);
        $this->assertNotNull($h3);
        $this->assertEquals($ho3->getId(), $h3->getId());
        $this->assertEquals($path3, $h3->getRpath());
        $this->assertFalse($h3->isMainRef('Ref'));
        
        $h2r = $h3->getRef('Ref');
        $this->assertEquals($h2->getId(), $h2r->getId());
        $this->assertEquals($h2->getRPath(), $h2r->getRPath());
        
        $h = new Handle($path0, CstMode::V_S_CREA, $ho);
        $ht = new Handle($path0, $ho);
        $ht->setAction(CstMode::V_S_CREA);
        $url= $ht->getUrl();
        $urle= $h->getUrl();
        $this->assertEquals($urle, $url);
        $url= $h1->getActionUrl(CstMode::V_S_CREA, []);
        $this->assertEquals($urle, $url);
        
        $h = new Handle($path0, CstMode::V_S_SLCT, $ho);
        $h=$h->getObjId($h1->getId());
        $this->assertEquals($h1->getUrl(), $h->getUrl());

        $url = $h1->getCrefUrl('CRef', CstMode::V_S_CREA, []);
        $res='"'.$h1->getDocRoot().$path0.'/'.$h1->getId().'/CRef?Action='.CstMode::V_S_CREA.'"';
        $this->assertEquals($res, $url);
        
        $id = $h1->getVal('Code');
        $hc1r=$h1->getCode('Code', $id);
        $this->assertEquals($hc2->getUrl(), $hc1r->getUrl());

        
        $h=$h1;
        $this->assertFalse($h->nullObj());
        $this->assertEquals($this->CName, $h->getModName());
        $this->assertEquals($this->CName, $h->getModCref('CRef'));

        $aList = $h->getAttrList();
        $id= $h->getId();
        $this->assertEquals(8, count($aList));
        $this->assertEquals(Mtype::M_REF, $h->getTyp('Ref'));
        $this->assertEquals($hu1->getId(), $h->getDflt($this->CUser));
        $this->assertEquals($id, $h->getVal('id'));
        $this->assertEquals(2, count($h->getValues($this->CUser)));
        $this->assertFalse($h->isProtected($this->CUser));
        $this->assertFalse($h->isMdtr($this->CUser));
        $this->assertFalse($h->isEval($this->CUser));
        $this->assertTrue($h->isModif($this->CUser));
        $this->assertTrue($h->isSelect($this->CUser));
        $this->assertTrue($h->existsAttr($this->CUser));
        $this->assertEquals($id, $h->save());
        $this->assertFalse($h->isErr());
        $this->assertEquals(0, $h->getErrLog()->logSize());
        $this->assertTrue($h->setCriteria(['Ref'], ['='], [1]));
        $this->assertEquals(1, count($h->select()));
        
        
        $h = new Handle('/', CstMode::V_S_READ, $ho);
        $this->assertTrue($h->nullObj());

        $path= '/'.$this->CName.'/'.$h1->getId().'/CRef';
        $h = new Handle($path, CstMode::V_S_CREA, $ho);
        $id = $h ->save();
        $this->assertNotNull($id);
        $res = $h->delet();
        $this->assertTrue($res);
        $this->assertEquals(0, $h->getId());

        return $rid;
        
        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     */
    /**
    * @depends testRoot
    */
    public function itestTilt($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $rid = 1;
        
        $mod = new Model($this->CName, $rid);
        
        $sess = new sessionHdl($mod, null);
        $he = new Handle('/'.$this->CName.'/~', $sess);
        
        $this->assertEquals($rid, $he->getId());
        
        
        $sess = new sessionHdl($mod);
        
        $he= new handle('/Ref/~', $sess);
        $this->assertTrue($he->nullObj());
        
        $db->commit();
    }
    
    /**
     * @dataProvider Provider1
     */
    /**
    * @depends estHomeObj
    */
    public function nestHome($typ)
    {
        $this->setTyp($typ);
        $db=$this->db;
        $db->beginTrans();
        
        $mod = new Model($this->CName);
        
        
        $db->commit();
    }
}
