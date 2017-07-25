<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;

class Adm
{
    const ADMIN='Admin';
    
    public static $config = [
            'Handlers' => [
                    self::ADMIN => ['dataBase',],
            ],
                    
            'Adm' => [
                    
            ],
            
            'View' => [
                    self::ADMIN =>[
                            'attrList' => [
                                    CstView::V_S_REF    => ['id'],
                                    CstMode::V_S_READ   => [
                                            'id',
                                            'Application',
                                            'Init',
                                            'Load',
                                            'Meta',
                                            'Delta',
                                            'vnum',
                                            'ctstp',
                                            'utstp',
                                    ],
                            ],
                            'lblList'  => [
                                    CstMode::V_S_UPDT => 'Load',
                            ],
                            'navList' => [
                                    CstMode::V_S_READ => [CstMode::V_S_UPDT],
                            ],
                    ],
                    
            ],
    ];
    
    public static function loadMeta()
    {
        return true;
    }
    
    public static function loadData()
    {
        return true;
    }
}
