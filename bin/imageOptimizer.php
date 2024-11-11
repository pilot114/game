<?php

declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

use PhpTui\Term\Terminal;
use PhpTui\Term\TerminalInformation\Size;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;

function geTopColors(Imagick $image, int $count = 32): array
{
    $histogram = $image->getImageHistogram();
    $totalPixels = $image->getImageWidth() * $image->getImageHeight();
    $colorStats = [];
    foreach ($histogram as $pixel) {
        $color = $pixel->getColor();
        $hexColor = sprintf("#%02x%02x%02x", $color['r'], $color['g'], $color['b']);
        $frequency = $pixel->getColorCount() / $totalPixels * 100;
        $colorStats[$hexColor] = $frequency;
    }
    arsort($colorStats);
    return array_slice($colorStats, 0 , $count);
}

function findNearestColor(array $color, array $palette): array
{
    $nearestColor = null;
    $nearestDistance = PHP_INT_MAX;

    foreach ($palette as $palColor) {
        $palRGB = sscanf($palColor, "#%02x%02x%02x");
        $distance = sqrt(pow($color['r'] - $palRGB[0], 2) + pow($color['g'] - $palRGB[1], 2) + pow($color['b'] - $palRGB[2], 2));
        if ($distance < $nearestDistance) {
            $nearestDistance = $distance;
            $nearestColor = $palRGB;
        }
    }
    return $nearestColor;
}

function resizeImage(string $sourcePath, string $destinationPath, int $newWidth, int $newHeight): void
{
    $image = new Imagick($sourcePath);

    // ограниченная палитра
//    $image->quantizeImage(64, $image->getImageColorspace(), 0, true, true);
    // ресайз
    $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_POINT, 0, true);

    $brightness = 105;
    $saturation = 135;
    $hue = 95;
    $image->modulateImage($brightness, $saturation, $hue);

    $image->writeImage($destinationPath);
    $image->clear();
    $image->destroy();
}

function batchResize(array $images, Size $size): void
{
    foreach ($images as $image) {
        [$path, $ext] = explode('.', realpath($image));
        resizeImage(
            $image,
            $path . '_resized.' . $ext,
            (int)($size->cols / 2) + 20,
            $size->lines * 2 + 20
        );
    }
}

$terminal = Terminal::new();
$display = DisplayBuilder::default()
    ->addExtension(new ImageMagickExtension())
    ->build();
$size = $terminal->info(Size::class);

// TODO: виджет, должен на лету определять, какой размер нужен и если картинки нет, делать и подставлять нужную

$images = glob(__DIR__ . '/../resources/images/hero/*');
$images = array_filter($images, static fn(string $x) => !str_contains($x, '_resized'));

batchResize($images, $size);
