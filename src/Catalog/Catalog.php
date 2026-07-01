<?php

namespace wwaz\Colornames\Catalog;

use wwaz\Colornames\Support\HexColor;

class Catalog
{
    protected array $entries;

    public function __construct(array|string $entries = [])
    {
        if (is_string($entries)) {
            $entries = json_decode($entries, true) ?? [];
        }

        $this->entries = self::normalize($entries);
    }

    public static function fromDefault(): self
    {
        return self::fromFile(self::storagePath('meodai/ntc.json'));
    }

    public static function resolve(string|self|null $catalog): self
    {
        if ($catalog === null) {
            return self::fromDefault();
        }

        if ($catalog instanceof self) {
            return $catalog;
        }

        return self::fromFile(self::resolvePath($catalog));
    }

    public static function fromFile(string $path): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new \RuntimeException('Color names file not found: ' . $path);
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException('Color names file not found: ' . $path);
        }

        $data = json_decode($contents, true);

        if (! is_array($data)) {
            throw new \RuntimeException('Invalid color names JSON: ' . $path);
        }

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->entries;
    }

    protected static function normalize(array $entries): array
    {
        $parsed = [];

        foreach ($entries as $entry) {
            if (! is_array($entry) || ! isset($entry['name'])) {
                continue;
            }

            $rgb = self::resolveRgb($entry);

            if ($rgb === null) {
                continue;
            }

            $parsed[] = [
                'name' => (string) $entry['name'],
                'rgb' => $rgb,
            ];
        }

        return $parsed;
    }

    protected static function storagePath(string $filename): string
    {
        return dirname(__DIR__, 2) . '/storage/' . $filename;
    }

    protected static function resolvePath(string $catalog): string
    {
        if (self::isAbsolutePath($catalog)) {
            return $catalog;
        }

        if (! str_contains($catalog, '/') && ! str_contains($catalog, '\\')) {
            return self::storagePath($catalog);
        }

        return $catalog;
    }

    protected static function isAbsolutePath(string $path): bool
    {
        if (str_starts_with($path, '/')) {
            return true;
        }

        return PHP_OS_FAMILY === 'Windows' && (bool) preg_match('/^[A-Za-z]:[\\\\\/]/', $path);
    }

    protected static function resolveRgb(array $entry): ?array
    {
        if (isset($entry['hex'])) {
            return HexColor::toRgb((string) $entry['hex']);
        }

        if (! isset($entry['rgb']) || ! is_array($entry['rgb']) || count($entry['rgb']) < 3) {
            return null;
        }

        return [
            (int) $entry['rgb'][0],
            (int) $entry['rgb'][1],
            (int) $entry['rgb'][2],
        ];
    }
}
