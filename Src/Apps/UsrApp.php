<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\App;
use ABridge\ABridge\Mod\ModUtils;

class UsrApp extends App
{
    
    static protected $defBind= [
            Usr::USER          ,
            Usr::ROLE          ,
            Usr::DISTRIBUTION  ,
            Usr::SESSION       ,
            Usr::USERGROUP     ,
            Usr::GROUPUSER     ,
    ];
    
    public static function initMeta($config)
    {
        return Usr::get()->initMeta();
    }
    
    public static function init($prm, $config)
    {
        $bindings = self::$defBind;
        if ($config != []) {
            $bindings=$config;
        }
        $res= self::$config;
        $res['Hdl']['Usr']=$bindings;
        return $res;
    }
    
    static public $config = [

            'Hdl'   => [
                    'Usr'   => [
                            Usr::USER          ,
                            Usr::ROLE          ,
                            Usr::DISTRIBUTION  ,
                            Usr::SESSION       ,
                            Usr::USERGROUP     ,
                            Usr::GROUPUSER     ,
                    ],
            ],
            'View' => [
                    Usr::USERGROUP => [
                            'attrList' => [
                                    CstView::V_S_REF        => ['Name'],
                            ],
                    ],
                    Usr::USER =>[
                            'lblList'  => [
                                    'Role'          => 'Default Role',
                                    'UserGroup'     =>'Default Group',
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_READ => [
                                            'Roles'=>[
                                                    CstView::V_SLICE=>15,
                                                    CstView::V_COUNTF=>false,
                                                    CstView::V_CTYP=>CstView::V_C_TYPN
                                                    
                                            ]
                                    ],
                                    CstMode::V_S_UPDT => [
                                            'Role'      =>CstHTML::H_T_SELECT,
                                            'UserGroup'     =>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_SLCT => [
                                            'Role'      =>CstHTML::H_T_SELECT,
                                            'UserGroup'     =>CstHTML::H_T_SELECT,
                                    ],
                            ],
                            'attrList' => [
                                    CstView::V_S_REF        => ['UserId'],
                                    CstMode::V_S_SLCT       => ['UserId'],
                            ],
                            'viewList' => [
                                    'Profile' => [
                                            'attrList' => [
                                                    CstMode::V_S_READ=> [
                                                            'id',
                                                            'UserId',
                                                            'Role',
                                                            'UserGroup',
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [
                                                            CstMode::V_S_SLCT,CstMode::V_S_CREA,CstMode::V_S_DELT,
                                                    ],
                                            ],
                                    ],
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
                                                            'UserId'
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [
                                                            CstMode::V_S_UPDT
                                                    ],
                                            ],
                                    ],
                                    'Roles'  => [
                                            'attrList' => [
                                                    CstMode::V_S_READ   => [
                                                            'UserId',
                                                            'Role',
                                                            'Roles',
                                                    ],
                                                    CstMode::V_S_UPDT   => [
                                                            'UserId',
                                                            'Password',
                                                            'Role',
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [
                                                            CstMode::V_S_UPDT
                                                    ],
                                            ],
                                    ],
                                    'Groups'  => [
                                            'attrList' => [
                                                    CstMode::V_S_READ   => [
                                                            'UserId',
                                                            'UserGroup',
                                                            'UserGroups',
                                                    ],
                                                    CstMode::V_S_UPDT   => [
                                                            'UserId',
                                                            'Password',
                                                            'UserGroup',
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
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'id',
                                                            'vnum',
                                                            'ctstp',
                                                            'utstp',
                                                            'MetaData',
                                                    ],
                                            ],
                                            'navList' => [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],

                            ],
                    ],
                    Usr::ROLE =>[
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
                    Usr::SESSION =>[
                            'attrList' => [
                                    CstView::V_S_CREF=> [
                                            'id',
                                            'User',
                                            'ActiveRole',
                                            'ActiveGroup',
                                            'ValidFlag',
                                            'BKey',
                                            'vnum',
                                            'ctstp'
                                    ],
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_UPDT => [
                                            'ActiveRole'=>CstHTML::H_T_SELECT,
                                            'ActiveGroup'=>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_SLCT => [
                                            'ActiveRole'=>CstHTML::H_T_SELECT,
                                            'ActiveGroup'=>CstHTML::H_T_SELECT,
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
                                                            'ActiveRole',
                                                            'ActiveGroup',
                                                    ],
                                                    CstMode::V_S_DELT=> [
                                                            'id',
                                                            'User',
                                                            'ActiveRole',
                                                            'ActiveGroup',
                                                    ],
                                                    CstMode::V_S_UPDT=> [
                                                            'id',
                                                            'UserId',
                                                            'Password',
                                                            'RoleName',
                                                            'GroupName',
                                                    ],
                                            ],
                                            
                                    ],
                                    'Trace' =>[
                                            'attrList' => [
                                                    CstMode::V_S_READ=> [
                                                            'id',
                                                            'Name',
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
                    Usr::DISTRIBUTION =>[
                            'attrList' => [
                                    CstView::V_S_CREF=> [
                                            'id',
                                            'User',
                                            'Role',
                                    ],
                            ],
                            'attrHtml' => [
                                    CstMode::V_S_CREA => [
                                            'Role'=>CstHTML::H_T_SELECT,
                                            'User'=>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_UPDT => [
                                            'Role'=>CstHTML::H_T_SELECT,
                                            'User'=>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_SLCT => [
                                            'User'=>CstHTML::H_T_SELECT,
                                            'Role'=>CstHTML::H_T_SELECT,
                                    ],
                            ],
                            
                    ],
                    Usr::GROUPUSER =>[
                            'attrHtml' => [
                                    CstMode::V_S_CREA => [
                                            'User'=>CstHTML::H_T_SELECT,
                                            'UserGroup'=>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_UPDT => [
                                            'User'=>CstHTML::H_T_SELECT,
                                            'UserGroup'=>CstHTML::H_T_SELECT,
                                    ],
                                    CstMode::V_S_SLCT => [
                                            'User'=>CstHTML::H_T_SELECT,
                                            'UserGroup'=>CstHTML::H_T_SELECT,
                                    ],
                            ],
                            
                    ],
            ],
    ];
            
    public static function initData($config)
    {
        // Role
        $bindings = self::$defBind;
        if ($config != []) {
            $bindings=$config;
        }
        $bindings=ModUtils::normBindings($bindings);
 
        $RSpec ='[["true","true","true"]]';
 
        if (isset($bindings[Usr::ROLE])) {
            $obj=new Model($bindings[Usr::ROLE]);
            $obj->setVal('Name', 'Root');
            $obj->setVal('JSpec', $RSpec);
            $RootRole=$obj->save();
            $obj->getErrLog()->show();
        }
 
        // Group
        if (isset($bindings[Usr::USERGROUP])) {
            $obj=new Model($bindings[Usr::USERGROUP]);
            $obj->setVal('Name', 'Root');
            $RootGroup=$obj->save();
            $obj->getErrLog()->show();
        }
       
        // User
        if (isset($bindings[Usr::USER])) {
            $obj=new Model($bindings[Usr::USER]);
            $obj->setVal('UserId', 'Root');
            $RootUser=$obj->save();
            $obj->getErrLog()->show();
        }

        
        // Distribution
        if (isset($bindings[Usr::DISTRIBUTION])) {
            $obj=new Model($bindings[Usr::DISTRIBUTION]);
            $obj->setVal('Role', $RootRole);
            $obj->setVal('User', $RootUser);
            $res=$obj->save();
            $obj->getErrLog()->show();
        }
       
        // GroupUser
        if (isset($bindings[Usr::GROUPUSER])) {
            $obj=new Model($bindings[Usr::GROUPUSER]);
            $obj->setVal('UserGroup', $RootGroup);
            $obj->setVal('User', $RootUser);
            $res=$obj->save();
            $obj->getErrLog()->show();
        }
       
        // Root Default
        if (isset($bindings[Usr::USER])) {
            $obj=new Model($bindings[Usr::USER], $RootUser);
            if (isset($bindings[Usr::ROLE])) {
                $obj->setVal('Role', $RootRole);
            };
            if (isset($bindings[Usr::USERGROUP])) {
                $obj->setVal('UserGroup', $RootGroup);
            };
            
            $RootUser=$obj->save();
            $obj->getErrLog()->show();
        }
        return $RootUser;
    }
}
