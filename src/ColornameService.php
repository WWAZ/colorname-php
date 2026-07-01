<?php

namespace wwaz\Colornames;

use wwaz\Colornames\Catalog\Catalog;
use wwaz\Colornames\Catalog\Matcher;

class ColornameService
{
    protected Catalog $catalog;

    public function __construct(string|array|null $catalog = null)
    {
        $this->catalog = self::resolveCatalog($catalog);
    }

    public function setCatalog(string|array $catalog): void
    {
        $this->catalog = self::resolveCatalog($catalog);
    }

    public function fromHex(string $hex, string|array|null $catalog = null): string
    {
        return $this->match($hex, $catalog);
    }

    public function fromRgb(string|array|object $rgb, string|array|null $catalog = null): string
    {
        return $this->match($rgb, $catalog);
    }

    public function match(string|array|object $color, string|array|null $catalog = null): string
    {
        $resolvedCatalog = $catalog !== null
            ? self::resolveCatalog($catalog)
            : $this->catalog;

        return (new Matcher($color, $resolvedCatalog))->match();
    }

    protected static function resolveCatalog(string|array|null $catalog): Catalog
    {
        if (is_array($catalog)) {
            return Catalog::fromArray($catalog);
        }

        return Catalog::resolve($catalog);
    }
}
