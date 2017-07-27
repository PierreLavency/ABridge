<?php
namespace ABridge\ABridge;

class CstError
{
    const E_ERC001 = 'ERC001';  //Predefine Attribute
    const E_ERC002 = 'ERC002';  //Attribute does not exist
    const E_ERC003 = 'ERC003';  //Attribute already exists
    const E_ERC004 = 'ERC004';  // is not a type
    const E_ERC005 = 'ERC005';  // is not a of base type
    const E_ERC006 = 'ERC006';  // no state handler
    const E_ERC007 = 'ERC007';  // object does not exist
    const E_ERC008 = 'ERC008';  // M_REF must have an associated path
    const E_ERC009 = 'ERC009';  // M_REF object does not exists
    const E_ERC010 = 'ERC010';  // invalid model name
    const E_ERC011 = 'ERC011';  // invalid type for object id
    const E_ERC012 = 'ERC012';  // null request
    const E_ERC013 = 'ERC013';  // cannot set CREF
    const E_ERC014 = 'ERC014';  // type requires statehdlr.
    const E_ERC015 = 'ERC015';  // no possible values for this type
    const E_ERC016 = 'ERC016';  // invalid value for this code -
    const E_ERC017 = 'ERC017';  // Bkey requires state handler -
    const E_ERC018 = 'ERC018';  // Bkey violation -
    const E_ERC019 = 'ERC019';  // Mdtr violation -
    const E_ERC020 = 'ERC020';  // Invalid Path - 'syntax error'
    const E_ERC021 = 'ERC021';  // mysqli errors
    const E_ERC022 = 'ERC022';  // NOT USED
    const E_ERC023 = 'ERC023';  // Invalid Path NOT USED
    const E_ERC024 = 'ERC024';  // can'save model has changed
    const E_ERC025 = 'ERC025';  // No connection open FB
    const E_ERC026 = 'ERC026';  // not a ref
    const E_ERC027 = 'ERC027';  // not a cref
    const E_ERC028 = 'ERC028';  // not a code
    const E_ERC029 = 'ERC029';  // Ckey must be an array
    const E_ERC030 = 'ERC030';  // attr in Ckey
    const E_ERC031 = 'ERC031';  // Ckey violation
    const E_ERC032 = 'ERC032';  // id not in CREF
    const E_ERC033 = 'ERC033';  // REF type missmatch
    const E_ERC034 = 'ERC034';  // protected attribute
    const E_ERC035 = 'ERC035';  // NOT USED not allowed on create path
    const E_ERC036 = 'ERC036';  // url not correctly formated
    const E_ERC037 = 'ERC037';  // not allowed on object path
    const E_ERC038 = 'ERC038';  // NOT USED not allowed on root path
    const E_ERC039 = 'ERC039';  // set not allowed on eval attribute
    const E_ERC040 = 'ERC040';  // eval attr without custom class
    const E_ERC041 = 'ERC041';  // no param for this type allowed
    const E_ERC042 = 'ERC042';  // set not allowed on evalP attribute
    const E_ERC043 = 'ERC043';  // invalid id
    const E_ERC044 = 'ERC044';  // cannot instanciate abstract class
    const E_ERC045 = 'ERC045';  // cannot set attr on abstract class
    const E_ERC046 = 'ERC046';  // NOT USED type change not supported
    const E_ERC047 = 'ERC047';  // NOT USED not supported at inherited level
    const E_ERC048 = 'ERC048';  // illegal action on path type
    const E_ERC049 = 'ERC049';  // illegal action on obj
    const E_ERC050 = 'ERC050';  // illegal condition in role
    const E_ERC051 = 'ERC051';  // NOT USED no sessionMg found
    const E_ERC052 = 'ERC052';  // cannot delete - ref integrity
    const E_ERC053 = 'ERC053';  // access denied
    const E_ERC054 = 'ERC054';  // must be a REF attr
    const E_ERC055 = 'ERC055';  // must be a CREF attr
    const E_ERC056 = 'ERC056';  // must be a Bkey attr
    const E_ERC057 = 'ERC057';  // wrong psw
    const E_ERC058 = 'ERC058';  //  psw1 != psw2
    const E_ERC059 = 'ERC059';  // usr does not exist
    const E_ERC060 = 'ERC060';  // role not allowed
    
