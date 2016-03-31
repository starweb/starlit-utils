<?php

namespace Starlit\Utils;

class StrTest extends \PHPUnit_Framework_TestCase
{

    public function testSeparatorToCamelCase()
    {
        $this->assertEquals('heyYou', Str::separatorToCamel('hey_you'));
        $this->assertEquals('heyYou', Str::separatorToCamel('hey-you', '-'));
        $this->assertEquals('heyyou', Str::separatorToCamel('heyYou'));
        $this->assertEquals('test1', Str::separatorToCamel('test1'));
        $this->assertEquals('1Test', Str::separatorToCamel('1_test'));
        $this->assertEquals('1test', Str::separatorToCamel('1test'));
        $this->assertEquals('test4You', Str::separatorToCamel('test4_you'));
        $this->assertEquals('test4you', Str::separatorToCamel('test4you'));
        $this->assertEquals('test', Str::separatorToCamel('TEST'));
        $this->assertEquals('test', Str::separatorToCamel('test'));
        $this->assertEquals('tEST', Str::separatorToCamel('t-e-s-t', '-'));
        $this->assertEquals('testOMatic', Str::separatorToCamel('test-o-matic', '-'));
    }

    public function testSeparatorToCamelCaseUpper()
    {
        $this->assertEquals('HeyYou', Str::separatorToCamel('hey_you', '_', true));
    }

    public function testCamelCaseToSeparator()
    {
        $this->assertEquals('hey_you', Str::camelToSeparator('heyYou'));
        $this->assertEquals('hey_you', Str::camelToSeparator('HeyYou'));
        $this->assertEquals('hey-you', Str::camelToSeparator('heyYou', '-'));
        $this->assertEquals('test1', Str::camelToSeparator('test1'));
        $this->assertEquals('1_test', Str::camelToSeparator('1Test'));
        $this->assertEquals('1test', Str::camelToSeparator('1test'));
        $this->assertEquals('test4_you', Str::camelToSeparator('test4You'));
        $this->assertEquals('test4you', Str::camelToSeparator('test4you'));
        $this->assertEquals('t-e-s-t', Str::camelToSeparator('tEST', '-'));
        $this->assertEquals('test', Str::camelToSeparator('test'));
        $this->assertEquals('test-o-matic', Str::camelToSeparator('testOMatic', '-'));
    }

    public function testRandom()
    {
        $str = Str::random(10);
        $this->assertEquals(10, strlen($str));
    }

    public function testTruncate()
    {
        $str = Str::truncate('abcdefghij', 5);
        $this->assertEquals($str, 'ab...');
    }

    public function testStartsWith()
    {
        $this->assertTrue(Str::startsWith('abcdefghij', 'abc'));
    }

    public function testStartsWithIsFalse()
    {
        $this->assertFalse(Str::startsWith('abcdefghij', 'ahh'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(Str::endsWith('abcdefghij', 'hij'));
    }

    public function testEndsWithIsFalse()
    {
        $this->assertFalse(Str::endsWith('abcdefghij', 'ehh'));
    }

    public function testStripLeft()
    {
        $str = Str::stripLeft('abcdefghij', 'abc');
        $this->assertEquals($str, 'defghij');
        $this->assertEquals(Str::stripLeft('hij', 'abc'), 'hij');
    }

    public function testStripRight()
    {
        $str = Str::stripRight('abcdefghij', 'hij');
        $this->assertEquals($str, 'abcdefg');
        $this->assertEquals(Str::stripRight('abc', 'hij'), 'abc');
    }

    public function testToNumber()
    {
        $this->assertEquals('123', Str::toNumber('abc123'));
        $this->assertEquals('123.45', Str::toNumber('123.45'));
        $this->assertEquals('-1234567890.5', Str::toNumber('[)c-12345q67n890!!,5'));
    }

    public function testIncrementSeparated()
    {
        $this->assertEquals('a-name-2', Str::incrementSeparated('a-name'));
        $this->assertEquals('a-name-3', Str::incrementSeparated('a-name-2'));
        $this->assertEquals('a-name-12', Str::incrementSeparated('a-name-11'));
    }

    public function testUppercaseFirst()
    {
        $this->assertEquals('Aname', Str::uppercaseFirst('aname'));
        $this->assertEquals('Aname', Str::uppercaseFirst('Aname'));
        $this->assertEquals('Öname', Str::uppercaseFirst('öname'));
    }

    public function testToFloat()
    {
        $this->assertEquals(123.45, Str::toFloat('123.45'));
        $this->assertEquals(123.45, Str::toFloat('abc123.45'));
        $this->assertEquals(123.45, Str::toFloat('123,45'));
        $this->assertEquals(0, Str::toFloat('-123,45'));
    }

    public function testToFloatAllowNegative()
    {
        $this->assertEquals(123.45, Str::toFloat('123.45', true));
        $this->assertEquals(-123.45, Str::toFloat('-123.45', true));
    }

    public function testReplaceFirst()
    {
        $this->assertEquals('HEY two tree one', Str::replaceFirst('one', 'HEY', 'one two tree one'));
        $this->assertEquals('HEYone two tree', Str::replaceFirst('one', 'HEY', 'oneone two tree'));
        $this->assertEquals('one two tree', Str::replaceFirst('nine', 'HEY', 'one two tree'));
    }
}
