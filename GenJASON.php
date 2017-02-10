<?php

require_once("Handle.php");
require_once("FormatLib.php");


function genJASON($h,$show)
{
    return genJasLvl($h, 0, $show);
}

function genJasLvl($h,$level,$show)
{
    $nl=getNl($level);
    $tbs=getTab($level);
    $tbss=getTab($level+1);
    $tbsss=getTab($level+2);
    if ($level == 0) {  
        $res= $nl. $tbs.'{' .$nl ;
        $res=$res.$tbs.'"'.$h->getModName().'"' . ' : {' .$nl ;
    } else {
        $res = $tbs.'{'.$nl;
    }   
    $aList = $h->getAttrList();
    $first = true;
    $c= count($aList);
    foreach ($aList as $attr) {
        $typ = $h->getTyp($attr);
        if (! $h->isEval($attr)) {
            if ($first) {
                $first = false;
            } else {
                $res=$res. ','.$nl;
            }
            $val = $h->getVal($attr);
            switch ($typ) {
                case M_CREF :
                    $res=$res. $tbss.'"'.$attr .'"'.' : {'.$nl ;
                    $res=$res. $tbsss.'"'.$h->getModCref($attr);
                    $res=$res. '" : ['.$nl ;
                    $lfirst = true;
                    foreach ($val as $id) {
                        if ($lfirst) {
                            $lfirst = false;
                        } else {
                            $res=$res. ',' . $nl;
                        }
                        $nh= $h->getCref($attr, $id);
                        $res = $res.genJasLvl($nh, $level+3, false);
                    }
                    $res=$res.$nl.$tbsss. " ] ".$nl ;        
                    $res=$res. $tbss. "}" ;
                    break;
                case M_REF :
                    $hc = $h->getRef($attr);
                    if (is_null($hc)) {
                        $res=$res. $tbss.'"'.$attr .'" : {}' ;
                    } else {
                        $res=$res. $tbss.'"'.$attr .'" : {' ;
                        $res=$res. '"'.$hc->getModName().'" : {"id" : ';
                        $res=$res. $hc->getId();
                        $res=$res. " }}" ;                        
                    }
                    break;
                case M_CODE :
                    $hc = $h->getCode($attr, $val);
                    if (is_null($hc)) {
                        $res=$res. $tbss.'"'.$attr .'" : {}' ;
                    } else {
                        $res=$res. $tbss.'"'.$attr .'" : {' ;
                        $res=$res. '"'.$hc->getModName().'" : {"id" : "';
                        $res=$res. $hc->getId();
                        $res=$res. " }}" ;                        
                    }
                    break;              
                default :
                    $res=$res. $tbss.'"'.$attr .'"'.' : "'. $val.'"';
            }
        }
    }
    $res=$res.$nl.$tbs.'}' ;
    if ($level == 0) {
        $res=$res.$nl.$tbs.'}' ;
    }
    if ($show) {
        echo $res;
    }
    return $res;
}       