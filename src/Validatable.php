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
        'object',
        'resource',
        'callable', // functions should be methods, not properties
        Closure::class,
        Exception::class,
        Error::class,
    ];

    public static function validate(array $params)
    {
        if (is_callable('static::beforeTypeCheck')) {
            $params = static::beforeTypeCheck($params);
        }
        static::_execTypeCheck($params);
        if (is_callable('static::afterTypeCheck')) {
            static::afterTypeCheck($params);
        }
        return $params;
    }

    private static function _execTypeCheck(array $params)
    {
        if (!defined('static::REQUIRED')) {
            throw new \LogicException(static::class . ' has no private const REQUIRED.');
        }

        foreach (static::REQUIRED as $prop => $type) {
            if (!isset($params[$prop])) {
                throw new \InvalidArgumentException(static::class . ": params['{$prop}'] is not set.");
            }
            static::_assertTypeOfPropValue($prop, $params[$prop], $type);
        }
        if (!defined('static::OPTIONAL')) {
            return;
        }
        foreach (static::OPTIONAL as $prop => $type) {
            if (!isset($params[$prop])) {
                continue;
            }
            static::_assertTypeOfPropValue($prop, $params[$prop], $type);
        }
    }

    private static function _assertTypeOfPropValue(string $prop, $value, string $type)
    {
        if ($type === static::$_TYPE_MIXED) {
            return;
        }

        if (in_array($type, static::$_DEFAULT_FORBIDDEN_TYPES, true)) {
            throw new \LogicException("using '{$type}' is not allowed (params['{$prop}'])");
        }

        foreach (static::$_DEFAULT_FORBIDDEN_TYPES as $forbidden_type) {
            if (is_subclass_of($type, $forbidden_type)) {
                throw new \LogicException("using '{$type}' (subclass of {$forbidden_type}) is not allowed (params['{$prop}'])");
            }
        }

        if (isset(static::$_DEFAULT_VALIDATORS[$type])) {
            $validator = static::$_DEFAULT_VALIDATORS[$type];
        } elseif (class_exists($type)) {
            $validator = function ($value) use ($type) {
                return $value instanceof $type;
            };
        } else {
            throw new \LogicException("params['{$prop}'] has unknown type: {$type}");
        }

        if (call_user_func($validator, $value) !== true) {
            throw new \TypeError("params['{$prop}'] is not type of {$type}");
        }
    }
}
