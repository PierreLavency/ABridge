<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\UtilsC;
use ABridge\ABridge\Mod\Model;

class Usr
{

    const USER ='User';
    const ROLE = 'Role';
    const SESSION ='Session';
    const DISTRIBUTION = 'Distribution';
    const GROUP ='UserGroup';
    
    static public $config = [
            'Handlers' =>
            [
                    self::USER          => ['dataBase',],
                    self::ROLE          => ['dataBase',],
                    self::DISTRIBUTION  => ['dataBase',],
                    self::SESSION       => ['dataBase',],
            ],
            'Hdl'   => [
                    'Usr'   => [self::SESSION=>'BKey'],
            ],
            'View' => [
                    self::USER =>[
                            'lblList' => [
                                    'Play'          => 'PlayRoles',
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_READ => [
                                            'Play'=>[
                                                    CstView::V_SLICE=>15,
                                                    CstView::V_COUNTF=>false,
                                                    CstView::V_CTYP=>CstView::V_C_TYPN
                                                    
                                            ]
                                    ],
                                    CstMode::V_S_UPDT => [
                                            'DefaultRole'=>CstHTML::H_T_SELECT],
                                    CstMode::V_S_SLCT => [
                                            'DefaultRole'=>CstHTML::H_T_SELECT],
                            ],
                            'attrList' => [
                                    CstView::V_S_REF        => ['UserId'],
//									CstMode::V_S_SLCT	=> ['UserId',self::GROUP],
                            ],
                            'viewList' => [
                                    'Password'  => [
                                            'attrList' => [
                                                    CstMode::V_S_READ   => [
                                                            'UserId',
                                                    ],
                                                    CstMode::V_S_CREA   => [
                                                            'UserId',
                                                            'NewPassword1',
                                                            'NewPassword2'
                                                    ],
                                                    CstMode::V_S_UPDT   => [
                                                            'UserId',
                                                            'Password',
                                                            'NewPassword1',
                                                            'NewPassword2'
                                                    ],
                                                    CstMode::V_S_DELT   => [
                                                            
                                                    'UserId'],
                                            ],
                                    ],
                                    'Role'  => [
                                            'attrList' => [
                                                    CstMode::V_S_READ   => [
                                                            'UserId',self::GROUP,
                                                            'DefaultRole',
                                                            'Play'
                                                    ],
                                                    CstMode::V_S_UPDT   => ['UserId',
                                                            'Password',self::GROUP,
                                                            'DefaultRole'
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [
                                                            CstMode::V_S_UPDT
                                                    ],
                                            ],
                                    ],
                                    'Trace' =>[
                                            'attrList' => [
                                                    CstMode::V_S_READ=> [
                                                            'id',
                                                            'vnum',
                                                            'ctstp',
                                                            'utstp',
                                                    'MetaData'],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                            ]
                    ],
                    self::ROLE =>[
                            'attrList' => [
                                    CstView::V_S_REF        => ['Name'],
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_UPDT   => [
                                            'JSpec' => [
                                                    CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,
                                                    CstHTML::H_COL=>160,CstHTML::H_ROW=> 30
                                            ]],
                                    CstMode::V_S_READ   => [
                                            'JSpec' => [
                                                    CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,
                                                    CstHTML::H_COL=>160,CstHTML::H_ROW=> 30
                                            ]],
                                    CstMode::V_S_CREA   => [
                                            'JSpec' => [
                                                    CstHTML::H_TYPE=>CstHTML::H_T_TEXTAREA,
                                                    CstHTML::H_COL=>160,CstHTML::H_ROW=> 30
                                            ]],
                            ]
                    ],
                    self::SESSION =>[
                            'attrList' => [
                                    CstView::V_S_CREF=> [
                                            'id',
                                            'User',
                                            'Role',
                                            'ValidFlag',
                                            'BKey',
                                            'vnum',
                                            'ctstp'
                                    ],
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_UPDT => [
                                            'Role'=>CstHTML::H_T_SELECT
                                    ],
                                    CstMode::V_S_SLCT => [
                                            'Role'=>CstHTML::H_T_SELECT
                                    ],
                            ],
                            'viewList' => [
                                    'Detail'  => [
                                            'lblList' => [
                                                    CstMode::V_S_UPDT =>'LogIn',
                                                    CstMode::V_S_DELT =>'LogOut',
                                            ],
                                            'attrList' => [
                                                    CstMode::V_S_READ=> [
                                                            'id',
                                                            'User',
                                                            'Role'
                                                    ],
                                                    CstMode::V_S_DELT=> [
                                                            'id',
                                                            'User',
                                                            'Role'
                                                    ],
                                                    CstMode::V_S_UPDT=> [
                                                            'id',
                                                            'UserId',
                                                            'Password',
                                                            'Role'
                                                    ],
                                            ],
                                            
                                    ],
                                    'Trace' =>[
                                            'attrList' => [
                                                    CstMode::V_S_READ=> [
                                                            'id',
                                                            'ValidStart',
                                                            'BKey',
                                                            'vnum',
                                                            'ctstp',
                                                            'utstp'
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                            ]
                            
                    ],
                    self::DISTRIBUTION =>[
                            'attrHtml' => [
                                    CstMode::V_S_CREA => [
                                            'ofRole'=>CstHTML::H_T_SELECT,
                                            'toUser'=>CstHTML::H_T_SELECT],
                                    CstMode::V_S_UPDT => [
                                            'ofRole'=>CstHTML::H_T_SELECT,
                                            'toUser'=>CstHTML::H_T_SELECT],
                                    CstMode::V_S_SLCT => [
                                            'ofRole'=>CstHTML::H_T_SELECT,
                                            'toUser'=>CstHTML::H_T_SELECT],
                            ],
                            
                    ],
            ],
    ];
        
    public static function loadMeta()
    {
        $bindings = [
                self::USER=>self::USER,
                self::ROLE=>self::ROLE,
                self::DISTRIBUTION=>self::DISTRIBUTION,
                self::SESSION=>self::SESSION,
        ];
        UtilsC::createMods($bindings);
    }
    
    public static function loadData()
    {
        $RSpec = '[["true","true","true"]]';
        
        $obj=new Model(self::ROLE);
        $obj->setVal('Name', 'Root');
        $obj->setVal('JSpec', $RSpec);
        $RootRole=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
        
        $RSpec = '[[["Read"],          	"true",         "true"],
		[["Read","Update","Delete"],  	"|Session",    {"Session":"id"}],
		[["Read","Update"],  			"|User",       {"User":"id<>User"}]
		]';
        
        $obj=new Model(self::ROLE);
        $obj->setVal('Name', 'Default');
        $obj->setVal('JSpec', $RSpec);
        $obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
                
        // User
        
        $obj=new Model(self::USER);
        $obj->setVal('UserId', 'Root');
        $RootUser=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
        
        // Distribution
        
        $obj=new Model(self::DISTRIBUTION);
        $obj->setVal('ofRole', $RootRole);
        $obj->setVal('toUser', $RootUser);
        $res=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
    }
}
