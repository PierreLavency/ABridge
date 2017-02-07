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
                $res=$res. "\t\t".'"'.$attr .'" : {'."\n" ;
                $res=$res. "\t\t\t".'"'.$h->getModCref($attr);
                $res=$res. '" : ['."\n" ;              
                $cc = count($val);
                foreach ($val as $id) {
                    $cc--;
                    $hc = $h->getCref($attr, $id);
                    $res=$res.  "\t\t\t\t".'{ "id" : "'.$id;
                    $res=$res.', "path" : "'.$path;
                    $res=$res.'/'.$attr.'/'.$id.'" }';                     
                    if ($cc) {
                        $res=$res. ',';
                    }
                    $res=$res. "\n";
                }
                $res=$res. "\t\t\t ] \n" ;        
                $res=$res. "\t\t }" ;
                break;
            case M_REF :
                $hc = $h->getRef($attr);
                if (is_null($hc)) {
                    $res=$res. "\t\t".'"'.$attr .'" : {}' ;
                } else {
                    $res=$res. "\t\t".'"'.$attr .'" : {'."\n" ;
                    $res=$res. "\t\t\t".'"'.$hc->getModName().'" : {"id" : ';
                    $res=$res. $hc->getId().', "path" : "';
                    $res=$res. $hc->getPath().'" }'."\n";
                    $res=$res. "\t\t}" ;                        
                }
                break;
            case M_CODE :
                $hc = $h->getCode($attr, $val);
                if (is_null($hc)) {
                    $res=$res. "\t\t".'"'.$attr .'" : {}' ;
                } else {
                    $res=$res. "\t\t".'"'.$attr .'" : {'."\n" ;
                    $res=$res. "\t\t\t".'"'.$hc->getModName().'" : {"id" : "';
                    $res=$res. $hc->getId().', "path" : "';
                    $res=$res. $hc->getPath().'" }'."\n";
                    $res=$res. "\t\t}" ;                        
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