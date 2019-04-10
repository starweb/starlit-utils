<?php declare(strict_types=1);

namespace Starlit\Utils;

use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testAnyIn(): void
    {
        $array = [1, 2, 3];
        $this->assertTrue(Arr::anyIn([1, 2, 3], $array));
        $this->assertTrue(Arr::anyIn(1, $array));
    }

    public function testAnyInIsFalse(): void
    {
        $array = [1, 2, 3];
        $this->assertFalse(Arr::anyIn([4, 5, 6], $array));
        $this->assertFalse(Arr::anyIn(6, $array));
    }

    public function testAllIn(): void
    {
        $array = [1, 2, 3];
        $this->assertTrue(Arr::allIn([1, 2, 3], $array));
        $this->assertTrue(Arr::allIn(1, $array));
        $this->assertFalse(Arr::allIn([], $array));
        $this->assertFalse(Arr::allIn([1, 2, 3], []));
    }

    public function testAllInFalse(): void
    {
        $array = [1, 2, 3];
        $this->assertFalse(Arr::allIn([1, 2, 3, 4], $array));
        $this->assertFalse(Arr::allIn([4], $array));
    }

    public function testGetArrayValuesWithPrefix(): void
    {
        $oldArray = ['one', 'two'];
        $newArray = Arr::valuesWithPrefix($oldArray, 'prefix');
        $this->assertEquals($newArray[1], 'prefixtwo');
    }

    public function testAllEmpty(): void
    {
        $this->assertTrue(Arr::allEmpty(['one' => null, 'two' => 0, 'three' => ['subone' => '']]));
        $this->assertTrue(Arr::allEmpty([]));
    }

    public function testArrayAllEmptyNotEmpty(): void
    {
        $this->assertFalse(Arr::allEmpty(['one' => null, 'two' => 1, 'three' => ['subone' => '']]));
        $this->assertFalse(Arr::allEmpty(['one' => null, 'two' => 1, 'three' => ['subone' => 'a']]));
    }

    public function testArrayFilterKeysValues(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(['c' => 3], Arr::filterKeys($array, ['c']));
    }

    public function testArrayFilterKeysCallback(): void
    {
        $array = [1 => 'a', false => 'b', 3 => 'c'];
        $this->assertEquals([false => 'b'], Arr::filterKeys($array, function ($key) {
            return ($key == false);
        }));
    }

    public function testArrayFilterKeys(): void
    {
        $array = [1 => 'a', false => 'b', 3 => 'c'];
        $this->assertEquals([1 => 'a', 3 => 'c'], Arr::filterKeys($array));
    }

    public function testGetObjectArrayMethodValues(): void
    {
        $object1 = $this->getTestObjectForObjectArray(1);
        $object2 = $this->getTestObjectForObjectArray(2);
        $object3 = $this->getTestObjectForObjectArray(3);

        $objectArray = [$object1, $object2, $object3];

        $this->assertEquals([1, 2, 3], Arr::objectsMethodValues($objectArray, 'getValue'));
    }

    public function testGetArrayValuesWithType(): void
    {
        $testArray = ['1', 'a'];
        $resultArray = Arr::valuesWithType($testArray, 'int');
        $this->assertIsInt($resultArray[0]);
        $this->assertIsInt($resultArray[1]);
    }

    public function testGetArrayValuesWithInvalidType(): void
    {
        $testArray = [['foo']];
        $resultArray = Arr::valuesWithType($testArray, 'int');
        $this->assertEmpty($resultArray);
    }

    public function testReplaceExisting(): void
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 100, 'd' => 200]
        );

        $this->assertEquals(['a' => 100, 'b' => 2, 'c' => 3], $result);
    }

    public function testReplaceExistingMultiDimensional(): void
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'm' => ['b' => 2, 'c' => 3]],
            ['m' => ['b' => 200, 'd' => 300]]
        );

        $this->assertEquals(['a' => 1, 'm' => ['b' => 200, 'd' => 300]], $result);
    }

    public function testReplaceExistingMultiDimensionalRecursive(): void
    {
        $result = Arr::replaceExisting(
            ['a' => 1, 'm' => ['b' => 2, 'c' => 3]],
            ['m' => ['b' => 200, 'd' => 300]],
            true
        );

        $this->assertEquals(['a' => 1, 'm' => ['b' => 200, 'c' => 3]], $result);
    }

    public function testSortByArray(): void
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

    protected function getTestObjectForObjectArray(int $value)
    {
        return (new class($value) {
            /**
             * @var int
             */
            private $value;

            public function __construct(int $value)
            {
                $this->value = $value;
            }

            public function getValue(): int
            {
                return $this->value;
            }
        });
    }
}
