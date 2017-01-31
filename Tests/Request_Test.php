<?php
	

require_once('Request.php');
require_once("ViewConstant.php");

class Request_Test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider Provider1
     */
 
	public function testRequest($a, $b, $c, $e1,$e2,$e3,$e4)
    {
		$_SERVER['PATH_INFO']=$a;
		$_SERVER['REQUEST_METHOD']=$b;
		if (is_null($c)) {		
			unset($_GET['View']);
			unset($_POST['action']);
		} else {
			$_GET['View']=$c;
			$_POST['action']=$c;
		}
		
		$r=new Request();
        $this->assertnotNull($r);
		$this->assertEquals($e1,$r->getAction());
		$this->assertEquals($e2,$r->isHomePath());
		$this->assertEquals($e3,$r->isClassPath());		
		$this->assertEquals($e4,$r->isObjPath());
		
	}
 
    public function Provider1() {
        return [
			['/', 	    'GET',null,		V_S_READ,true,false,false],
			['/X', 	    'GET',null,		V_S_SLCT,false,true,false],
			['/X/1', 	'GET',null,		V_S_READ,false,false,true],
            ['/X/1/X', 	'GET',null,		V_S_SLCT,false,true,false],
            ['/X/1/X/1','GET',null,		V_S_READ,false,false,true],
			
			['/', 	    'GET',V_S_UPDT,	V_S_UPDT,true,false,false],
			['/X', 	    'GET',V_S_CREA,	V_S_CREA,false,true,false],
			['/X/1', 	'GET',V_S_UPDT,	V_S_UPDT,false,false,true],
            ['/X/1/X', 	'GET',V_S_SLCT,	V_S_SLCT,false,true,false],
            ['/X/1/X/1','GET',V_S_DELT,	V_S_DELT,false,false,true],
			
			['/X', 	    'POST',null,	V_S_CREA,false,true,false],
			
			['/', 	    'POST',V_S_UPDT,V_S_UPDT,true,false,false],
			['/X', 	    'POST',V_S_CREA,V_S_CREA,false,true,false],
			['/X/1', 	'POST',V_S_UPDT,V_S_UPDT,false,false,true],
            ['/X/1/X', 	'POST',V_S_SLCT,V_S_SLCT,false,true,false],
            ['/X/1/X/1','POST',V_S_DELT,V_S_DELT,false,false,true],
	
 			];
    }

    /**
     * @dataProvider Provider2
     */
 	public function testGetAction($a, $b, $c, $d,$e1)
    {
		$_SERVER['PATH_INFO']=$a;
		$_SERVER['REQUEST_METHOD']=$b;
		if (is_null($c)) {		
			unset($_GET['View']);
			unset($_POST['action']);
		} else {
			$_GET['View']=$c;
			$_POST['action']=$c;
		}
		
		$r=new Request();
        $this->assertnotNull($r);
		if (!is_null($e1)) {
			$e= $r->prfxPath($e1);
		} else {
			$e=null;
		}
		$this->assertEquals($e,$r->getActionPath($d));
	}
	
	public function Provider2() {
        return [
			['/', 	    'GET',null,		V_S_READ,'/'],
			['/', 	    'GET',null,		V_S_UPDT,'/?View='.V_S_UPDT],
			['/X', 	    'GET',null,		V_S_SLCT,'/X?View='.V_S_SLCT],
			['/X', 	    'GET',null,		V_S_READ,'/'],			
			['/X/1', 	'GET',null,		V_S_READ,'/X/1'],
			['/X/1', 	'GET',null,		V_S_SLCT,'/X?View='.V_S_SLCT],
            ['/X/1/X', 	'GET',null,		V_S_CREA,'/X/1/X?View='.V_S_CREA],
	        ['/X/1/X', 	'GET',null,		V_S_READ,'/X/1'],
	        ['/X/1/X', 	'GET',null,		V_S_UPDT,null],
		    ['/', 		'GET',null,		V_S_DELT,null],		
			['/X/1', 	'GET',null,		'X',null],			
            ['/X/1/X/1','GET',null,		V_S_DELT,'/X/1/X/1?View='.V_S_DELT],
 			];
    }
	
	public function testMethods() 
	{
			$r=new Request('/X/1/X',V_S_CREA);
			$this->assertnotNull($r);
			
			$this->assertEquals($r->objN(),2);
			$this->assertEquals(count($r->pathArr()),3);
			$res = $r->getClassPath('X',V_S_CREA);
			$this->assertEquals($res,$r->prfxPath('/X?View='.V_S_CREA));

			
			$r=new Request('/X/1/X/1',V_S_READ);
			$this->assertnotNull($r);
			
			$res = $r->getCrefPath('X',V_S_CREA);
			$this->assertEquals($res,$r->prfxPath('/X/1/X/1/X?View='.V_S_CREA));
			
			$p = $r->pop();
			$this->assertEquals('/X/1',$p);
			
			$r=new Request('/X/1',V_S_READ);
			$this->assertnotNull($r);
			$p = $r->pop();
			$this->assertEquals('/',$p);
			
			
			
	}
	
	
	
	
}

?>	