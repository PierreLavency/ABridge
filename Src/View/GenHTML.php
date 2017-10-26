<?php
namespace ABridge\ABridge\View;

use ABridge\ABridge\View\CstHTML;

class GenHTML
{
   
    private static function getTab($level)
    {
        $tab = "";
        if ($level >= 0) {
            for ($i=0; $i<$level; $i++) {
                $tab=$tab."\t";
            }
        }
        return $tab;
    }
    
    protected static function genFormL($action, $url, $hidden, $dspecL, $level)
    {
        $formS = '<form method='.$action.' action= '.$url. ' >';
        foreach ($hidden as $name => $val) {
            $formS=$formS."<input type='hidden' name='";
            $formS=$formS.$name."' value='".$val."' >" ;
        }
        
        $formES = '</form>  ';
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
    
        $result=$tab.$formS.$newLine;
        foreach ($dspecL as $dspec) {
            $result=$result. self::genFormElemL($dspec, $level+1);
        }
        $result=$result.$tab.$formES.$newLine;
        return $result;
    }
    
    protected static function genNTableL($tablen, $dspecL, $level)
    {
        $tableS = '<table >';
        $tableSE = '</table>';
        $elementS   = '<tr>'  ;
        $elementES   = '</tr>'  ;
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
        $tabn=self::getTab($level+1);
        
        $flatList = [];
        foreach ($dspecL as $dspecElm) {
            if (isset($dspecElm[CstHTML::H_ARG])) {
                $flatList=array_merge($flatList, $dspecElm[CstHTML::H_ARG]);
            } else {
                $flatList[]=$dspecElm;
            }
        }
        
        $c=array_chunk($flatList, $tablen);
        
        $result= $tab.$tableS;
        
        foreach ($c as $dspec) {
            $result=$result . $newLine . $tabn. $elementS. $newLine;
            $result=$result .self::genLineNL($dspec, $level+1).$tabn.$elementES;
        }
        $result = $result.$newLine.$tab.$tableSE.$newLine;
        return $result;
    }
    
    protected static function genLineNL($dspecL, $level)
    {
        $elementS   = '<td>'  ;
        $elementES   = '</td>'  ;
        $result = "";
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
        
        foreach ($dspecL as $elm) {
            $result=$result.$tab.$elementS.$newLine;
            $result=$result.self::genFormElemL($elm, $level+1);
            $result=$result.$tab.$elementES.$newLine;
        }
     
        return $result;
    }
    
    protected static function gen1TableL($dspecL, $level)
    {
        $tableS = '<table > <tr>';
        $tableSE = '</tr> </table>';
        $elementS   = '<td>'  ;
        $elementES   = '</td>'  ;
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
        $tabn=self::getTab($level+1);
        
        $result= $tab.$tableS;
        foreach ($dspecL as $dspec) {
            $result=$result . $newLine . $tabn. $elementS. $newLine;
            $result=$result .self::genFormElemL($dspec, $level+2).$tabn.$elementES;
        }
        $result = $result.$newLine.$tab.$tableSE.$newLine;
        return $result;
    }
    
    protected static function genTableL($dspecL, $level)
    {
        $tableS = '<table >';
        $tableSE = '</table>';
        $elementS   = '<tr>'  ;
        $elementES   = '</tr>'  ;
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
        $tabn=self::getTab($level+1);
        
        $result= $tab.$tableS;
        foreach ($dspecL as $dspec) {
            $result=$result . $newLine . $tabn. $elementS. $newLine;
            $result=$result .self::genLineL($dspec, $level+2).$tabn.$elementES;
        }
        $result = $result.$newLine.$tab.$tableSE.$newLine;
        return $result;
    }
    
    protected static function genLineL($dspecL, $level)
    {
        $elementS   = '<td>'  ;
        $elementES   = '</td>'  ;
        $result = "";
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
    
        if (isset($dspecL[CstHTML::H_ARG])) {
            if ($dspecL[CstHTML::H_TYPE]==CstHTML::H_T_TABELBL) {
                $elementS   = '<th>'  ;
                $elementES   = '</th>'  ;
            }
            $elmL = $dspecL[CstHTML::H_ARG];
            foreach ($elmL as $elm) {
                $result=$result.$tab.$elementS.$newLine;
                $result=$result.self::genFormElemL($elm, $level+1);
                $result=$result.$tab.$elementES.$newLine;
            }
        } else {
            $result=$result.$tab.$elementS. $newLine;
            $result=$result.self::genFormElemL($dspecL, $level+1);
            $result=$result.$tab.$elementES.$newLine;
        }
        return $result;
    }
    
    protected static function genListL($dspecL, $level)
    {
        $listS   = '<ul>'  ;
        $listES = '</ul>';
        $elementS   = '<li>'  ;
        $elementES   = '</li>'  ;
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
        $tabn=self::getTab($level+1);
        
        $result = $tab.$listS;
        foreach ($dspecL as $dspec) {
            $result=$result . $newLine . $tabn. $elementS. $newLine;
            $result=$result .self::genFormElemL($dspec, $level+2).$tabn.$elementES;
        }
        $result = $result.$newLine.$tab.$listES.$newLine;
        return $result;
    }
    
