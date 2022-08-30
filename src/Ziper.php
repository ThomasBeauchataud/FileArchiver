<?php

namespace TBCD\FileArchiver;

use Symfony\Component\Filesystem\Path;
use TBCD\FileArchiver\Exception\ZiperException;
use ZipArchive;

/**
 * @author Thomas Beauchataud
 * @since 30/08/2022
 */
class Ziper
{

    public const DELETE_AFTER_ZIP = 'DELETE_AFTER_ZIP';
    public const DELETE_AFTER_UNZIP = 'DELETE_AFTER_UNZIP';

    /**
     * @param string|array $filePath
     * @param string $zipPath
     * @param array $options
     * @return void
     * @throws ZiperException
     */
    public static function zip(string|array $filePath, string $zipPath, array $options = []): void
    {
        if (Path::getExtension($zipPath) !== 'zip') {
            throw new ZiperException("The zipPath has to have a zip extension");
        }

        if (is_string($filePath)) {
            $filePath = [$filePath];
        }

        $zip = new ZipArchive();

        if (($error = $zip->open($zipPath, ZipArchive::CREATE)) !== true) {
            throw new ZiperException("Impossible to open the file $zipPath : Zip error code $error");
        }

        foreach ($filePath as $fp) {
            if (!file_exists($fp)) {
                throw new ZiperException("The file $fp doesn't exists");
            }
            $zip->addFile($fp, Path::getFilenameWithoutExtension($fp) . "." . Path::getExtension($fp));
        }

        $zip->close();

        if (in_array(self::DELETE_AFTER_ZIP, $options)) {
            foreach ($filePath as $fp) {
                unlink($fp);
            }
        }
    }

    /**
     * @param string $zipPath
     * @param string $extractionPath
     * @param string|array|null $files
     * @param array $options
     * @return void
     * @throws ZiperException
     */
    public static function unzip(string $zipPath, string $extractionPath, string|array|null $files = null, array $options = []): void
    {
        if (!file_exists($zipPath)) {
            throw new ZiperException("The zip $zipPath doesn't exists");
        }

        $zip = new ZipArchive();

        if (($error = $zip->open($zipPath, ZipArchive::CREATE)) !== true) {
            throw new ZiperException("Impossible to open the file $zipPath : Zip error code $error");
        }

        $zip->extractTo($extractionPath, $files);

        $zip->close();

        if (in_array(self::DELETE_AFTER_UNZIP, $options)) {
            unlink($zipPath);
        }
    }
}