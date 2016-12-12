<?php

namespace Starlit\Utils;

class ArrTest extends \PHPUnit_Framework_TestCase
{
    public function testAnyIn()
    {
        $array = [1, 2, 3];
        $this->assertTrue(Arr::anyIn([1, 2, 3], $array));
        $this->assertTrue(Arr::anyIn(1, $array));
    }

    public function testAnyInIsFalse()
    {
        $array = [1, 2, 3];
        $this->assertFalse(Arr::anyIn([4 ,5 ,6], $array));
        $this->assertFalse(Arr::anyIn(6, $array));
    }

    public function testAllIn()
    {
        $array = [1, 2, 3];
        $this->assertTrue(Arr::allIn([1, 2, 3], $array));
        $this->assertTrue(Arr::allIn(1, $array));
        $this->assertFalse(Arr::allIn([], $array));
        $this->assertFalse(Arr::allIn([1, 2, 3], []));
    }

    public function testAllInFalse()
    {
        $array = [1, 2, 3];
        $this->assertFalse(Arr::allIn([1, 2, 3, 4], $array));
        $this->assertFalse(Arr::allIn([4], $array));
    }

    public function testGetArrayValuesWithPrefix()
    {
        $oldArray = ['one', 'two'];
        $newArray = Arr::valuesWithPrefix($oldArray, 'prefix');
        $this->assertEquals($newArray[1], 'prefixtwo');
    }

    public function testAllEmpty()
    {
        $this->assertTrue(Arr::allEmpty(['one' => null, 'two' => 0, 'three' => ['subone' => '']]));
        $this->assertTrue(Arr::allEmpty([]));
    }

    public function testArrayAllEmptyNotEmpty()
    {
        $this->assertFalse(Arr::allEmpty(['one' => null, 'two' => 1, 'three' => ['subone' => '']]));
        $this->assertFalse(Arr::allEmpty(['one' => null, 'two' => 1, 'three' => ['subone' => 'a']]));
    }

    public function testArrayFilterKeysValues()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(['c' => 3], Arr::filterKeys($array, ['c']));
    }

    public function testArrayFilterKeysCallback()
    {
        $array = [1 => 'a', false => 'b', 3 => 'c'];
        $this->assertEquals([false => 'b'], Arr::filterKeys($array, function ($key) {
            return ($key == false);
        }));
    }

    public function testArrayFilterKeys()
    {
        $array = [1 => 'a', false => 'b', 3 => 'c'];
        $this->assertEquals([1 => 'a', 3 => 'c'], Arr::filterKeys($array));
    }

    public function testGetObjectArrayMethodValues()
    {
        $object1 = new TestObjectForObjectArray(1);
        $object2 = new TestObjectForObjectArray(2);
        $object3 = new TestObjectForObjectArray(3);

        $objectArray = [$object1, $object2, $object3];

        $this->assertEquals([1, 2, 3], Arr::objectsMethodValues($objectArray, 'getValue'));
    }

    public function testGetArrayValuesWithType()
    {
        $testArray = ['1', 'a'];
        $resultArray = Arr::valuesWithType($testArray, 'int');
        $this->assertInternalType('int', $resultArray[0]);
        $this->assertInternalType('int', $resultArray[1]);
    }

    public function testGetArrayValuesWithInvalidType()
    {
        $testArray = [['foo']];
        $resultArray = Arr::valuesWithType($testArray, 'int');
        $this->assertEmpty($resultArray);
    }

    public function testReplaceExisting()
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 100, 'd' => 200]
        );

        $this->assertEquals(['a' => 100, 'b' => 2, 'c' => 3], $result);
    }

    public function testReplaceExistingMultiDimensional()
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'm' => ['b' => 2, 'c' => 3]],
            ['m' => ['b' => 200, 'd' => 300]]
        );

        $this->assertEquals(['a' => 1, 'm' => ['b' => 200, 'd' => 300]], $result);
    }

    public function testReplaceExistingMultiDimensionalRecursive()
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'm' => ['b' => 2, 'c' => 3]],
            ['m' => ['b' => 200, 'd' => 300]],
            true
        );

        $this->assertEquals(['a' => 1, 'm' => ['b' => 200, 'c' => 3]], $result);
    }

    public function testSortByArray()
    {
        $arrayIndexes = [
            0 => '4',
            1 => '3',
            2 => '5',
            3 => '1',
        ];
        $arrayObjects = [
            1 => (object) ['id' => 1],
            3 => (object) ['id' => 3],
            4 => (object) ['id' => 4],
            5 => (object) ['id' => 5]
        ];
        $desiredArray = [
            0 => (object) ['id' => 4],
            1 => (object) ['id' => 3],
            2 => (object) ['id' => 5],
            3 => (object) ['id' => 1]
        ];

        // Sort the array
        $sortedArray = Arr::sortByArray($arrayObjects, $arrayIndexes);

        // It should be sorted correctly
        $this->assertEquals($desiredArray, $sortedArray);
    }

    public function testGetValue()
    {
        $testArray = ['key' => 'value'];
        $this->assertEquals('value', Arr::getValue($testArray, 'key'));
    }

    public function testGetValueDefault()
    {
        $testArray = ['key' => 'value'];
        $this->assertEquals('default', Arr::getValue($testArray, 'nonExistingKey', 'default'));
    }
}

class TestObjectForObjectArray
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
