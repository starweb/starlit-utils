<?php declare(strict_types=1);

namespace Starlit\Utils;

use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{

    public function testSeparatorToCamelCase(): void
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

    public function testSeparatorToCamelCaseUpper(): void
    {
        $this->assertEquals('HeyYou', Str::separatorToCamel('hey_you', '_', true));
    }

    public function testCamelCaseToSeparator(): void
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

    public function testRandom(): void
    {
        $str = Str::random(10);
        $this->assertEquals(10, strlen($str));
    }

    public function testTruncate(): void
    {
        $str = Str::truncate('abcdefghij', 5);
        $this->assertEquals($str, 'ab...');
    }

    public function testStartsWith(): void
    {
        $this->assertTrue(Str::startsWith('abcdefghij', 'abc'));
    }

    public function testStartsWithIsFalse(): void
    {
        $this->assertFalse(Str::startsWith('abcdefghij', 'ahh'));
    }

    public function testEndsWith(): void
    {
        $this->assertTrue(Str::endsWith('abcdefghij', 'hij'));
    }

    public function testEndsWithIsFalse(): void
    {
        $this->assertFalse(Str::endsWith('abcdefghij', 'ehh'));
    }

    public function testStripLeft(): void
    {
        $str = Str::stripLeft('abcdefghij', 'abc');
        $this->assertEquals($str, 'defghij');
        $this->assertEquals(Str::stripLeft('hij', 'abc'), 'hij');
    }

    public function testStripRight(): void
    {
        $str = Str::stripRight('abcdefghij', 'hij');
        $this->assertEquals($str, 'abcdefg');
        $this->assertEquals(Str::stripRight('abc', 'hij'), 'abc');
    }

    public function testToNumber(): void
    {
        $this->assertSame('0', Str::toNumber('0'));
        $this->assertSame('0', Str::toNumber('000'));
        $this->assertSame('123', Str::toNumber('123'));
        $this->assertSame('123', Str::toNumber('000123'));
        $this->assertSame('123', Str::toNumber('abc123'));
        $this->assertSame('123', Str::toNumber('abc123abc'));
        $this->assertSame('0', Str::toNumber('-123'));
    }

    public function testToNumberAllowDecimal(): void
    {
        $this->assertSame('123.45', Str::toNumber('123.4500', true));
        $this->assertSame('123.45', Str::toNumber('123.45', true));
        $this->assertSame('123', Str::toNumber('123.0', true));
        $this->assertSame('123', Str::toNumber('123', true));
        $this->assertSame('1234567890.5', Str::toNumber('[)c12345q67n890!!.5', true));
    }

    public function testToNumberAllowNegative(): void
    {
        $this->assertSame('0', Str::toNumber('-0', false, true));
        $this->assertSame('-123', Str::toNumber('-123', false, true));
        $this->assertSame('-123', Str::toNumber('-123.45', false, true));
        $this->assertSame('123', Str::toNumber('123', false, true));
        $this->assertSame('123123', Str::toNumber('123-123', false, true));
    }

    public function testIncrementSeparated(): void
    {
        $this->assertEquals('a-name-2', Str::incrementSeparated('a-name'));
        $this->assertEquals('a-name-3', Str::incrementSeparated('a-name-2'));
        $this->assertEquals('a-name-12', Str::incrementSeparated('a-name-11'));
    }

    public function testUppercaseFirst(): void
    {
        $this->assertEquals('Aname', Str::uppercaseFirst('aname'));
        $this->assertEquals('Aname', Str::uppercaseFirst('Aname'));
        $this->assertEquals('Öname', Str::uppercaseFirst('öname'));
    }

    public function testReplaceFirst(): void
    {
        $this->assertEquals('HEY two tree one', Str::replaceFirst('one', 'HEY', 'one two tree one'));
        $this->assertEquals('HEYone two tree', Str::replaceFirst('one', 'HEY', 'oneone two tree'));
        $this->assertEquals('one two tree', Str::replaceFirst('nine', 'HEY', 'one two tree'));
    }
}
