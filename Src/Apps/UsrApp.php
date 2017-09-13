<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\View\CstView;

use ABridge\ABridge\Mod\Model;
use ABridge\ABridge\Usr\Usr;
use ABridge\ABridge\App;

class UsrApp extends App
{

    const USER ='User';
    const ROLE = 'Role';
    const SESSION ='Session';
    const DISTRIBUTION = 'Distribution';
    const USERGROUP ='UserGroup';
    const GROUPUSER ='GroupUser';
    
    public static function loadMeta($prm = null)
    {
        Usr::initMeta([], self::$config['Hdl']['Usr']);
    }
    
    static public $config = [

            'Hdl'   => [
                    'Usr'   => [
                            self::USER          ,
                            self::ROLE          ,
                            self::DISTRIBUTION  ,
                            self::SESSION       ,
                            self::USERGROUP     ,
                            self::GROUPUSER     ,
                    ],
            ],
            'View' => [
                    self::USERGROUP => [
                            'attrList' => [
                                    CstView::V_S_REF        => ['Name'],
                            ],
                    ],
                    self::USER =>[
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
                    self::DISTRIBUTION =>[
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
                    self::GROUPUSER =>[
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
        

    
    public static function loadData($prm = null)
    {
        // Role
        
        $RSpec ='[["true","true","true"]]';
        
        $obj=new Model(self::ROLE);
        $obj->setVal('Name', 'Root');
        $obj->setVal('JSpec', $RSpec);
        $RootRole=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
 
        // Group
        
        $obj=new Model(self::USERGROUP);
        $obj->setVal('Name', 'Root');
        $RootGroup=$obj->save();
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
        $obj->setVal('Role', $RootRole);
        $obj->setVal('User', $RootUser);
        $res=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
        
        // GroupUser
        
        $obj=new Model(self::GROUPUSER);
        $obj->setVal('UserGroup', $RootGroup);
        $obj->setVal('User', $RootUser);
        $res=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
 
        
        // Root Default
        
        $obj=new Model(self::USER, $RootUser);
        $obj->setVal('Role', $RootRole);
        $obj->setVal('UserGroup', $RootGroup);
        $RootUser=$obj->save();
        echo $obj->getModName().':'.$obj->getId().' '.$obj->getErrLog()->show();
        echo "<br>";
    }
}
