<?php

namespace Donphan;

/**
 * Utility trait that allows to validate arrays
 *
 * class using Validatable...
 *
 * - MUST have `const REQUIRED`
 * - can  have `const OPTIONAL`
 */
trait Validatable
{
    private static $_TYPE_MIXED = 'mixed';

    private static $_DEFAULT_VALIDATORS = [
        'int' => 'is_int',
        'float' => 'is_float',
        'numeric' => 'is_numeric',
        'string' => 'is_string',
        'boolean' => 'is_bool',
        'array' => 'is_array',
    ];

    private static $_DEFAULT_FORBIDDEN_TYPES = [
        'null',
        'resource',
        'callable', // functions should be methods, not properties
        Closure::class,
        Exception::class,
        Error::class,
    ];

    public static function validate(array $params)
    {
        if (is_callable('self::beforeTypeCheck')) {
            $params = self::beforeTypeCheck($params);
        }
        self::_execTypeCheck($params);
        if (is_callable('self::afterTypeCheck')) {
            self::afterTypeCheck($params);
        }
        return $params;
    }

    private static function _execTypeCheck(array $params)
    {
        if (!defined('self::REQUIRED')) {
            throw new LogicException(__CLASS__ . ' has no private const REQUIRED.');
        }

        foreach (self::REQUIRED as $prop => $type) {
            if (!isset($params[$prop])) {
                throw new InvalidArgumentException(__CLASS__ . ": params['{$prop}'] is not set.");
            }
            self::_assertTypeOfPropValue($prop, $params[$prop], $type);
        }
        if (!defined('self::OPTIONAL')) {
            return;
        }
        foreach (self::OPTIONAL as $prop => $type) {
            if (!isset($params[$prop])) {
                continue;
            }
            self::_assertTypeOfPropValue($prop, $params[$prop], $type);
        }
    }

    private static function _assertTypeOfPropValue(string $prop, $value, string $type)
    {
        if ($type === self::$_TYPE_MIXED) {
            return;
        }

        if (in_array($type, self::$_DEFAULT_FORBIDDEN_TYPES, true)) {
            throw new LogicException("using '{$type}' is not allowed (params['{$prop}'])");
        }

        foreach (self::$_DEFAULT_FORBIDDEN_TYPES as $forbidden_type) {
            if (is_subclass_of($type, $forbidden_type)) {
                throw new LogicException("using '{$type}' (subclass of {$forbidden_type}) is not allowed (params['{$prop}'])");
            }
        }

        if (isset(self::$_DEFAULT_VALIDATORS[$type])) {
            $validator = self::$_DEFAULT_VALIDATORS[$type];
        } elseif (class_exists($type)) {
            $validator = function ($value) use ($type) {
                return $value instanceof $type;
            };
        } else {
            throw new LogicException("params['{$prop}'] has unknown type: {$type}");
        }

        if (call_user_func($validator, $value) !== true) {
            throw new TypeError("params['{$prop}'] is not type of {$type}");
        }
    }
}
