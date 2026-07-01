<?php

namespace wwaz\Colorname\Tests;

use PHPUnit\Framework\TestCase;
use wwaz\Colorname\ColornameService;

class ColornameServiceTest extends TestCase
{
    protected string $storagePath;

    protected function setUp(): void
    {
        $this->storagePath = dirname(__DIR__) . '/storage';
    }

    public function test_from_hex_uses_default_catalog(): void
    {
        $service = new ColornameService();

        $this->assertSame('Red', $service->fromHex('f00'));
    }

    public function test_set_catalog_changes_only_instance(): void
    {
        $serviceA = new ColornameService();
        $serviceB = new ColornameService();

        $serviceA->setCatalog('colornames-en.json');

        $this->assertSame('Red', $serviceA->fromHex('f00'));
        $this->assertSame('Red', $serviceB->fromHex('f00'));
    }

    public function test_from_hex_catalog_override_affects_only_single_call(): void
    {
        $service = new ColornameService('colornames-en.json');

        $this->assertSame('Custom Red', $service->fromHex('f00', [
            ['name' => 'Custom Red', 'hex' => '#f00'],
        ]));
        $this->assertSame('Red', $service->fromHex('f00'));
    }

    public function test_constructor_accepts_bundled_filename(): void
    {
        $service = new ColornameService('colornames-de.json');

        $this->assertSame('Rot', $service->fromHex('f00'));
    }

    public function test_constructor_accepts_absolute_path(): void
    {
        $path = $this->storagePath . '/colornames-en.json';
        $service = new ColornameService($path);

        $this->assertSame('Red', $service->fromHex('f00'));
    }

    public function test_set_catalog_accepts_absolute_path(): void
    {
        $service = new ColornameService();
        $path = $this->storagePath . '/colornames-en.json';

        $service->setCatalog($path);

        $this->assertSame('Red', $service->fromHex('f00'));
    }

    public function test_constructor_accepts_array_catalog(): void
    {
        $catalog = [
            ['name' => 'Custom Red', 'hex' => '#f00'],
        ];

        $service = new ColornameService($catalog);

        $this->assertSame('Custom Red', $service->fromHex('f00'));
    }

    public function test_from_rgb_accepts_array(): void
    {
        $service = new ColornameService([
            ['name' => 'Array Red', 'hex' => '#f00'],
        ]);

        $this->assertSame('Array Red', $service->fromRgb([255, 0, 0]));
    }

    public function test_from_rgb_accepts_object_with_to_array(): void
    {
        $rgb = new class {
            public function toArray(): array
            {
                return [255, 0, 0];
            }
        };

        $service = new ColornameService([
            ['name' => 'Object Red', 'hex' => '#f00'],
        ]);

        $this->assertSame('Object Red', $service->fromRgb($rgb));
    }
}
