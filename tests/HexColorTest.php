<?php

namespace wwaz\Colorname\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use wwaz\Colorname\Support\HexColor;

class HexColorTest extends TestCase
{
    #[DataProvider('hexExpansionProvider')]
    public function test_expand_normalizes_supported_hex_formats(string $input, string $expected): void
    {
        $this->assertSame($expected, HexColor::expand($input));
    }

    public static function hexExpansionProvider(): array
    {
        return [
            'six digit with hash' => ['#ff0000', 'ff0000'],
            'six digit without hash' => ['00ff00', '00ff00'],
            'short with hash' => ['#0f0', '00ff00'],
            'short without hash' => ['abc', 'aabbcc'],
            'uppercase' => ['#ABCDEF', 'ABCDEF'],
            'surrounding whitespace' => [' #123456 ', '123456'],
        ];
    }

    #[DataProvider('invalidHexProvider')]
    public function test_expand_rejects_invalid_hex_formats(string $input): void
    {
        $this->assertNull(HexColor::expand($input));
    }

    public static function invalidHexProvider(): array
    {
        return [
            'empty' => [''],
            'too short' => ['#12'],
            'too long' => ['#1234567'],
            'invalid character' => ['#12x456'],
            'rgb string' => ['255,0,0'],
        ];
    }

    public function test_to_rgb_converts_hex_to_rgb_components(): void
    {
        $this->assertSame([255, 0, 170], HexColor::toRgb('#f0a'));
    }

    public function test_to_rgb_returns_null_for_invalid_hex(): void
    {
        $this->assertNull(HexColor::toRgb('not-a-color'));
    }
}
