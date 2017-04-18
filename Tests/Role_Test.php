<?php
	

require_once 'Role.php' ;

class Role_Test extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider Provider1
     */
 	public function testCond($a, $b, $c, $e1)
	{
		$role =[
		[[V_S_READ,V_S_SLCT],      		'true',         						'true'],
        [V_S_SLCT,      				'/User',        						'false'],
        [V_S_READ,      				'/User',        						['User'=>'User']],		
        [V_S_UPDT,           			'/Application',                      	['Application'=>'User']],
        [[V_S_CREA,V_S_UPDT,V_S_DELT],	['/Application/In','Application/Out'],	['Application'=>'User']],
        [[V_S_CREA,V_S_DELT],    		'/Application/BuiltFrom',            	['Application'=>'User','BuiltFrom'=>'User']],
		];
		
		$x = new Role($role,null);
		$r = $x->getAttrCond($b,$a,$c);
		$this->assertEquals($e1,$r);
		if ($r) {
			$this->assertTrue($x->checkPath($b,$a));
		} else {
			$this->assertFalse($x->checkPath($b,$a));
		}
		

	}

	public function Provider1() {
        return [
			['/Userrr', 				V_S_UPDT, 'User'		,false],
			['/User', 					V_S_SLCT, 'User'		,false],
			['/User', 					V_S_READ, 'User'		,['User']],		
			['/Application'  , 			V_S_SLCT, 'Application'	,['true']],
			['/Application', 			V_S_UPDT, 'Application'	,['User']],
			['/Application/In', 		V_S_CREA, 'Application'	,['User']],
			['/Application/BuiltFrom',	V_S_CREA, 'Application'	,['User']],
			['/Application/BuiltFrom',	V_S_CREA, 'BuiltFrom'	,['User']],	
			['/Application/BuiltFrom',	V_S_CREA, 'User'		,['true']],							
			['/Application/Ins', 		V_S_CREA, 'Application'	,false],
			['/Application/Ins', 		V_S_SLCT, 'Application'	,['true']],			
 			];
    }

	 /**
     * @dataProvider Provider1
     */
 	public function testRoot($a, $b, $c, $e1)
	{
		$y = new Role();
		$r = $y->getAttrCond($b,$a,$c);
		$this->assertEquals(['true'],$r);
	}
	
	/**
     * @dataProvider Provider2
     */
	
	public function testCheck($a, $b, $e1, $e2)
	{
		$role =[
		[[V_S_READ,V_S_SLCT],      		'true',         						'true'],
        [V_S_UPDT,           			'/Application',                      	['Application'=>'User:User']],
        [[V_S_CREA,V_S_UPDT,V_S_DELT],	['/Application/In','Application/Out'],	['Application'=>'User']],
        [[V_S_CREA,V_S_DELT],    		'/Application/BuiltFrom',            	['Application'=>'User','BuiltFrom'=>'User']],
		];
		
		
		$y = new Model('TestSess');
		$y->addAttr('User',M_INT);
		$y->setVal('User',1);
		
		$r = new Role($role,$y);
		
		$x = new Model('TestApp');
		$x->addAttr('User',M_INT);		
		$x->setVal('User',1);
		
		$res = $r->checkARight($b, $a, [['Application',$x],['BuiltFrom',$x]]);
		$this->assertEquals($e1,$res);

		$x = new Model('TestApp');
		$x->addAttr('User',M_INT);
		$x->setVal('User',2);
		
		$res = $r->checkARight($b, $a, [['Application',$x]]);
		$this->assertEquals($e2,$res);
		
	}

	public function Provider2() {
        return [			
			['/ApplicationA' , 			V_S_UPDT, false, false],
			['/Application'  , 			V_S_SLCT, true, true],
			['/Application', 			V_S_UPDT, true, false],
			['/Application/In', 		V_S_CREA, true, false],
			['/Application/BuiltFrom',	V_S_CREA, true, false],			
			['/Application/Ins', 		V_S_CREA, false,false],
			['/Application/Ins', 		V_S_SLCT, true, true],			
 			];
    }

	
	
}

?>	