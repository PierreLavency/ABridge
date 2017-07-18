<?php
namespace ABridge\ABridge\Mod;

use DateTime;

class Mtype
{

    const M_INT= "m_int";
    const M_INTP = "m_intp";
    const M_FLOAT = "m_float";
    const M_BOOL = "m_bool";
    const M_STRING = "m_string";
    const M_ID = "m_id";
    const M_REF = "m_ref";
    const M_CREF = "m_cref";
    const M_CODE = "m_code";
    const M_TMSTP = "m_tmstp";
    const M_DATE = "m_date";
    const M_ALNUM = "m_alnum";
    const M_ALPHA = "m_alpha";
    const M_TXT = "m_txt";
    const M_RTXT = "m_rawtxt";
    const M_JSON = "m_jason";
    const M_HTML = "m_html";
    
    
    const M_FORMAT_T = 'Y-m-d H:i:s';
    const M_FORMAT_D = 'Y-m-d';
    
    
    
    public static function isMtype($x)
    {
        $l=[
                self::M_INT,self::M_INTP,self::M_FLOAT,self::M_BOOL,
                self::M_STRING,self::M_TXT,self::M_RTXT,
                self::M_JSON, self::M_HTML,
                self::M_ID,self::M_REF,self::M_CREF,self::M_CODE,
                self::M_TMSTP,self::M_DATE, self::M_ALPHA,self::M_ALNUM,
        ];
        return (in_array($x, $l));
    }
    
    
    public static function baseType($type)
    {
        if ($type==self::M_ID or $type == self::M_REF or $type == self::M_CREF or $type==self::M_CODE) {
            return (self::M_INTP) ;
        }
        return ($type);
    }
    
    public static function isStruct($type)
    {
        if ($type == self::M_TXT or $type == self::M_RTXT or $type == self::M_JSON or $type==self::M_HTML) {
            return false;
        }
        return true;
    }
    
    public static function isRaw($type)
    {
        return (! Mtype::isStruct($type));
    }
    
    public static function convertString($x, $typ)
    {
        $type = self::baseType($typ);
        if ($type== self::M_INTP) {
            $type=self::M_INT;
        }
        if (is_string($x)) {
            if ($x=='') {
                return null;
            }
            switch ($type) {
                case self::M_INT:
                    if (ctype_digit($x)) {
                        $x = (int) $x;
                        return $x;
                    };
                    break;
                case self::M_FLOAT:
                    if (is_numeric($x)) {
                        $x = (float) $x;
                        return $x;
                    };
                    break;
                case self::M_BOOL:
                    if ($x == "false") {
                        $x = false;
                        return $x;
                    };
                    if ($x == "true") {
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
	    $t = date(self::M_FORMAT_T, $timestamp);
	    return $t;
	}
	function convertDate($x) 
	{
	    $d=DateTime::createFromFormat(self::M_FORMAT_D, $x);
	    $t = date(self::M_FORMAT_D, $timestamp);
	    return $t;
	}
	*/
    
    public static function checkType($x, $type)
    {
        if (is_null($x)) {
            return true;
        }
        switch ($type) {
            case self::M_DATE:
                $d=DateTime::createFromFormat(self::M_FORMAT_D, $x);
                return ($d && $d->format(self::M_FORMAT_D)==$x);
            case self::M_TMSTP:
                $d=DateTime::createFromFormat(self::M_FORMAT_T, $x);
                return ($d && $d->format(self::M_FORMAT_T)==$x);
            case self::M_INT:
                return is_int($x);
                break;
            case self::M_FLOAT:
                return is_float($x);
                break;
            case self::M_BOOL:
                return is_bool($x);
                break;
            case self::M_STRING:
                return (is_string($x));
                return false;
                break;
            case self::M_HTML:
            case self::M_RTXT:
                return true;
                break;
            case self::M_JSON:
                $r= json_decode($x);
                return (! is_null($r));
                break;
            case self::M_TXT:
                return ($x===trim(strip_tags($x)));
                break;
            case self::M_ALNUM:
                if (is_string($x)) {
                    return ctype_alnum($x);
                }
                return false;
                break;
            case self::M_ALPHA:
                if (is_string($x)) {
                    return ctype_alpha($x);
                }
                return false;
                break;
            case self::M_INTP:
                if (is_int($x)) {
                    return ($x>0);
                }
                return false;
                break;
            default:
                return false;
        }
    }
    
    public static function checkIdentifier($name)
    {
        $res = preg_match("#^[a-zA-Z_][a-zA-Z0-9_]*$#", $name);
        if ($res == 1) {
            return true;
        }
        return false;
    }
    
    
    public static function convertSqlType($typ)
    {
        $type = self::baseType($typ);
        switch ($type) {
            case self::M_DATE:
                return 'DATE';
            case self::M_TMSTP:
                return 'TIMESTAMP';
            case self::M_INT:
                return 'INT(11)';
            case self::M_FLOAT:
                return 'FLOAT';
            case self::M_BOOL:
                return 'BOOLEAN';
            case self::M_STRING:
                return 'VARCHAR(255)';
            case self::M_RTXT:
            case self::M_HTML:
            case self::M_JSON:
            case self::M_TXT:
                return 'TEXT';
            case self::M_ALNUM:
                return 'VARCHAR(255)';
            case self::M_ALPHA:
                return 'VARCHAR(255)';
            case self::M_INTP:
                return 'INT(11) UNSIGNED';
            default:
                return false;
        }
    }
}
