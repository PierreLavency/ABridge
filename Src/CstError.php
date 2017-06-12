<?php

define('E_ERC001', "ERC001");  //Predefine Attribute
define('E_ERC002', "ERC002");  //Attribute does not exist
define('E_ERC003', "ERC003");  //Attribute already exists
define('E_ERC004', "ERC004");  // is not a type
define('E_ERC005', "ERC005");  // is not a of base type
define('E_ERC006', "ERC006");  // no state handler
define('E_ERC007', "ERC007");  // object does not exist
define('E_ERC008', "ERC008");  // M_REF must have an associated path
define('E_ERC009', "ERC009");  // M_REF object does not exists
define('E_ERC010', "ERC010");  // invalid model name
define('E_ERC011', "ERC011");  // invalid type for object id
define('E_ERC012', "ERC012");  // null request
define('E_ERC013', "ERC013");  // cannot set CREF
define('E_ERC014', "ERC014");  // type requires statehdlr.
define('E_ERC015', "ERC015");  // no possible values for this type
define('E_ERC016', "ERC016");  // invalid value for this code -
define('E_ERC017', "ERC017");  // Bkey requires state handler -
define('E_ERC018', "ERC018");  // Bkey violation -
define('E_ERC019', "ERC019");  // Mdtr violation -
define('E_ERC020', "ERC020");  // Invalid Path - "syntax error"
define('E_ERC021', "ERC021");  // mysqli errors
define('E_ERC022', "ERC022");  // NOT USED
define('E_ERC023', "ERC023");  // Invalid Path NOT USED
define('E_ERC024', "ERC024");  // can'save model has changed
define('E_ERC025', "ERC025");  // No connection open FB
define('E_ERC026', "ERC026");  // not a ref
define('E_ERC027', "ERC027");  // not a cref
define('E_ERC028', "ERC028");  // not a code
define('E_ERC029', "ERC029");  // Ckey must be an array
define('E_ERC030', "ERC030");  // attr in Ckey
define('E_ERC031', "ERC031");  // Ckey violation
define('E_ERC032', "ERC032");  // id not in CREF
define('E_ERC033', "ERC033");  // REF type missmatch
define('E_ERC034', "ERC034");  // protected attribute
define('E_ERC035', "ERC035");  // NOT USED not allowed on create path
define('E_ERC036', "ERC036");  // url not correctly formated
define('E_ERC037', "ERC037");  // not allowed on object path
define('E_ERC038', "ERC038");  // NOT USED not allowed on root path
define('E_ERC039', "ERC039");  // set not allowed on eval attribute
define('E_ERC040', "ERC040");  // eval attr without custom class
define('E_ERC041', "ERC041");  // no param for this type allowed
define('E_ERC042', "ERC042");  // set not allowed on evalP attribute
define('E_ERC043', "ERC043");  // invalid id
define('E_ERC044', "ERC044");  // cannot instanciate abstract class
define('E_ERC045', "ERC045");  // cannot set attr on abstract class
define('E_ERC046', "ERC046");  // NOT USED type change not supported
define('E_ERC047', "ERC047");  // NOT USED not supported at inherited level
define('E_ERC048', "ERC048");  // illegal action on path type
define('E_ERC049', "ERC049");  // illegal action on obj
define('E_ERC050', "ERC050");  // illegal condition in role
define('E_ERC051', "ERC051");  // NOT USED no sessionMg found
define('E_ERC052', "ERC052");  // cannot delete - ref integrity
define('E_ERC053', "ERC053");  // access denied
define('E_ERC054', "ERC054");  // must be a REF attr
define('E_ERC055', "ERC055");  // must be a CREF attr
define('E_ERC056', "ERC056");  // must be a Bkey attr
define('E_ERC057', "ERC057");  // wrong psw
define('E_ERC058', "ERC058");  //  psw1 != psw2
define('E_ERC059', "ERC059");  // usr does not exist
define('E_ERC060', "ERC060");  // role not allowed

