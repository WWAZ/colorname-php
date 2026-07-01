<?php

namespace wwaz\Colornames\Catalog;

use wwaz\Colornames\Support\HexColor;

class Matcher
{
    protected array $rgb;

    protected array $catalog;

    public function __construct(
        string|array|object $rgb,
        ?Catalog $catalog = null,
    ) {
        $this->catalog = $catalog?->toArray() ?? self::fallbackCatalog();
        $this->rgb = self::parseRgb($rgb);
    }

    public function match(): string
    {
        $nearestName = '';
        $nearestDistance = INF;

        foreach ($this->catalog as $entry) {
            $distance = self::squaredDistance($entry['rgb'], $this->rgb);

            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestName = $entry['name'];
            }
        }

        return $nearestName;
    }

    protected static function parseRgb(string|array|object $rgb): array
    {
        if (is_array($rgb) && count($rgb) >= 3) {
            return [
                (int) $rgb[0],
                (int) $rgb[1],
                (int) $rgb[2],
            ];
        }

        if (is_object($rgb) && method_exists($rgb, 'toArray')) {
            return array_map('intval', $rgb->toArray());
        }

        if (is_string($rgb)) {
            $hexRgb = self::parseHexRgb($rgb);

            if ($hexRgb !== null) {
                return $hexRgb;
            }

            $parts = array_map('trim', explode(',', $rgb));

            if (count($parts) === 3) {
                return array_map('intval', $parts);
            }
        }

        return [0, 0, 0];
    }

    protected static function parseHexRgb(string $value): ?array
    {
        return HexColor::toRgb($value);
    }

    protected static function squaredDistance(array $a, array $b): float
    {
        return ($a[0] - $b[0]) ** 2 +
            ($a[1] - $b[1]) ** 2 +
            ($a[2] - $b[2]) ** 2;
    }

    protected static function fallbackCatalog(): array
    {
        return [
            ['name' => 'black', 'rgb' => [0, 0, 0]],
            ['name' => 'green', 'rgb' => [0, 128, 0]],
            ['name' => 'silver', 'rgb' => [192, 192, 192]],
            ['name' => 'lime', 'rgb' => [0, 255, 0]],
            ['name' => 'gray', 'rgb' => [128, 128, 128]],
            ['name' => 'olive', 'rgb' => [128, 128, 0]],
            ['name' => 'white', 'rgb' => [255, 255, 255]],
            ['name' => 'yellow', 'rgb' => [255, 255, 0]],
            ['name' => 'maroon', 'rgb' => [128, 0, 0]],
            ['name' => 'navy', 'rgb' => [0, 0, 128]],
            ['name' => 'red', 'rgb' => [255, 0, 0]],
            ['name' => 'blue', 'rgb' => [0, 0, 255]],
            ['name' => 'purple', 'rgb' => [128, 0, 128]],
            ['name' => 'teal', 'rgb' => [0, 128, 128]],
            ['name' => 'fuchsia', 'rgb' => [255, 0, 255]],
            ['name' => 'aqua', 'rgb' => [0, 255, 255]],
        ];
    }
}
