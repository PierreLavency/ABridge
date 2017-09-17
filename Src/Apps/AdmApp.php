<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\App;
use ABridge\ABridge\Adm\Adm;

class AdmApp extends App
{
    const ADMIN='Admin';
    
    public static $config = [

            'Handlers' => [
                    self::ADMIN => ['memBase',],
            ],
                  
            'Adm' => [
                    
            ],
            
            'View' => [
                    self::ADMIN =>[
                            'attrList' =>
                            [
                                    CstView::V_S_REF    => ['id'],
                            ],
                            'lblList'  =>
                            [
                                    CstMode::V_S_UPDT => 'Load',
                            ],
                            'viewList' =>
                            [
                                    'Parameters'=>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'id',
                                                            'name',
                                                            'base',
                                                            'dBase',
                                                            'fileBase',
                                                            'memBase',
                                                            'path',
                                                            'host',
                                                            'user',
                                                            'pass',
                                                    ],
                                                    
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                                    'Files' =>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'id',
                                                            'name',
                                                            'Load',
                                                            'Meta',
                                                            'Delta',
                                                    ],
                                                    CstMode::V_S_UPDT=>
                                                    [
                                                            'Load',
                                                            'Meta',
                                                            'Delta',
                                                    ],
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [CstMode::V_S_UPDT],
                                            ],
                                    ],
                                    'ModState' =>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'ModState',
                                                            
                                                    ],
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                                    'Trace' =>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'vnum',
                                                            'ctstp',
                                                            'utstp',
                                                            'MetaData',
                                                            
                                                    ],
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                            ],
                                
                    ],
                    
            ],
    ];
    
    public static function loadMeta($prm = null)
    {
        return Adm::get()->InitMeta([], []);
    }
    
    public static function loadData($prm = null)
    {
        return true;
    }
}
