<?php
// helpers/FileHelper.php

class FileHelper {
    /**
     * Parse evidence_file raw string into an array of filenames.
     * Supports JSON array string, comma-separated string, or legacy single filename.
     */
    public static function parseEvidenceFiles(?string $raw): array {
        if (empty($raw)) {
            return [];
        }
        $raw = trim($raw);
        if (strpos($raw, '[') === 0 && substr($raw, -1) === ']') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }
        }
        if (strpos($raw, ',') !== false) {
            $parts = array_map('trim', explode(',', $raw));
            return array_values(array_filter($parts));
        }
        return [$raw];
    }

    /**
     * Check if a filename is an image.
     */
    public static function isImage(string $filename): bool {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'], true);
    }

    /**
     * Categorize parsed files into images and documents.
     */
    public static function categorizeFiles(?string $raw): array {
        $files = self::parseEvidenceFiles($raw);
        $images = [];
        $docs = [];
        foreach ($files as $f) {
            if (self::isImage($f)) {
                $images[] = $f;
            } else {
                $docs[] = $f;
            }
        }
        return [
            'all'    => $files,
            'images' => $images,
            'docs'   => $docs,
            'total'  => count($files)
        ];
    }

    /**
     * Get font awesome icon class according to file extension.
     */
    public static function getIcon(string $filename): string {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'pdf':
                return 'fa-solid fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fa-solid fa-file-word';
            case 'xls':
            case 'xlsx':
            case 'csv':
                return 'fa-solid fa-file-excel';
            case 'ppt':
            case 'pptx':
                return 'fa-solid fa-file-powerpoint';
            case 'zip':
            case 'rar':
            case '7z':
            case 'tar':
            case 'gz':
                return 'fa-solid fa-file-zipper';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
            case 'gif':
                return 'fa-solid fa-file-image';
            case 'txt':
                return 'fa-solid fa-file-lines';
            default:
                return 'fa-solid fa-file';
        }
    }

    /**
     * Get color hex code according to file extension.
     */
    public static function getColor(string $filename): string {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'pdf':
                return '#ef4444';
            case 'doc':
            case 'docx':
                return '#2563eb';
            case 'xls':
            case 'xlsx':
            case 'csv':
                return '#16a34a';
            case 'ppt':
            case 'pptx':
                return '#ea580c';
            case 'zip':
            case 'rar':
            case '7z':
                return '#d97706';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
            case 'gif':
                return '#7c3aed';
            default:
                return '#64748b';
        }
    }

    /**
     * Format file size from bytes to human readable format.
     */
    public static function formatBytes(int $bytes): string {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get human readable file size if file exists on disk.
     */
    public static function getFileSizeFormatted(string $filename): string {
        $path = __DIR__ . '/../public/uploads/' . basename($filename);
        if (!file_exists($path)) {
            return '-';
        }
        $bytes = @filesize($path);
        if ($bytes === false) {
            return '-';
        }
        return self::formatBytes($bytes);
    }

    /**
     * Safely delete files from disk.
     */
    public static function deleteFiles(array $filenames): void {
        $uploadDir = __DIR__ . '/../public/uploads/';
        foreach ($filenames as $fn) {
            $path = $uploadDir . basename($fn);
            if (file_exists($path) && is_file($path)) {
                @unlink($path);
            }
        }
    }
}
