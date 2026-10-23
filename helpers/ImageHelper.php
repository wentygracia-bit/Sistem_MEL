<?php
// Image compression helper

class ImageHelper {
    /**
     * Compress an uploaded image if GD extension is available and file is an image.
     * Resizes if larger than $maxWidth/$maxHeight and compresses with JPEG quality or PNG compression.
     */
    public static function compressAndSave(string $sourcePath, string $destinationPath, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 75): bool {
        if (!extension_loaded('gd')) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            // Not a valid image (e.g. PDF or document), move standardly
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        [$width, $height, $type] = $imageInfo;

        $sourceImage = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = @imagecreatefromwebp($sourcePath);
                }
                break;
        }

        if (!$sourceImage) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        // Calculate aspect-ratio preserved dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1.0);
        $targetWidth = (int) round($width * $ratio);
        $targetHeight = (int) round($height * $ratio);

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG and WEBP
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $saved = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $saved = imagejpeg($targetImage, $destinationPath, $quality);
                break;
            case IMAGETYPE_PNG:
                // PNG quality parameter is compression level from 0 (no compression) to 9
                $saved = imagepng($targetImage, $destinationPath, 6);
                break;
            case IMAGETYPE_WEBP:
                $saved = function_exists('imagewebp') ? imagewebp($targetImage, $destinationPath, $quality) : false;
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if (!$saved) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        @unlink($sourcePath);
        return true;
    }
}
