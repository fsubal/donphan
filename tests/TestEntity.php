<?php

namespace Donphan;

final class TestEntity {
    use Immutable;

    const REQUIRED = [
        'int'     => 'int',
        'float'   => 'float',
        'numeric' => 'numeric',
        'string'  => 'string',
        'boolean' => 'boolean',
        'array'   => 'array',
        'date'    => '\DateTimeImmutable',
    ];

    const OPTIONAL = [
        '_int'     => 'int',
        '_float'   => 'float',
        '_numeric' => 'numeric',
        '_string'  => 'string',
        '_boolean' => 'boolean',
        '_array'   => 'array',
        '_date'    => '\DateTimeImmutable',
    ];

    const DEFAULT = [
        'array' => [1, 2, 3, 4, 5]
    ];

    private static function beforeTypeCheck (array $params)
    {
        return array_merge(self::DEFAULT, $params);
    }

    private static function afterTypeCheck (array $params)
    {
        if (strlen($params['string']) == 0) {
            throw new \InvalidArgumentException('params[string] must not be empty');
        }
    }
};
