<?php
use ABridge\ABridge\Hdl\Handle;
use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Mod\Mod;
use ABridge\ABridge\Mod\Mtype;

class Handle_Test extends PHPUnit_Framework_TestCase
{
    protected static $db1;
    protected static $db2;

    protected $CName;
    protected $CUser;
    protected $CCode;
    
    protected $db;
    
    
    public function testInit()
    {
    	$classes = ['Name','User','Code'];
    	$prm=UtilsC::genPrm($classes, get_called_class());
    	
    	Mod::get()->reset();
    	
    	$mod= Mod::get();
    	
    	$mod->init($prm['application'],$prm['handlers']);
    	
    	
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
	        
	// code
	        $mod = new Model($bd['Code']);
	        $res = $mod->deleteMod();
	        $res = $mod->saveMod();
	        $r = $mod-> getErrLog();
	        $r->show();
	        $this->assertfalse($mod->isErr());
	        
	        $hc1 = new Handle('/'.$bd['Code'], CstMode::V_S_CREA, $ho);
	        $hc1->save();
	        $this->assertEquals(1,$hc1->getId());
	        
	        $hc2 = new Handle('/'.$bd['Code'], CstMode::V_S_CREA, $ho);
	        $hc2->save();
	        $this->assertEquals(2,$hc2->getId());
	
	
	// User		
	        $mod = new Model($bd['User']);
	        $res = $mod->deleteMod();
	        $res = $mod->saveMod();
	        $this->assertfalse($mod->isErr());
	        
	        $hu1 = new Handle('/'.$bd['User'], CstMode::V_S_CREA, $ho);
	        $hu1->save();
	        $this->assertEquals(1,$hu1->getId());
	        
	        $hu2 = new Handle('/'.$bd['User'], CstMode::V_S_CREA, $ho);
	        $hu2->save();
	        $this->assertEquals(2,$hu2->getId());
	
	
	// Class
	        
	        $mod = new Model($bd['Name']);
	        $res= $mod->deleteMod();
	        $res = $mod->addAttr('Ref', Mtype::M_REF, '/'.$bd['Name']);
	        $res = $mod->addAttr('CRef', Mtype::M_CREF, '/'.$bd['Name'].'/Ref');
	        $res = $mod->addAttr('Code', Mtype::M_CODE, '/'.$bd['Code']);
	        $res = $mod->addAttr('User', Mtype::M_REF, '/'.$bd['User']);
	        $res = $mod->setDflt('User', 1);
	        $res = $mod->saveMod();
	        $this->assertfalse($mod->isErr());
	        
	        $ho1 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $ho);
	        $res=$ho1->setVal('User', $hu1->getId());
	        $res=$ho1->setVal('Code', $hc2->getId());
	        $id1 = $ho1->save();
	        $obj1 = $ho1;

	        $this->assertEquals(1,$ho1->getId());
	        
	        $ho2 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $ho);
	        $res=$ho2->setVal('User', $hu2->getId());
	        $res=$ho2->setVal('Code', $hc2->getid());
	        $res=$ho2->setVal('Ref', $ho1->getId());
	        $id2 = $ho2->save();
	        $this->assertEquals(2,$ho2->getId());
	
	        $ho3 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $ho);
	        $res=$ho3->setVal('User', $hu1->getId());
	        $res=$ho3->setVal('Code', $hc2->getId());
	        $res=$ho3->setVal('Ref', $ho2->getId());
	        $id3 = $ho3->save();
	        $this->assertEquals(3,$ho3->getId());
	
	        $ho4 = new Handle('/'.$bd['Name'], CstMode::V_S_CREA, $ho);
	        $res=$ho4->setVal('User', $hu1->getId());
	        $res=$ho4->setVal('Code', $hc2->getId());
	        $res=$ho4->setVal('Ref', $ho3->getId());
	        $id4 = $ho4->save();
	        $this->assertEquals(4,$ho4->getId());
	        Mod::get()->end();
    	}
    	return $prm;
    }
	
    /**
     * @depends testSave
     */
    public function testHandle($prm)
    {
    	Mod::get();
    	
    	foreach ($prm['bindL'] as $bd) {
    		
    		Mod::get()->begin();
    		
	        $ho = null;
	        
	        $path0= '/'.$bd['Name'];
	        $path1 = $path0.'/1';	
	    
	        $h1 = new Handle($path1, CstMode::V_S_READ, $ho);
	        $this->assertNotNull($h1);
	        $this->assertTrue($h1->isMain());
	        $this->assertEquals($path1, $h1->getRPath());
	        $this->assertNull($h1->getRef('Ref'));
	                
	        $path2=$path1.'/CRef/2';
	        $h2 = $h1->getCref('CRef', 2);
	        $this->assertNotNull($h2);
	        $this->assertFalse($h2->isMain());
	        $this->assertEquals(2, $h2->getId());
	        $this->assertEquals($path2, $h2->getRpath());
	        $this->assertTrue($h2->isMainRef('Ref'));
	        
	        $path3=$path2.'/CRef/3';
	        $h3 = new Handle($path3, CstMode::V_S_READ, $ho);
	        $this->assertNotNull($h3);
	        $this->assertEquals(3, $h3->getId());
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
	        $hc2=new Handle('/'.$bd['Code'].'/2', CstMode::V_S_READ, $ho);
	        $this->assertEquals($hc2->getUrl(), $hc1r->getUrl());
	
	        
	        $h=$h1;
	        $this->assertFalse($h->nullObj());
	        $this->assertEquals($bd['Name'], $h->getModName());
	        $this->assertEquals($bd['Name'], $h->getModCref('CRef'));
	
	        $aList = $h->getAttrList();
	        $id= $h->getId();
	        $this->assertEquals(8, count($aList));
	        $this->assertEquals(Mtype::M_REF, $h->getTyp('Ref'));
	        $this->assertEquals(1, $h->getDflt('User'));
	        $this->assertEquals($id, $h->getVal('id'));
	        $this->assertEquals(2, count($h->getValues('User')));
	        $this->assertFalse($h->isProtected('User'));
	        $this->assertFalse($h->isMdtr('User'));
	        $this->assertFalse($h->isEval('User'));
	        $this->assertTrue($h->isModif('User'));
	        $this->assertTrue($h->isSelect('User'));
	        $this->assertTrue($h->existsAttr('User'));
	        $this->assertEquals($id, $h->save());
	        $this->assertFalse($h->isErr());
	        $this->assertEquals(0, $h->getErrLog()->logSize());
	        $this->assertTrue($h->setCriteria(['Ref'], ['='], [1]));
	        $this->assertEquals(1, count($h->select()));
	        
	        $vnum= $h->getVal('vnum');
	        $this->assertTrue($h->checkVers($vnum));
	        $this->assertFalse($h->checkVers($vnum+1));
	        
	        $h = new Handle('/', CstMode::V_S_READ, $ho);
	        $this->assertTrue($h->nullObj());
	
	        $path= '/'.$bd['Name'].'/'.$h1->getId().'/CRef';
	        $h = new Handle($path, CstMode::V_S_CREA, $ho);
	        $id = $h ->save();
	        $this->assertNotNull($id);
	        $res = $h->delet();
	        $this->assertTrue($res);
	        $this->assertEquals(0, $h->getId());
        
		Mod::get()->end();
    	}
    	return $prm;
    }
    
    
}
