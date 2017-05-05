<?php
	

require_once 'Request.php';
require_once 'CstMode.php';

class Request_Test extends PHPUnit_Framework_TestCase {

	public function testR()
	{
		$_SERVER['REQUEST_METHOD']='GET';
		$r=new Request();
		$this->assertnotNull($r);
		$this->assertEquals($r->getRootUrl(),$r->getUrl());
	}
	

    /**
     * @dataProvider Provider1
     */
 
	public function testRequest($a, $b, $c, $e1,$e2,$e3,$e4,$e5,$e6,$e7)
    {
		$_SERVER['PATH_INFO']=$a;
		$_SERVER['REQUEST_METHOD']=$b;
		if (is_null($c)) {		
			unset($_GET['Action']);
			unset($_POST['Action']);
		} else {
			$_GET['Action']=$c;
			$_POST['Action']=$c;
		}
		
		$r=new Request();
		$this->assertEquals($a,$r->getRPath());
		$this->assertEquals($b,$r->getMethod());
		$this->assertEquals($e1,$r->getAction());
		$this->assertEquals($e2,$r->isRoot());
		$this->assertEquals($e3,$r->isClassPath());		
		$this->assertEquals($e4,$r->isObjPath());
		$this->assertEquals($e5,$r->ObjN());
		$this->assertEquals($e6,count($r->pathArr()));
		$this->assertEquals($e7,$r->getModPath());	
	}
 
    public function Provider1() {
        return [
			['/', 	    'GET',null,		V_S_READ,true,false,false,0,0,'|'],
			['/X', 	    'GET',null,		V_S_SLCT,false,true,false,0,1,'|X'],
			['/X/1', 	'GET',null,		V_S_READ,false,false,true,2,2,'|X'],
            ['/X/1/Y', 	'GET',null,		V_S_SLCT,false,true,false,2,3,'|X|Y'],
            ['/X/1/X/1','GET',null,		V_S_READ,false,false,true,4,4,'|X|X'],
			['/X', 	    'GET',V_S_CREA,	V_S_CREA,false,true,false,0,1,'|X'],
			['/X/1', 	'GET',V_S_UPDT,	V_S_UPDT,false,false,true,2,2,'|X'],
            ['/X/1/X', 	'GET',V_S_SLCT,	V_S_SLCT,false,true,false,2,3,'|X|X'],
            ['/X/1/X/1','GET',V_S_DELT,	V_S_DELT,false,false,true,4,4,'|X|X'],	
			['/X', 	    'POST',null,	V_S_CREA,false,true,false,0,1,'|X'],
			['/X', 	    'POST',V_S_CREA,V_S_CREA,false,true,false,0,1,'|X'],
			['/X/1', 	'POST',V_S_UPDT,V_S_UPDT,false,false,true,2,2,'|X'],
            ['/X/1/X', 	'POST',V_S_SLCT,V_S_SLCT,false,true,false,2,3,'|X|X'],
            ['/X/1/X/1','POST',V_S_DELT,V_S_DELT,false,false,true,4,4,'|X|X'],
	
 			];
    }

    /**
     * @dataProvider Prov_testRequest1
     */	
	public function testRequest1($p,$e,$prm,$e2)
	{
			$req= new Request($p);
			$res=$req->getAction();
			$this->assertEquals($e,$res);
			$this->assertEquals('"'.$req->getDocRoot().$e2,$req->getUrl($prm));
	}

    public function Prov_testRequest1() {
        return [
			['/',   	V_S_READ, ['X'=>'x'], '/?X=x"'],
			['/X', 		V_S_SLCT, ['X'=>'x'], '/X?Action='.V_S_SLCT.' & X=x"'],
			['/X/1',	V_S_READ, ['X'=>'x','Y'=>'y'], '/X/1?X=x & Y=y"'],
			['/X/1/X', 	V_S_SLCT, ['X'=>'x','Y'=>'y'], '/X/1/X?Action='.V_S_SLCT.' & X=x & Y=y"'],
			['/X/1/X/2',V_S_READ, [], '/X/1/X/2"'],
 			];
    }	
	
    /**
     * @dataProvider Provider2
     */
 	public function testGetAction($a, $b, $c, $e0, $d,$e1)
    {
		$_SERVER['PATH_INFO']=$a;
		$_SERVER['REQUEST_METHOD']=$b;
		if (is_null($c)) {		
			unset($_GET['Action']);
			unset($_POST['Action']);
		} else {
			$_GET['Action']=$c;
			$_POST['Action']=$c;
		}
		
		$r1=new Request();		
		$this->assertEquals($r1->getAction(),$e0);
		
		if (!is_null($e1)) {
			$e= '"'.$r1->getDocRoot().$e1.'"';
		} else {
			$e=null;
		}
		$req1 = $r1->getActionReq($d);
		$res = null;
		if (! is_null($req1)) {
			$res = $req1->getUrl();
		}
		$this->assertEquals($e,$res);
		
		$r2=new Request($a,$e0);
		$req2 = $r2->getActionReq($d);		
		$res = null;
		if (! is_null($req2)) {
			$res = $req2->getUrl();
		}
		$this->assertEquals($e,$res);
	}
	
