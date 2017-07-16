<?php

use ABridge\ABridge\Handle;

require_once("FormatLib.php");


function genJASON($h, $show, $tmst, $depth = -1)
{
    $mod= $h->getModName();
    $id = $h->getId();
    return genJasLvl($h, $depth, 0, $show, $mod, $id, $tmst);
}

function genJasLvl($h, $depth, $level, $show, $tmod, $tid, $tmst)
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
    $skip = false;
    $c= count($aList);
    foreach ($aList as $attr) {
        $typ = $h->getTyp($attr);
        if ((! $h->isEval($attr))
            and (!($typ==M_CREF and $depth==0))
            and ($tmst or ($attr!='ctstp' and $attr!='utstp'))) {
            if (!$first and !$skip) {
                $res=$res. ','.$nl;
            }
            if ($skip) {
                $skip=false;
            }
            $val = $h->getVal($attr);
            switch ($typ) {
                case M_CREF:
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
                        $res = $res.genJasLvl(
                            $nh,
                            $depth-1,
                            $level+3,
                            false,
                            $tmod,
                            $tid,
                            $tmst
                        );
                    }
                    $res=$res.$nl.$tbsss. " ] ".$nl ;
                    $res=$res. $tbss. "}" ;
                    break;
                case M_REF:
                    $hc = $h->getRef($attr);
                    if (is_null($hc)) {
                        $res=$res. $tbss.'"'.$attr .'" : {}' ;
                    } else {
                        $rmod = $hc->getModName();
                        $rid = $hc->getId();
                        if ($rmod != $tmod or $tid != $rid) {
                            $res=$res. $tbss.'"'.$attr .'" : {' ;
                            $res=$res. '"'.$rmod.'" : {"id" : ';
                            $res=$res. $rid;
                            $res=$res. " }}" ;
                        } else {
                            $skip = true;
                        }
                    }
                    break;
                case M_CODE:
                    if (is_null($val)) {
                        $res=$res. $tbss.'"'.$attr .'" : {}' ;
                    } else {
                        $hc = $h->getCode($attr, $val);
                        $res=$res. $tbss.'"'.$attr .'" : {' ;
                        $res=$res. '"'.$hc->getModName().'" : {"id" : "';
                        $res=$res. $hc->getId();
                        $res=$res. " }}" ;
                    }
                    break;
                default:
                    $res=$res. $tbss.'"'.$attr .'"'.' : "'. $val.'"';
            }
            if ($first and !$skip) {
                $first = false;
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
