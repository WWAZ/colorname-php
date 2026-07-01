<?php

namespace wwaz\Colorname\Tests;

use PHPUnit\Framework\TestCase;
use wwaz\Colorname\Catalog\Catalog;

class CatalogTest extends TestCase
{
    public function test_constructor_accepts_json_string(): void
    {
        $catalog = new Catalog(json_encode([
            [
                'name' => 'Json Red',
                'hex' => '#f00',
            ],
        ], JSON_THROW_ON_ERROR));

        $this->assertSame([
            [
                'name' => 'Json Red',
                'rgb' => [255, 0, 0],
            ],
        ], $catalog->toArray());
    }

    public function test_normalize_skips_entries_without_usable_color_data(): void
    {
        $catalog = Catalog::fromArray([
            [
                'hex' => '#f00',
            ],
            [
                'name' => 'Invalid Hex',
                'hex' => '#ggg',
            ],
            [
                'name' => 'Incomplete RGB',
                'rgb' => [255, 0],
            ],
            [
                'name' => 'Valid Blue',
                'rgb' => [0, 0, 255],
            ],
        ]);

        $this->assertSame([
            [
                'name' => 'Valid Blue',
                'rgb' => [0, 0, 255],
            ],
        ], $catalog->toArray());
    }

    public function test_hex_value_takes_precedence_over_rgb_value(): void
    {
        $catalog = Catalog::fromArray([
            [
                'name' => 'Prefers Hex',
                'hex' => '#0f0',
                'rgb' => [255, 0, 0],
            ],
        ]);

        $this->assertSame([0, 255, 0], $catalog->toArray()[0]['rgb']);
    }

    public function test_from_file_throws_when_file_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Color names file not found:');

        Catalog::fromFile(sys_get_temp_dir() . '/missing-colornames-catalog.json');
    }

    public function test_from_file_throws_when_json_is_invalid(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'colornames-invalid-json-');

        if ($path === false) {
            $this->fail('Could not create temporary file.');
        }

        file_put_contents($path, '{invalid-json');

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('Invalid color names JSON:');

            Catalog::fromFile($path);
        } finally {
            unlink($path);
        }
    }
}
