<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasImages
{
    /**
     * Generates random image
     *
     * @param string $disk
     * @param string $fileName
     * @param int<1, max> $width
     * @param int<1, max> $height
     * @return string
     */
    protected function saveRandomImage(string $disk, string $fileName, int $width = 640, int $height = 480): string
    {
        $im = imagecreatetruecolor($width, $height);
        /** @var int<0, max> $bgColor */
        $bgColor = imagecolorallocate($im, rand(100, 255), rand(100, 255), rand(100, 255));
        imagefill($im, 0, 0, $bgColor);

        $path = Storage::disk($disk)->path($fileName . '.jpg');
        imagejpeg($im, $path);
        imagedestroy($im);

        return $fileName . '.jpg';
    }
}
