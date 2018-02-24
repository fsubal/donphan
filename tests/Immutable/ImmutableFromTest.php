<?php

namespace Donphan;

final class ImmutableFromTest extends \PHPUnit\Framework\TestCase
{
    public function test_from_expectedly_works_with_required()
    {
        $date = new \DateTimeImmutable();
        $immutable = TestEntity::from([
            'int' => 1,
            'float' => 1.0,
            'numeric' => '1000',
            'string' => 'hello!',
            'boolean' => true,
            'array' => [],
            'date' => $date,
        ]);

        $this->assertSame($immutable->int, 1);
        $this->assertSame($immutable->float, 1.0);
        $this->assertSame($immutable->numeric, '1000');
        $this->assertSame($immutable->string, 'hello!');
        $this->assertSame($immutable->boolean, true);
        $this->assertSame($immutable->array, []);
        $this->assertSame($immutable->date, $date);
    }

    public function test_from_expectedly_works_with_optional()
    {
        $date = new \DateTimeImmutable();
        $immutable = TestEntity::from([
            'int' => 1,
            'float' => 1.0,
            'numeric' => '1000',
            'string' => 'hello!',
            'boolean' => true,
            'array' => [],
            'date' => $date,
            '_int' => 1,
            '_float' => 1.0,
            '_numeric' => '1000',
            '_string' => 'hello!',
            '_boolean' => true,
            '_array' => [],
            '_date' => $date,
        ]);

        $this->assertSame($immutable->_int, 1);
        $this->assertSame($immutable->_float, 1.0);
        $this->assertSame($immutable->_numeric, '1000');
        $this->assertSame($immutable->_string, 'hello!');
        $this->assertSame($immutable->_boolean, true);
        $this->assertSame($immutable->_array, []);
        $this->assertSame($immutable->_date, $date);
    }

    public function test_beforeTypeCheck_expectedly_works()
    {
        $date = new \DateTimeImmutable();
        $immutable = TestEntity::from([
            'int' => 1,
            'float' => 1.0,
            'numeric' => '1000',
            'string' => 'hello!',
            'boolean' => true,
            'date' => $date,
        ]);

        $this->assertSame($immutable->array, TestEntity::DEFAULTS['array']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage params[string] must not be empty
     */
    public function test_afterTypeCheck_expectedly_works()
    {
        $date = new \DateTimeImmutable();
        $immutable = TestEntity::from([
            'int' => 1,
            'float' => 1.0,
            'numeric' => '1000',
            'string' => '',
            'boolean' => true,
            'array' => [],
            'date' => $date,
        ]);
    }
}
