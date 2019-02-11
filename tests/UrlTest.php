<?php

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

    protected function setUp()
    {
        $this->relativeUrl = new Url('/about');
        $this->absoluteUrl = new Url('http://www.example.org/about?search=hello&page=2#bottom');
    }

    public function testGetPath()
    {
        $this->assertEquals('/about', $this->absoluteUrl->getPath());
    }

    public function testReplacePath()
    {
        $this->assertEquals(
            'http://www.example.org/signup?search=hello&page=2#bottom',
            (string) $this->absoluteUrl->replacePath('/signup')
        );
    }


    public function testReplacePathWithEmptyUrl()
    {
        $emptyUrl = new Url('');
        $this->assertEquals('/signup', (string) $emptyUrl->replacePath('/signup'));
    }

    public function testGetQuery()
    {
        $this->assertEquals('search=hello&page=2', $this->absoluteUrl->getQuery());
    }

    public function testGetFragment()
    {
        $this->assertEquals('bottom', $this->absoluteUrl->getFragment());
    }

    public function testGetQueryParameters()
    {
        $expectedParameters = ['search' => 'hello', 'page' => 2];

        $this->assertEquals($expectedParameters, $this->absoluteUrl->getQueryParameters());
    }

    public function testWithoutQueryAndFragment()
    {
        $this->assertEquals('http://www.example.org/about', (string) $this->absoluteUrl->withoutQueryAndFragment());
    }

    public function testAddQueryParameter()
    {
        $this->assertEquals(
            'http://www.example.org/about?search=hello&page=3&order=asc#bottom',
            (string) $this->absoluteUrl->addQueryParameters(['page' => 3, 'order' => 'asc'])
        );
    }

    public function testAddQueryParameterNoMerge()
    {
        $this->assertEquals(
            'http://www.example.org/about?search=hello&page=2&order=asc#bottom',
            (string) $this->absoluteUrl->addQueryParameters(['page' => 3, 'order' => 'asc'], false)
        );
    }
}