    public static function genHTML($dspec, $show = true)
    {
        $htmlS="<!DOCTYPE html>\n<html>\n<head>\n<title>ABridge</title>";
        $htmlS= $htmlS."\n</head>\n<body> \n ";
        $htmlSE="\n </body> \n </html>";
        $res=self::genFormElemL($dspec, 0);
        $result = $htmlS.$res.$htmlSE;
        if ($show) {
            echo $result;
        }
        return $result;
    }
    
    
    public static function genFormElem($dspec, $show = true)
    {
        $res=self::genFormElemL($dspec, 0);
        if ($show) {
            echo $res;
        }
        return $res;
    }
    
    protected static function genFormElemL($dspec, $level)
    {
        $buttonS    = '<input type="submit" value = ';
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
        $hidden=[];
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
        $tab =self::getTab($level);
        $newLine  = ($level >= 0) ? "\n" : '';
    
        foreach ($dspec as $t => $v) {
            switch ($t) {
                case CstHTML::H_TYPE:
                    $type = $v;
                    break;
                case CstHTML::H_NAME:
                    $name = $v;
                    break;
                case CstHTML::H_LABEL:
                    $label = $v;
                    break;
                case CstHTML::H_SEPARATOR:
                    $separator = $v;
                    break;
                case CstHTML::H_DISABLED:
                    if ($v) {
                        $disabled = 'disabled';
                    }
                    break;
                case CstHTML::H_DEFAULT:
                    $default = $v;
                    break;
                case CstHTML::H_TABLEN:
                    $tablen = $v;
                    break;
                case CstHTML::H_COL:
                    $col = $v;
                    break;
                case CstHTML::H_ROW:
                    $row = $v;
                    break;
                case CstHTML::H_COLP:
                    $colp = $v;
                    break;
                case CstHTML::H_ROWP:
                    $rowp = $v;
                    break;
                case CstHTML::H_VALUES:
                    $values = $v;
                    break;
                case CstHTML::H_ACTION:
                    $action = $v;
                    break;
                case CstHTML::H_HIDDEN:
                    $hidden = $v;
                    break;
                case CstHTML::H_URL:
                    $url = $v;
                    break;
                case CstHTML::H_BACTION:
                    $baction = $v;
                    break;
                case CstHTML::H_ARG:
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
    
        if ($type == CstHTML::H_T_PASSWORD) {
            $type=CstHTML::H_T_TEXT;
        };
        switch ($type) {
            case CstHTML::H_T_LINK:
                if (is_array($label)) {
                    $label = self::genFormElemL($label, $level);
                }
                $result = $tab.$linkS.$name.$endS.$label.$linkES.$newLine;
                break;
            case CstHTML::H_T_LIST_BR:
                $result = "";
                foreach ($arg as $elm) {
                    $res= self::genFormElemL($elm, false, $level);
                    $result = $result. $res . "\n";
                }
                break;
            case CstHTML::H_T_LIST:
                $result = self::genListL($arg, $level);
                break;
            case CstHTML::H_T_TABLE:
                $result = self::genTableL($arg, $level);
                break;
            case CstHTML::H_T_1TABLE:
                $result = self::gen1TableL($arg, $level);
                break;
            case CstHTML::H_T_NTABLE:
                $result = self::genNTableL($tablen, $arg, $level);
                break;
            case CstHTML::H_T_CONCAT:
                $result = $tab;
                $c = count($arg);
                $i = 0;
                foreach ($arg as $elem) {
                    $result=$result.self::genFormElemL($elem, false, -1);
                    $i++;
                    if ($i < $c) {
                        $result = $result . $separator;
                    }
                }
                break;
            case CstHTML::H_T_FORM:
                $result = self::genFormL($action, $url, $hidden, $arg, $level);
                break;
            case CstHTML::H_T_TEXTAREA:
                $result = $textareaS . $nameS . $disabled;
                $result = $result . $colS . $col . '" ' ;
                $result = $result . $rowS . $row . '" ' . $endS ;
                if ($default) {
                    $result = $result.$default;
                };
                $result = $tab.$result . $textareaES . $newLine;
                break;
            case CstHTML::H_T_SUBMIT:
                $result = $tab.$buttonS.$label;
                if (isset($baction)) {
                    $result = $result.' formaction='.$baction;
                }
                $result = $result.$endS.$newLine;
                break;
            case CstHTML::H_T_TEXT:
                $result = $inputS;
                $result = $result . $typeS;
                $result = $result . $nameS;
                $valueS ='';
                if ($default) {
                    $valueS = 'value = "' . $default .  '" ';
                };
                $result = $result . $valueS;
                $result = $result . $endS;
                $result = $tab.$result . $newLine;
                break;
            case CstHTML::H_T_RADIO:
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
                    $result = $result. $newLine;
                };
                break;
            case CstHTML::H_T_SELECT:
                $result = $selectS;
                $result = $tab.$result. $nameS . $endS . $newLine ;
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
                    $result = $result.$optionES. $newLine;
                };
                $result = $result . $tab.$selectES. $newLine;
                break;
            case CstHTML::H_T_IMG:
                $result = $imgS.$default.'"'.$altS.$default.'"'.$widthS.$colp.'"'.$heightS.$rowp.'"'.$endS;
                break;
            case CstHTML::H_T_PLAIN:
                $result = $tab.$default.$newLine;
                break;
            default:
                $result = ' Unknown H_TYPE '.$type;
        }
        return $result;
    }
}
