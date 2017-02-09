<?php

require_once("Handle.php");

function genJASON($h)
{
   
    $res= "\n". '{' ."\n" ;
    $res=$res. "\t".'"'.$h->getModName().'"' . ' : {' ."\n" ;
    $aList = $h->getAttrList();
    $path=$h->getPath();
    $c= count($aList);
    foreach ($aList as $attr) {
        $c--;
        $typ = $h->getTyp($attr);
        $val = $h->getVal($attr);
        switch ($typ) {
            case M_CREF :
                $res=$res. "\t\t".'"'.$attr .'"'.' : {'."\n" ;
                $res=$res. "\t\t\t".'"'.$h->getModCref($attr);
                $res=$res. '" : [' ;
                $valS=implode(',', $val);
                $res=$res.$valS;
                $res=$res. " ] \n" ;        
                $res=$res. "\t\t }" ;
                break;
            case M_REF :
                $hc = $h->getRef($attr);
                if (is_null($hc)) {
                    $res=$res. "\t\t".'"'.$attr .'" : {}' ;
                } else {
                    $res=$res. "\t\t".'"'.$attr .'" : {'."\n" ;
                    $res=$res. "\t\t\t".'"'.$hc->getModName().'" : {"id" : ';
                    $res=$res. $hc->getId();
                    $res=$res. " }" ;                        
                }
                break;
            case M_CODE :
                $hc = $h->getCode($attr, $val);
                if (is_null($hc)) {
                    $res=$res. "\t\t".'"'.$attr .'" : {}' ;
                } else {
                    $res=$res. "\t\t".'"'.$attr .'" : {'."\n" ;
                    $res=$res. "\t\t\t".'"'.$hc->getModName().'" : {"id" : "';
                    $res=$res. $hc->getId();
                    $res=$res. " }" ;                        
                }
                break;              
            default :
                $res=$res. "\t\t".'"'.$attr .'"'.' : "'. $val.'"';
        }
        if ($c) {
            $res=$res. ',';
        }
        $res=$res. "\n";
    }
    $res=$res. "\t".'}'."\n" ;
    $res=$res. '}'."\n" ;
    echo $res;
}       