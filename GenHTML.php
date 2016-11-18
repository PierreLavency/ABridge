<?php

define('NL_O', "\n");
define('TAB_O', "\t");

require_once("ViewConstant.php");

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
    $tab = "";
    for ($i=0;$i<$level;$i++) {
        $tab=$tab.TAB_O;
    }
    $result=$tab.$formS.NL_O;
    foreach ($dspecL as $dspec) {
        $result=$result. genFormElemL($dspec, false, $level+1);
    }
    $result=$result.$tab.$formES.NL_O;
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
    $tab = "";
    for ($i=0;$i<$level;$i++) {
        $tab=$tab.TAB_O;
    }
    $tabn=$tab.TAB_O;
    
    $result = $tab.$listS ; 
    foreach ($dspecL as $dspec) {
        $result=$result . NL_O . $tabn. $elementS. NL_O;
        $result=$result .genFormElemL($dspec, false, $level+2).$tabn.$elementES;
    }
    $result = $result . NL_O. $tab. $listES .NL_O;
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

    $buttonS   = '<input type="submit" value="Submit">';
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
    $tab = "";
    for ($i=0;$i<$level;$i++) {
        $tab=$tab.TAB_O;
    }
    
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
            $result = $tab.$linkS.$name.$endS.$label.$linkES.NL_O;
            break;
        case H_T_LIST:
            $result = genListL($arg, false, $level);
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
            $result = $tab.$result . $textareaES . NL_O;
            break;
        case H_T_SUBMIT:
            $result = $tab.$buttonS.NL_O; 
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
            $result = $tab.$result . NL_O;
            break;
        case H_T_RADIO:
            $result = "";
            foreach ($values as $value) {
                $valueS = ' value = "' . $value .  '" ';
                $checkedS = "";
                if ($value == $default) {
                    $checkedS = " checked ";
                };
                $result = $result.$tab . $inputS . $typeS . $nameS;
                $result = $result. $valueS . $checkedS . $endS . $separator;
                $result = $result. NL_O;
            };
            break;    
        case H_T_SELECT:
            $result = $selectS;
            $result = $tab.$result. $nameS . $endS . NL_O ;
            foreach ($values as $value) {
                $valueS = ' value = "' . $value .  '" ';
                $selectedS = "";
                if ($value == $default) {
                    $selectedS = " selected ";
                };
                $result = $result.$tab . TAB_O. $optionS . $valueS;
                $result = $result . $selectedS . $endS .$value .$optionES. NL_O;
            };
            $result = $result . $tab.$selectES. NL_O;
            break;
        case H_T_PLAIN:
            $result = $tab.$default.NL_O;
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
