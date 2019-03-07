<?php declare(strict_types=1);

namespace Starlit\Utils;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    private $relativeUrl;

    /**
     * @var Url
     */
    private $absoluteUrl;

    protected function setUp(): void
    {
        $this->relativeUrl = new Url('/about');
        $this->absoluteUrl = new Url('http://www.example.org/about?search=hello&page=2#bottom');
    }

    public function testGetPath(): void
    {
        $this->assertEquals('/about', $this->absoluteUrl->getPath());
    }

    public function testReplacePath(): void
    {
        $this->assertEquals(
            'http://www.example.org/signup?search=hello&page=2#bottom',
            (string) $this->absoluteUrl->replacePath('/signup')
        );
    }


    public function testReplacePathWithEmptyUrl(): void
    {
        $emptyUrl = new Url('');
        $this->assertEquals('/signup', (string) $emptyUrl->replacePath('/signup'));
    }

    public function testGetQuery(): void
    {
        $this->assertEquals('search=hello&page=2', $this->absoluteUrl->getQuery());
    }

    public function testGetFragment(): void
    {
        $this->assertEquals('bottom', $this->absoluteUrl->getFragment());
    }

    public function testGetQueryParameters(): void
    {
        $expectedParameters = ['search' => 'hello', 'page' => 2];

        $this->assertEquals($expectedParameters, $this->absoluteUrl->getQueryParameters());
    }

    public function testWithoutQueryAndFragment(): void
    {
        $this->assertEquals('http://www.example.org/about', (string) $this->absoluteUrl->withoutQueryAndFragment());
    }

    public function testAddQueryParameter(): void
    {
        $this->assertEquals(
            'http://www.example.org/about?search=hello&page=3&order=asc#bottom',
            (string) $this->absoluteUrl->addQueryParameters(['page' => 3, 'order' => 'asc'])
        );
    }

    public function testAddQueryParameterNoMerge(): void
    {
        $this->assertEquals(
            'http://www.example.org/about?search=hello&page=2&order=asc#bottom',
            (string) $this->absoluteUrl->addQueryParameters(['page' => 3, 'order' => 'asc'], false)
        );
    }
}
