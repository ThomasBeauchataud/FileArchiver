<?php

namespace TBCD\FileArchiver;

use DateInterval;
use DateTimeInterface;
use TBCD\FileArchiver\Exception\FileArchiverException;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
interface FileArchiverInterface
{

    public const THROW_ON_MISSING_FILE = 'THROW_ON_MISSING_FILE';

    /**
     * Archive a file for a specific duration
     *
     * @param string $filePath The absolute path of the file to archive
     * @param DateInterval $duration The duration of the archive
     * @param array $context THROW_ON_MISSING_FILE: determine if the method must throw an exception when the file doesn't exists
     * @return string|null The path of the archived file or null if nothing has been archived
     * @throws FileArchiverException
     */
    public function archive(string $filePath, DateInterval $duration, array $context = []): string|null;

    /**
     * @param DateTimeInterface|null $from
     * @return void
     * @throws FileArchiverException
     */
    public function clear(DateTimeInterface $from = null): void;

    /**
     * @param string $fileName
     * @return array
     * @throws FileArchiverException
     */
    public function find(string $fileName): array;

}