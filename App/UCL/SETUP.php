<?php
require_once 'CstMode.php';
require_once 'CstView.php';
require_once 'CModel.php';
require_once 'User.php';
require_once 'Session.php';

	require_once 'CLASSDEC.php';

	$config = [
	'Handlers' =>
		[
		'Session'  	 	=> ['fileBase',$DBDEC,],
		'CodeValue'  	=> ['fileBase',$DBDEC,],
		'Code' 			=> ['fileBase',$DBDEC,],
		'Student'	 	=> ['fileBase',$DBDEC,],
		'Cours'		 	=> ['fileBase',$DBDEC,],
		'Inscription'	=> ['fileBase',$DBDEC,],
		'Prof'		 	=> ['fileBase',$DBDEC,],
		'Charge'	 	=> ['fileBase',$DBDEC,],
		'User'	 	 	=> ['dataBase',$DBDEC,],
		'UGroup'	 	=> ['dataBase',$DBDEC,],		
		'Role'	 	 	=> ['dataBase',$DBDEC,],
		'Distribution'	=> ['dataBase',$DBDEC,],
		'Page'		  	=> ['dataBase',$DBDEC,],		
		],
	'Session' => 
		['Session'=>'BKey'],

	'Home' =>
		['/','/Session/~','/User/~'],		
	'Views' => [
	
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
		
		'Distribution' =>[
			'attrHtml' => [
				V_S_CREA => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_UPDT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
				V_S_SLCT => ['ofRole'=>H_T_SELECT,'toUser'=>H_T_SELECT],
			],

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
						V_S_READ	=> ['UserId','Profile','ProfProfile',$Group,'DefaultRole','Play'],
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
		'UGroup' =>[		
			'attrList' => [
					V_S_REF		=> ['Name'],
				],
		],		
		'Role' =>[	
				'attrList' => [
					V_S_REF		=> ['Name'],
				]

		],
		'Student' => [
		
				'attrList' => [
					V_S_REF		=> ['SurName','Name'],
				],
				'attrHtml' => [
					V_S_CREA => ['Sexe'=>H_T_RADIO],
					V_S_UPDT => ['Sexe'=>H_T_RADIO],
					V_S_SLCT => ['Sexe'=>H_T_RADIO],
					V_S_READ => ['InscritA'=>[H_SLICE=>5,V_COUNTF=>true,V_CTYP=>V_C_TYPN]]	
				],
				'attrProp' => [
					V_S_SLCT =>[V_P_LBL,V_P_OP,V_P_VAL],
				],			
				'lblList' => [
					'id'		=> 'Noma',
					'Name' 		=> 'Nom',
					'SurName' 	=> 'Prenom',
					'BirthDay'	=> 'Date de naissance',
				],
				'viewList' => [
					'Resume'  => [
						'attrList' => [
							V_S_READ=> ['id','SurName','Name','NbrCours','NbrCredits'],
							V_S_UPDT=> ['SurName','Name'],
							V_S_CREA=> ['SurName','Name'],
							V_S_DELT=> ['SurName','Name'],							
						],
					],
					'Detail'  => [
						'attrList' => [
							V_S_READ=> ['SurName','Name','BirthDay','Sexe','Country',],
							V_S_UPDT=> ['SurName','Name','BirthDay','Sexe','Country'],
						],
						'navList' => [
							V_S_READ => [V_S_UPDT],
						],
					],
					'Inscription' =>[
						'attrList' => [
							V_S_READ=> ['SurName','Name','InscritA'],
						],
						'navList' => [V_S_READ => []
						],	
					],
					'Image' =>[
						'attrProp' => [
							V_S_READ =>[V_P_VAL],
						],				
						'attrList' => [
							V_S_READ=> ['Image'],
							V_S_UPDT=> ['Image'],
						],
						'attrHtml' => [
							V_S_READ => ['Image'=>[H_TYPE=>H_T_IMG,H_ROWP=> 80]],
						],						
						'navList' => [V_S_READ => [V_S_UPDT]
						],	
					],						
					'Jason' =>[
						'attrProp' => [
							V_S_READ =>[V_P_VAL],
						],	
						'attrList' => [
							V_S_READ=> ['Jason'],
						],
						'attrHtml' => [
							V_S_READ => ['Jason'=>[H_TYPE=>H_T_TEXTAREA,H_COL=>90,H_ROW=> 40]],
						],					
						'navList' => [V_S_READ => []
						],	
					],						
					'Trace' =>[
						'attrList' => [
							V_S_READ=> ['id','vnum','ctstp','utstp','User'],
							V_S_UPDT=> ['User'],
						],
						'navList' => [V_S_READ => [V_S_UPDT]
						],
						'attrHtml' => [
							V_S_UPDT => ['User'=>H_T_SELECT],
						]
					],					
				],
				
		],
		'Cours' => [

				'attrList' => [
					V_S_REF		=> ['SurName','Name'],
					V_S_CREF	=> ['id','Credits','User'],
				],
				'attrHtml' => [
							V_S_UPDT => ['User'=>H_T_SELECT],
							V_S_SLCT => [V_S_SLCT=>[H_SLICE=>15,V_COUNTF=>true,V_CTYP=>V_C_TYPN]]
				]				
		],
		'Prof' => [
		
				'attrList' => [
					V_S_REF		=> ['SurName','Name'],
					V_S_CREF	=> ['id','User'],
				]
				
		],
		'Code' => [
		
				'attrList' => [
					V_S_REF		=> ['Name'],
				]
				
		],
		'CodeValue' =>[
		
				'attrList' => [
					V_S_REF		=> ['Name'],
				]

		],
		'Inscription' =>[
		
				'attrHtml' => [
					V_S_CREA => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
//					V_S_READ => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
					V_S_READ => ['De'=>'Resume'],
					V_S_UPDT => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
					V_S_SLCT => ['A'=>H_T_SELECT,'De'=>H_T_SELECT],
				],

		],
		'Charge' =>[
		
				'attrHtml' => [
					V_S_CREA => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
					V_S_UPDT => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
					V_S_SLCT => ['Par'=>H_T_SELECT,'De'=>H_T_SELECT],
				],
				
		],

	]
	];


	
class Cours extends CModel 
{

	private $_credit=0;

	function __construct($mod) 
	{
		$this->mod=$mod;
		if ($mod->getId()) {
			$this->_credit=$mod->getValN('Credits');
		}
	}
		
	public function save()
	{
		$res=$this->mod->saveN();
		$ncredit = $this->mod->getValN('Credits');
		if ($ncredit != $this->_credit) {
			$list = $this->mod->getValN('SuivitPar');
			foreach ($list as $id) {
				$inscription = $this->mod->getCref('SuivitPar',$id);
				$inscription->save();
			}
		}
		return $res;
	}	
}
	
class Student extends CModel 
{

	public function getVal($attr) 
	{
		if ($attr == 'NbrCours') {
			$a = $this->mod->getValN('InscritA');
			$res = count($a);
			return $res;
		}
		if ($attr == 'Jason') {
			$res = genJASON($this->mod,false,true);
			return $res;
		}
		return $this->mod->getValN($attr);
	}
	
	public function save()
	{
		$credits = 0;
		$list = $this->mod->getValN('InscritA');
		foreach ($list as $id) {
			$inscription = $this->mod->getCref('InscritA',$id);
			$cours = $inscription->getRef('A');
			$credit = $cours->getVal('Credits');
			if (!is_null($credit)) {
				$credits = $credits + $credit;
			}
		}
		$this->mod->setVal('NbrCredits',$credits);
		return $this->mod->saveN();
	}
}

class Inscription extends CModel 
{

	private $_student;
	
	public function delet()
	{
		$this->_student = $this->mod->getRef('De');
		$res=$this->mod->deletN();
		if (!is_null($this->_student)) {
			$this->_student ->save();
		}
		return $res;
	}

	public function save()
	{
		$res=$this->mod->saveN();
		$student = $this->mod->getRef('De');
		if (! is_null($student)) {
			$student->save();
		}
		return $res;
	}
	
}