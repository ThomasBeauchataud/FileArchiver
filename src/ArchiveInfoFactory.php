<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Filesystem\Path;

class ArchiveInfoFactory
{

    /**
     * @param string $archivedFilename
     * @return ArchiveInfo
     * @throws Exception
     */
    public static function buildFromArchivedFilename(string $archivedFilename): ArchiveInfo
    {
        $explode = explode('_', Path::getFilenameWithoutExtension($archivedFilename));
        $expiresAt = new DateTime($explode[count($explode) - 1]);
        $archivedAt = new DateTime($explode[count($explode) - 2]);
        $suffix = self::createSuffix($archivedAt, $expiresAt);
        $filename = substr($archivedFilename, 0, strrpos($archivedFilename, $suffix)) . '.' . Path::getExtension($archivedFilename);
        return new ArchiveInfo($filename, $archivedFilename, $archivedAt, $expiresAt);
    }

    /**
     * @param string $filename
     * @param DateInterval $archiveDuration
     * @return ArchiveInfo
     */
    public static function buildFromFilenameAndDuration(string $filename, DateInterval $archiveDuration): ArchiveInfo
    {
        $now = new DateTime();
        $realFilename = Path::getFilenameWithoutExtension($filename);
        $extension = Path::getExtension($filename);
        $archivedAt = clone $now;
        $expiresAt = $now->add($archiveDuration);
        $suffix = self::createSuffix($archivedAt, $expiresAt);
        $archivedFilename = sprintf("%s%s.%s", $realFilename, $suffix, $extension);
        return new ArchiveInfo($filename, $archivedFilename, $archivedAt, $expiresAt);
    }

    /**
     * @param DateTimeInterface $archivedAt
     * @param DateTimeInterface $expiresAt
     * @return string
     */
    private static function createSuffix(DateTimeInterface $archivedAt, DateTimeInterface $expiresAt): string
    {
        return sprintf("_%s_%s", $archivedAt->format('YmdHis'), $expiresAt->format('YmdHis'));
    }
}