define('E_ERC061', "ERC061");  // no custom class defined


class CstError
{
    protected static $disp =
    [
        E_ERC001 => 'Illegal operation on predefined attribute',
        E_ERC002 => 'Attribute does not exist',
        E_ERC003 => 'Attribute already exists',
        E_ERC004 => 'Invalid type',
        E_ERC005 => 'Invalid base type',
        E_ERC006 => 'No state handler',
        E_ERC007 => 'Object does not exist',
        E_ERC008 => 'M_REF must have an associated path',
        E_ERC009 => 'M_REF object does not exists',

        E_ERC010 => 'Invalid model name',
        E_ERC011 => 'Invalid object id',
        E_ERC012 => 'Request is null',
        E_ERC013 => 'Cannot set CREF attribute',
        E_ERC014 => 'Attribute type requires state handler',
        E_ERC015 => 'List of possible values not available for this type of attribute',
        E_ERC016 => 'invalid value for this code',
        E_ERC017 => 'Bkey requires state handler',
        E_ERC018 => 'Attribute with this value already exists',
        E_ERC019 => 'Attribute is mandatory',

        E_ERC020 => 'Invalid path syntaxe',
        E_ERC021 => 'mysqli errors',
        E_ERC022 => 'NOT USED',
        E_ERC023 => 'NOT USED',
        E_ERC024 => 'Model has changed and must be saved before value can be saved',
        E_ERC025 => 'No connection open',
        E_ERC026 => 'Attribute is not a ref attribute',
        E_ERC027 => 'Attribute is not a cref attribute',
        E_ERC028 => 'Attribute is not a code attribute',
        E_ERC029 => 'Ckey must be an array'     ,

        E_ERC030 => 'Attribute is used in Ckey and cannot be deleted',
        E_ERC031 => 'Attributes with this combinations of values already exist',
        E_ERC032 => 'id not in CREF values of this attribute',
        E_ERC033 => 'Object type does not match expected reference type',
        E_ERC034 => 'Attempt to change protected attribute',
        E_ERC035 => 'NOT USED',
        E_ERC036 => 'Url not correctly formated',
        E_ERC037 => 'Not allowed on object path',
        E_ERC038 => 'NOT USED',
        E_ERC039 => 'Cannot set EVAL attribute',

        E_ERC040 => 'Type of attribute requires a custom class',
        E_ERC041 => 'No additionale parameter allowed for this type of attribute',
        E_ERC042 => 'Cannot set EVALP attribute',
        E_ERC043 => 'invalid base id',
        E_ERC044 => 'Abstract class cannot be instanciated',
        E_ERC045 => 'Attempt to set attribute of Abstract class',
        E_ERC046 => 'NOT USED',
        E_ERC047 => 'NOT USED',
        E_ERC048 => 'Illegal action on this type of path',
        E_ERC049 => 'Action not authorized on this path',

        E_ERC050 => 'illegal condition in role',
        E_ERC051 => 'NOT USED',
        E_ERC052 => 'Cannot delete object- referential integrity',
        E_ERC053 => 'Action not authorized on this object',
        E_ERC054 => 'Parameter must be a REF attribute',
        E_ERC055 => 'Parameter must be a CREF attribute',
        E_ERC056 => 'Parameter must be a BKEY attribute',
        E_ERC057 => 'Wrong passsword',
        E_ERC058 => 'New password do not match confirmed new password',
        E_ERC059 => 'User does not exists',

        E_ERC060 => 'Role is not allowed',
        E_ERC061 => 'Init Mod called without custom class',
    
    ];
        
    public static function subst($mes)
    {
        $res = explode(':', $mes);
        if (isset(self::$disp[$res[0]])) {
            $message = self::$disp[$res[0]];
            return $message.':'.$mes;
        }
        return $mes;
    }
}
