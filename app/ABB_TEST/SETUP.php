<?php
require_once 'CstMode.php';
require_once 'CstView.php';
require_once 'CModel.php';
require_once 'User.php';
require_once 'Session.php';
require_once 'Role.php';
require_once 'Distribution.php'; 
	
	require_once 'CLASSDEC.php';

	$config = [
	'Handlers' =>
		[
		$ABB		 => ['dataBase',$DBDEC,false],
		$Application => ['dataBase',$DBDEC,],
		$Component	 => ['dataBase',$DBDEC,],
		$Interface	 => ['dataBase',$DBDEC,],
		$Exchange	 => ['dataBase',$DBDEC,],
		$IUse	 	 => ['dataBase',$DBDEC,],			
		$IType	 	 => ['dataBase',$DBDEC,],		
		$CType	 	 => ['dataBase',$DBDEC,],
		$SLevel 	 => ['dataBase',$DBDEC,],
		$AStyle 	 => ['dataBase',$DBDEC,],
		$SControl 	 => ['dataBase',$DBDEC,],
		$ACode	 	 => ['dataBase',$DBDEC,],
		$User	 	 => ['dataBase',$DBDEC,],
		$Group		 => ['dataBase',$DBDEC,],		
		$Role	 	 => ['dataBase',$DBDEC,],
		$Distribution=> ['dataBase',$DBDEC,],
		$Session	 => ['dataBase',$DBDEC,],
		],
	'Home' =>
		['/',"/$Session/~","/$User/~",],
	'Session' => 
		[$Session=>'BKey'],		
	'Views' => [
		$Application=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_UPDT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_SLCT => ['Authenticity'=>H_T_SELECT,'Confidentiality'=>H_T_SELECT,'Availability'=>H_T_SELECT,'Integrity'=>H_T_SELECT,'Style'=>H_T_SELECT,],
					V_S_READ => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],					V_S_CREF => ['Authenticity'=>H_T_PLAIN,'Confidentiality'=>H_T_PLAIN,'Availability'=>H_T_PLAIN,'Integrity'=>H_T_PLAIN,'Style'=>H_T_PLAIN,],
				],
				'lblList'  => [
					'In' => 'Interfaces', 'Out' => 'Exchanges', 'BuiltFrom' => 'Components',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Owner','Alias','Style'],
							V_S_UPDT=> ['id','Name','CodeNm','Owner','Alias','Style',],
							V_S_CREA=> ['id','Name','CodeNm','Owner','Alias','Style',],
							V_S_DELT=> ['id','Name','CodeNm','Owner','Alias','Style',],							
						],
					],
					'Decription'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','ShortDesc','LongDesc'],
							V_S_UPDT=> ['id','ShortDesc','LongDesc'],							
						],
						'navList' => [V_S_READ => [V_S_UPDT],
						],
					],
					'Components' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','BuiltFrom'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Interface' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Security'  => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Authenticity','Availability','Confidentiality','Integrity'],
							V_S_UPDT=> ['id','Authenticity','Availability','Confidentiality','Integrity'],							
						],
					    'navList' => [V_S_READ => [V_S_UPDT],
						],
					],					
				]
				
		],			
		$Component=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				],
				'attrHtml' => [
					V_S_CREA => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_UPDT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_SLCT => ['CType'=>H_T_SELECT,'SourceControl'=>H_T_SELECT],
					V_S_READ => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
					V_S_CREF => ['CType'=>H_T_PLAIN,'SourceControl'=>H_T_PLAIN],
				],
				'lblList'  => [
					'CType' => 'Type', 'Of' => 'Application ', 'In' => 'Interfaces', 'Out' => 'Exchanges',
				],	
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_UPDT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_CREA=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],
							V_S_DELT=> ['id','Of','Name','CodeNm','Owner','Alias','CType','SourceControl','Url','Queue','OutQueue','BatchNme','Frequency'],							
						],	1
					],						
					'Interface' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','In'],							
						],
						'navList' => [V_S_READ => []
						],
					],
					'Exchanges' => [
						'attrList' => [
							V_S_READ=> ['id','Name','CodeNm','Out'],							
						],
						'navList' => [V_S_READ => []
						],
					],				
				],
			],	
		$Interface=> [
		
				'attrList' => [
	//				V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_CREA => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_UPDT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_SLCT => ['IType'=>H_T_SELECT,'IUse'=>H_T_SELECT],
					V_S_READ => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
					V_S_CREF => ['IType'=>H_T_PLAIN,'IUse'=>H_T_PLAIN],
				],
				'lblList'  => [
					'IType' => 'Type', 'Of' => 'Application ', 'IUse' => 'Usage', 'UsedBy' => 'Exchanges',
				],	
				
		],
		$Exchange=> [
		
				'attrList' => [
					V_S_REF		=> ['CodeNm'],
				]
				
		],
		$CType=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$SLevel=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$AStyle=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$SControl=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$IType=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$IUse=> [
		
				'attrList' => [
					V_S_REF		=> ['Value'],
				]
				
		],
		$User =>[
			'lblList' => [
					'Play'			=> 'PlayRoles',
				],
			'attrHtml' => [
					V_S_READ 	=> ['Play'=>[H_SLICE=>15,V_COUNTF=>false,V_CTYP=>V_C_TYPN]],
				],						
			'attrList' => [
					V_S_REF		=> ['UserId'],
					V_S_SLCT	=> ['UserId',$Group,'DefaultRole'],
				],
			'viewList' => [
				'Password'  => [
					'attrList' => [
						V_S_READ	=> ['UserId',],
						V_S_CREA	=> ['UserId','NewPassword1','NewPassword2'],
						V_S_UPDT	=> ['UserId','Password','NewPassword1','NewPassword2'],
						V_S_DELT	=> ['UserId'],		
					],
				],
				'Role'  => [
					'attrList' => [
						V_S_READ	=> ['UserId',$Group,'DefaultRole','Play'],
						V_S_UPDT	=> ['UserId','Password',$Group,'DefaultRole'],
					],
					'navList' => [
						V_S_READ => [V_S_UPDT],
					],					
				],
				'Trace' =>[
					'attrList' => [
						V_S_READ=> ['id','vnum','ctstp','utstp'],
					],
					'navList' => [
						V_S_READ => [],
					],
				],
			]				
		],
		$Group =>[		
			'attrList' => [
				V_S_REF		=> ['Name'],
				],
		],		
		$Role =>[	
				'attrList' => [
					V_S_REF		=> ['Name'],
				],
				'attrHtml' => [
					V_S_UPDT	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_READ	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
					V_S_CREA	=> ['JSpec' => [H_TYPE=>H_T_TEXTAREA,H_COL=>160,H_ROW=> 30]],
				]
		],
		$Session =>[
			'attrList' => [
						V_S_CREF=> ['id','User','Role','ValidFlag','BKey','vnum','ctstp'],									
			],
			'attrHtml' => [
						V_S_UPDT => ['Role'=>H_T_SELECT],
						V_S_SLCT => ['Role'=>H_T_SELECT],					
			],		
			'viewList' => [
				'Detail'  => [
					'lblList' => [
						V_S_UPDT			=> 'LogIn',
						V_S_DELT			=> 'LogOut',	
					],				
					'attrList' => [
						V_S_READ=> ['id','User','Role'],
						V_S_DELT=> ['id','User','Role'],
						V_S_UPDT=> ['id','UserId','Password','Role'],
					],
					
				],
				'Trace' =>[
					'attrList' => [
						V_S_READ=> ['id','ValidStart','BKey','vnum','ctstp','utstp'],
					],
					'navList' => [
						V_S_READ => [],
					],
				],
			]							

		],
		$Distribution =>[
			'attrHtml' => [
				V_S_CREA => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_UPDT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_SLCT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
			],

		],		
		],
	];		
	

	