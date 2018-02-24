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
        $params = self::validate($params);
        return new self($params);
    }

    public function toArray($deep = true)
    {
        if ($deep === false) {
            return $this->_value;
        }
        return json_decode(json_encode($this->_value), true);
    }

    public function __get($prop)
    {
        if (!isset($this->_value[$prop])) {
            throw new \InvalidArgumentException(__CLASS__ . " has no property called '{$prop}'");
        }
        return $this->_value[$prop];
    }

    public function __isset($prop)
    {
        return isset($this->_value[$prop]);
    }
}
