<?php

require_once 'CstView.php';
require_once 'FormatLib.php';

function genFormL($action, $url, $hidden, $dspecL, $level)
{
    $formS = '<form method='.$action.' action= '.$url. ' >';
    foreach ($hidden as $name => $val) {
        $formS=$formS."<input type='hidden' name='";
        $formS=$formS.$name."' value='".$val."' >" ;
    }
    
    $formES = '</form>  ';
    $endS    = ' > '     ;
    $tab = getTab($level);
    $nl  = getNl($level);

    $result=$tab.$formS.$nl;
    foreach ($dspecL as $dspec) {
        $result=$result. genFormElemL($dspec, $level+1);
    }
    $result=$result.$tab.$formES.$nl;
    return $result;
}

function genNTableL($tablen, $dspecL, $level)
{
    $tableS = '<table >';
    $tableSE = '</table>';
    $elementS   = '<tr>'  ;
    $elementES   = '</tr>'  ;
    $tab = getTab($level);
    $nl  = getNl($level);
    $tabn=getTab($level+1);
    
    $c=array_chunk($dspecL, $tablen);
    
    $result= $tab.$tableS;
    
    foreach ($c as $dspec) {
        $result=$result . $nl . $tabn. $elementS. $nl;
        $result=$result .genLineNL($dspec, $level+1).$tabn.$elementES;
    }
    $result = $result.$nl.$tab.$tableSE.$nl;
    return $result;
}

function genLineNL($dspecL, $level)
{
    $elementS   = '<td>'  ;
    $elementES   = '</td>'  ;
    $result = "";
    $tab = getTab($level);
    $nl  = getNl($level);
    
    foreach ($dspecL as $elm) {
        $result=$result.$tab.$elementS.$nl;
        $result=$result.genFormElemL($elm, $level+1);
        $result=$result.$tab.$elementES.$nl;
    }
 
    return $result;
}

function gen1TableL($dspecL, $level)
{
    $tableS = '<table > <tr>';
    $tableSE = '</tr> </table>';
    $elementS   = '<td>'  ;
    $elementES   = '</td>'  ;
    $tab = getTab($level);
    $nl  = getNl($level);
    $tabn= getTab($level+1);
    
    $result= $tab.$tableS;
    foreach ($dspecL as $dspec) {
        $result=$result . $nl . $tabn. $elementS. $nl;
        $result=$result .genFormElemL($dspec, $level+2).$tabn.$elementES;
    }
    $result = $result.$nl.$tab.$tableSE.$nl;
    return $result;
}

function genTableL($dspecL, $level)
{
    $tableS = '<table >';
    $tableSE = '</table>';
    $elementS   = '<tr>'  ;
    $elementES   = '</tr>'  ;
    $tab = getTab($level);
    $nl  = getNl($level);
    $tabn=getTab($level+1);
    
    $result= $tab.$tableS;
    foreach ($dspecL as $dspec) {
        $result=$result . $nl . $tabn. $elementS. $nl;
        $result=$result .genLineL($dspec, $level+2).$tabn.$elementES;
    }
    $result = $result.$nl.$tab.$tableSE.$nl;
    return $result;
}

function genLineL($dspecL, $level)
{
    $elementS   = '<td>'  ;
    $elementES   = '</td>'  ;
    $result = "";
    $tab = getTab($level);
    $nl  = getNl($level);

    if (isset($dspecL[H_ARG])) {
        $elmL = $dspecL[H_ARG];
        foreach ($elmL as $elm) {
            $result=$result.$tab.$elementS.$nl;
            $result=$result.genFormElemL($elm, $level+1);
            $result=$result.$tab.$elementES.$nl;
        }
    } else {
        $result=$result.$tab.$elementS. $nl;
        $result=$result.genFormElemL($dspecL, $level+1);
        $result=$result.$tab.$elementES.$nl;
    }
    return $result;
}

function genListL($dspecL, $level)
{
    $listS   = '<ul>'  ;
    $listES = '</ul>';
    $elementS   = '<li>'  ;
    $elementES   = '</li>'  ;
    $tab = getTab($level);
    $nl  = getNl($level);
    $tabn=getTab($level+1);
    
    $result = $tab.$listS;
    foreach ($dspecL as $dspec) {
        $result=$result . $nl . $tabn. $elementS. $nl;
        $result=$result .genFormElemL($dspec, $level+2).$tabn.$elementES;
    }
    $result = $result.$nl.$tab.$listES.$nl;
    return $result;
}

function genHTML($dspec, $show = true)
{
    $htmlS="<!DOCTYPE html>\n<html>\n<head>\n<title>ABridge</title>";
    $htmlS= $htmlS."\n</head>\n<body> \n ";
    $htmlSE="\n </body> \n </html>";
    $res=genFormElemL($dspec, 0);
    $result = $htmlS.$res.$htmlSE;
    if ($show) {
        echo $result;
    }
    return $result;
}


function genFormElem($dspec, $show = true)
{
    $res=genFormElemL($dspec, 0);
    if ($show) {
        echo $res;
    }
    return $res;
}

