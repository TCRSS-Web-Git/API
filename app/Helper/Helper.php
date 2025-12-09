<?php

namespace App\Helper;

class Helper
{
    public static function getPlaceholderImageUrl(string $text = 'image', int $width = 500, int $height = 500, ?string $bgHexColor = null, $textHexColor = null): string
    {
        if (empty($bgHexColor) && empty($textHexColor)) {
            $bgHexColor = self::generateRandomColor();
            $textHexColor = self::getComplementaryColor($bgHexColor);
        } else {
            $bgHexColor = $bgHexColor ?? 'd4d4d4';
            $textHexColor = $textHexColor ?? '878787';
        }
        // Remove the '#' if it exists
        $bgHexColor = ltrim($bgHexColor, '#');
        $textHexColor = ltrim($textHexColor, '#');
        $text = urlencode($text);

        return "https://placehold.co/{$width}x{$height}/{$bgHexColor}/{$textHexColor}?text={$text}";
    }

    public static function generateRandomColor(): string
    {
        $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        return $randomColor;
    }

    public static function getComplementaryColor($hexColor): string
    {
        // Remove the '#' if it exists
        $hexColor = ltrim($hexColor, '#');

        // Convert hex to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // Get the complementary color by subtracting each component from 255
        $compR = 255 - $r;
        $compG = 255 - $g;
        $compB = 255 - $b;

        // Convert back to hex
        return sprintf('#%02X%02X%02X', $compR, $compG, $compB);
    }
}
