<?php
namespace ABridge\ABridge\Apps;

use ABridge\ABridge\Hdl\CstMode;
use ABridge\ABridge\View\CstView;
use ABridge\ABridge\View\CstHTML;
use ABridge\ABridge\AppComp;
use ABridge\ABridge\Adm\Adm;

class AdmApp extends AppComp
{
    
	public function initOwnMeta($prm)
	{
		return Adm::get()->initMeta();
	}	
	
	public function __construct($prm, $bindings)
    {
    	$this->prm=$prm;
    	$this->bindings=$bindings;
        $adm=Adm::ADMIN;
        if (isset($bindings[Adm::ADMIN])) {
            $adm = $bindings[Adm::ADMIN];
        }
        
        $this->config = [

            'Handlers' => [
                    $adm => ['memBase',],
            ],
                  
            'Adm' => [
                    Adm::ADMIN=>$adm,
            ],
            
            'View' => [
                    $adm =>[
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
                                    'Parameter'=>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'id',
                                                            'Name',
                                                            'Parameters',
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
                                                            'Name',
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
                                                    CstMode::V_S_READ =>
                                                    [
                                                            CstMode::V_S_UPDT
                                                            
                                                    ],
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
                                            'attrProp' =>
                                            [
                                                    CstMode::V_S_READ =>[CstView::V_P_VAL],
                                            ],
                                            'attrHtml' => [
                                                    CstMode::V_S_READ =>
                                                    [
                                                     'ModState'=>[
                                                        CstHTML::H_TYPE=>
                                                        CstHTML::H_T_TEXTAREA,
                                                        CstHTML::H_COL=>90,
                                                        CstHTML::H_ROW=> 36,
                                                            
                                                     ]
                                                            
                                                    ],
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [],
                                            ],
                                    ],
                                    'StateHandler' =>
                                    [
                                            'attrList' =>
                                            [
                                                    CstMode::V_S_READ=>
                                                    [
                                                            'Model','StateHandler',
                                                            
                                                    ],
                                                    CstMode::V_S_UPDT=>
                                                    [
                                                            'Model'
                                                            
                                                    ],
                                            ],
                                            'attrProp' =>
                                            [
                                                    CstMode::V_S_READ =>[CstView::V_P_VAL],
                                            ],
                                            'attrHtml' => [
                                                    CstMode::V_S_READ =>
                                                    [
                                                     'StateHandler'=> [
                                                        CstHTML::H_TYPE=>
                                                        CstHTML::H_T_TEXTAREA,
                                                        CstHTML::H_COL=>90,
                                                        CstHTML::H_ROW=> 36
                                                     ]
                                                            
                                                    ],
                                            ],
                                            'navList' =>
                                            [
                                                    CstMode::V_S_READ => [CstMode::V_S_UPDT],
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
    }
    
}
