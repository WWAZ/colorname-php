<?php

namespace wwaz\Colorname\Tests;

use PHPUnit\Framework\TestCase;
use wwaz\Colorname\Catalog\Catalog;
use wwaz\Colorname\Catalog\Matcher;

class MatcherTest extends TestCase
{
    public function test_match_exact_rgb_from_string(): void
    {
        $matcher = new Matcher('255,0,0');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_nearest_color(): void
    {
        $matcher = new Matcher('250,5,5');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_from_object_with_to_array(): void
    {
        $rgb = new class {
            public function toArray(): array
            {
                return [0, 0, 255];
            }
        };

        $matcher = new Matcher($rgb);

        $this->assertSame('blue', $matcher->match());
    }

    public function test_match_from_hex_string(): void
    {
        $matcher = new Matcher('#ff0000');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_from_hex_string_without_hash(): void
    {
        $matcher = new Matcher('ff0000');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_from_short_hex_string(): void
    {
        $matcher = new Matcher('f00');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_from_short_hex_string_with_hash(): void
    {
        $matcher = new Matcher('#f00');

        $this->assertSame('red', $matcher->match());
    }

    public function test_match_with_catalog(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Air Force Blue (Raf)',
                'hex' => '#5d8aa8',
            ],
        ]);

        $matcher = new Matcher('93,138,168', $catalog);

        $this->assertSame('Air Force Blue (Raf)', $matcher->match());
    }

    public function test_catalog_from_default_is_not_empty(): void
    {
        $catalog = Catalog::fromDefault();

        $this->assertNotEmpty($catalog->toArray());
    }

    public function test_catalog_normalizes_indexed_hex_array(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Alice Blue',
                'hex' => '#f0f8ff',
            ],
            [
                'name' => 'Air Force Blue',
                'hex' => '#5d8aa8',
            ],
        ]);

        $entries = $catalog->toArray();

        $this->assertCount(2, $entries);
        $this->assertSame('Alice Blue', $entries[0]['name']);
        $this->assertSame([240, 248, 255], $entries[0]['rgb']);
        $this->assertSame('Air Force Blue', $entries[1]['name']);
        $this->assertSame([93, 138, 168], $entries[1]['rgb']);
    }

    public function test_catalog_supports_legacy_rgb_fallback(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Legacy Red',
                'rgb' => [255, 0, 0],
            ],
        ]);

        $entries = $catalog->toArray();

        $this->assertCount(1, $entries);
        $this->assertSame('Legacy Red', $entries[0]['name']);
        $this->assertSame([255, 0, 0], $entries[0]['rgb']);
    }

    public function test_catalog_normalizes_short_hex_entries(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Short Red',
                'hex' => '#f00',
            ],
        ]);

        $entries = $catalog->toArray();

        $this->assertCount(1, $entries);
        $this->assertSame('Short Red', $entries[0]['name']);
        $this->assertSame([255, 0, 0], $entries[0]['rgb']);
    }

    public function test_catalog_preserves_duplicate_names(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Air Force Blue',
                'hex' => '#5d8aa8',
            ],
            [
                'name' => 'Air Force Blue',
                'hex' => '#00308f',
            ],
        ]);

        $entries = $catalog->toArray();

        $this->assertCount(2, $entries);
        $this->assertSame('Air Force Blue', $entries[0]['name']);
        $this->assertSame('Air Force Blue', $entries[1]['name']);
        $this->assertNotSame($entries[0]['rgb'], $entries[1]['rgb']);
    }

    public function test_match_with_duplicate_names_picks_nearest(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Air Force Blue',
                'hex' => '#5d8aa8',
            ],
            [
                'name' => 'Air Force Blue',
                'hex' => '#00308f',
            ],
        ]);

        $matcher = new Matcher('93,138,168', $catalog);

        $this->assertSame('Air Force Blue', $matcher->match());
    }

    public function test_catalog_from_default_matches_known_color(): void
    {
        $catalog = Catalog::fromDefault();
        $matcher = new Matcher('#f0f8ff', $catalog);

        $this->assertSame('Alice Blue', $matcher->match());
    }
}