    const E_ERC061 = 'ERC061';  // no custom class defined
    const E_ERC062 = 'ERC062';  // no custom class defined
    const E_ERC063 = 'ERC063';
    
    protected static $disp =
    [
        self::E_ERC001 => 'Illegal operation on predefined attribute',
        self::E_ERC002 => 'Attribute does not exist',
        self::E_ERC003 => 'Attribute already exists',
        self::E_ERC004 => 'Invalid type',
        self::E_ERC005 => 'Invalid base type',
        self::E_ERC006 => 'No state handler',
        self::E_ERC007 => 'Object does not exist',
        self::E_ERC008 => 'M_REF must have an associated path',
        self::E_ERC009 => 'M_REF object does not exists',

        self::E_ERC010 => 'Invalid model name',
        self::E_ERC011 => 'Invalid object id',
        self::E_ERC012 => 'Request is null',
        self::E_ERC013 => 'Cannot set CREF attribute',
        self::E_ERC014 => 'Attribute type requires state handler',
        self::E_ERC015 => 'List of possible values not available for this type of attribute',
        self::E_ERC016 => 'invalid value for this code',
        self::E_ERC017 => 'Bkey requires state handler',
        self::E_ERC018 => 'Attribute with this value already exists',
        self::E_ERC019 => 'Attribute is mandatory',

        self::E_ERC020 => 'Invalid path syntaxe',
        self::E_ERC021 => 'mysqli errors',
        self::E_ERC022 => 'NOT USED',
        self::E_ERC023 => 'NOT USED',
        self::E_ERC024 => 'Model has changed and must be saved before value can be saved',
        self::E_ERC025 => 'No connection open',
        self::E_ERC026 => 'Attribute is not a ref attribute',
        self::E_ERC027 => 'Attribute is not a cref attribute',
        self::E_ERC028 => 'Attribute is not a code attribute',
        self::E_ERC029 => 'Ckey must be an array'     ,

        self::E_ERC030 => 'Attribute is used in Ckey and cannot be deleted',
        self::E_ERC031 => 'Attributes with this combinations of values already exist',
        self::E_ERC032 => 'id not in CREF values of this attribute',
        self::E_ERC033 => 'Object type does not match expected reference type',
        self::E_ERC034 => 'Attempt to change protected attribute',
        self::E_ERC035 => 'NOT USED',
        self::E_ERC036 => 'Url not correctly formated',
        self::E_ERC037 => 'Not allowed on object path',
        self::E_ERC038 => 'NOT USED',
        self::E_ERC039 => 'Cannot set EVAL attribute',

        self::E_ERC040 => 'Type of attribute requires a custom class',
        self::E_ERC041 => 'No additionale parameter allowed for this type of attribute',
        self::E_ERC042 => 'Cannot set EVALP attribute',
        self::E_ERC043 => 'invalid base id',
        self::E_ERC044 => 'Abstract class cannot be instanciated',
        self::E_ERC045 => 'Attempt to set attribute of Abstract class',
        self::E_ERC046 => 'NOT USED',
        self::E_ERC047 => 'NOT USED',
        self::E_ERC048 => 'Illegal action on this type of path',
        self::E_ERC049 => 'Action not authorized on this path',

        self::E_ERC050 => 'illegal condition in role',
        self::E_ERC051 => 'NOT USED',
        self::E_ERC052 => 'Cannot delete object- referential integrity',
        self::E_ERC053 => 'Action not authorized on this object',
        self::E_ERC054 => 'Parameter must be a REF attribute',
        self::E_ERC055 => 'Parameter must be a CREF attribute',
        self::E_ERC056 => 'Parameter must be a BKEY attribute',
        self::E_ERC057 => 'Wrong passsword',
        self::E_ERC058 => 'New password do not match confirmed new password',
        self::E_ERC059 => 'User does not exists',

        self::E_ERC060 => 'Role is not allowed',
        self::E_ERC061 => 'Init Mod called without custom class',
        self::E_ERC062 => 'Object has been changed',
        self::E_ERC063 => 'Invalid Base type',
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
