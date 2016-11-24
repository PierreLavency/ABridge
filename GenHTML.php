<?php

require_once("ViewConstant.php");

function getTab($level) 
{
    $tab = "";
    if ($level >= 0) {
        for ($i=0;$i<$level;$i++) {
            $tab=$tab."\t";
        }
    }
    return $tab;
}

function getNl($level) 
{
    $nl = "";
    if ($level >= 0) {
        $nl = "\n";
    }
    return $nl;
}

function genForm($action,$url,$hidden,$dspec,$show=true)
{
    genformL($action, $dspec, $hidden, $show, 0);
}

function genFormL($action,$url,$hidden,$dspecL,$show,$level) 
{
    $formS = '<form method='.$action.' action= '.$url. ' >';
    $formS = $formS."<input type='hidden' name='action' value='";
    $formS = $formS.$hidden. "' >" ;
    $formES = '</form>  ';
    $endS    = ' > '     ;
    $tab = getTab($level);
    $nl  = getNl($level);

    $result=$tab.$formS.$nl;
    foreach ($dspecL as $dspec) {
        $result=$result. genFormElemL($dspec, false, $level+1);
    }
    $result=$result.$tab.$formES.$nl;
    if ($show) {
        echo $result;
    };
    return $result;
}

function genList($dspec,$show=true)
{
    return(genListL($dspec, $show, 0));
}

function genListL($dspecL,$show,$level)
{
    $listS   = '<ul>'  ;
    $listES = '</ul>';
    $elementS   = '<li>'  ;
    $elementES   = '</li>'  ;
    $tab = getTab($level);
    $nl  = getNl($level);
    $tabn=getTab($level+1);;
    
    $result = $tab.$listS ; 
    foreach ($dspecL as $dspec) {
        $result=$result . $nl . $tabn. $elementS. $nl;
        $result=$result .genFormElemL($dspec, false, $level+2).$tabn.$elementES;
    }
    $result = $result.$nl.$tab.$listES.$nl;
    if ($show) {
        echo $result;
    };
    return $result;
}

function genFormElem($dspec,$show = true)    
{
    return (genFormElemL($dspec, $show, 0));
}   

function genFormElemL($dspec,$show,$level)   
{

    $buttonS   = '<input type="submit" value = ';
    $textareaS = '<textarea ';
    $textareaES = '</textarea>';
    $selectS   = '<select '  ;
    $selectES = '</select>';
    $inputS    = '<input '   ;
    $optionS   = '<option '  ;
    $optionES = '</option>';
    $linkS   = '<a href='  ;
    $linkES = '</a>';
    $endS      = ' >'    ;
    $colS      = ' cols="'    ;
    $rowS      = ' rows="'    ;
    
    $type="";
    $default="";
    $hidden="";
    $separator="";
    $name="";
    $action="";
    $url="";
    $arg = [];
    $plain;
    $col = 30;
    $row = 10;
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
            case H_DEFAULT:
                $default = $v;
                break; 
            case H_COL:
                $col = $v;
                break; 
            case H_ROW:
                $row = $v;
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
            case H_ARG:
                $arg = $v;
                break;
        }; 
    };

    $nameS = 'name = "' . $name .  '" ';
    $typeS = 'type = "' . $type .  '" ';


    if ($type == H_T_PASSWORD) {
        $type="text";
    };
    switch ($type) {
        case H_T_LINK:
            $result = $tab.$linkS.$name.$endS.$label.$linkES.$nl;
            break;
        case H_T_LIST:
            $result = genListL($arg, false, $level);
            break;
        case H_T_CONCAT:
            $result = $tab;
            $c = count($arg);
            $i = 0;
            foreach ($arg as $elem) {
                $result=$result.genFormElemL($elem, false, -1);
                $i++;
                if ($i < $c ) {
                    $result = $result . $separator;
                }
            }
            break;
        case H_T_FORM:
            $result = genFormL($action, $url, $hidden, $arg, false, $level);
            break;
        case H_T_TEXTAREA:
            $result = $textareaS . $nameS; 
            $result = $result . $colS . $col . '" ' ; 
            $result = $result . $rowS . $row . '" ' . $endS ; 
            if ($default) {
                $result = $result.$default;
            };
            $result = $tab.$result . $textareaES . $nl;
            break;
        case H_T_SUBMIT:
            $result = $tab.$buttonS.$label.$endS.$nl; 
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
                $result = $result.$tab . TAB_O. $optionS . $valueS;
                $result = $result . $selectedS . $endS .$valuelbl;
                $result = $result.$optionES. $nl;
            };
            $result = $result . $tab.$selectES. $nl;
            break;
        case H_T_PLAIN:
            $result = $tab.$default.$nl;
            break;
        case H_T_NULL:
            $result="";
            break;
        default:
            $result = $plain;
    }
    if ($show) {
        echo $result;
    };
    return $result;
}
