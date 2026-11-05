<?php

declare(strict_types=1);

namespace App\Core;

final class ImageProcessor
{
    /** @var array<string, int> */
    public const SIZES = [
        'thumb' => 480,
        'medium' => 960,
        'large' => 1600,
    ];

    /** @var list<string> */
    private const MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * @param array{tmp_name?:string, size?:int, error?:int, name?:string} $file
     * @return array{stem:string, width:int, height:int, meta:array<string, mixed>}
     */
    public function storeUpload(array $file, string $directory): array
    {
        $max = (int) (config('app')['upload_max_bytes'] ?? (8 * 1024 * 1024));
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload failed.');
        }
        $tmp = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new \RuntimeException('Upload was not accepted.');
        }
        if ($size <= 0 || $size > $max) {
            throw new \RuntimeException('Each image must be 8MB or smaller.');
        }
        return $this->processFile($tmp, $directory);
    }

    /**
     * @return array{stem:string, width:int, height:int, meta:array<string, mixed>}
     */
    public function processFile(string $source, string $directory): array
    {
        $mime = $this->mime($source);
        if (!in_array($mime, self::MIME, true)) {
            throw new \RuntimeException('Only JPEG, PNG, WebP, and GIF images are allowed.');
        }
        $binary = file_get_contents($source);
        if ($binary === false) {
            throw new \RuntimeException('The image could not be read.');
        }
        $image = @imagecreatefromstring($binary);
        if ($image === false) {
            throw new \RuntimeException('The image could not be decoded.');
        }
        $image = $this->orient($image, $source, $mime);
        return $this->writeSet($image, $directory);
    }

    /**
     * @return array{stem:string, width:int, height:int, meta:array<string, mixed>}
     */
    public function placeholder(string $directory, string $label): array
    {
        $image = imagecreatetruecolor(1600, 1000);
        if ($image === false) {
            throw new \RuntimeException('Placeholder image failed.');
        }
        $bg = imagecolorallocate($image, 7, 11, 10);
        $accent = imagecolorallocate($image, 16, 185, 129);
        $ink = imagecolorallocate($image, 232, 241, 238);
        imagefilledrectangle($image, 0, 0, 1600, 1000, $bg);
        imagefilledrectangle($image, 80, 80, 1520, 920, imagecolorallocate($image, 13, 20, 18));
        imagestring($image, 5, 120, 430, $this->ascii($label), $ink);
        imagestring($image, 3, 120, 470, 'Placeholder — source image unavailable', $accent);
        $written = $this->writeSet($image, $directory);
        return $written;
    }

    public function deleteSet(string $directory, string $stem): void
    {
        if (!preg_match('/^[a-f0-9]{20}$/', $stem)) {
            return;
        }
        foreach (array_keys(self::SIZES) as $name) {
            foreach (['webp', 'jpg'] as $ext) {
                $path = rtrim($directory, '/') . '/' . $stem . '-' . $name . '.' . $ext;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }
    }

    public function deleteTree(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $items = scandir($directory);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $directory . '/' . $item;
            if (is_dir($path)) {
                $this->deleteTree($path);
                @rmdir($path);
            } elseif (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /** @return \GdImage */
    private function orient(\GdImage $image, string $source, string $mime): \GdImage
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return $image;
        }
        $exif = @exif_read_data($source);
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
        if ($rotated instanceof \GdImage && $rotated !== $image) {
            imagedestroy($image);
            return $rotated;
        }
        return $image;
    }

    /**
     * @return array{stem:string, width:int, height:int, meta:array<string, mixed>}
     */
    private function writeSet(\GdImage $image, string $directory): array
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            imagedestroy($image);
            throw new \RuntimeException('Upload directory could not be created.');
        }
        $width = imagesx($image);
        $height = imagesy($image);
        $stem = bin2hex(random_bytes(10));
        $widths = [];
        foreach (self::SIZES as $name => $target) {
            $targetWidth = min($target, $width);
            $targetHeight = (int) max(1, round($height * ($targetWidth / $width)));
            $frame = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($frame === false) {
                continue;
            }
            $white = imagecolorallocate($frame, 255, 255, 255);
            imagefilledrectangle($frame, 0, 0, $targetWidth, $targetHeight, $white);
            imagecopyresampled($frame, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            $base = rtrim($directory, '/') . '/' . $stem . '-' . $name;
            imagewebp($frame, $base . '.webp', 82);
            imagejpeg($frame, $base . '.jpg', 82);
            imagedestroy($frame);
            $widths[$name] = $targetWidth;
        }
        $blur = '';
        try {
            $blur = $this->blurData($image);
        } catch (\Throwable) {
            $blur = '';
        }
        imagedestroy($image);
        return [
            'stem' => $stem,
            'width' => $width,
            'height' => $height,
            'meta' => [
                'widths' => $widths,
                'blur' => $blur,
            ],
        ];
    }

    private function blurData(\GdImage $image): string
    {
        $frame = imagecreatetruecolor(16, 10);
        if ($frame === false) {
            return '';
        }
        imagecopyresampled($frame, $image, 0, 0, 0, 0, 16, 10, imagesx($image), imagesy($image));
        $temp = tempnam(sys_get_temp_dir(), 'blur');
        if ($temp === false) {
            imagedestroy($frame);
            return '';
        }
        imagewebp($frame, $temp, 40);
        $binary = file_get_contents($temp);
        @unlink($temp);
        imagedestroy($frame);
        if (!is_string($binary) || $binary === '') {
            return '';
        }
        return 'data:image/webp;base64,' . base64_encode($binary);
    }

    private function mime(string $path): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        return is_string($mime) ? $mime : '';
    }

    private function ascii(string $label): string
    {
        $label = preg_replace('/[^\x20-\x7E]/', '', $label) ?? 'Image';
        return substr($label, 0, 60);
    }
}
