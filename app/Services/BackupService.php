<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use ZipArchive;

final class BackupService
{
    public static function download(): void
    {
        $pdo = Database::pdo();
        $tables = [];
        $stmt = $pdo->query('SHOW TABLES');
        if ($stmt !== false) {
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                if (isset($row[0])) {
                    $tables[] = (string) $row[0];
                }
            }
        }
        $sql = "-- Portfolio backup " . date('c') . "\nSET NAMES utf8mb4;\nSET foreign_key_checks = 0;\n";
        $json = [];
        foreach ($tables as $table) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
                continue;
            }
            $create = $pdo->query('SHOW CREATE TABLE `' . $table . '`');
            $createRow = $create ? $create->fetch(\PDO::FETCH_NUM) : false;
            if (is_array($createRow) && isset($createRow[1])) {
                $sql .= "\nDROP TABLE IF EXISTS `{$table}`;\n" . $createRow[1] . ";\n";
            }
            $rows = $pdo->query('SELECT * FROM `' . $table . '`');
            $data = $rows ? $rows->fetchAll() : [];
            $json[$table] = $data;
            foreach ($data as $record) {
                $cols = array_map(static fn ($col) => '`' . $col . '`', array_keys($record));
                $vals = [];
                foreach ($record as $value) {
                    $vals[] = $value === null ? 'NULL' : $pdo->quote((string) $value);
                }
                $sql .= 'INSERT INTO `' . $table . '` (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $vals) . ");\n";
            }
        }
        $sql .= "SET foreign_key_checks = 1;\n";
        $dir = project_root() . '/storage/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        $stamp = date('Ymd-His');
        $zipPath = $dir . '/portfolio-' . $stamp . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Backup archive could not be created.');
        }
        $zip->addFromString('database.sql', $sql);
        $zip->addFromString('database.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}');
        $uploads = Media::root();
        if (is_dir($uploads)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploads, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }
                $path = $file->getPathname();
                $local = 'uploads/' . ltrim(substr($path, strlen($uploads)), '/');
                $zip->addFile($path, $local);
            }
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="portfolio-' . $stamp . '.zip"');
        header('Content-Length: ' . (string) filesize($zipPath));
        header('X-Content-Type-Options: nosniff');
        readfile($zipPath);
        @unlink($zipPath);
        exit;
    }
}
