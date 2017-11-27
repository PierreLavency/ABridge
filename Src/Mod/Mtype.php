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
    
    protected static $typeList = [
            self::M_INT,self::M_INTP,self::M_FLOAT,self::M_BOOL,
            self::M_STRING,self::M_TXT,self::M_RTXT,
            self::M_JSON, self::M_HTML,
            self::M_ID,self::M_REF,self::M_CREF,self::M_CODE,
            self::M_TMSTP,self::M_DATE, self::M_ALPHA,self::M_ALNUM,
    ];
    
    public static function isMtype($type)
    {
        return (in_array($type, self::$typeList));
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
    
    public static function convertString($val, $type)
    {
        if (is_string($val)) {
            if ($val=='') {
                return null;
            }
            switch ($type) {
                case self::M_ID:
                case self::M_CODE:
                case self::M_REF:
                case self::M_CREF:
                case self::M_INTP:
                case self::M_INT:
                    if (ctype_digit($val)) {
                        $val = (int) $val;
                        return $val;
                    };
                    break;
                case self::M_FLOAT:
                    if (is_numeric($val)) {
                        $val = (float) $val;
                        return $val;
                    };
                    break;
                case self::M_BOOL:
                    if ($val == "false") {
                        $val = false;
                        return $val;
                    };
                    if ($val == "true") {
                        $val = true;
                        return $val;
                    };
                    break;
                default:
                    return $val;
            }
        };
        return $val;
    }
        
    public static function checkType($val, $type)
    {
        if (is_null($val)) {
            return true;
        }
        switch ($type) {
            case self::M_DATE:
                $dat=DateTime::createFromFormat(self::M_FORMAT_D, $val);
                return ($dat && $dat->format(self::M_FORMAT_D)==$val);
            case self::M_TMSTP:
                $dat=DateTime::createFromFormat(self::M_FORMAT_T, $val);
                return ($dat && $dat->format(self::M_FORMAT_T)==$val);
            case self::M_INT:
                return is_int($val);
                break;
            case self::M_FLOAT:
                return is_float($val);
                break;
            case self::M_BOOL:
                return is_bool($val);
                break;
            case self::M_STRING:
                return (is_string($val));
                return false;
                break;
            case self::M_HTML:
            case self::M_RTXT:
                return true;
                break;
            case self::M_JSON:
                $res= json_decode($val);
                return (! is_null($res));
                break;
            case self::M_TXT:
                return ($val===trim(strip_tags($val)));
                break;
            case self::M_ALNUM:
                if (is_string($val)) {
                    return ctype_alnum($val);
                }
                return false;
                break;
            case self::M_ALPHA:
                if (is_string($val)) {
                    return ctype_alpha($val);
                }
                return false;
                break;
            case self::M_ID:
            case self::M_CODE:
            case self::M_REF:
            case self::M_CREF:
            case self::M_INTP:
                if (is_int($val)) {
                    return ($val>0);
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
        $conv = [
                self::M_DATE=>'DATE',
                self::M_TMSTP=>'TIMESTAMP',
                self::M_INT=>'INT(11)',
                self::M_FLOAT=>'FLOAT',
                self::M_BOOL=>'BOOLEAN',
                self::M_STRING=>'VARCHAR(255)',
                self::M_TXT=>'TEXT',
                self::M_RTXT=>'TEXT',
                self::M_HTML=>'TEXT',
                self::M_JSON=>'TEXT',
                self::M_ALNUM=>'VARCHAR(255)',
                self::M_ALPHA=>'VARCHAR(255)',
                self::M_INTP=>'INT(11) UNSIGNED',
        ];
        $type = self::baseType($typ);
        return $conv[$type];
    }
}
