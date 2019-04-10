<?php declare(strict_types=1);
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils;

class Url
{
    /**
     * @var string
     */
    protected $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getPath(): string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    /**
     * @param string $newPath
     * @return string|self
     */
    public function replacePath(string $newPath)
    {
        if (!$this->url) {
            return $newPath;
        }

        $this->url = Str::replaceFirst($this->getPath(), $newPath, $this->url);

        return $this;
    }

    public function getQuery(): ?string
    {
        return parse_url($this->url, PHP_URL_QUERY);
    }

    public function getFragment(): ?string
    {
        return parse_url($this->url, PHP_URL_FRAGMENT);
    }

    public function getQueryParameters(): array
    {
        $parameters = [];
        if (($query = $this->getQuery()) !== null) {
            parse_str($query, $parameters);
        }

        return $parameters;
    }

    public function withoutQueryAndFragment(): self
    {
        if (($pos = strpos($this->url, '?')) !== false) {
            $this->url = substr($this->url, 0, $pos);
        }

        return $this;
    }

    /**
     * @param array  $newParameters
     * @param bool   $merge If parameters should be merged (overwrite existing) or added (not overwriting existing)
     * @param string $argSeparator
     * @return self
     */
    public function addQueryParameters(array $newParameters = [], bool $merge = true, string $argSeparator = '&'): self
    {
        $currentParameters = $this->getQueryParameters();
        if ($merge) {
            $parameters = array_merge($currentParameters, $newParameters);
        } else {
            $parameters = $currentParameters + $newParameters;
        }

        $fragment = $this->getFragment();
        $this->withoutQueryAndFragment();

        if ($parameters) {
            $this->url .= '?' . http_build_query($parameters, '', $argSeparator);
        }

        if ($fragment) {
            $this->url .= '#' . urlencode($fragment);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
