<?php

namespace wwaz\Colorname\Support;

class HexColor
{
    public static function toRgb(string $hex): ?array
    {
        $expanded = self::expand($hex);

        if ($expanded === null) {
            return null;
        }

        return [
            hexdec(substr($expanded, 0, 2)),
            hexdec(substr($expanded, 2, 2)),
            hexdec(substr($expanded, 4, 2)),
        ];
    }

    public static function expand(string $hex): ?string
    {
        $hex = ltrim(trim($hex), '#');

        if (preg_match('/^[0-9a-fA-F]{3}$/', $hex)) {
            return $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return $hex;
        }

        return null;
    }
}