function genFormElemL($dspec, $level)
{
    $buttonS    = '<input type="submit" value = ';
    $buttonES   = '</button>';
    $altS       = ' alt="';
    $textareaS  = '<textarea ' ;
    $textareaES = '</textarea>';
    $selectS    = '<select '   ;
    $selectES   = '</select>'  ;
    $inputS     = '<input '    ;
    $optionS    = '<option '   ;
    $optionES   = '</option>'  ;
    $imgS       = '<img src="' ;
    $linkS      = '<a href='   ;
    $linkES     = '</a>'       ;
    $endS       = ' >'         ;
    $colS       = ' cols="'    ;
    $rowS       = ' rows="'    ;
    $widthS     = ' width="'    ;
    $heightS    = ' height="'    ;
    
    $type="";
    $default="";
    $hidden="";
    $separator="";
    $disabled="";
    $name="";
    $action="";
    $url="";
    $arg = [];
    $tablen=2;
    $col = 90;
    $row = 20;
    $colp = 70;
    $rowp = 70;
    $label="";
    $tab = getTab($level);
    $nl  = getNl($level);

    foreach ($dspec as $t => $v) {
        switch ($t) {
            case H_TYPE:
                $type = $v;
                break;
            case H_NAME:
                $name = $v;
                break;
            case H_LABEL:
                $label = $v;
                break;
            case H_SEPARATOR:
                $separator = $v;
                break;
            case H_DISABLED:
                if ($v) {
                    $disabled = 'disabled';
                }
                break;
            case H_DEFAULT:
                $default = $v;
                break;
            case H_TABLEN:
                $tabeln = $v;
                break;
            case H_COL:
                $col = $v;
                break;
            case H_ROW:
                $row = $v;
                break;
            case H_COLP:
                $colp = $v;
                break;
            case H_ROWP:
                $rowp = $v;
                break;
            case H_VALUES:
                $values = $v;
                break;
            case H_ACTION:
                $action = $v;
                break;
            case H_HIDDEN:
                $hidden = $v;
                break;
            case H_URL:
                $url = $v;
                break;
            case H_BACTION:
                $baction = $v;
                break;
            case H_ARG:
                $arg = $v;
                break;
        }
    }
    
    $nameS="";
    if (! is_null($name)) {
        $nameS = 'name = "' . $name .  '" ';
    }
    $typeS="";
    if ((! is_null($type)) and is_string($type)) {
        $typeS = 'type = "' . $type .  '" ';
    }

    if ($type == H_T_PASSWORD) {
        $type=H_T_TEXT;
    };
    switch ($type) {
        case H_T_LINK:
            if (is_array($label)) {
                $label = genFormElemL($label, $level);
            }
            $result = $tab.$linkS.$name.$endS.$label.$linkES.$nl;
            break;
        case H_T_LIST_BR:
            $result = "";
            foreach ($arg as $elm) {
                $res= genFormElemL($elm, false, $level);
                $result = $result. $res . "\n";
            }
            break;
        case H_T_LIST:
            $result = genListL($arg, $level);
            break;
        case H_T_TABLE:
            $result = genTableL($arg, $level);
            break;
        case H_T_1TABLE:
            $result = gen1TableL($arg, $level);
            break;
        case H_T_NTABLE:
            $result = genNTableL($tablen, $arg, $level);
            break;
        case H_T_CONCAT:
            $result = $tab;
            $c = count($arg);
            $i = 0;
            foreach ($arg as $elem) {
                $result=$result.genFormElemL($elem, false, -1);
                $i++;
                if ($i < $c) {
                    $result = $result . $separator;
                }
            }
            break;
        case H_T_FORM:
            $result = genFormL($action, $url, $hidden, $arg, $level);
            break;
        case H_T_TEXTAREA:
            $result = $textareaS . $nameS . $disabled;
            $result = $result . $colS . $col . '" ' ;
            $result = $result . $rowS . $row . '" ' . $endS ;
            if ($default) {
                $result = $result.$default;
            };
            $result = $tab.$result . $textareaES . $nl;
            break;
        case H_T_SUBMIT:
            $result = $tab.$buttonS.$label;
            if (isset($baction)) {
                $result = $result.' formaction='.$baction;
            }
            $result = $result.$endS.$nl;
            break;
        case H_T_TEXT:
            $result = $inputS;
            $result = $result . $typeS;
            $result = $result . $nameS;
            $valueS ='';
            if ($default) {
                $valueS = 'value = "' . $default .  '" ';
            };
            $result = $result . $valueS;
            $result = $result . $endS;
            $result = $tab.$result . $nl;
            break;
        case H_T_RADIO:
            $result = "";
            foreach ($values as $valueA) {
                $value=array_shift($valueA);
                $valuelbl = array_shift($valueA);
                $valueS = ' value = "' . $value .  '" ';
                $checkedS = "";
                if ($value == $default) {
                    $checkedS = " checked ";
                };
                $result = $result.$tab . $inputS . $typeS . $nameS;
                $result = $result. $valueS . $checkedS . $endS;
                $result = $result . $valuelbl. $separator;
                $result = $result. $nl;
            };
            break;
        case H_T_SELECT:
            $result = $selectS;
            $result = $tab.$result. $nameS . $endS . $nl ;
            foreach ($values as $valueA) {
                $value=array_shift($valueA);
                $valuelbl = array_shift($valueA);
                $valueS = ' value = "' . $value .  '" ';
                $selectedS = "";
                if ($value == $default) {
                    $selectedS = " selected ";
                };
                $result = $result.$tab . "\t". $optionS . $valueS;
                $result = $result . $selectedS . $endS .$valuelbl;
                $result = $result.$optionES. $nl;
            };
            $result = $result . $tab.$selectES. $nl;
            break;
        case H_T_IMG:
            $result = $imgS.$default.'"'.$altS.$default.'"'.$widthS.$colp.'"'.$heightS.$rowp.'"'.$endS;
            break;
        case H_T_PLAIN:
            $result = $tab.$default.$nl;
            break;
        default:
            $result = ' Unknown H_TYPE ';
    }
    return $result;
}
