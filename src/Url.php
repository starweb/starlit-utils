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

    public function getPath(): ?string
    {
        $urlPath = parse_url($this->url, PHP_URL_PATH);

        return $urlPath === false ? null : $urlPath;
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

        $path = $this->getPath();
        if ($path !== null) {
            $this->url = Str::replaceFirst($path, $newPath, $this->url);
        }

        return $this;
    }

    public function getQuery(): ?string
    {
        $urlQuery = parse_url($this->url, PHP_URL_QUERY);

        return $urlQuery === false ? null : $urlQuery;
    }

    public function getFragment(): ?string
    {
        $urlFragment = parse_url($this->url, PHP_URL_FRAGMENT);

        return $urlFragment === false ? null : $urlFragment;
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
