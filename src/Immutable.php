<?php

namespace Donphan;

trait Immutable
{
    use Validatable;

    /** @var array */
    private $_value = [];

    private function __construct(array $params)
    {
        $this->_value = $params;
    }

    public static function from(array $params)
    {
        $params = static::validate($params);
        return new static($params);
    }

    public function toArray($deep = true)
    {
        if ($deep === false) {
            return $this->_value;
        }

        $result = [];
        foreach ($this->_value as $prop => $value) {
            if (is_callable([$value, 'toArray'])) {
                $result[$prop] = $value->toArray(true);
                continue;
            }
            $result[$prop] = $value;
        }

        return json_decode(json_encode($result), true);
    }

    public function __get($prop)
    {
        if (!isset($this->_value[$prop])) {
            throw new \InvalidArgumentException(static::class . " has no property called '{$prop}'");
        }
        return $this->_value[$prop];
    }

    public function __isset($prop)
    {
        return isset($this->_value[$prop]);
    }
}