	public function Provider2() {
        return [
			['/', 	    'GET',null,V_S_READ,		V_S_READ,'/'],
			['/X', 	    'GET',null,V_S_SLCT,		V_S_SLCT,'/X?Action='.V_S_SLCT],
			['/X', 	    'GET',null,V_S_SLCT,		V_S_READ,'/'],			
			['/X/1', 	'GET',null,V_S_READ,		V_S_READ,'/X/1'],
			['/X/1', 	'GET',null,V_S_READ,		V_S_SLCT,'/X?Action='.V_S_SLCT],
            ['/X/1/X', 	'GET',null,V_S_SLCT,		V_S_CREA,'/X/1/X?Action='.V_S_CREA],
	        ['/X/1/X', 	'GET',null,V_S_SLCT,		V_S_READ,'/X/1'],
	        ['/X/1/X', 	'GET',null,V_S_SLCT,		V_S_UPDT,null],
		    ['/', 		'GET',null,V_S_READ,		V_S_DELT,null],		
			['/X/1', 	'GET',null,V_S_READ,		'X'		,null],			
            ['/X/1/X/1','GET',null,V_S_READ,		V_S_DELT,'/X/1/X/1?Action='.V_S_DELT],
 			];
    }

	public function testMethods() 
	{
	
			$p1 = new Request('/X',V_S_CREA);
			$this->assertNotNull($p1);

			$p1 = new Request('/X/1',V_S_READ);
			$this->assertNotNull($p1);	
			
			$p1->setAction(V_S_UPDT);
			$this->assertEquals(V_S_UPDT,$p1->getAction());
		
			try {$x=$p1->pushId(1);} catch (Exception $e) {$r= $e->getMessage();}
			$this->assertEquals($r, E_ERC037);
			
			$_SERVER['PATH_INFO']='/X/1';
			$_SERVER['REQUEST_METHOD']='GET';
			$_SERVER['PHP_SELF']='/API.php';
			$_GET['Name']='X';
			
			$r=new Request();
			$this->assertnotNull($r);
			$this->assertEquals('/API.php',$r->getDocRoot());
			$this->assertEquals('"/API.php/"',$r->getRootUrl());
			$this->assertEquals('X',$r->getPrm('Name'));
						
			unset($_GET['Name']);
			$_SERVER['PATH_INFO']='/X/1';
			$_SERVER['REQUEST_METHOD']='POST';
			$_SERVER['PHP_SELF']='/ABridge.php';
			$_POST['Name']='X';	
			
			try {$x=new Request();} catch (Exception $e) {$r= $e->getMessage();}
			$this->assertEquals($r, E_ERC048);
			
			$_SERVER['PATH_INFO']='/X';
			$r=new Request();
			$this->assertEquals('X',$r->getPrm('Name'));
			$this->assertNull($r->getPrm('XX'));
			
	}

	
	public function testPathErr() 
	{				
		$tc = [
			['1',		V_S_READ,	E_ERC036.':1'],
			['/*/1',	V_S_READ,	E_ERC036.':/*/1:0'],
			['/a/$',	V_S_READ,	E_ERC036.':/a/$:1'],
			['/a/1/$',	V_S_READ,	E_ERC036.':/a/1/$:2'],
			['/X',		V_S_READ,	E_ERC048.':'.V_S_READ.':/X'],
			['/X/1',	V_S_SLCT,	E_ERC048.':'.V_S_SLCT.':/X/1'],
			['/',		V_S_SLCT,	E_ERC048.':'.V_S_SLCT.':/'],
			
		];
	
		foreach ($tc as $d) {
			try {$x=new Request($d[0],$d[1]);} catch (Exception $e) {$r= $e->getMessage();}
			$this->assertEquals($r,$d[2]);
		}
	
	}

	
    /**
     * @dataProvider Provider_pop
     */
	public function testPop ($p,$e)
	{
			$r=new Request($p);
		
			$r = $r->popReq();
			$res=$r->getRPath();
			$this->assertEquals($e,$res);


	}
	public function Provider_pop() {
        return [
				['/X/1/X/1','/X/1'],
				['/X/1','/'],
				['/','/'],
				['/X','/'],
				['/X/1/X', '/X/1']
 			];
    }
	
    /**
     * @dataProvider Provider_push
     */
	public function testpushId($p,$e,$attr,$act,$e2)
	{
			$r=new Request($p,V_S_CREA);
		
			$res = $r->pushId(1);
			$this->assertEquals($e,$res);

			$r2 = $r->getCrefReq($attr,$act);
			$this->assertEquals($e2,$r2->getRPath());
			$this->assertEquals($act,$r2->getAction());
			
			$r2->pushId(1);
			$r3=$r2->popReq();
			$this->assertEquals($e,$r3->getRPath());

	}
	public function Provider_push() {
        return [
				['/X',		'/X/1',		'A',V_S_CREA,	'/X/1/A'],
				['/X/1/Y',	'/X/1/Y/1',	'B',V_S_CREA,	'/X/1/Y/1/B'],
 			];
    }
	
    /**
     * @dataProvider Provider_prm
     */
	public function testPrm($p1,$p2,$p3,$p4,$e)
	{
			$_SERVER['PHP_SELF']='/ABridge.php';
			$_SERVER['PATH_INFO']=$p1;
			$_SERVER['REQUEST_METHOD']=$p2;
			$_GET['Action']=$p3;
			if ($p2 == 'GET') {
				unset($_POST['a_param']);
				$_GET['a_param']=$p4;
			} else {
				unset($_GET['a_param']);
				$_POST['a_param']=$p4;
			}

			$r=new Request();
			$this->assertEquals($p4,$r->getPrm('a_param',true));
			$this->assertEquals($e,$r->getPrm('a_param',false));
			

	}

	public function Provider_prm() {
        return [
				['/X/1',		'GET' , V_S_READ,'param_value',		'param_value'],
				['/X/1',		'GET' , V_S_READ,'param <br> value','param  value'],				
				['/X/1/Y/1',	'POST',	V_S_UPDT,'param_value',		'param_value'],
				['/X/1/Y/1',	'POST',	V_S_UPDT,'param_value<>',	'param_value'],
				['/X/1/Y/1',	'POST',	V_S_UPDT,null,	null],				
 			];
    }
	
}

