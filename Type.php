<?php

    require_once("TypeConstant.php");

    
    function isMtype ($x) 
    {
        $l=[
        M_INT,M_INTP,M_FLOAT,M_BOOL,M_STRING,
        M_ID,M_REF,M_CREF,M_CODE,M_TMSTP,M_DATE, M_ALPHA,M_ALNUM, ];
        return (in_array($x, $l));
    }
    
    
    function baseType($type) 
    {
        if ($type==M_ID or $type == M_REF or $type == M_CREF or $type==M_CODE) {
            return (M_INTP) ;
        }
        return ($type);
    }
    
    
    function convertString($x,$typ) 
    {
        $type = baseType($typ);
        if ($type== M_INTP) {
            $type=M_INT;
        }
        if (is_string($x)) {
            if ($x=='') {
                return NULL;
            }
            switch($type) {
                case M_INT:
                    if (ctype_digit($x)) {
                        $x = (int) $x; 
                        return $x;
                    };
                    break; 
                case M_FLOAT:
                    if (is_numeric($x)) {
                        $x = (float) $x;
                        return $x;
                    };
                    break;
                case M_BOOL:
                    if ($x == "false" ) {
                        $x = false;
                        return $x;
                    };
                    if ($x == "true"  ) {
                        $x = true;
                        return $x;
                    };
                    break;
                default: 
                    return $x;
            }
        };
        return $x;
    }

    /* not used 
    function convertTime($x) 
    {
        if (($timestamp=strtotime($x))==false) {
            return false;
        }
        $t = date(M_FORMAT_T, $timestamp);
        return $t;
    }
    function convertDate($x) 
    {
        $d=DateTime::createFromFormat(M_FORMAT_D, $x);
        $t = date(M_FORMAT_D, $timestamp);
        return $t;
    }
    */    

    function checkType ($x,$type) 
    {
        if (is_null($x)) {
            return true;
        }
        switch($type) {
            case M_DATE:
                $d=DateTime::createFromFormat(M_FORMAT_D, $x);
                return ($d && $d->format(M_FORMAT_D)==$x);
            case M_TMSTP:
                $d=DateTime::createFromFormat(M_FORMAT_T, $x);
                return ($d && $d->format(M_FORMAT_T)==$x);
            case M_INT:
                return is_int($x);
                break; 
            case M_FLOAT:
                return is_float($x);
                break;
            case M_BOOL:
                return is_bool($x);
                break; 
            case M_STRING:
                return is_string($x);
                break; 
            case M_ALNUM:
                if (is_string($x)) {
                    return ctype_alnum($x);
                }
                return false;
                break;
            case M_ALPHA:
                if (is_string($x)) {
                    return ctype_alpha($x);
                }
                return false;
                break;
            case M_INTP:
                if (is_int($x)) {
                    return ($x>0);
                }
                return false;
                break;
            default:
                return false;
        }
    }

    function convertSqlType($typ) 
    {
        $type = baseType($typ);
        switch($type) {
            case M_DATE:
                return 'DATE';
            case M_TMSTP:
                return 'TIMESTAMP';
            case M_INT:
                return 'INT(11)';
            case M_FLOAT:
                return 'FLOAT';
            case M_BOOL:
                return 'BOOLEAN';
            case M_STRING:
                return 'VARCHAR(255)';
            case M_ALNUM:
                return 'VARCHAR(255)';
            case M_ALPHA:
                return 'VARCHAR(255)';
            case M_INTP:
                return 'INT(11) UNSIGNED';
            default:
                return false;
        }
    }
    
