<?php
namespace ABridge\ABridge;

abstract class Comp
{
    abstract public static function get();

    abstract static public function reset();
    
    abstract public function init($appPrm, $config);
    
    abstract public function begin($appPrm, $config);
    
    abstract public function isNew();
    
    abstract public function initMeta($appPrm, $config);
    
    
    public static function normBindings($bindings)
    {
        $normBindings=[];
        foreach ($bindings as $logicalName => $physicalName) {
            if (is_numeric($logicalName)) {
                $normBindings[$physicalName]=$physicalName;
            } else {
                $normBindings[$logicalName]=$physicalName;
            }
        }
        return $normBindings;
    }
    
    public static function defltHandlers($bindings)
    {
        $defltHandlers=[];
        foreach ($bindings as $logicalName => $physicalName) {
            $defltHandlers[$physicalName]=[];
        }
        return $defltHandlers;
    }
}
